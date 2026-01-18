<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case CONCEPT = 'concept';
    case SENT = 'verzonden';
    case ACCEPTED = 'geaccepteerd';
    case REJECTED = 'afgewezen';
    case EXPIRED = 'verlopen';

    /**
     * Get the human-readable label for this status.
     */
    public function label(): string
    {
        return match ($this) {
            self::CONCEPT => 'Concept',
            self::SENT => 'Verzonden',
            self::ACCEPTED => 'Geaccepteerd',
            self::REJECTED => 'Afgewezen',
            self::EXPIRED => 'Verlopen',
        };
    }

    /**
     * Get the CSS color class for this status.
     */
    public function color(): string
    {
        return match ($this) {
            self::CONCEPT => 'gray',
            self::SENT => 'blue',
            self::ACCEPTED => 'green',
            self::REJECTED => 'red',
            self::EXPIRED => 'yellow',
        };
    }

    /**
     * Get the badge CSS classes for this status.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::CONCEPT => 'bg-slate-100 text-slate-700 border-slate-200',
            self::SENT => 'bg-blue-100 text-blue-700 border-blue-200',
            self::ACCEPTED => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            self::REJECTED => 'bg-red-100 text-red-700 border-red-200',
            self::EXPIRED => 'bg-amber-100 text-amber-700 border-amber-200',
        };
    }

    /**
     * Check if quote can be edited in this status.
     */
    public function canEdit(): bool
    {
        return $this === self::CONCEPT;
    }

    /**
     * Check if quote can be sent in this status.
     */
    public function canSend(): bool
    {
        return $this === self::CONCEPT;
    }

    /**
     * Check if quote can be converted to invoice in this status.
     */
    public function canConvert(): bool
    {
        return in_array($this, [self::CONCEPT, self::SENT, self::ACCEPTED]);
    }

    /**
     * Check if this is a final status (no further changes expected).
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::ACCEPTED, self::REJECTED, self::EXPIRED]);
    }

    /**
     * Get all statuses as an array for dropdowns.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }

    /**
     * Get all status values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get statuses that are considered "open" (not final).
     *
     * @return array<self>
     */
    public static function openStatuses(): array
    {
        return [self::CONCEPT, self::SENT];
    }
}
