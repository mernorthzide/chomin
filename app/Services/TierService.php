<?php

namespace App\Services;

class TierService
{
    public static function tiers(): array
    {
        return config('chomin.tiers', []);
    }

    public static function resolve(float $lifetimeSpend): array
    {
        $tiers = self::tiers();
        $firstKey = array_key_first($tiers);
        $current = $tiers[$firstKey];
        $currentKey = $firstKey;

        foreach ($tiers as $key => $tier) {
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
        $locale = app()->getLocale();

        return [
            'key' => $currentKey,
            'name' => $locale === 'en' ? $current['name_en'] : $current['name_th'],
            'multiplier' => $current['points_multiplier'],
            'shipping_perk' => $current['shipping_perk'],
            'early_access_days' => $current['early_access_days'],
            'birthday_bonus' => $current['birthday_bonus'],
            'lifetime_spend' => $lifetimeSpend,
            'next' => $next ? [
                'key' => self::nextTierKey($currentKey),
                'name' => $locale === 'en' ? $next['name_en'] : $next['name_th'],
                'min_spend' => $next['min_spend'],
                'to_next' => $toNext,
            ] : null,
            'progress' => round($progress, 1),
        ];
    }

    private static function nextTier(string $currentKey): ?array
    {
        $tiers = self::tiers();
        $keys = array_keys($tiers);
        $idx = array_search($currentKey, $keys);

        return ($idx !== false && isset($keys[$idx + 1])) ? $tiers[$keys[$idx + 1]] : null;
    }

    private static function nextTierKey(string $currentKey): ?string
    {
        $keys = array_keys(self::tiers());
        $idx = array_search($currentKey, $keys);

        return ($idx !== false && isset($keys[$idx + 1])) ? $keys[$idx + 1] : null;
    }
}
