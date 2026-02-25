<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            // 0=Sunday, 1=Monday ... 6=Saturday
            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->boolean('is_off')->default(false);

            $table->timestamps();

            $table->index(['employee_id','day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_working_hours');
    }
};