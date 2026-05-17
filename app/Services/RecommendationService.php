<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    private const CACHE_TTL = 3600;

    private const RELATIONS = [
        'primaryImage',
        'images',
        'variants',
        'translations',
        'collection.translations',
        'colors.translations',
        'category.translations',
    ];

    public function frequentlyBoughtTogether(int $productId, int $limit = 3): Collection
    {
        $ids = Cache::remember("fbt:product:{$productId}:{$limit}", self::CACHE_TTL, function () use ($productId, $limit) {
            $orderIds = OrderItem::where('product_id', $productId)->pluck('order_id')->unique();

            if ($orderIds->isEmpty()) {
                return [];
            }

            return OrderItem::whereIn('order_id', $orderIds)
                ->where('product_id', '!=', $productId)
                ->select('product_id', DB::raw('COUNT(*) as freq'))
                ->groupBy('product_id')
                ->orderByDesc('freq')
                ->limit($limit)
                ->pluck('product_id')
                ->all();
        });

        if (empty($ids)) {
            return collect();
        }

        return Product::active()
            ->whereIn('id', $ids)
            ->with(self::RELATIONS)
            ->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids))
            ->values();
    }

    public function customersAlsoBought(int $productId, int $limit = 6): Collection
    {
        return $this->frequentlyBoughtTogether($productId, $limit);
    }

    public function basedOnRecentlyViewed(array $productIds, int $limit = 6): Collection
    {
        if (empty($productIds)) {
            return collect();
        }

        $ids = Cache::remember(
            'bov:'.md5(implode(',', $productIds)).":{$limit}",
            self::CACHE_TTL / 4,
            function () use ($productIds, $limit) {
                $orderIds = OrderItem::whereIn('product_id', $productIds)->pluck('order_id')->unique();

                if ($orderIds->isEmpty()) {
                    return [];
                }

                return OrderItem::whereIn('order_id', $orderIds)
                    ->whereNotIn('product_id', $productIds)
                    ->select('product_id', DB::raw('COUNT(*) as freq'))
                    ->groupBy('product_id')
                    ->orderByDesc('freq')
                    ->limit($limit)
                    ->pluck('product_id')
                    ->all();
            }
        );

        if (empty($ids)) {
            $ids = Product::active()
                ->whereNotIn('id', $productIds)
                ->featured()
                ->inRandomOrder()
                ->limit($limit)
                ->pluck('id')
                ->all();
        }

        if (empty($ids)) {
            return collect();
        }

        return Product::active()
            ->whereIn('id', $ids)
            ->with(self::RELATIONS)
            ->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids))
            ->values();
    }

    public function trending(int $limit = 6, int $daysWindow = 30): Collection
    {
        $ids = Cache::remember("trending:{$limit}:{$daysWindow}", self::CACHE_TTL, function () use ($limit, $daysWindow) {
            return OrderItem::query()
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.created_at', '>=', now()->subDays($daysWindow))
                ->select('order_items.product_id', DB::raw('SUM(order_items.quantity) as sold'))
                ->groupBy('order_items.product_id')
                ->orderByDesc('sold')
                ->limit($limit)
                ->pluck('product_id')
                ->all();
        });

        if (empty($ids)) {
            return collect();
        }

        return Product::active()
            ->whereIn('id', $ids)
            ->with(self::RELATIONS)
            ->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids))
            ->values();
    }
}
