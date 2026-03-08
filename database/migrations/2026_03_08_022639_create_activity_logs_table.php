<?php

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
        Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();

        // Sino ang gumawa ng action
        $table->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();

        // Ano ang ginawa
        // Examples: 'placed_order', 'cancelled_order',
        //           'listed_product', 'logged_in', 'banned_user'
        $table->string('action');

        // Sa anong bagay ginawa (flexible — pwede order, product, etc.)
        $table->string('subject_type')->nullable(); // "Order", "Product"
        $table->unsignedBigInteger('subject_id')->nullable(); // ID ng order/product

        // Extra details (JSON format)
        // Example: {"total": 500, "items": 2, "product_title": "Bag"}
        $table->json('properties')->nullable();

        // IP address — para sa security
        $table->string('ip_address')->nullable();

        $table->timestamps(); // created_at = exact time ng action
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
