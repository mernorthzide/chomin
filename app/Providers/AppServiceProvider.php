<?php

namespace App\Providers;

use App\Listeners\MergeCartOnLogin;
use App\Services\CartService;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        URL::defaults(['locale' => config('chomin.locales.default', 'th')]);
        Event::listen(Login::class, MergeCartOnLogin::class);

        View::composer('components.navbar', function ($view) {
            $cartCount = app(CartService::class)->getItemCount();
            $view->with('cartCount', $cartCount);
        });
    }
}
