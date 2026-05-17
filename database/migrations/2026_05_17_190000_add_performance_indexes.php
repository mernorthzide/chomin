<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order'], 'products_active_sort_idx');
            $table->index('is_featured', 'products_featured_idx');
            $table->index(['sale_price', 'sale_ends_at'], 'products_sale_idx');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->index(['product_id', 'stock'], 'variants_product_stock_idx');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->index(['product_id', 'is_primary'], 'images_product_primary_idx');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->index(['cart_id', 'product_variant_id'], 'cart_items_cart_variant_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'orders_user_created_idx');
            $table->index(['status', 'created_at'], 'orders_status_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_sort_idx');
            $table->dropIndex('products_featured_idx');
            $table->dropIndex('products_sale_idx');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('variants_product_stock_idx');
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex('images_product_primary_idx');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex('cart_items_cart_variant_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_created_idx');
            $table->dropIndex('orders_status_created_idx');
        });
    }
};
