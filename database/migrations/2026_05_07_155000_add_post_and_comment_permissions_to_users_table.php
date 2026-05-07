<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'can_post')) {
                $table->boolean('can_post')->default(true);
            }
            if (!Schema::hasColumn('users', 'can_comment')) {
                $table->boolean('can_comment')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'can_post')) {
                $table->dropColumn('can_post');
            }
            if (Schema::hasColumn('users', 'can_comment')) {
                $table->dropColumn('can_comment');
            }
        });
    }
};
