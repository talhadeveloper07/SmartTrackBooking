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
        Schema::table('appointments', function (Blueprint $table) {
            // if you have foreign keys, drop them first
            // names may differ; adjust if needed
            try { $table->dropForeign(['service_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['employee_id']); } catch (\Throwable $e) {}

            $table->unsignedBigInteger('service_id')->nullable()->change();
            $table->unsignedBigInteger('employee_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable(false)->change();
            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
        });
    }
};
