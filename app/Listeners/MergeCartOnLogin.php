<?php
namespace App\Listeners;

use App\Services\CartService;
use Illuminate\Auth\Events\Login;

class MergeCartOnLogin
{
    public function handle(Login $event): void
    {
        app(CartService::class)->mergeSessionCart();
    }
}
