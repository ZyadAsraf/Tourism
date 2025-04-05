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
        Schema::create('attraction_staff', function (Blueprint $table) {
           // $table->uuid(column: 'id')->primary();
            $table->id();
            $table->string('FirstName',50);
            $table->string('LastName',50);
            $table->string('Email',60);
            $table->timestamp('EmailVerifiedAt')->nullable();
            $table->string('Password',255);
            $table->date('BirthDate');
            $table->string('PhoneNumber',11);
            $table->foreignId('AttractionId')->constrained('attractions','id')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attraction__staff');
    }
};
