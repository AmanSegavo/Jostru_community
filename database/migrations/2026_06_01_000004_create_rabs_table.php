<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rabs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('cascade');
            $table->string('title');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('PENDING'); // PENDING, APPROVED, REJECTED
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('rab_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rab_id')->constrained('rabs')->onDelete('cascade');
            $table->string('name');
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rab_items');
        Schema::dropIfExists('rabs');
    }
};
