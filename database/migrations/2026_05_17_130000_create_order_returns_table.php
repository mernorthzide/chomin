<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->string('rma_number')->unique();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['return', 'exchange'])->default('return');
            $table->enum('reason', [
                'size_too_small', 'size_too_large', 'color_different',
                'defective', 'not_as_described', 'changed_mind', 'other',
            ]);
            $table->text('reason_detail')->nullable();
            $table->json('items');
            $table->enum('status', ['requested', 'approved', 'in_transit', 'received', 'refunded', 'rejected', 'cancelled'])->default('requested');
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->json('photos')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};
