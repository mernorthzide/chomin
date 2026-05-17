<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case AwaitingPayment = 'awaiting_payment';
    case Paid = 'paid';
    case Shipping = 'shipping';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public static function paidStatuses(): array
    {
        return [self::Paid->value, self::Shipping->value, self::Completed->value];
    }

    public function labelTh(): string
    {
        return match ($this) {
            self::Pending => 'รอชำระเงิน',
            self::AwaitingPayment => 'รอตรวจสอบ',
            self::Paid => 'ชำระเงินแล้ว',
            self::Shipping => 'กำลังจัดส่ง',
            self::Completed => 'สำเร็จ',
            self::Cancelled => 'ยกเลิก',
        };
    }

    public function labelEn(): string
    {
        return match ($this) {
            self::Pending => 'Pending payment',
            self::AwaitingPayment => 'Awaiting verification',
            self::Paid => 'Paid',
            self::Shipping => 'Shipping',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }
}
