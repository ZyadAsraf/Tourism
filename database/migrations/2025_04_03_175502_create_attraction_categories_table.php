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
        Schema::create('attraction_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('AttractionId')
                  ->constrained('attractions')
                  ->cascadeOnDelete();
            
            $table->foreignId('CategoryId')
                  ->constrained('categories')
                  ->cascadeOnDelete(); 

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attraction_category');
    }
};
