<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAttractionFieldsForLocalization extends Migration
{
    public function up(): void
    {
        Schema::table('attractions', function (Blueprint $table) {
            $table->json('AttractionName')->change();
            $table->json('Description')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('attractions', function (Blueprint $table) {
            $table->string('AttractionName', 50)->change();
            $table->text('Description')->nullable()->change();
        });
    }
}
