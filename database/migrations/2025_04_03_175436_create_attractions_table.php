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
        Schema::create('attractions', function (Blueprint $table) {
            //$table->uuid(column: 'id')->primary();
            $table->id();
            $table->string('Attraction_Name',50);
            $table->text('Description');
            $table->string('address', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('street', 50)->nullable();
            $table->text('location_link')->nullable();
            $table->string('img');
            $table->float('entryFee');
            $table->string('status',50);
            $table->foreignId('admin_id')->constrained('admins', 'id')->restrictOnDelete();
            $table->foreignId('governorate_id')->constrained('governorates', 'id')->restrictOnDelete();
            $table->foreignId('ticket_types_id')->constrained('ticket__types', 'id')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attractions');
    }
};
