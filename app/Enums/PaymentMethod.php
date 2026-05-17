<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case PromptpaySlip = 'promptpay_slip';
    case Cod = 'cod';
    case BankTransfer = 'bank_transfer';

    public function configKey(): string
    {
        return "chomin.payment.methods.{$this->value}";
    }

    public function isEnabled(): bool
    {
        return (bool) config($this->configKey().'.enabled');
    }
}
