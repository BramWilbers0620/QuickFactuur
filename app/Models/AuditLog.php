<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'auditable_type',
        'auditable_id',
        'event',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * The user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The model that was audited.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get a summary of what changed.
     */
    public function getChangesSummaryAttribute(): string
    {
        if (!$this->old_values || !$this->new_values) {
            return $this->event;
        }

        $changes = [];
        foreach ($this->new_values as $key => $value) {
            $oldValue = $this->old_values[$key] ?? 'null';
            $changes[] = "{$key}: {$oldValue} → {$value}";
        }

        return implode(', ', $changes);
    }

    /**
     * Scope to get logs for a specific model.
     */
    public function scopeForModel($query, Model $model)
    {
        return $query->where('auditable_type', get_class($model))
            ->where('auditable_id', $model->getKey());
    }

    /**
     * Scope to get logs by event type.
     */
    public function scopeByEvent($query, string $event)
    {
        return $query->where('event', $event);
    }

    /**
     * Scope to get logs for a specific user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
