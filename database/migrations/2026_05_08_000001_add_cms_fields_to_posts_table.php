<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'link_url')) {
                $table->string('link_url')->nullable()->after('media_type');
            }
            if (!Schema::hasColumn('posts', 'tags')) {
                $table->string('tags')->nullable()->after('link_url');
            }
            if (!Schema::hasColumn('posts', 'pinned')) {
                $table->boolean('pinned')->default(false)->after('tags');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['link_url', 'tags', 'pinned']);
        });
    }
};
