<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('production_batches', function (Blueprint $table) {
            $table->id();
            $table->string('product_sku');
            $table->decimal('quantity_produced', 10, 2);
            $table->decimal('price', 15, 2)->default(0); // Kolom harga langsung ditambahkan di sini
            $table->unsignedBigInteger('source_waste_id')->nullable();
            $table->date('produced_at');
            $table->timestamps();

            // Menambahkan Foreign Key (Opsional, agar terhubung rapi dengan tabel waste_deposits)
            $table->foreign('source_waste_id')
                ->references('id')->on('waste_deposits')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_batches');
    }
};