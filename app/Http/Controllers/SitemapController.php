<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Product;
use App\Models\Story;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    private const STATIC_PAGES = [
        ['path' => '', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['path' => 'shop', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['path' => 'collections', 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['path' => 'sale', 'priority' => '0.8', 'changefreq' => 'daily'],
        ['path' => 'stories', 'priority' => '0.7', 'changefreq' => 'weekly'],
        ['path' => 'about', 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['path' => 'stores', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ['path' => 'color-library', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ['path' => 'faq', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['path' => 'size-guide', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['path' => 'contact', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['path' => 'gift-cards', 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['path' => 'member', 'priority' => '0.4', 'changefreq' => 'monthly'],
        ['path' => 'careers', 'priority' => '0.3', 'changefreq' => 'monthly'],
        ['path' => 'partnerships', 'priority' => '0.3', 'changefreq' => 'monthly'],
        ['path' => 'wholesale', 'priority' => '0.3', 'changefreq' => 'monthly'],
        ['path' => 'privacy', 'priority' => '0.2', 'changefreq' => 'yearly'],
        ['path' => 'terms', 'priority' => '0.2', 'changefreq' => 'yearly'],
        ['path' => 'shipping', 'priority' => '0.3', 'changefreq' => 'monthly'],
        ['path' => 'returns', 'priority' => '0.3', 'changefreq' => 'monthly'],
    ];

    public function __invoke(): Response
    {
        $locales = config('chomin.locales.supported', ['th', 'en']);
        $default = config('chomin.locales.default', 'th');
        $urls = [];
        $now = now()->toAtomString();

        foreach (self::STATIC_PAGES as $page) {
            $urls[] = $this->buildUrlWithAlternates($page['path'], $locales, $default, $now, $page['changefreq'], $page['priority']);
        }

        Product::query()->active()->select(['id', 'slug', 'updated_at'])->chunk(200, function ($products) use (&$urls, $locales, $default) {
            foreach ($products as $product) {
                $lastmod = ($product->updated_at ?? now())->toAtomString();
                $urls[] = $this->buildUrlWithAlternates("products/{$product->slug}", $locales, $default, $lastmod, 'weekly', '0.8');
            }
        });

        Collection::query()->select(['id', 'slug', 'updated_at'])->chunk(100, function ($collections) use (&$urls, $locales, $default) {
            foreach ($collections as $collection) {
                $lastmod = ($collection->updated_at ?? now())->toAtomString();
                $urls[] = $this->buildUrlWithAlternates("collections/{$collection->slug}", $locales, $default, $lastmod, 'weekly', '0.7');
            }
        });

        if (class_exists(Story::class)) {
            Story::query()->where('is_published', true)->select(['id', 'slug', 'updated_at'])->chunk(100, function ($stories) use (&$urls, $locales, $default) {
                foreach ($stories as $story) {
                    $lastmod = ($story->updated_at ?? now())->toAtomString();
                    $urls[] = $this->buildUrlWithAlternates("stories/{$story->slug}", $locales, $default, $lastmod, 'monthly', '0.5');
                }
            });
        }

        $xml = view('sitemap', ['entries' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function buildUrlWithAlternates(string $path, array $locales, string $default, string $lastmod, string $changefreq, string $priority): array
    {
        $base = rtrim(config('app.url'), '/');
        $primary = $base.'/'.$default.($path !== '' ? '/'.$path : '');

        $alternates = [];
        foreach ($locales as $locale) {
            $alternates[] = [
                'hreflang' => $locale,
                'href' => $base.'/'.$locale.($path !== '' ? '/'.$path : ''),
            ];
        }
        $alternates[] = ['hreflang' => 'x-default', 'href' => $primary];

        return [
            'loc' => $primary,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
            'alternates' => $alternates,
        ];
    }
}
