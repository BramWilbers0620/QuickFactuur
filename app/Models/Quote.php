<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quote_number',
        'company_name',
        'company_address',
        'company_email',
        'company_phone',
        'company_vat',
        'company_kvk',
        'company_iban',
        'customer_name',
        'customer_email',
        'customer_address',
        'customer_phone',
        'customer_vat',
        'quote_date',
        'valid_until',
        'description',
        'items',
        'amount',
        'vat_amount',
        'total',
        'vat_rate',
        'notes',
        'pdf_path',
        'status',
        'template',
        'brand_color',
        'logo_path',
        'sent_at',
        'accepted_at',
        'converted_invoice_id',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'valid_until' => 'date',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'items' => 'array',
    ];

    /**
     * Status labels in Dutch.
     */
    public static array $statusLabels = [
        'concept' => 'Concept',
        'verzonden' => 'Verzonden',
        'geaccepteerd' => 'Geaccepteerd',
        'afgewezen' => 'Afgewezen',
        'verlopen' => 'Verlopen',
    ];

    /**
     * Status colors for badges.
     */
    public static array $statusColors = [
        'concept' => 'bg-slate-100 text-slate-700 border-slate-200',
        'verzonden' => 'bg-blue-100 text-blue-700 border-blue-200',
        'geaccepteerd' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'afgewezen' => 'bg-red-100 text-red-700 border-red-200',
        'verlopen' => 'bg-amber-100 text-amber-700 border-amber-200',
    ];

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    /**
     * Get status color classes.
     */
    public function getStatusColorAttribute(): string
    {
        return self::$statusColors[$this->status] ?? self::$statusColors['concept'];
    }

    /**
     * Get the user that owns the quote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the invoice this quote was converted to.
     */
    public function convertedInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'converted_invoice_id');
    }

    /**
     * Check if quote is expired.
     */
    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    /**
     * Check if quote can be converted to invoice.
     */
    public function canConvertToInvoice(): bool
    {
        return in_array($this->status, ['concept', 'verzonden', 'geaccepteerd'])
            && !$this->converted_invoice_id;
    }

    /**
     * Convert quote to invoice.
     */
    public function convertToInvoice(): Invoice
    {
        $invoice = Invoice::create([
            'user_id' => $this->user_id,
            'invoice_number' => Invoice::generateNextNumber($this->user_id),
            'company_name' => $this->company_name,
            'company_address' => $this->company_address,
            'company_email' => $this->company_email,
            'company_phone' => $this->company_phone,
            'company_vat' => $this->company_vat,
            'company_kvk' => $this->company_kvk,
            'company_iban' => $this->company_iban,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_address' => $this->customer_address,
            'customer_phone' => $this->customer_phone,
            'customer_vat' => $this->customer_vat,
            'invoice_date' => now(),
            'payment_terms' => '30',
            'description' => $this->description,
            'items' => $this->items,
            'amount' => $this->amount,
            'vat_amount' => $this->vat_amount,
            'total' => $this->total,
            'vat_rate' => $this->vat_rate,
            'notes' => $this->notes,
            'template' => $this->template,
            'brand_color' => $this->brand_color,
            'logo_path' => $this->logo_path,
            'status' => 'concept',
        ]);

        $this->update([
            'status' => 'geaccepteerd',
            'accepted_at' => now(),
            'converted_invoice_id' => $invoice->id,
        ]);

        return $invoice;
    }

    /**
     * Generate next quote number for user.
     */
    public static function generateNextNumber(int $userId): string
    {
        $lastQuote = self::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastQuote) {
            return 'OFF0001';
        }

        $number = intval(substr($lastQuote->quote_number, 3)) + 1;
        return 'OFF' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope for user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get formatted total.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '€' . number_format($this->total, 2, ',', '.');
    }
}
