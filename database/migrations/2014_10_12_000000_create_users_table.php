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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('UserName')->unique();
            $table->string('FirstName');
            $table->string('LastName');
            $table->dateTime('birthdate');
            $table->string('PhoneNumber',20);
            $table->string('Email')->unique();
            $table->timestamp('EmailVerifiedAt')->nullable();
            $table->string('Password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
