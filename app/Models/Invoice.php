<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

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
     * Status labels in Dutch.
     */
    public static array $statusLabels = [
        'concept' => 'Concept',
        'verzonden' => 'Verzonden',
        'betaald' => 'Betaald',
        'te_laat' => 'Te laat',
    ];

    /**
     * Status colors for badges.
     */
    public static array $statusColors = [
        'concept' => 'bg-slate-100 text-slate-700 border-slate-200',
        'verzonden' => 'bg-blue-100 text-blue-700 border-blue-200',
        'betaald' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
        'te_laat' => 'bg-red-100 text-red-700 border-red-200',
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
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status !== 'betaald'
            && $this->due_date
            && $this->due_date->isPast();
    }

    /**
     * Mark as sent.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'verzonden',
            'sent_at' => now(),
        ]);
    }

    /**
     * Mark as paid.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'betaald',
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
            // Include soft-deleted records to avoid reusing invoice numbers
            $lastInvoice = self::withTrashed()
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            if (!$lastInvoice) {
                return 'FAC0001';
            }

            $number = intval(substr($lastInvoice->invoice_number, 3)) + 1;
            return 'FAC' . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
}
