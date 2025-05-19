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
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('PhoneNumber', 20);
            $table->date('BookingTime');
            $table->integer('Quantity');
            $table->date('VisitDate');
            $table->float('TotalCost');
            $table->string('Attraction');
            $table->foreign('Attraction')->references('AttractionName')->on('attractions')->onDelete('cascade');
            $table->string('state')->nullable();
            $table->foreignUuid('TouristId')->constrained('users', 'id')->restrictOnDelete();
            $table->foreignUuid('AttractionStaffId')
                ->nullable()
                ->constrained('users', 'id')
                ->restrictOnDelete();
            $table->foreignId('TicketTypesId')->constrained('ticket_types', 'id')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
