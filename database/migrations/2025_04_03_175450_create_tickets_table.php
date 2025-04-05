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
            $table->text('QRCode');
            $table->dateTime('BookingTime');
            $table->integer('Quantity');
            $table->date('VisitDate');
            $table->float('TotalCost');
            $table->foreignUuid('TouristId')->constrained('users', 'id')->restrictOnDelete();
            $table->foreignId('AttractionId')->constrained('attractions', 'id')->restrictOnDelete();
            $table->foreignId('AttractionStaffId')->constrained('attraction_staff', 'id')->restrictOnDelete()->nullable();

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
