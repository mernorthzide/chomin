<?php

namespace App\Services;

use App\Models\GiftCard;
use App\Models\Order;
use Illuminate\Support\Collection;

class GiftCardService
{
    public function validationErrors(array $codes): array
    {
        $errors = [];

        foreach ($codes as $index => $code) {
            if (! is_string($code) || trim($code) === '') {
                continue;
            }

            $card = GiftCard::findRedeemable($code);

            if (! $card) {
                $errors["gift_card_codes.{$index}"] = __('รหัสบัตรของขวัญไม่ถูกต้อง');

                continue;
            }

            if (! $card->isRedeemable()) {
                $errors["gift_card_codes.{$index}"] = __('บัตรของขวัญนี้หมดอายุ ถูกปิดใช้งาน หรือใช้หมดแล้ว');
            }
        }

        return $errors;
    }

    public function resolveRedeemableCards(array $codes): Collection
    {
        return collect($codes)
            ->filter(fn ($code) => is_string($code) && trim($code) !== '')
            ->map(fn ($code) => GiftCard::findRedeemable($code))
            ->filter(fn (?GiftCard $card) => $card && $card->isRedeemable())
            ->unique('id')
            ->values();
    }

    public function lockRedeemableCards(array $codes): Collection
    {
        $hashes = collect($codes)
            ->filter(fn ($code) => is_string($code) && trim($code) !== '')
            ->map(fn ($code) => GiftCard::hashCode($code))
            ->unique()
            ->values();

        if ($hashes->isEmpty()) {
            return collect();
        }

        return GiftCard::whereIn('code_hash', $hashes)
            ->lockForUpdate()
            ->get()
            ->filter(fn (GiftCard $card) => $card->isRedeemable())
            ->values();
    }

    public function redeemForOrder(Order $order, Collection $cards, float $amount): float
    {
        $remaining = round($amount, 2);
        $redeemed = 0.0;

        foreach ($cards as $card) {
            if ($remaining <= 0) {
                break;
            }

            $available = (float) $card->balance;
            $use = min($available, $remaining);

            if ($use <= 0) {
                continue;
            }

            $card->balance = round($available - $use, 2);
            if ((float) $card->balance <= 0) {
                $card->status = 'redeemed';
            }
            $card->save();

            $card->transactions()->create([
                'order_id' => $order->id,
                'type' => 'redeem',
                'amount' => -$use,
                'balance_after' => $card->balance,
                'note' => "Redeemed for {$order->order_number}",
            ]);

            $order->giftCardRedemptions()->create([
                'gift_card_id' => $card->id,
                'amount' => $use,
            ]);

            $redeemed += $use;
            $remaining = round($remaining - $use, 2);
        }

        return round($redeemed, 2);
    }

    public function refundOrder(Order $order): void
    {
        foreach ($order->giftCardRedemptions as $redemption) {
            $card = $redemption->giftCard;
            $amount = (float) $redemption->amount;

            $alreadyRefunded = $card->transactions()
                ->where('order_id', $order->id)
                ->where('type', 'refund')
                ->exists();

            if ($alreadyRefunded) {
                continue;
            }

            $card->balance = round((float) $card->balance + $amount, 2);
            $card->status = 'active';
            $card->save();

            $card->transactions()->create([
                'order_id' => $order->id,
                'type' => 'refund',
                'amount' => $amount,
                'balance_after' => $card->balance,
                'note' => "Refunded from cancelled {$order->order_number}",
            ]);
        }
    }
}
