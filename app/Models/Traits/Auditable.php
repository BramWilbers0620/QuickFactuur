<?php

namespace App\Models\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the Auditable trait.
     */
    protected static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->logAudit('created', [], $model->getAuditableAttributes());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            $original = array_intersect_key($model->getOriginal(), $changes);

            // Filter out timestamps
            unset($changes['updated_at'], $original['updated_at']);

            if (!empty($changes)) {
                $model->logAudit('updated', $original, $changes);
            }
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted', $model->getAuditableAttributes(), []);
        });

        // Handle soft deletes restoration
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                $model->logAudit('restored', [], $model->getAuditableAttributes());
            });
        }
    }

    /**
     * Get all audit logs for this model.
     */
    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable')->orderBy('created_at', 'desc');
    }

    /**
     * Log an audit event.
     */
    protected function logAudit(string $event, array $oldValues, array $newValues): void
    {
        // Don't log during seeding or testing if disabled
        if (app()->runningInConsole() && !config('audit.log_console', false)) {
            // Still log if it's a queue worker
            if (!app()->runningUnitTests()) {
                return;
            }
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'event' => $event,
            'old_values' => $this->sanitizeValues($oldValues),
            'new_values' => $this->sanitizeValues($newValues),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get attributes that should be audited.
     */
    protected function getAuditableAttributes(): array
    {
        $attributes = $this->getAttributes();

        // Remove excluded fields
        $excluded = $this->getAuditExcluded();
        foreach ($excluded as $field) {
            unset($attributes[$field]);
        }

        return $attributes;
    }

    /**
     * Get fields that should not be audited.
     */
    protected function getAuditExcluded(): array
    {
        return property_exists($this, 'auditExcluded')
            ? $this->auditExcluded
            : ['password', 'remember_token', 'created_at', 'updated_at', 'deleted_at'];
    }

    /**
     * Sanitize values before storing (remove sensitive data).
     */
    protected function sanitizeValues(array $values): array
    {
        $sensitive = ['password', 'token', 'secret', 'key', 'api_key'];

        foreach ($values as $key => $value) {
            foreach ($sensitive as $sensitiveWord) {
                if (stripos($key, $sensitiveWord) !== false) {
                    $values[$key] = '[REDACTED]';
                    break;
                }
            }
        }

        return $values;
    }

    /**
     * Manually log a custom audit event.
     */
    public function logCustomAudit(string $event, array $metadata = []): void
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'auditable_type' => get_class($this),
            'auditable_id' => $this->getKey(),
            'event' => $event,
            'old_values' => [],
            'new_values' => $metadata,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
