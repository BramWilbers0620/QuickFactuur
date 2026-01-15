<?php

namespace App\Services;

use App\Models\Quote;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;

class QuoteService
{
    public function __construct(
        private LogoService $logoService,
        private InvoiceService $invoiceService
    ) {}

    /**
     * Create a new quote with all associated data.
     *
     * @param User $user The user creating the quote
     * @param array $data Validated quote data
     * @param string|null $logoPath Path to stored logo file
     * @return Quote
     */
    public function createQuote(User $user, array $data, ?string $logoPath = null): Quote
    {
        $processedItems = $this->processItems($data['items']);
        $totals = $this->calculateTotals($processedItems, $data['vat_rate']);
        $validUntil = Carbon::parse($data['quote_date'])->addDays($data['valid_days']);

        return DB::transaction(function () use ($user, $data, $processedItems, $totals, $validUntil, $logoPath) {
            $quoteNumber = Quote::generateNextNumber($user->id);

            $quote = Quote::create([
                'user_id' => $user->id,
                'quote_number' => $quoteNumber,
                'company_name' => $data['company_name'],
                'company_email' => $data['company_email'],
                'company_address' => $data['company_address'],
                'company_phone' => $data['company_phone'] ?? null,
                'company_kvk' => $data['company_kvk'] ?? null,
                'company_iban' => $data['company_iban'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'quote_date' => $data['quote_date'],
                'valid_until' => $validUntil,
                'description' => $processedItems[0]['description'] ?? 'Diverse',
                'items' => $processedItems,
                'amount' => $totals['subtotal'],
                'vat_amount' => $totals['vat'],
                'vat_rate' => $data['vat_rate'],
                'total' => $totals['total'],
                'notes' => $data['notes'] ?? null,
                'brand_color' => $data['brand_color'],
                'logo_path' => $logoPath,
                'status' => 'concept',
            ]);

            Log::info('Quote created', [
                'user_id' => $user->id,
                'quote_id' => $quote->id,
                'quote_number' => $quoteNumber,
            ]);

            return $quote;
        });
    }

    /**
     * Generate PDF for a quote.
     *
     * @param Quote $quote The quote to generate PDF for
     * @param string|null $logoData Base64 encoded logo data
     * @return string Path to the generated PDF
     */
    public function generatePdf(Quote $quote, ?string $logoData = null): string
    {
        $data = [
            'quote_number' => $quote->quote_number,
            'date' => Carbon::parse($quote->quote_date)->format('d-m-Y'),
            'valid_until' => Carbon::parse($quote->valid_until)->format('d-m-Y'),
            'logo_data' => $logoData,
            'brand_color' => $quote->brand_color,
            'company' => [
                'name' => $quote->company_name,
                'email' => $quote->company_email,
                'address' => $quote->company_address,
                'phone' => $quote->company_phone,
                'kvk' => $quote->company_kvk,
                'iban' => $quote->company_iban,
            ],
            'customer' => [
                'name' => $quote->customer_name,
                'email' => $quote->customer_email,
                'address' => $quote->customer_address,
                'phone' => $quote->customer_phone,
            ],
            'items' => $quote->items,
            'subtotal' => $quote->amount,
            'vat_amount' => $quote->vat_amount,
            'vat_percentage' => $quote->vat_rate,
            'total' => $quote->total,
            'notes' => $quote->notes,
        ];

        $pdf = PDF::loadView('quotes.pdf', $data);

        $pdfFileName = 'offerte-' . $quote->quote_number . '.pdf';
        $pdfPath = 'quotes/' . $quote->user_id . '/' . $pdfFileName;

        Storage::disk('local')->put($pdfPath, $pdf->output());

        $quote->update(['pdf_path' => $pdfPath]);

        Log::info('Quote PDF generated', [
            'quote_id' => $quote->id,
            'path' => $pdfPath,
        ]);

        return $pdfPath;
    }

    /**
     * Convert a quote to an invoice.
     *
     * @param Quote $quote The quote to convert
     * @return Invoice
     */
    public function convertToInvoice(Quote $quote): Invoice
    {
        return $quote->convertToInvoice();
    }

    /**
     * Process quote items and calculate line totals.
     *
     * @param array $items Raw item data
     * @return array Processed items with totals
     */
    public function processItems(array $items): array
    {
        $processedItems = [];

        foreach ($items as $item) {
            $lineTotal = round($item['rate'] * $item['quantity'], 2);
            $processedItems[] = [
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'price' => $item['rate'],
                'total' => $lineTotal,
            ];
        }

        return $processedItems;
    }

    /**
     * Calculate quote totals.
     *
     * @param array $items Processed items
     * @param int $vatRate VAT rate as percentage (0, 9, or 21)
     * @return array{subtotal: float, vat: float, total: float}
     */
    public function calculateTotals(array $items, int $vatRate): array
    {
        $subtotal = array_sum(array_column($items, 'total'));
        $vat = round($subtotal * ($vatRate / 100), 2);
        $total = round($subtotal + $vat, 2);

        return [
            'subtotal' => $subtotal,
            'vat' => $vat,
            'total' => $total,
        ];
    }

    /**
     * Update quote status.
     *
     * @param Quote $quote
     * @param string $status
     * @return Quote
     */
    public function updateStatus(Quote $quote, string $status): Quote
    {
        $quote->update(['status' => $status]);
        return $quote->fresh();
    }
}
