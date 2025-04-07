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
        Schema::create('articles', function (Blueprint $table) {
            //$table->uuid(column: 'id')->primary();
            $table->id();
            $table->string('ArticleLinks', 100);
            $table->string('ArticleHeading', 100);
            $table->text('ArticleBody'); // Changed from string(500) to text()
            $table->string('Img');
            $table->foreignUuid('AdminId')->constrained('users','id')->restrictOnDelete(); // Fixed foreign key
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
