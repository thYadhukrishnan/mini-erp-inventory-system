<?php

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case DRAFT = 'DRAFT';
    case APPROVED = 'APPROVED';
    case RECEIVED = 'RECEIVED';
    case CANCELLED = 'CANCELLED';

    /**
     * Returns allowed transitions FROM a given status.
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::DRAFT     => [self::APPROVED, self::CANCELLED],
            self::APPROVED  => [self::RECEIVED, self::CANCELLED],
            self::RECEIVED  => [],   // Terminal state – immutable
            self::CANCELLED => [],   // Terminal state – immutable
        };
    }

    public function canTransitionTo(self $new): bool
    {
        return in_array($new, $this->allowedTransitions(), true);
    }

    public function label(): string
    {
        return match($this) {
            self::DRAFT     => 'Draft',
            self::APPROVED  => 'Approved',
            self::RECEIVED  => 'Received',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match($this) {
            self::DRAFT     => 'badge-draft',
            self::APPROVED  => 'badge-approved',
            self::RECEIVED  => 'badge-received',
            self::CANCELLED => 'badge-cancelled',
        };
    }
}
