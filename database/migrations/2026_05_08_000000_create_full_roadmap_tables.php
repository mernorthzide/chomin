<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            $table->timestamp('sale_starts_at')->nullable()->after('sale_price');
            $table->timestamp('sale_ends_at')->nullable()->after('sale_starts_at');
        });

        Schema::table('product_colors', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name')->index();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('gift_card_discount', 10, 2)->default(0)->after('discount');
        });

        Schema::create('content_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('template')->default('default');
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('content_page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_page_id')->constrained('content_pages')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->unique(['content_page_id', 'locale']);
        });

        Schema::create('faq_items', function (Blueprint $table) {
            $table->id();
            $table->string('category')->default('general');
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('faq_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_item_id')->constrained('faq_items')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('question');
            $table->text('answer');
            $table->timestamps();
            $table->unique(['faq_item_id', 'locale']);
        });

        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('cover_image')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('story_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained('stories')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->unique(['story_id', 'locale']);
        });

        Schema::create('store_locations', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('map_url')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('store_location_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_location_id')->constrained('store_locations')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->text('address')->nullable();
            $table->text('hours')->nullable();
            $table->timestamps();
            $table->unique(['store_location_id', 'locale']);
        });

        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
            $table->unique(['product_id', 'locale']);
        });

        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->timestamps();
            $table->unique(['category_id', 'locale']);
        });

        Schema::create('collection_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['collection_id', 'locale']);
        });

        Schema::create('product_color_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_color_id')->constrained('product_colors')->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->timestamps();
            $table->unique(['product_color_id', 'locale']);
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('locale', 5)->default('th');
            $table->string('status')->default('subscribed');
            $table->string('source')->default('footer');
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('contact')->index();
            $table->string('locale', 5)->default('th');
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('topic')->nullable();
            $table->text('message');
            $table->string('status')->default('new');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('cookie_consents', function (Blueprint $table) {
            $table->id();
            $table->uuid('consent_id')->unique();
            $table->string('locale', 5)->default('th');
            $table->json('categories');
            $table->string('ip_hash')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('consented_at');
            $table->timestamps();
        });

        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code_hash')->unique();
            $table->string('code_last4', 8);
            $table->decimal('initial_balance', 10, 2);
            $table->decimal('balance', 10, 2);
            $table->string('currency', 3)->default('THB');
            $table->string('status')->default('active')->index();
            $table->string('recipient_email')->nullable();
            $table->string('recipient_name')->nullable();
            $table->text('message')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('gift_card_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gift_card_id')->constrained('gift_cards')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('type');
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('order_gift_card_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('gift_card_id')->constrained('gift_cards')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_gift_card_redemptions');
        Schema::dropIfExists('gift_card_transactions');
        Schema::dropIfExists('gift_cards');
        Schema::dropIfExists('cookie_consents');
        Schema::dropIfExists('customer_inquiries');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('product_color_translations');
        Schema::dropIfExists('collection_translations');
        Schema::dropIfExists('category_translations');
        Schema::dropIfExists('product_translations');
        Schema::dropIfExists('store_location_translations');
        Schema::dropIfExists('store_locations');
        Schema::dropIfExists('story_translations');
        Schema::dropIfExists('stories');
        Schema::dropIfExists('faq_item_translations');
        Schema::dropIfExists('faq_items');
        Schema::dropIfExists('content_page_translations');
        Schema::dropIfExists('content_pages');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('gift_card_discount');
        });

        Schema::table('product_colors', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sale_price', 'sale_starts_at', 'sale_ends_at']);
        });
    }
};
