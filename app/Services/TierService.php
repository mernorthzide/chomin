<?php

namespace App\Services;

class TierService
{
    public const TIERS = [
        'bronze' => [
            'min_spend' => 0,
            'name_th' => 'Bronze',
            'name_en' => 'Bronze',
            'points_multiplier' => 1.0,
            'shipping_perk' => null,
            'early_access_days' => 0,
            'birthday_bonus' => 100,
        ],
        'silver' => [
            'min_spend' => 5000,
            'name_th' => 'Silver',
            'name_en' => 'Silver',
            'points_multiplier' => 1.25,
            'shipping_perk' => null,
            'early_access_days' => 1,
            'birthday_bonus' => 250,
        ],
        'gold' => [
            'min_spend' => 15000,
            'name_th' => 'Gold',
            'name_en' => 'Gold',
            'points_multiplier' => 1.5,
            'shipping_perk' => 'priority',
            'early_access_days' => 3,
            'birthday_bonus' => 500,
        ],
        'platinum' => [
            'min_spend' => 40000,
            'name_th' => 'Platinum',
            'name_en' => 'Platinum',
            'points_multiplier' => 2.0,
            'shipping_perk' => 'express',
            'early_access_days' => 7,
            'birthday_bonus' => 1000,
        ],
    ];

    public static function resolve(float $lifetimeSpend): array
    {
        $current = self::TIERS['bronze'];
        $currentKey = 'bronze';

        foreach (self::TIERS as $key => $tier) {
            if ($lifetimeSpend >= $tier['min_spend']) {
                $current = $tier;
                $currentKey = $key;
            }
        }

        $next = self::nextTier($currentKey);
        $progress = $next
            ? min(100, (($lifetimeSpend - $current['min_spend']) / max(1, $next['min_spend'] - $current['min_spend'])) * 100)
            : 100;
        $toNext = $next ? max(0, $next['min_spend'] - $lifetimeSpend) : 0;

        return [
            'key' => $currentKey,
            'name' => app()->getLocale() === 'en' ? $current['name_en'] : $current['name_th'],
            'multiplier' => $current['points_multiplier'],
            'shipping_perk' => $current['shipping_perk'],
            'early_access_days' => $current['early_access_days'],
            'birthday_bonus' => $current['birthday_bonus'],
            'lifetime_spend' => $lifetimeSpend,
            'next' => $next ? [
                'key' => self::nextTierKey($currentKey),
                'name' => app()->getLocale() === 'en' ? $next['name_en'] : $next['name_th'],
                'min_spend' => $next['min_spend'],
                'to_next' => $toNext,
            ] : null,
            'progress' => round($progress, 1),
        ];
    }

    private static function nextTier(string $currentKey): ?array
    {
        $keys = array_keys(self::TIERS);
        $idx = array_search($currentKey, $keys);

        return ($idx !== false && isset($keys[$idx + 1])) ? self::TIERS[$keys[$idx + 1]] : null;
    }

    private static function nextTierKey(string $currentKey): ?string
    {
        $keys = array_keys(self::TIERS);
        $idx = array_search($currentKey, $keys);

        return ($idx !== false && isset($keys[$idx + 1])) ? $keys[$idx + 1] : null;
    }
}
