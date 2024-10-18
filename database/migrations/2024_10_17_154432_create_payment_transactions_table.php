<?php

use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->uuid('id');
            $table->json('payment_response')->nullable();
            $table->decimal('total', 10, 2);
            $table->enum('status', [PaymentStatus::PENDING->value, PaymentStatus::PAID->value, PaymentStatus::CANCELED->value])
                ->index()->default(PaymentStatus::PENDING->value);
            $table->string('handler');
            $table->morphs('transactionable', 'transaction_index');
            $table->primary('id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
