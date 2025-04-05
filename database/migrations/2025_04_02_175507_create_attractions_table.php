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
            $table->string('AttractionName',50);
            $table->text('Description');
            $table->string('Address', 50)->nullable();
            $table->string('City', 50)->nullable();
            $table->string('Street', 50)->nullable();
            $table->text('LocationLink')->nullable();
            $table->string('Img');
            $table->float('EntryFee');
            $table->string('Status',50);
            $table->foreignId('AdminId')->constrained('normal_admins', 'id')->restrictOnDelete();
            $table->foreignId('GovernorateId')->constrained('governorates', 'id')->restrictOnDelete();
            $table->foreignId('TicketTypesId')->constrained('ticket_types', 'id')->restrictOnDelete();
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
