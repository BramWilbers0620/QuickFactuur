<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case CONCEPT = 'concept';
    case SENT = 'verzonden';
    case PAID = 'betaald';
    case OVERDUE = 'te_laat';

    /**
     * Get the human-readable label for this status.
     */
    public function label(): string
    {
        return match ($this) {
            self::CONCEPT => 'Concept',
            self::SENT => 'Verzonden',
            self::PAID => 'Betaald',
            self::OVERDUE => 'Te laat',
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
            self::PAID => 'green',
            self::OVERDUE => 'red',
        };
    }

    /**
     * Get the badge CSS classes for this status.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::CONCEPT => 'bg-gray-100 text-gray-800',
            self::SENT => 'bg-blue-100 text-blue-800',
            self::PAID => 'bg-green-100 text-green-800',
            self::OVERDUE => 'bg-red-100 text-red-800',
        };
    }

    /**
     * Check if invoice can be edited in this status.
     */
    public function canEdit(): bool
    {
        return $this === self::CONCEPT;
    }

    /**
     * Check if invoice can be sent in this status.
     */
    public function canSend(): bool
    {
        return in_array($this, [self::CONCEPT, self::OVERDUE]);
    }

    /**
     * Check if invoice can be marked as paid in this status.
     */
    public function canMarkAsPaid(): bool
    {
        return in_array($this, [self::SENT, self::OVERDUE]);
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
}
