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
        Schema::table('users', function (Blueprint $table) {
            $table->string('ktp_path')->nullable();
            $table->string('kk_path')->nullable();
            $table->string('ijazah_path')->nullable();
            $table->string('cv_path')->nullable();
            $table->string('sertifikat_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ktp_path', 'kk_path', 'ijazah_path', 'cv_path', 'sertifikat_path']);
        });
    }
};
