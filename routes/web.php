<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ColorLibraryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ContentPageController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\CustomerInquiryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\PaymentSlipController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StoreLocationController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\SetLocaleFromRoute;
use Illuminate\Support\Facades\Route;

$localizedRedirect = function (string $path = '') {
    $path = trim($path, '/');
    $target = '/'.config('chomin.locales.default', 'th').($path ? "/{$path}" : '');
    $query = request()->getQueryString();

    return redirect($target.($query ? "?{$query}" : ''));
};

Route::get('/', HomeController::class)->middleware(SetLocaleFromRoute::class);

foreach ([
    'about', 'cart', 'collections', 'shop', 'privacy', 'terms', 'shipping', 'returns',
    'faq', 'size-guide', 'contact', 'sale', 'search', 'color-library', 'member',
    'gift-cards', 'stores', 'stories', 'careers', 'partnerships', 'wholesale',
] as $legacyPath) {
    Route::match(['get', 'post'], "/{$legacyPath}", fn () => $localizedRedirect($legacyPath));
}

Route::get('/collections/{collection:slug}', fn (\App\Models\Collection $collection) => $localizedRedirect("collections/{$collection->slug}"));
Route::get('/products/{product:slug}', fn (\App\Models\Product $product) => $localizedRedirect("products/{$product->slug}"));
Route::get('/stories/{story:slug}', fn (\App\Models\Story $story) => $localizedRedirect("stories/{$story->slug}"));

Route::prefix('{locale}')
    ->where(['locale' => 'th|en'])
    ->middleware(SetLocaleFromRoute::class)
    ->group(function () {
        // Public storefront routes
        Route::get('/', HomeController::class)->name('home');
        Route::get('/collections', [CollectionController::class, 'index'])->name('collections.index');
        Route::get('/collections/{collection:slug}', [CollectionController::class, 'show'])->name('collections.show');
        Route::get('/shop', ShopController::class)->name('shop.index');
        Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/about', AboutController::class)->name('about');
        Route::get('/search', SearchController::class)->name('search');
        Route::get('/sale', SaleController::class)->name('sale');
        Route::get('/color-library', ColorLibraryController::class)->name('color-library');
        Route::get('/faq', FaqController::class)->name('faq');
        Route::get('/stores', StoreLocationController::class)->name('stores');
        Route::get('/stories', [StoryController::class, 'index'])->name('stories.index');
        Route::get('/stories/{story:slug}', [StoryController::class, 'show'])->name('stories.show');

        foreach (['privacy', 'terms', 'shipping', 'returns', 'size-guide', 'member', 'gift-cards', 'contact', 'careers', 'partnerships', 'wholesale'] as $pageSlug) {
            Route::get("/{$pageSlug}", [ContentPageController::class, 'show'])
                ->defaults('slug', $pageSlug)
                ->name("pages.{$pageSlug}");
        }

        Route::post('/newsletter', [NewsletterController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('newsletter.store');
        Route::post('/cookies/consent', [CookieConsentController::class, 'store'])
            ->middleware('throttle:20,1')
            ->name('cookies.consent');
        Route::post('/contact', [CustomerInquiryController::class, 'store'])
            ->defaults('type', 'contact')
            ->middleware('throttle:5,1')
            ->name('contact.store');
        foreach (['careers', 'partnerships', 'wholesale'] as $type) {
            Route::post("/{$type}", [CustomerInquiryController::class, 'store'])
                ->defaults('type', $type)
                ->middleware('throttle:5,1')
                ->name("{$type}.store");
        }

        // Cart routes (public)
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');

        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

            // Profile pages
            Route::get('/profile/me', fn () => view('pages.profile.index'))->name('profile.index');
            Route::get('/profile/points', fn () => view('pages.profile.points', [
                'transactions' => auth()->user()->pointTransactions()->latest()->paginate(20),
            ]))->name('profile.points');

            // Addresses
            Route::resource('addresses', AddressController::class)->except(['create', 'show', 'edit']);

            // Order history
            Route::get('/orders', [OrderHistoryController::class, 'index'])->name('orders.index');
            Route::get('/orders/{order}', [OrderHistoryController::class, 'show'])->name('orders.show');

            // Wishlist
            Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
            Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

            // Checkout routes
            Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
            Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
            Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');

            // Payment slip upload
            Route::post('/orders/{order}/slip', [PaymentSlipController::class, 'store'])->name('orders.slip.store');
        });
    });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
