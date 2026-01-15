<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Quote;
use App\Models\Customer;
use App\Http\Controllers\DashboardController;
use PDF;

class QuoteController extends Controller
{
    /**
     * Display a listing of quotes.
     */
    public function index()
    {
        $this->ensureUserHasAccess();

        $quotes = Quote::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('quotes.index', compact('quotes'));
    }

    /**
     * Show the form for creating a new quote.
     */
    public function create()
    {
        $this->ensureUserHasAccess();

        $user = auth()->user();

        // Generate next quote number preview using user's prefix
        $prefix = $user->quote_prefix ?? 'OFF';
        $quoteCount = Quote::withTrashed()->where('user_id', $user->id)->count();
        $nextQuoteNumber = $prefix . sprintf('%04d', $quoteCount + 1);
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

        return view('quotes.form', compact('nextQuoteNumber', 'companyProfile', 'customers'));
    }

    /**
     * Generate a PDF quote.
     */
    public function generate(Request $request)
    {
        $this->ensureUserHasAccess();

        $validated = $request->validate([
            'logo' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'brand_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_address' => 'required|string|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_kvk' => 'nullable|string|max:20',
            'company_btw' => 'nullable|string|max:20',
            'company_iban' => 'nullable|string|max:50',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'quote_date' => 'required|date',
            'valid_days' => 'required|integer|in:14,30,60,90',
            'vat_rate' => 'required|integer|in:0,9,21',
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.rate' => 'required|numeric|min:0|max:999999.99',
            'items.*.quantity' => 'required|integer|min:1|max:10000',
        ]);

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

                    Log::info('Logo saved for quote', ['path' => $logoPath, 'user_id' => auth()->id()]);
                }
            } catch (\Exception $e) {
                $logoWarning = 'Logo kon niet worden verwerkt. De offerte is aangemaakt zonder logo.';
                Log::warning('Logo processing failed', ['error' => $e->getMessage()]);
            }
        }

        $vatRate = $validated['vat_rate'] / 100;

        // Process items
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

        $validUntil = Carbon::parse($validated['quote_date'])->addDays($validated['valid_days']);

        // Quote number will be generated inside transaction
        $quoteNumber = null;

        $data = [
            'quote_number' => null, // Will be set after generation
            'date' => Carbon::parse($validated['quote_date'])->format('d-m-Y'),
            'valid_until' => $validUntil->format('d-m-Y'),
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
            // Use transaction to ensure data consistency
            $result = DB::transaction(function () use ($validated, $processedItems, $subtotal, $vat, $total, $validUntil, $logoPath, &$data, &$quoteNumber) {
                // Generate quote number with locking to prevent race conditions
                $quoteNumber = Quote::generateNextNumber(auth()->id());

                // Update data array with the generated quote number
                $data['quote_number'] = $quoteNumber;

                $quote = Quote::create([
                    'user_id' => auth()->id(),
                    'quote_number' => $quoteNumber,
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
                    'quote_date' => $validated['quote_date'],
                    'valid_until' => $validUntil,
                    'description' => $processedItems[0]['description'] ?? 'Diverse',
                    'items' => $processedItems,
                    'amount' => $subtotal,
                    'vat_amount' => $vat,
                    'vat_rate' => $validated['vat_rate'],
                    'total' => $total,
                    'notes' => $validated['notes'] ?? null,
                    'brand_color' => $validated['brand_color'],
                    'logo_path' => $logoPath,
                    'status' => 'concept',
                ]);

                return $quote;
            });

            $quote = $result;

            // Generate PDF outside transaction (file operations shouldn't be in DB transaction)
            $pdf = PDF::loadView('quotes.pdf', $data);

            $pdfFileName = 'offerte-' . $quoteNumber . '.pdf';
            $pdfPath = 'quotes/' . auth()->id() . '/' . $pdfFileName;
            Storage::disk('local')->put($pdfPath, $pdf->output());

            $quote->update(['pdf_path' => $pdfPath]);

            // Clear dashboard cache since we have new data
            DashboardController::clearStatsCache(auth()->id());

            // Flash logo warning if there was one
            if ($logoWarning) {
                session()->flash('warning', $logoWarning);
            }

            return $pdf->download($pdfFileName);
        } catch (\Exception $e) {
            Log::error('Quote generation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Er ging iets mis bij het genereren van de offerte.');
        }
    }

    /**
     * Download a quote PDF.
     */
    public function download(Quote $quote)
    {
        $this->authorize('download', $quote);

        if (!$quote->pdf_path || !Storage::disk('local')->exists($quote->pdf_path)) {
            return redirect()->route('quotes.index')
                ->with('error', 'PDF bestand niet gevonden.');
        }

        return Storage::disk('local')->download(
            $quote->pdf_path,
            'offerte-' . $quote->quote_number . '.pdf'
        );
    }

    /**
     * Convert quote to invoice.
     */
    public function convertToInvoice(Quote $quote)
    {
        $this->authorize('convert', $quote);

        try {
            $invoice = $quote->convertToInvoice();

            // Clear dashboard cache since we have new data
            DashboardController::clearStatsCache(auth()->id());

            return redirect()->route('invoice.index')
                ->with('success', 'Offerte omgezet naar factuur ' . $invoice->invoice_number);
        } catch (\Exception $e) {
            Log::error('Quote to invoice conversion failed', [
                'quote_id' => $quote->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Er ging iets mis bij het omzetten naar een factuur.');
        }
    }

    /**
     * Update quote status.
     */
    public function updateStatus(Request $request, Quote $quote)
    {
        $this->authorize('update', $quote);

        $validated = $request->validate([
            'status' => 'required|in:concept,verzonden,geaccepteerd,afgewezen,verlopen',
        ]);

        $quote->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', 'Status bijgewerkt naar ' . Quote::$statusLabels[$validated['status']]);
    }

    /**
     * Check if user has active access.
     *
     * @throws HttpResponseException
     */
    private function ensureUserHasAccess(): void
    {
        if (!auth()->user()->hasActiveAccess()) {
            throw new HttpResponseException(
                redirect()->route('billing')->with('error', 'Je hebt een actief abonnement nodig.')
            );
        }
    }
}
