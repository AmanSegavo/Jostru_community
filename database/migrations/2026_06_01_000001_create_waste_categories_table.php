<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('point_multiplier')->default(10); // e.g. 10 points per kg
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert initial categories to prevent empty states
        \DB::table('waste_categories')->insert([
            ['name' => 'Organik', 'point_multiplier' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Anorganik', 'point_multiplier' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kertas', 'point_multiplier' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_categories');
    }
};
