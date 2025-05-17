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
        Schema::create('itinerary_items', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('itinerary_id')->constrained('itineraries', 'uuid')->onDelete('cascade');
            $table->foreignId('attraction_id')->constrained('attractions')->onDelete('cascade');
            $table->date('date');
            $table->time('time')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->foreignId('TicketTypeId')->constrained('ticket_types', 'id')->restrictOnDelete();
            $table->unsignedInteger('position'); //index
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itinerary_items');
    }
};
