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
            //$table->uuid(column: 'id')->primary();
            $table->id();
            $table->string('PhoneNumber',20);
            $table->text('QR_Code');
            $table->dateTime('Booking_Time');
            $table->integer('Quantity');
            $table->date('Visit_Date');
            $table->float('total_cost');
            $table->foreignUuid('tourist_id')->constrained('users', 'id')->restrictOnDelete();
            $table->foreignId('attraction_id')->constrained('attractions', 'id')->restrictOnDelete();
            $table->foreignId('attraction_staff_id')->constrained('attraction__staff', 'id')->restrictOnDelete()->nullable();

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
