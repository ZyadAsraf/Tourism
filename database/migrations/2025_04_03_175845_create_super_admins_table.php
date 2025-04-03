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
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->string('FirstName', 50);
            $table->string('LastName', 50);
            $table->string('PhoneNumber', 20);
            $table->date('BirthDate');
            $table->string('Email', 60)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('Password', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admins');
    }
};
