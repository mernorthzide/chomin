<?php

namespace Tests\Feature;

use App\Exports\TopProductsExport;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Collection;
use App\Models\GiftCard;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ShippingSetting;
use App\Models\SiteSetting;
use App\Models\Story;
use App\Models\User;
use App\Services\PointsService;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\ContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CompletionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_auth_views_do_not_use_undefined_brand_brown_classes(): void
    {
        foreach ([
            'resources/views/auth/login.blade.php',
            'resources/views/auth/register.blade.php',
            'resources/views/auth/forgot-password.blade.php',
        ] as $view) {
            $contents = file_get_contents(base_path($view));

            $this->assertStringNotContainsString('brand-brown', $contents, "{$view} should use defined brand tokens.");
        }
    }

    public function test_contact_style_content_pages_render_clear_form_controls(): void
    {
        $this->seed(ContentSeeder::class);

        $this->get('/th/contact')
            ->assertOk()
            ->assertSee('contact-field', false)
            ->assertSee('contact-submit', false)
            ->assertSee('aria-label="ชื่อ"', false)
            ->assertSee('aria-label="Email"', false)
            ->assertSee('aria-label="ข้อความ"', false);
    }

    public function test_product_page_orders_size_options_from_small_to_extended_sizes(): void
    {
        $product = $this->createCatalogProduct(['slug' => 'ordered-size-shirt']);
        $color = $product->colors()->firstOrFail();

        foreach (['XS', 'S', 'L', 'XL', '2XL', '3XL'] as $size) {
            ProductVariant::create([
                'product_id' => $product->id,
                'product_color_id' => $color->id,
                'size' => $size,
                'stock' => 10,
                'sku' => 'ORDERED-'.$size,
            ]);
        }

        $content = $this->get('/th/products/ordered-size-shirt')
            ->assertOk()
            ->content();

        $orderedSizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'];
        $positions = [];

        foreach ($orderedSizes as $size) {
            $position = strpos($content, "selectSize('{$size}')");
            $this->assertNotFalse($position, "Missing size {$size} on the product page.");
            $positions[$size] = $position;
        }

        $this->assertSame($positions, collect($positions)->sort()->all());
    }

    public function test_shop_filters_and_cookie_actions_use_mobile_safe_targets(): void
    {
        $shop = file_get_contents(base_path('resources/views/pages/shop.blade.php'));
        $css = file_get_contents(base_path('resources/css/app.css'));
        $cookie = file_get_contents(base_path('resources/views/components/cookie-consent.blade.php'));
        $footer = file_get_contents(base_path('resources/views/components/footer.blade.php'));
        $navbar = file_get_contents(base_path('resources/views/components/navbar.blade.php'));
        $product = file_get_contents(base_path('resources/views/pages/products/show.blade.php'));

        $this->assertStringContainsString('shop-filter-bar', $shop);
        $this->assertStringContainsString('min-height: 2.75rem', $css);
        $this->assertStringContainsString('padding-bottom: env(safe-area-inset-bottom)', $css);
        $this->assertStringContainsString('min-h-[44px]', $cookie);
        $this->assertStringContainsString('max-h-[38svh]', $cookie);
        $this->assertStringContainsString('min-h-[44px]', $footer);
        $this->assertStringContainsString('inline-flex min-h-11 items-center', $navbar);
        $this->assertStringContainsString('h-11 w-11', $product);
    }

    public function test_shop_color_shortcuts_ignore_hero_images_when_building_filter_keys(): void
    {
        $product = $this->createCatalogProduct([
            'name' => 'Warm Greige Shirt',
            'slug' => 'warm-greige-shirt',
        ]);

        $color = $product->colors()->firstOrFail();
        $color->update([
            'name' => 'Warm Light Greige',
            'slug' => null,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'image_path' => 'products/hero/model-brown1.png',
            'is_primary' => true,
            'sort_order' => 0,
        ]);
        ProductImage::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'image_path' => 'products/colors/7.png',
            'is_primary' => false,
            'sort_order' => 1,
        ]);

        $this->get('/th/shop')
            ->assertOk()
            ->assertSee('/th/shop?color=7', false)
            ->assertDontSee('color=model-brown1', false);

        $this->get('/th/shop?color=7')
            ->assertOk()
            ->assertSee('Warm Greige Shirt');
    }

    public function test_product_page_renders_wishlist_toggle_form_for_authenticated_customers(): void
    {
        $user = User::factory()->create();
        $product = $this->createCatalogProduct(['slug' => 'wishlist-shirt']);

        $this->actingAs($user)
            ->get('/th/products/wishlist-shirt')
            ->assertOk()
            ->assertSee('action="'.route('wishlist.toggle').'"', false)
            ->assertSee('name="product_id"', false)
            ->assertSee('value="'.$product->id.'"', false);
    }

    public function test_product_page_uses_product_seo_metadata_and_open_graph_image(): void
    {
        $product = $this->createCatalogProduct([
            'name' => 'SEO Shirt',
            'slug' => 'seo-shirt',
            'description' => 'Default product description',
        ]);

        DB::table('product_translations')->insert([
            'product_id' => $product->id,
            'locale' => 'th',
            'name' => 'เชิ้ต SEO',
            'description' => 'รายละเอียด SEO',
            'seo_title' => 'SEO Classic Shirt',
            'seo_description' => 'SEO description for classic shirt.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'product_color_id' => $product->colors()->first()->id,
            'image_path' => 'products/colors/7.png',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $this->get('/th/products/seo-shirt')
            ->assertOk()
            ->assertSee('<title>SEO Classic Shirt | CHOMIN</title>', false)
            ->assertSee('<meta name="description" content="SEO description for classic shirt.">', false)
            ->assertSee('<meta property="og:image" content="'.url('/storage/products/colors/7.png').'">', false);
    }

    public function test_collection_page_uses_collection_metadata_and_open_graph_image(): void
    {
        $collection = Collection::create([
            'name' => 'Core Shirts',
            'slug' => 'core-shirts',
            'description' => 'Core shirts collection description.',
            'image' => 'collections/core.png',
            'banner_image' => 'collections/core-banner.png',
            'is_active' => true,
        ]);

        $this->get('/th/collections/'.$collection->slug)
            ->assertOk()
            ->assertSee('<title>Core Shirts | CHOMIN</title>', false)
            ->assertSee('<meta name="description" content="Core shirts collection description.">', false)
            ->assertSee('<meta property="og:image" content="'.url('/storage/collections/core-banner.png').'">', false);
    }

    public function test_about_page_uses_site_setting_metadata(): void
    {
        SiteSetting::updateOrCreate(['key' => 'about_title'], ['value' => 'รู้จัก CHOMIN']);
        SiteSetting::updateOrCreate(['key' => 'about_content'], ['value' => '<p>CHOMIN ออกแบบเชิ้ตให้เลือกสีและไซส์ได้อย่างอิสระ</p>']);
        SiteSetting::updateOrCreate(['key' => 'about_image'], ['value' => 'about/brand.jpg']);

        $this->get('/th/about')
            ->assertOk()
            ->assertSee('<title>รู้จัก CHOMIN | CHOMIN</title>', false)
            ->assertSee('<meta name="description" content="CHOMIN ออกแบบเชิ้ตให้เลือกสีและไซส์ได้อย่างอิสระ">', false)
            ->assertSee('<meta property="og:image" content="'.url('/storage/about/brand.jpg').'">', false);
    }

    public function test_story_page_adds_chomin_brand_to_title_when_seo_title_is_empty(): void
    {
        $story = Story::create([
            'slug' => 'daily-shirt-style',
            'is_published' => true,
            'published_at' => now(),
        ]);
        $story->translations()->create([
            'locale' => 'th',
            'title' => 'เลือกเชิ้ตสำหรับทุกวัน',
            'excerpt' => 'ไอเดียแต่งตัวกับเชิ้ต CHOMIN',
            'body' => 'เลือกสีที่เข้ากับวันของคุณ',
        ]);

        $this->get('/th/stories/'.$story->slug)
            ->assertOk()
            ->assertSee('<title>เลือกเชิ้ตสำหรับทุกวัน | CHOMIN</title>', false)
            ->assertSee('<meta name="description" content="ไอเดียแต่งตัวกับเชิ้ต CHOMIN">', false);
    }

    public function test_mail_failure_does_not_block_checkout_order_creation(): void
    {
        $user = User::factory()->create(['points' => 0]);
        $product = $this->createCatalogProduct(['price' => 1000]);
        $variant = $product->variants()->firstOrFail();

        ShippingSetting::create(['shipping_fee' => 50, 'free_shipping_min_amount' => 1500]);
        SiteSetting::updateOrCreate(['key' => 'site_email'], ['value' => 'admin@example.com']);

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        Mail::shouldReceive('to')->andThrow(new \RuntimeException('SMTP unavailable'));

        $this->actingAs($user)
            ->post('/th/checkout', [
                'shipping_name' => 'Chomin Customer',
                'shipping_phone' => '0812345678',
                'shipping_address' => '1 Silom Road',
                'shipping_district' => 'Bang Rak',
                'shipping_province' => 'Bangkok',
                'shipping_postal_code' => '10500',
                'payment_method' => 'promptpay_slip',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'subtotal' => 1000,
            'total' => 1050,
        ]);
    }

    public function test_cart_checkout_alpine_state_handles_customers_without_points(): void
    {
        $user = User::factory()->create(['points' => 0]);
        $product = $this->createCatalogProduct(['price' => 590]);
        $variant = $product->variants()->firstOrFail();

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);
        $user->setAttribute('points', null);

        $this->actingAs($user)
            ->get('/th/cart')
            ->assertOk()
            ->assertSee('maxPoints: 0', false)
            ->assertDontSee('maxPoints: ,', false);

        $this->actingAs($user)
            ->get('/th/checkout')
            ->assertOk()
            ->assertSee('maxPoints: 0', false)
            ->assertDontSee('maxPoints: ,', false);
    }

    public function test_checkout_new_address_fields_are_not_overridden_by_hidden_saved_address_inputs(): void
    {
        $user = User::factory()->create(['points' => 0]);
        $product = $this->createCatalogProduct(['price' => 590]);
        $variant = $product->variants()->firstOrFail();

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get('/th/checkout')
            ->assertOk()
            ->assertSee('x-init="init()"', false)
            ->assertDontSee('type="hidden" name="shipping_name"', false)
            ->assertDontSee('type="hidden" name="shipping_phone"', false);
    }

    public function test_points_are_awarded_only_once_per_completed_order(): void
    {
        $user = User::factory()->create(['points' => 0]);
        SiteSetting::updateOrCreate(['key' => 'points_per_baht'], ['value' => '100']);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'CHO-20260508-0001',
            'status' => 'completed',
            'subtotal' => 1000,
            'shipping_fee' => 0,
            'discount' => 0,
            'gift_card_discount' => 0,
            'total' => 1000,
            'shipping_name' => 'Chomin Customer',
            'shipping_phone' => '0812345678',
            'shipping_address' => '1 Silom Road',
            'shipping_district' => 'Bang Rak',
            'shipping_province' => 'Bangkok',
            'shipping_postal_code' => '10500',
            'completed_at' => now(),
        ]);

        app(PointsService::class)->earnPoints($order);
        app(PointsService::class)->earnPoints($order->fresh());

        $this->assertSame(10, $user->fresh()->points);
        $this->assertDatabaseCount('point_transactions', 1);
        $this->assertSame(10, (int) $order->fresh()->points_earned);
    }

    public function test_expired_order_cancellation_restores_stock_points_and_gift_card(): void
    {
        $user = User::factory()->create(['points' => 5]);
        $product = $this->createCatalogProduct(['price' => 1000]);
        $variant = $product->variants()->firstOrFail();
        $variant->update(['stock' => 3]);

        $giftCard = GiftCard::create([
            'code_hash' => GiftCard::hashCode('REFUND-500'),
            'code_last4' => '-500',
            'initial_balance' => 500,
            'balance' => 0,
            'currency' => 'THB',
            'status' => 'redeemed',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'CHO-20260508-0002',
            'status' => 'pending',
            'subtotal' => 1000,
            'shipping_fee' => 50,
            'discount' => 2,
            'gift_card_discount' => 500,
            'total' => 548,
            'points_used' => 20,
            'shipping_name' => 'Chomin Customer',
            'shipping_phone' => '0812345678',
            'shipping_address' => '1 Silom Road',
            'shipping_district' => 'Bang Rak',
            'shipping_province' => 'Bangkok',
            'shipping_postal_code' => '10500',
        ]);
        $order->forceFill([
            'created_at' => now()->subHours(49),
            'updated_at' => now()->subHours(49),
        ])->save();

        $order->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'color_name' => 'Black',
            'size' => 'M',
            'price' => 1000,
            'quantity' => 2,
        ]);
        $order->giftCardRedemptions()->create([
            'gift_card_id' => $giftCard->id,
            'amount' => 500,
        ]);

        $this->artisan('orders:cancel-expired')
            ->assertExitCode(0);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
        $this->assertSame(5, $variant->fresh()->stock);
        $this->assertSame(25, $user->fresh()->points);
        $this->assertDatabaseHas('gift_cards', [
            'id' => $giftCard->id,
            'balance' => 500,
            'status' => 'active',
        ]);
    }

    public function test_reports_include_top_products_excel_export(): void
    {
        $product = $this->createCatalogProduct(['name' => 'Report Shirt']);
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'CHO-20260508-0003',
            'status' => 'completed',
            'subtotal' => 1500,
            'shipping_fee' => 0,
            'discount' => 0,
            'gift_card_discount' => 0,
            'total' => 1500,
            'shipping_name' => 'Chomin Customer',
            'shipping_phone' => '0812345678',
            'shipping_address' => '1 Silom Road',
            'shipping_district' => 'Bang Rak',
            'shipping_province' => 'Bangkok',
            'shipping_postal_code' => '10500',
            'completed_at' => now(),
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $product->variants()->first()->id,
            'product_name' => $product->name,
            'color_name' => 'Black',
            'size' => 'M',
            'price' => 500,
            'quantity' => 3,
        ]);

        $this->assertTrue(class_exists(TopProductsExport::class));

        $rows = (new TopProductsExport(
            now()->subDay()->toDateTimeString(),
            now()->addDay()->toDateTimeString(),
        ))->collection();

        $this->assertSame('Report Shirt', $rows->first()->product->name);
        $this->assertSame(3, (int) $rows->first()->total_qty);
    }

    public function test_admin_user_seeder_does_not_create_default_password_admin_in_production(): void
    {
        app()->detectEnvironment(fn () => 'production');

        (new AdminUserSeeder)->run();

        $this->assertDatabaseMissing('users', [
            'email' => 'admin@chomin.com',
        ]);
    }

    public function test_deploy_workflow_installs_builds_migrates_links_storage_and_health_checks(): void
    {
        $workflow = file_get_contents(base_path('.github/workflows/deploy.yml'));

        $this->assertStringContainsString('composer install', $workflow);
        $this->assertStringContainsString('npm ci', $workflow);
        $this->assertStringContainsString('npm run build', $workflow);
        $this->assertStringContainsString('php artisan migrate --force', $workflow);
        $this->assertStringContainsString('php artisan storage:link', $workflow);
        $this->assertStringContainsString('curl -fsS', $workflow);
    }

    public function test_public_deploy_webhook_does_not_include_a_default_secret(): void
    {
        $deploy = file_get_contents(base_path('deploy.php'));

        $this->assertStringContainsString("getenv('DEPLOY_TOKEN')", $deploy);
        $this->assertStringNotContainsString('6be882483ccb5c22df004f92696671d72e4bac81ad606ba732a3012270e4ec29', $deploy);
        $this->assertStringNotContainsString("?: '", $deploy);
    }

    public function test_project_docs_and_env_example_describe_chomin_launch_requirements(): void
    {
        $readme = file_get_contents(base_path('README.md'));
        $envExample = file_get_contents(base_path('.env.example'));

        $this->assertStringContainsString('# CHOMIN', $readme);
        $this->assertStringContainsString('php artisan db:seed --class=ProductSeeder', $readme);
        $this->assertStringContainsString('Production launch checklist', $readme);

        $this->assertStringContainsString('APP_NAME=CHOMIN', $envExample);
        $this->assertStringContainsString('APP_LOCALE=th', $envExample);
        $this->assertStringContainsString('ADMIN_EMAIL=', $envExample);
        $this->assertStringContainsString('DEPLOY_TOKEN=', $envExample);
    }

    private function createCatalogProduct(array $attributes = []): Product
    {
        $collection = Collection::create([
            'name' => 'Core',
            'slug' => 'core',
            'description' => 'Core collection',
            'image' => 'collections/core.png',
            'banner_image' => 'collections/core-banner.png',
            'is_active' => true,
        ]);

        $category = Category::create([
            'name' => 'Shirts',
            'slug' => 'shirts',
            'is_active' => true,
        ]);

        $product = Product::create(array_merge([
            'name' => 'Classic Shirt',
            'slug' => 'classic-shirt',
            'description' => 'Everyday shirt',
            'price' => 990,
            'collection_id' => $collection->id,
            'category_id' => $category->id,
            'is_active' => true,
            'is_featured' => true,
        ], $attributes));

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name' => 'Black',
            'slug' => 'black',
            'color_code' => '#111111',
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'size' => 'M',
            'stock' => 10,
            'sku' => 'CLASSIC-BLK-M-'.$product->id,
        ]);

        return $product->fresh(['colors', 'variants']);
    }
}
