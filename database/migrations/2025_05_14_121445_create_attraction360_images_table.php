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
        Schema::create('attraction360_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attraction_id');
            $table->string('filename');
            $table->string('alt_text')->nullable();
            $table->timestamps();

            $table->foreign('attraction_id')
              ->references('id')->on('attractions')
              ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attraction360_images');
    }
};
