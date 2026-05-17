<?php

namespace App\Providers;

use App\Listeners\MergeCartOnLogin;
use App\Models\Category;
use App\Models\Collection;
use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public const NAV_CACHE_TTL = 3600;

    public function register(): void {}

    public function boot(): void
    {
        URL::defaults(['locale' => config('chomin.locales.default', 'th')]);
        Event::listen(Login::class, MergeCartOnLogin::class);

        View::composer('components.navbar', function ($view) {
            $cartCount = app(CartService::class)->getItemCount();
            $locale = app()->getLocale();

            $navCollections = Cache::remember("nav:collections:{$locale}", self::NAV_CACHE_TTL, fn () => Collection::active()
                ->ordered()
                ->with('translations')
                ->limit(8)
                ->get()
                ->map(fn (Collection $c) => [
                    'slug' => $c->slug,
                    'name' => $c->localized_name,
                ])
                ->all());

            $navCategories = Cache::remember("nav:categories:{$locale}", self::NAV_CACHE_TTL, fn () => Category::active()
                ->ordered()
                ->with('translations')
                ->limit(8)
                ->get()
                ->map(fn (Category $c) => [
                    'slug' => $c->slug,
                    'name' => $c->localized_name,
                ])
                ->all());

            $view->with([
                'cartCount' => $cartCount,
                'navCollections' => $navCollections,
                'navCategories' => $navCategories,
            ]);
        });

        $flushNav = function () {
            foreach (config('chomin.locales.supported', ['th', 'en']) as $locale) {
                Cache::forget("nav:collections:{$locale}");
                Cache::forget("nav:categories:{$locale}");
            }
        };
        Collection::saved($flushNav);
        Collection::deleted($flushNav);
        Category::saved($flushNav);
        Category::deleted($flushNav);
    }
}
