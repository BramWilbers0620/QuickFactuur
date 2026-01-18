<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Enums\QuoteStatus;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Quote extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    /**
     * Fields to exclude from audit logging.
     */
    protected array $auditExcluded = [
        'created_at',
        'updated_at',
        'deleted_at',
        'pdf_path',
        'logo_path',
    ];

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
     * Get status as enum.
     */
    public function getStatusEnumAttribute(): ?QuoteStatus
    {
        return QuoteStatus::tryFrom($this->status);
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status_enum?->label() ?? $this->status;
    }

    /**
     * Get status color classes.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status_enum?->badgeClasses() ?? QuoteStatus::CONCEPT->badgeClasses();
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
        return $this->belongsTo(Invoice::class, 'converted_invoice_id')->withTrashed();
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
        $convertableStatuses = [
            QuoteStatus::CONCEPT->value,
            QuoteStatus::SENT->value,
            QuoteStatus::ACCEPTED->value,
        ];

        return in_array($this->status, $convertableStatuses)
            && !$this->converted_invoice_id;
    }

    /**
     * Convert quote to invoice.
     * Uses transaction to ensure data consistency.
     */
    public function convertToInvoice(): Invoice
    {
        return DB::transaction(function () {
            // Get user's default payment terms
            $user = User::find($this->user_id);

            if (!$user) {
                throw new \RuntimeException("User with ID {$this->user_id} not found");
            }

            $paymentTerms = $user->default_payment_terms ?? '30';
            $paymentDays = $user->getPaymentTermsDays();

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
                'due_date' => now()->addDays($paymentDays),
                'payment_terms' => $paymentTerms,
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
                'status' => InvoiceStatus::CONCEPT->value,
            ]);

            $this->update([
                'status' => QuoteStatus::ACCEPTED->value,
                'accepted_at' => now(),
                'converted_invoice_id' => $invoice->id,
            ]);

            return $invoice;
        });
    }

    /**
     * Generate next quote number for user.
     * Uses database locking to prevent race conditions.
     */
    public static function generateNextNumber(int $userId): string
    {
        return DB::transaction(function () use ($userId) {
            // Get user's prefix (default: OFF)
            $user = User::find($userId);

            if (!$user) {
                throw new \RuntimeException("User with ID {$userId} not found");
            }

            $prefix = $user->quote_prefix ?? 'OFF';

            // Include soft-deleted records to avoid reusing quote numbers
            $lastQuote = self::withTrashed()
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            if (!$lastQuote) {
                return $prefix . '0001';
            }

            // Extract numeric part from the end of the quote number
            preg_match('/(\d+)$/', $lastQuote->quote_number, $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $number = $lastNumber + 1;

            return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
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
