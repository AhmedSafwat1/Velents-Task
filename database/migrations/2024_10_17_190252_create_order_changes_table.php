<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_changes', function (Blueprint $table) {
            $table->uuid("id");
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->json('changes');            // Store grouped changes in JSON format
            $table->string('changed_by');       // Track who made the change
            $table->primary("id");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_changes');
    }
};
