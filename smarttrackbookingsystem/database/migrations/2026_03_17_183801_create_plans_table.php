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
        Schema::create('plans', function (Blueprint $table) {
             $table->id();

            $table->string('name');

            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);

            $table->integer('max_employees')->nullable();
            $table->integer('max_services')->nullable();
            $table->integer('max_bookings')->nullable();

            $table->string('stripe_price_id')->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
