<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_lake_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->string('category')->index(); // e.g., 'MAPS_LOCATION', 'FINANCIAL_DUMP', 'SURVEY', 'MEDIA_DUMP'
            $table->enum('status', ['RAW', 'PROCESSED'])->default('RAW');
            $table->json('payload')->nullable(); // For semi-structured / structured data (Key-Value)
            $table->json('media_paths')->nullable(); // Array of strings (paths to unstructured files: images, videos, pdfs)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_lake_records');
    }
};
