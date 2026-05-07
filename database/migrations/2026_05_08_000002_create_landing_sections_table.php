<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique(); // hero, program1, program2, program3, download, stats
            $table->string('section_label');          // Label untuk admin
            $table->string('icon')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('heading')->nullable();
            $table->text('subheading')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_link')->nullable();
            $table->string('media_path')->nullable();
            $table->string('media_type')->nullable();  // image | video
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_sections');
    }
};
