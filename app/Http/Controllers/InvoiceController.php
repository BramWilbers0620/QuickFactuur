<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Customer;
use App\Mail\InvoiceMail;
use App\Enums\InvoiceStatus;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Mail;
use PDF;

class InvoiceController extends Controller
{
    /**
     * Toon lijst van gemaakte facturen.
     */
    public function index(Request $request)
    {
        $this->ensureUserHasAccess();

        $query = Invoice::where('user_id', auth()->id());

        // Search by invoice number or customer name
        if ($search = $request->input('search')) {
            // Escape LIKE wildcards to prevent unintended pattern matching
            $escapedSearch = str_replace(['%', '_'], ['\\%', '\\_'], $search);
            $query->where(function ($q) use ($escapedSearch) {
                $q->where('invoice_number', 'like', "%{$escapedSearch}%")
                  ->orWhere('customer_name', 'like', "%{$escapedSearch}%")
                  ->orWhere('customer_email', 'like', "%{$escapedSearch}%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by date range
        if ($dateFrom = $request->input('date_from')) {
            $query->where('invoice_date', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->where('invoice_date', '<=', $dateTo);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Get filter options for the view
        $statuses = InvoiceStatus::options();

        return view('invoice.index', compact('invoices', 'statuses'));
    }

    /**
     * Toon het factuurformulier.
     */
    public function create()
    {
        $this->ensureUserHasAccess();

        // Get user's company profile for pre-filling
        $user = auth()->user();

        // Generate next invoice number preview using user's prefix
        $prefix = $user->invoice_prefix ?? 'FAC';
        $invoiceCount = Invoice::withTrashed()->where('user_id', $user->id)->count();
        $nextInvoiceNumber = $prefix . sprintf('%04d', $invoiceCount + 1);
        $companyProfile = [
            'name' => $user->company_name,
            'address' => $user->company_address,
            'email' => $user->email,
            'phone' => $user->company_phone,
            'kvk' => $user->company_kvk,
            'btw' => $user->company_btw,
            'iban' => $user->company_iban,
        ];

        // Get user's customers for dropdown
        $customers = Customer::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        return view('invoice.form', compact('nextInvoiceNumber', 'companyProfile', 'customers'));
    }

    /**
     * Genereer een PDF factuur.
     */
    public function generate(StoreInvoiceRequest $request)
    {
        $this->ensureUserHasAccess();

        $validated = $request->validated();

        // Handle logo upload - save to disk and convert to base64 for PDF
        $logoData = null;
        $logoPath = null;
        $logoWarning = null;

        if ($request->hasFile('logo')) {
            try {
                $file = $request->file('logo');

                // Validate actual MIME type (not client-provided extension)
                $actualMimeType = $file->getMimeType();
                $allowedMimeTypes = ['image/png', 'image/jpeg'];

                if (!in_array($actualMimeType, $allowedMimeTypes)) {
                    $logoWarning = 'Logo heeft een ongeldig bestandstype. Alleen PNG en JPG zijn toegestaan.';
                    Log::warning('Logo rejected: invalid MIME type', ['mime' => $actualMimeType]);
                } else {
                    // Determine extension from actual MIME type
                    $extension = $actualMimeType === 'image/png' ? 'png' : 'jpg';

                    // Generate safe filename
                    $logoFileName = 'logo-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $extension;
                    $logoPath = 'logos/' . auth()->id() . '/' . $logoFileName;
                    Storage::disk('local')->put($logoPath, file_get_contents($file->getRealPath()));

                    // Convert to base64 for PDF generation
                    $imageData = file_get_contents($file->getRealPath());
                    $logoData = 'data:' . $actualMimeType . ';base64,' . base64_encode($imageData);

                    Log::info('Logo saved', ['path' => $logoPath, 'user_id' => auth()->id()]);
                }
            } catch (\Exception $e) {
                $logoWarning = 'Logo kon niet worden verwerkt. De factuur is aangemaakt zonder logo.';
                Log::warning('Logo processing failed', ['error' => $e->getMessage()]);
            }
        }

        $vatRate = $validated['vat_rate'] / 100;

        // Process items and calculate totals
        $processedItems = [];
        $subtotal = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = round($item['rate'] * $item['quantity'], 2);
            $processedItems[] = [
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['rate'],
                'total' => $lineTotal,
            ];
            $subtotal += $lineTotal;
        }

        $vat = round($subtotal * $vatRate, 2);
        $total = round($subtotal + $vat, 2);

        // Calculate due date
        $paymentDays = $validated['payment_terms'] === 'direct' ? 0 : (int) $validated['payment_terms'];
        $dueDate = Carbon::parse($validated['invoice_date'])->addDays($paymentDays)->format('d-m-Y');

        // Generate invoice number will be done inside transaction
        $invoiceNumber = null;

        $data = [
            'invoice_number' => null, // Will be set after generation
            'date' => Carbon::parse($validated['invoice_date'])->format('d-m-Y'),
            'due_date' => $dueDate,
            'payment_terms' => $validated['payment_terms'],
            'logo_data' => $logoData,
            'brand_color' => $validated['brand_color'],
            'company' => [
                'name' => $validated['company_name'],
                'email' => $validated['company_email'],
                'address' => $validated['company_address'],
                'phone' => $validated['company_phone'] ?? null,
                'kvk' => $validated['company_kvk'] ?? null,
                'btw' => $validated['company_btw'] ?? null,
                'iban' => $validated['company_iban'] ?? null,
            ],
            'customer' => [
                'name' => $validated['customer_name'],
                'email' => $validated['customer_email'] ?? null,
                'address' => $validated['customer_address'] ?? null,
                'phone' => $validated['customer_phone'] ?? null,
            ],
            'items' => $processedItems,
            'subtotal' => $subtotal,
            'vat_amount' => $vat,
            'vat_percentage' => $validated['vat_rate'],
            'total' => $total,
            'notes' => $validated['notes'] ?? null,
        ];

        try {
            // Calculate due date for database storage
            $dueDateForDb = Carbon::parse($validated['invoice_date'])->addDays($paymentDays);

            // Use transaction to ensure data consistency
            $result = DB::transaction(function () use ($validated, $processedItems, $subtotal, $vat, $total, $dueDateForDb, $logoData, $logoPath, &$data, &$invoiceNumber) {
                // Generate invoice number with locking to prevent race conditions
                $invoiceNumber = Invoice::generateNextNumber(auth()->id());

                // Update data array with the generated invoice number
                $data['invoice_number'] = $invoiceNumber;

                // Store invoice in database
                $invoice = Invoice::create([
                    'user_id' => auth()->id(),
                    'invoice_number' => $invoiceNumber,
                    'company_name' => $validated['company_name'],
                    'company_email' => $validated['company_email'],
                    'company_address' => $validated['company_address'],
                    'company_phone' => $validated['company_phone'] ?? null,
                    'company_kvk' => $validated['company_kvk'] ?? null,
                    'company_vat' => $validated['company_btw'] ?? null,
                    'company_iban' => $validated['company_iban'] ?? null,
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'] ?? null,
                    'customer_address' => $validated['customer_address'] ?? null,
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'customer_vat' => $validated['customer_vat'] ?? null,
                    'invoice_date' => $validated['invoice_date'],
                    'due_date' => $dueDateForDb,
                    'payment_terms' => $validated['payment_terms'],
                    'description' => $processedItems[0]['description'] ?? 'Diverse',
                    'items' => $processedItems,
                    'amount' => $subtotal,
                    'vat_amount' => $vat,
                    'total' => $total,
                    'vat_rate' => $validated['vat_rate'],
                    'notes' => $validated['notes'] ?? null,
                    'brand_color' => $validated['brand_color'],
                    'logo_path' => $logoPath,
                ]);

                return $invoice;
            });

            $invoice = $result;

            // Generate PDF outside transaction (file operations shouldn't be in DB transaction)
            $pdf = PDF::loadView('invoice.pdf', $data);

            // Save PDF to storage
            $pdfFileName = 'factuur-' . $invoiceNumber . '.pdf';
            $pdfPath = 'invoices/' . auth()->id() . '/' . $pdfFileName;
            Storage::disk('local')->put($pdfPath, $pdf->output());

            // Update invoice with PDF path
            $invoice->update(['pdf_path' => $pdfPath]);

            Log::info('Invoice generated', [
                'user_id' => auth()->id(),
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoiceNumber,
                'amount' => $total,
            ]);

            // Clear dashboard cache since we have new data
            DashboardController::clearStatsCache(auth()->id());

            // Flash logo warning if there was one
            if ($logoWarning) {
                session()->flash('warning', $logoWarning);
            }

            return $pdf->download($pdfFileName);
        } catch (\Exception $e) {
            Log::error('Invoice generation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Er ging iets mis bij het genereren van de factuur. Probeer het opnieuw.');
        }
    }

    /**
     * Download een opgeslagen factuur PDF.
     */
    public function download(Invoice $invoice)
    {
        $this->authorize('download', $invoice);

        // Check if PDF exists
        if (!$invoice->pdf_path || !Storage::disk('local')->exists($invoice->pdf_path)) {
            return redirect()->route('invoice.index')
                ->with('error', 'PDF bestand niet gevonden. Deze factuur is mogelijk aangemaakt voordat PDF opslag werd geactiveerd.');
        }

        return Storage::disk('local')->download(
            $invoice->pdf_path,
            'factuur-' . $invoice->invoice_number . '.pdf'
        );
    }

    /**
     * Update invoice status.
     */
    public function updateStatus(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $validated = $request->validate([
            'status' => 'required|in:concept,verzonden,betaald,te_laat',
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'verzonden' && !$invoice->sent_at) {
            $updateData['sent_at'] = now();
        }

        if ($validated['status'] === 'betaald' && !$invoice->paid_at) {
            $updateData['paid_at'] = now();
        }

        $invoice->update($updateData);

        // Return JSON for AJAX requests, redirect for normal requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status bijgewerkt naar ' . InvoiceStatus::from($validated['status'])->label()
            ]);
        }

        return redirect()->back()->with('success', 'Status bijgewerkt naar ' . InvoiceStatus::from($validated['status'])->label());
    }

    /**
     * Send invoice via email.
     */
    public function sendEmail(Invoice $invoice)
    {
        $this->authorize('sendEmail', $invoice);

        if (!$invoice->customer_email) {
            return redirect()->back()->with('error', 'Deze klant heeft geen e-mailadres.');
        }

        if (!$invoice->pdf_path || !Storage::disk('local')->exists($invoice->pdf_path)) {
            return redirect()->back()->with('error', 'PDF bestand niet gevonden.');
        }

        try {
            Mail::to($invoice->customer_email)->send(new InvoiceMail($invoice));

            $invoice->update([
                'status' => 'verzonden',
                'sent_at' => now(),
            ]);

            Log::info('Invoice emailed', [
                'invoice_id' => $invoice->id,
                'to' => $invoice->customer_email,
            ]);

            return redirect()->back()->with('success', 'Factuur verzonden naar ' . $invoice->customer_email);
        } catch (\Exception $e) {
            Log::error('Invoice email failed', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Er ging iets mis bij het versturen van de e-mail.');
        }
    }

    /**
     * Show invoice details.
     */
    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return view('invoice.show', compact('invoice'));
    }

    /**
     * Duplicate an existing invoice (show form pre-filled with invoice data).
     */
    public function duplicate(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $this->ensureUserHasAccess();

        $user = auth()->user();

        // Generate next invoice number preview
        $prefix = $user->invoice_prefix ?? 'FAC';
        $invoiceCount = Invoice::withTrashed()->where('user_id', $user->id)->count();
        $nextInvoiceNumber = $prefix . sprintf('%04d', $invoiceCount + 1);

        $companyProfile = [
            'name' => $user->company_name,
            'address' => $user->company_address,
            'email' => $user->email,
            'phone' => $user->company_phone,
            'kvk' => $user->company_kvk,
            'btw' => $user->company_btw,
            'iban' => $user->company_iban,
        ];

        $customers = Customer::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        // Prepare duplicate data (invoice data to pre-fill the form)
        $duplicateData = [
            'customer_name' => $invoice->customer_name,
            'customer_email' => $invoice->customer_email,
            'customer_address' => $invoice->customer_address,
            'customer_phone' => $invoice->customer_phone,
            'customer_vat' => $invoice->customer_vat,
            'items' => $invoice->items,
            'vat_rate' => $invoice->vat_rate,
            'payment_terms' => $invoice->payment_terms,
            'notes' => $invoice->notes,
            'brand_color' => $invoice->brand_color,
        ];

        return view('invoice.form', compact('nextInvoiceNumber', 'companyProfile', 'customers', 'duplicateData'));
    }

    /**
     * Check if user has active access, redirect to billing if not.
     *
     * @throws HttpResponseException
     */
    private function ensureUserHasAccess(): void
    {
        if (!auth()->user()->hasActiveAccess()) {
            $errorMessage = auth()->user()->trial_ends_at && now()->gt(auth()->user()->trial_ends_at)
                ? 'Je gratis trial is verlopen. Kies een abonnement om facturen aan te maken.'
                : 'Je hebt een actief abonnement nodig om facturen aan te maken.';

            throw new HttpResponseException(
                redirect()->route('billing')->with('error', $errorMessage)
            );
        }
    }
}
