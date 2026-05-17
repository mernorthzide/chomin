<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('gift_wrap')->default(false)->after('note');
            $table->decimal('gift_wrap_fee', 8, 2)->default(0)->after('gift_wrap');
            $table->string('gift_message_to')->nullable()->after('gift_wrap_fee');
            $table->string('gift_message_from')->nullable()->after('gift_message_to');
            $table->text('gift_message')->nullable()->after('gift_message_from');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['gift_wrap', 'gift_wrap_fee', 'gift_message_to', 'gift_message_from', 'gift_message']);
        });
    }
};
