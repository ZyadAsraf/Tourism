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
        Schema::create('ticket_attraction', function (Blueprint $table) {
            $table->uuid('ticket_id');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
            
            $table->foreignId('attraction_id')->constrained()->onDelete('cascade');
            
            // Additional fields for each ticket-attraction pair
            $table->integer('quantity')->default(1);  // Quantity for this attraction in the ticket
            $table->date('visit_date');  // Date when the user plans to visit this attraction
    
            // Composite primary key to avoid duplicate entries
            $table->primary(['ticket_id', 'attraction_id']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_attraction');
    }
};
