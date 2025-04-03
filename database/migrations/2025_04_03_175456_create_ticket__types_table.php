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
        Schema::create('ticket__types', function (Blueprint $table) {
            //$table->uuid(column: 'id')->primary();
            $table->id();
            $table->string('title',50);
            $table->string('description',250)->nullable();
            $table->float('Discount_Amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket__types');
    }
};
