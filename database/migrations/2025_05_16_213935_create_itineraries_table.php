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
        Schema::create('itineraries', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_id')->constrained('itinerary_types')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('public')->default(false);
            $table->unsignedInteger('likes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itineraries');
    }
};
