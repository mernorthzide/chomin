<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\GiftCardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FullRoadmapTest extends TestCase
{
    use RefreshDatabase;

    public function test_legacy_public_pages_redirect_to_default_locale(): void
    {
        $this->get('/privacy?from=footer')
            ->assertRedirect('/th/privacy?from=footer');
    }

    public function test_localized_content_pages_render_seeded_translations_and_hreflang(): void
    {
        $this->seed(\Database\Seeders\ContentSeeder::class);

        $this->get('/en/privacy')
            ->assertOk()
            ->assertSee('Privacy Policy')
            ->assertSee('rel="alternate"', false)
            ->assertSee('hreflang="th"', false)
            ->assertSee('/th/privacy', false);

        $this->get('/th/privacy')
            ->assertOk()
            ->assertSee('นโยบายความเป็นส่วนตัว');
    }

    public function test_newsletter_and_contact_forms_store_customer_records(): void
    {
        $this->post('/th/newsletter', [
            'email' => 'reader@example.com',
        ])->assertRedirect();

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'reader@example.com',
            'locale' => 'th',
            'status' => 'subscribed',
        ]);

        $this->post('/th/contact', [
            'name' => 'Chomin Customer',
            'email' => 'customer@example.com',
            'phone' => '0812345678',
            'topic' => 'shipping',
            'message' => 'อยากสอบถามเรื่องจัดส่ง',
            'company' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('customer_inquiries', [
            'email' => 'customer@example.com',
            'type' => 'contact',
            'locale' => 'th',
        ]);
    }

    public function test_search_sale_and_color_library_use_translated_catalog_data(): void
    {
        $product = $this->createCatalogProduct([
            'name' => 'Classic Shirt',
            'slug' => 'classic-shirt',
            'description' => 'Everyday shirt',
            'price' => 990,
            'sale_price' => 790,
            'sale_starts_at' => now()->subDay(),
            'sale_ends_at' => now()->addDay(),
        ]);

        DB::table('product_translations')->insert([
            'product_id' => $product->id,
            'locale' => 'th',
            'name' => 'เชิ้ตคลาสสิก',
            'description' => 'เชิ้ตใส่ได้ทุกวัน',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get('/th/search?q='.urlencode('คลาสสิก'))
            ->assertOk()
            ->assertSee('เชิ้ตคลาสสิก');

        $this->get('/th/sale')
            ->assertOk()
            ->assertSee('เชิ้ตคลาสสิก')
            ->assertSee('790');

        $this->get('/th/color-library')
            ->assertOk()
            ->assertSee('Black')
            ->assertSee('/th/shop?color=black', false);
    }

    public function test_gift_card_balance_can_be_redeemed_during_checkout(): void
    {
        $user = User::factory()->create(['points' => 0]);
        $product = $this->createCatalogProduct(['price' => 1000]);
        $variant = $product->variants()->first();

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        DB::table('gift_cards')->insert([
            'id' => 1,
            'code_hash' => hash('sha256', 'GIFT-500'),
            'code_last4' => '-500',
            'initial_balance' => 500,
            'balance' => 500,
            'currency' => 'THB',
            'status' => 'active',
            'recipient_email' => 'friend@example.com',
            'issued_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)->post('/th/checkout', [
            'shipping_name' => 'Chomin Customer',
            'shipping_phone' => '0812345678',
            'shipping_address' => '1 Silom Road',
            'shipping_district' => 'Bang Rak',
            'shipping_province' => 'Bangkok',
            'shipping_postal_code' => '10500',
            'gift_card_codes' => ['GIFT-500'],
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'gift_card_discount' => 500,
            'total' => 550,
        ]);

        $this->assertDatabaseHas('gift_card_transactions', [
            'gift_card_id' => 1,
            'type' => 'redeem',
            'amount' => -500,
        ]);

        $this->assertDatabaseHas('gift_cards', [
            'id' => 1,
            'balance' => 0,
        ]);
    }

    public function test_sale_price_is_used_in_cart_and_checkout_totals(): void
    {
        $user = User::factory()->create(['points' => 0]);
        $product = $this->createCatalogProduct([
            'price' => 1000,
            'sale_price' => 800,
            'sale_starts_at' => now()->subDay(),
            'sale_ends_at' => now()->addDay(),
        ]);
        $variant = $product->variants()->first();

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->assertSame(800.0, $cart->fresh(['items.product'])->subtotal);

        $this->actingAs($user)->post('/th/checkout', [
            'shipping_name' => 'Chomin Customer',
            'shipping_phone' => '0812345678',
            'shipping_address' => '1 Silom Road',
            'shipping_district' => 'Bang Rak',
            'shipping_province' => 'Bangkok',
            'shipping_postal_code' => '10500',
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'subtotal' => 800,
            'total' => 850,
        ]);
    }

    public function test_invalid_gift_card_code_is_rejected_before_order_is_created(): void
    {
        $user = User::factory()->create(['points' => 0]);
        $product = $this->createCatalogProduct(['price' => 1000]);
        $variant = $product->variants()->first();

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->from('/th/checkout')->post('/th/checkout', [
            'shipping_name' => 'Chomin Customer',
            'shipping_phone' => '0812345678',
            'shipping_address' => '1 Silom Road',
            'shipping_district' => 'Bang Rak',
            'shipping_province' => 'Bangkok',
            'shipping_postal_code' => '10500',
            'gift_card_codes' => ['NOT-A-CARD'],
        ])->assertRedirect('/th/checkout')
            ->assertSessionHasErrors('gift_card_codes.0');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_gift_card_refund_restores_balance_when_order_is_cancelled(): void
    {
        $user = User::factory()->create(['points' => 0]);
        $product = $this->createCatalogProduct(['price' => 1000]);
        $variant = $product->variants()->first();

        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        DB::table('gift_cards')->insert([
            'id' => 1,
            'code_hash' => hash('sha256', 'REFUND-500'),
            'code_last4' => '-500',
            'initial_balance' => 500,
            'balance' => 500,
            'currency' => 'THB',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)->post('/th/checkout', [
            'shipping_name' => 'Chomin Customer',
            'shipping_phone' => '0812345678',
            'shipping_address' => '1 Silom Road',
            'shipping_district' => 'Bang Rak',
            'shipping_province' => 'Bangkok',
            'shipping_postal_code' => '10500',
            'gift_card_codes' => ['REFUND-500'],
        ]);

        $order = $user->orders()->firstOrFail();
        app(GiftCardService::class)->refundOrder($order);

        $this->assertDatabaseHas('gift_cards', [
            'id' => 1,
            'balance' => 500,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('gift_card_transactions', [
            'gift_card_id' => 1,
            'type' => 'refund',
            'amount' => 500,
        ]);
    }

    public function test_product_add_to_cart_form_uses_localized_route(): void
    {
        $product = $this->createCatalogProduct(['slug' => 'localized-shirt']);

        $this->get('/th/products/localized-shirt')
            ->assertOk()
            ->assertSee('/th/cart/add', false);
    }

    public function test_homepage_surfaces_campaign_first_storefront_sections(): void
    {
        $this->createCatalogProduct([
            'name' => 'CM Classic Shirt',
            'slug' => 'cm-classic-shirt',
        ]);

        $this->get('/th')
            ->assertOk()
            ->assertSee('Design Your Own Shirt')
            ->assertSee('Build Your Shirt')
            ->assertSee('50+ สี')
            ->assertSee('XS-6XL')
            ->assertSee('/th/color-library', false)
            ->assertSee('/th/member', false);
    }

    public function test_shop_can_filter_products_by_variant_size(): void
    {
        $mediumProduct = $this->createCatalogProduct([
            'name' => 'Medium Shirt',
            'slug' => 'medium-shirt',
        ]);

        $largeProduct = Product::create([
            'name' => 'Large Shirt',
            'slug' => 'large-shirt',
            'description' => 'Large only',
            'price' => 990,
            'collection_id' => $mediumProduct->collection_id,
            'category_id' => $mediumProduct->category_id,
            'is_active' => true,
            'is_featured' => true,
        ]);

        $largeColor = ProductColor::create([
            'product_id' => $largeProduct->id,
            'name' => 'White',
            'slug' => 'white',
            'color_code' => '#ffffff',
        ]);

        ProductVariant::create([
            'product_id' => $largeProduct->id,
            'product_color_id' => $largeColor->id,
            'size' => 'L',
            'stock' => 10,
            'sku' => 'LARGE-WHT-L',
        ]);

        $this->get('/th/shop?size=M')
            ->assertOk()
            ->assertSee('Medium Shirt')
            ->assertDontSee('Large Shirt')
            ->assertSee('value="M" selected', false);
    }

    public function test_shop_accepts_factory_color_code_filters_from_color_library(): void
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
            'image_path' => 'products/colors/7.png',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        $this->get('/th/color-library')
            ->assertOk()
            ->assertSee('/th/shop?color=7', false);

        $this->get('/th/shop?color=7&size=M')
            ->assertOk()
            ->assertSee('Warm Greige Shirt');
    }

    public function test_cookie_consent_logs_optional_categories(): void
    {
        $this->postJson('/th/cookies/consent', [
            'categories' => [
                'necessary' => true,
                'analytics' => true,
                'marketing' => false,
                'embeds' => true,
            ],
        ])->assertOk()
            ->assertJsonStructure(['consent_id']);

        $consent = DB::table('cookie_consents')->first();

        $this->assertNotNull($consent);
        $this->assertSame('th', $consent->locale);
        $this->assertTrue(json_decode($consent->categories, true)['embeds']);
    }

    private function createCatalogProduct(array $attributes = []): Product
    {
        $collection = Collection::create([
            'name' => 'Core',
            'slug' => 'core',
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
            'sku' => 'CLASSIC-BLK-M',
        ]);

        return $product->fresh(['colors', 'variants']);
    }
}
