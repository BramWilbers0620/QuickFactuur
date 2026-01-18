<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use App\Models\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
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
        'invoice_number',
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
        'invoice_date',
        'payment_terms',
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
        'paid_at',
        'due_date',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'items' => 'array',
    ];

    /**
     * Get status as enum.
     */
    public function getStatusEnumAttribute(): ?InvoiceStatus
    {
        return InvoiceStatus::tryFrom($this->status);
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
        return $this->status_enum?->badgeClasses() ?? InvoiceStatus::CONCEPT->badgeClasses();
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status !== InvoiceStatus::PAID->value
            && $this->due_date
            && $this->due_date->isPast();
    }

    /**
     * Mark as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => InvoiceStatus::SENT->value,
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark as paid.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => InvoiceStatus::PAID->value,
            'paid_at' => now(),
        ]);
    }

    /**
     * Get the user that owns the invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include invoices for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get formatted total with currency symbol.
     */
    public function getFormattedTotalAttribute(): string
    {
        return '€' . number_format($this->total, 2, ',', '.');
    }

    /**
     * Get formatted invoice date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->invoice_date->format('d-m-Y');
    }

    /**
     * Generate next invoice number for user.
     * Uses database locking to prevent race conditions.
     */
    public static function generateNextNumber(int $userId): string
    {
        return DB::transaction(function () use ($userId) {
            // Get user's prefix (default: FAC)
            $user = User::find($userId);

            if (!$user) {
                throw new \RuntimeException("User with ID {$userId} not found");
            }

            $prefix = $user->invoice_prefix ?? 'FAC';

            // Include soft-deleted records to avoid reusing invoice numbers
            $lastInvoice = self::withTrashed()
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            if (!$lastInvoice) {
                return $prefix . '0001';
            }

            // Extract numeric part from the end of the invoice number
            preg_match('/(\d+)$/', $lastInvoice->invoice_number, $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
            $number = $lastNumber + 1;

            return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
}
