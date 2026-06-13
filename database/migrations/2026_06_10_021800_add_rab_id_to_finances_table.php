<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->unsignedBigInteger('rab_id')->nullable()->after('division_id');
            $table->foreign('rab_id')->references('id')->on('rabs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropForeign(['rab_id']);
            $table->dropColumn('rab_id');
        });
    }
};
