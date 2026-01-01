<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Customer;
use App\Mail\InvoiceMail;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Mail;
use PDF;

class InvoiceController extends Controller
{
    /**
     * Toon lijst van gemaakte facturen.
     */
    public function index()
    {
        $this->ensureUserHasAccess();

        $invoices = Invoice::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('invoice.index', compact('invoices'));
    }

    /**
     * Toon het factuurformulier.
     */
    public function create()
    {
        $this->ensureUserHasAccess();

        // Generate next invoice number based on user's invoice count
        $invoiceCount = Invoice::where('user_id', auth()->id())->count();
        $nextInvoiceNumber = sprintf('FAC%04d', $invoiceCount + 1);

        // Get user's company profile for pre-filling
        $user = auth()->user();
        $companyProfile = [
            'name' => $user->company_name,
            'address' => $user->company_address,
            'email' => $user->email,
            'phone' => $user->company_phone,
            'kvk' => $user->company_kvk,
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
    public function generate(Request $request)
    {
        $this->ensureUserHasAccess();

        $validated = $request->validate([
            // Logo and branding
            'logo' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'brand_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',

            // Company fields
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_address' => 'required|string|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_kvk' => 'nullable|string|max:20',
            'company_iban' => 'nullable|string|max:50',

            // Customer fields
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',

            // Invoice fields
            'invoice_date' => 'required|date',
            'payment_terms' => 'required|string|in:14,30,60,direct',
            'vat_rate' => 'required|integer|in:0,9,21',
            'notes' => 'nullable|string|max:2000',

            // Items
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.rate' => 'required|numeric|min:0|max:999999.99',
            'items.*.quantity' => 'required|integer|min:1|max:10000',
        ], [
            'logo.max' => 'Het logo mag maximaal 2MB zijn.',
            'logo.mimes' => 'Het logo moet een PNG of JPG bestand zijn.',
            'items.required' => 'Voeg minimaal één factuurregel toe.',
            'items.*.description.required' => 'Vul een beschrijving in voor elke regel.',
            'items.*.rate.required' => 'Vul een tarief in voor elke regel.',
            'items.*.quantity.required' => 'Vul een aantal in voor elke regel.',
        ]);

        // Handle logo upload - convert to base64 for PDF
        // Note: Requires PHP GD extension to be enabled
        $logoData = null;
        $logoWarning = null;

        if ($request->hasFile('logo')) {
            if (!extension_loaded('gd')) {
                $logoWarning = 'Logo kon niet worden verwerkt: GD extensie niet beschikbaar.';
                Log::warning('Logo processing failed: GD extension not loaded');
            } else {
                try {
                    $file = $request->file('logo');
                    $imageData = file_get_contents($file->getRealPath());

                    // Determine mime type from extension
                    $extension = strtolower($file->getClientOriginalExtension());
                    $mimeTypes = [
                        'png' => 'image/png',
                        'jpg' => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                    ];
                    $mimeType = $mimeTypes[$extension] ?? 'image/png';

                    $logoData = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
                } catch (\Exception $e) {
                    $logoWarning = 'Logo kon niet worden verwerkt. De factuur is aangemaakt zonder logo.';
                    Log::warning('Logo processing failed', ['error' => $e->getMessage()]);
                }
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
            $result = DB::transaction(function () use ($validated, $processedItems, $subtotal, $vat, $total, $dueDateForDb, $logoData, &$data, &$invoiceNumber) {
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
                    'company_iban' => $validated['company_iban'] ?? null,
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'] ?? null,
                    'customer_address' => $validated['customer_address'] ?? null,
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'invoice_date' => $validated['invoice_date'],
                    'due_date' => $dueDateForDb,
                    'payment_terms' => $validated['payment_terms'],
                    'description' => $processedItems[0]['description'] ?? 'Diverse',
                    'items' => $processedItems,
                    'amount' => $subtotal,
                    'vat_amount' => $vat,
                    'total' => $total,
                    'notes' => $validated['notes'] ?? null,
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

        return redirect()->back()->with('success', 'Status bijgewerkt naar ' . Invoice::$statusLabels[$validated['status']]);
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
     * Check if user has active access, redirect to billing if not.
     */
    private function ensureUserHasAccess(): void
    {
        if (!auth()->user()->hasActiveAccess()) {
            $errorMessage = auth()->user()->trial_ends_at && now()->gt(auth()->user()->trial_ends_at)
                ? 'Je gratis trial is verlopen. Kies een abonnement om facturen aan te maken.'
                : 'Je hebt een actief abonnement nodig om facturen aan te maken.';

            abort(redirect()->route('billing')->with('error', $errorMessage));
        }
    }
}
