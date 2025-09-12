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
        Schema::create('common_complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complaint_category_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('form_data')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('complaint_category_id')->references('id')->on('complaint_categories')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['complaint_category_id']);
            $table->index(['title']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('common_complaints');
    }
};
