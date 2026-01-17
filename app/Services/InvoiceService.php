<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PDF;

class InvoiceService
{
    public function __construct(
        private LogoService $logoService
    ) {}

    /**
     * Create a new invoice with all associated data.
     *
     * @param User $user The user creating the invoice
     * @param array $data Validated invoice data
     * @param string|null $logoData Base64 encoded logo data for PDF
     * @param string|null $logoPath Path to stored logo file
     * @return Invoice
     */
    public function createInvoice(User $user, array $data, ?string $logoData = null, ?string $logoPath = null): Invoice
    {
        $processedItems = $this->processItems($data['items']);
        $totals = $this->calculateTotals($processedItems, $data['vat_rate']);
        $dueDate = $this->calculateDueDate($data['invoice_date'], $data['payment_terms']);

        return DB::transaction(function () use ($user, $data, $processedItems, $totals, $dueDate, $logoPath) {
            $invoiceNumber = Invoice::generateNextNumber($user->id);

            $invoice = Invoice::create([
                'user_id' => $user->id,
                'invoice_number' => $invoiceNumber,
                'company_name' => $data['company_name'],
                'company_email' => $data['company_email'],
                'company_address' => $data['company_address'],
                'company_phone' => $data['company_phone'] ?? null,
                'company_kvk' => $data['company_kvk'] ?? null,
                'company_vat' => $data['company_btw'] ?? null,
                'company_iban' => $data['company_iban'] ?? null,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_vat' => $data['customer_vat'] ?? null,
                'invoice_date' => $data['invoice_date'],
                'due_date' => $dueDate,
                'payment_terms' => $data['payment_terms'],
                'description' => $processedItems[0]['description'] ?? 'Diverse',
                'items' => $processedItems,
                'amount' => $totals['subtotal'],
                'vat_amount' => $totals['vat'],
                'total' => $totals['total'],
                'vat_rate' => $data['vat_rate'],
                'notes' => $data['notes'] ?? null,
                'brand_color' => $data['brand_color'],
                'logo_path' => $logoPath,
            ]);

            Log::info('Invoice created', [
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoiceNumber,
            ]);

            return $invoice;
        });
    }

    /**
     * Generate PDF for an invoice.
     *
     * @param Invoice $invoice The invoice to generate PDF for
     * @param string|null $logoData Base64 encoded logo data
     * @return string Path to the generated PDF
     */
    public function generatePdf(Invoice $invoice, ?string $logoData = null): string
    {
        $dueDate = Carbon::parse($invoice->due_date)->format('d-m-Y');

        $data = [
            'invoice_number' => $invoice->invoice_number,
            'date' => Carbon::parse($invoice->invoice_date)->format('d-m-Y'),
            'due_date' => $dueDate,
            'payment_terms' => $invoice->payment_terms,
            'logo_data' => $logoData,
            'brand_color' => $invoice->brand_color,
            'company' => [
                'name' => $invoice->company_name,
                'email' => $invoice->company_email,
                'address' => $invoice->company_address,
                'phone' => $invoice->company_phone,
                'kvk' => $invoice->company_kvk,
                'btw' => $invoice->company_vat,
                'iban' => $invoice->company_iban,
            ],
            'customer' => [
                'name' => $invoice->customer_name,
                'email' => $invoice->customer_email,
                'address' => $invoice->customer_address,
                'phone' => $invoice->customer_phone,
                'vat' => $invoice->customer_vat,
            ],
            'items' => $invoice->items,
            'subtotal' => $invoice->amount,
            'vat_amount' => $invoice->vat_amount,
            'vat_percentage' => $invoice->vat_rate,
            'total' => $invoice->total,
            'notes' => $invoice->notes,
        ];

        $pdf = PDF::loadView('invoice.pdf', $data);

        $pdfFileName = 'factuur-' . $invoice->invoice_number . '.pdf';
        $pdfPath = 'invoices/' . $invoice->user_id . '/' . $pdfFileName;

        Storage::disk('local')->put($pdfPath, $pdf->output());

        $invoice->update(['pdf_path' => $pdfPath]);

        Log::info('Invoice PDF generated', [
            'invoice_id' => $invoice->id,
            'path' => $pdfPath,
        ]);

        return $pdfPath;
    }

    /**
     * Process invoice items and calculate line totals.
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
     * Calculate invoice totals.
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
     * Calculate due date based on invoice date and payment terms.
     *
     * @param string $invoiceDate Invoice date string
     * @param string $paymentTerms Payment terms (14, 30, 60, or 'direct')
     * @return Carbon
     */
    public function calculateDueDate(string $invoiceDate, string $paymentTerms): Carbon
    {
        $paymentDays = $paymentTerms === 'direct' ? 0 : (int) $paymentTerms;
        return Carbon::parse($invoiceDate)->addDays($paymentDays);
    }

    /**
     * Mark invoice as sent.
     *
     * @param Invoice $invoice
     * @return Invoice
     */
    public function markAsSent(Invoice $invoice): Invoice
    {
        $invoice->markAsSent();
        return $invoice;
    }

    /**
     * Mark invoice as paid.
     *
     * @param Invoice $invoice
     * @return Invoice
     */
    public function markAsPaid(Invoice $invoice): Invoice
    {
        $invoice->markAsPaid();
        return $invoice;
    }
}
