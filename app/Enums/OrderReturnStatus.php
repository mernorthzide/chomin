<?php

namespace App\Enums;

enum OrderReturnStatus: string
{
    case Requested = 'requested';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public static function openStatuses(): array
    {
        return [self::Requested->value, self::Approved->value];
    }

    public static function closedStatuses(): array
    {
        return [self::Rejected->value, self::Cancelled->value, self::Refunded->value];
    }
}
