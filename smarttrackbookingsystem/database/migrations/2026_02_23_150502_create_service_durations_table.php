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
        Schema::create('service_durations', function (Blueprint $table) {
             $table->id();

            $table->foreignId('service_id')
                ->constrained()
                ->cascadeOnDelete();

            // optional label like "Basic", "Premium"
            $table->string('duration_name')->nullable();

            // 30, 60, 90 minutes
            $table->integer('duration_minutes');

            // service price
            $table->decimal('price', 10, 2);

            // optional deposit
            $table->decimal('deposit', 10, 2)->default(0);

            $table->enum('status',['active','inactive'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_durations');
    }
};
