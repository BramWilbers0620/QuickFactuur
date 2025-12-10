<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Quote;
use App\Models\Customer;
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

        $quoteCount = Quote::where('user_id', auth()->id())->count();
        $nextQuoteNumber = sprintf('OFF%04d', $quoteCount + 1);

        $user = auth()->user();
        $companyProfile = [
            'name' => $user->company_name,
            'address' => $user->company_address,
            'email' => $user->email,
            'phone' => $user->company_phone,
            'kvk' => $user->company_kvk,
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

        // Handle logo
        $logoData = null;
        if ($request->hasFile('logo') && extension_loaded('gd')) {
            try {
                $file = $request->file('logo');
                $imageData = file_get_contents($file->getRealPath());
                $extension = strtolower($file->getClientOriginalExtension());
                $mimeTypes = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg'];
                $mimeType = $mimeTypes[$extension] ?? 'image/png';
                $logoData = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
            } catch (\Exception $e) {
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

        $quoteCount = Quote::where('user_id', auth()->id())->count();
        $quoteNumber = sprintf('OFF%04d', $quoteCount + 1);

        $validUntil = Carbon::parse($validated['quote_date'])->addDays($validated['valid_days']);

        $data = [
            'quote_number' => $quoteNumber,
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
                'status' => 'concept',
            ]);

            $pdf = PDF::loadView('quotes.pdf', $data);

            $pdfFileName = 'offerte-' . $quoteNumber . '.pdf';
            $pdfPath = 'quotes/' . auth()->id() . '/' . $pdfFileName;
            Storage::disk('local')->put($pdfPath, $pdf->output());

            $quote->update(['pdf_path' => $pdfPath]);

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
        if ($quote->user_id !== auth()->id()) {
            abort(403);
        }

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
        if ($quote->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$quote->canConvertToInvoice()) {
            return redirect()->back()
                ->with('error', 'Deze offerte kan niet worden omgezet naar een factuur.');
        }

        try {
            $invoice = $quote->convertToInvoice();

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
        if ($quote->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:concept,verzonden,geaccepteerd,afgewezen,verlopen',
        ]);

        $quote->update(['status' => $validated['status']]);

        return redirect()->back()
            ->with('success', 'Status bijgewerkt naar ' . Quote::$statusLabels[$validated['status']]);
    }

    /**
     * Check if user has active access.
     */
    private function ensureUserHasAccess(): void
    {
        if (!auth()->user()->hasActiveAccess()) {
            abort(redirect()->route('billing')->with('error', 'Je hebt een actief abonnement nodig.'));
        }
    }
}
