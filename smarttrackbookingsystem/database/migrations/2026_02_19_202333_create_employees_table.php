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
        Schema::create('employees', function (Blueprint $table) {
               $table->id();
            
            // Foreign Keys
            $table->foreignId('business_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            // Basic Information
            $table->string('employee_id')->unique()->comment('Unique employee code');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('joining_date');
            
            // Status
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('employee_id');
            $table->index('status');
            $table->index('joining_date');
            $table->unique(['business_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
