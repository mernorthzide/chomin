<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Facades\Request;

class Seo
{
    public static function canonical(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $base = rtrim(config('app.url'), '/');
        $path = self::pathWithoutLocale();

        return $base.'/'.$locale.($path !== '' ? '/'.$path : '');
    }

    public static function alternates(): array
    {
        $base = rtrim(config('app.url'), '/');
        $path = self::pathWithoutLocale();
        $locales = config('chomin.locales.supported', ['th', 'en']);
        $default = config('chomin.locales.default', 'th');

        $alternates = [];
        foreach ($locales as $locale) {
            $alternates[] = [
                'hreflang' => $locale,
                'href' => $base.'/'.$locale.($path !== '' ? '/'.$path : ''),
            ];
        }
        $alternates[] = [
            'hreflang' => 'x-default',
            'href' => $base.'/'.$default.($path !== '' ? '/'.$path : ''),
        ];

        return $alternates;
    }

    public static function pathWithoutLocale(): string
    {
        $segments = explode('/', trim(Request::path(), '/'));
        $locales = config('chomin.locales.supported', ['th', 'en']);

        if (! empty($segments) && in_array($segments[0], $locales, true)) {
            array_shift($segments);
        }

        return implode('/', $segments);
    }

    public static function organizationJsonLd(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'CHOMIN',
            'alternateName' => 'CHO.MIN',
            'url' => config('app.url'),
            'logo' => url('/images/logo.png'),
            'sameAs' => array_values(array_filter([
                'https://www.facebook.com/chomin.official',
                'https://www.instagram.com/chomin.official',
                'https://line.me/R/ti/p/@chomin',
            ])),
        ];
    }

    public static function websiteJsonLd(): array
    {
        $base = rtrim(config('app.url'), '/');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'CHOMIN',
            'url' => $base,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $base.'/'.app()->getLocale().'/search?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    public static function breadcrumbJsonLd(array $crumbs): array
    {
        $list = [];
        foreach ($crumbs as $i => $crumb) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    public static function productJsonLd(Product $product, ?string $image = null): array
    {
        $base = rtrim(config('app.url'), '/');
        $totalStock = (int) $product->variants->sum('stock');
        $availability = $totalStock > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';

        $offers = [
            '@type' => 'Offer',
            'url' => self::canonical(),
            'priceCurrency' => 'THB',
            'price' => number_format((float) $product->display_price, 2, '.', ''),
            'availability' => $availability,
            'itemCondition' => 'https://schema.org/NewCondition',
            'seller' => [
                '@type' => 'Organization',
                'name' => 'CHOMIN',
            ],
        ];

        if ($product->is_on_sale && $product->sale_ends_at) {
            $offers['priceValidUntil'] = $product->sale_ends_at->toDateString();
        }

        $aggregateRating = null;
        if (method_exists($product, 'approvedReviews')) {
            $reviews = $product->approvedReviews;
            if ($reviews && $reviews->count() > 0) {
                $aggregateRating = [
                    '@type' => 'AggregateRating',
                    'ratingValue' => round($reviews->avg('rating'), 1),
                    'reviewCount' => $reviews->count(),
                ];
            }
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->localized_name,
            'description' => strip_tags($product->localized_description ?? ''),
            'sku' => 'CHOMIN-'.$product->id,
            'brand' => [
                '@type' => 'Brand',
                'name' => 'CHOMIN',
            ],
            'image' => $image ? [$image] : [],
            'offers' => $offers,
        ];

        if ($aggregateRating) {
            $schema['aggregateRating'] = $aggregateRating;
        }

        if ($product->category) {
            $schema['category'] = $product->category->name;
        }

        return $schema;
    }
}
