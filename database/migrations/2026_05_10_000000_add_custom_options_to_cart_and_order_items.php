<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->json('custom_options')->nullable()->after('quantity');
            $table->string('options_hash', 64)->nullable()->after('custom_options')->index();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->json('custom_options')->nullable()->after('size');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('custom_options');
        });

        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['options_hash']);
            $table->dropColumn(['custom_options', 'options_hash']);
        });
    }
};
