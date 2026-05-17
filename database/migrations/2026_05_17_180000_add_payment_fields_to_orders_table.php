<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 32)->nullable()->after('status')->index();
            $table->string('payment_gateway_ref', 100)->nullable()->after('payment_method');
            $table->decimal('cod_fee', 8, 2)->default(0)->after('shipping_fee');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
            $table->dropColumn(['payment_method', 'payment_gateway_ref', 'cod_fee']);
        });
    }
};
