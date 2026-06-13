<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('can_input_waste')->default(true)->after('can_comment');
            $table->integer('points')->default(0)->after('can_input_waste');
        });

        Schema::table('waste_deposits', function (Blueprint $table) {
            $table->foreignId('waste_category_id')->nullable()->after('type')->constrained()->nullOnDelete();
            $table->integer('points_awarded')->default(0)->after('weight');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['can_input_waste', 'points']);
        });

        Schema::table('waste_deposits', function (Blueprint $table) {
            $table->dropForeign(['waste_category_id']);
            $table->dropColumn(['waste_category_id', 'points_awarded']);
        });
    }
};
