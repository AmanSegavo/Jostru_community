<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. DIVISIONS
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('GENERAL'); // FARM, LIVESTOCK, PRODUCTION, CAFE, GENERAL
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. BUDGETS (Alokasi Dana Pusat ke Divisi)
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->decimal('allocated_amount', 15, 2)->default(0);
            $table->decimal('used_amount', 15, 2)->default(0);
            $table->string('period')->nullable(); // e.g. "2026-06"
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. ALTER USERS
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('set null');
            $table->boolean('can_view_finances')->default(false);
            $table->boolean('can_manage_division')->default(false);
        });

        // 4. ALTER FINANCES
        Schema::table('finances', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('set null');
            $table->foreignId('budget_id')->nullable()->constrained('budgets')->onDelete('set null');
            $table->string('proof_path')->nullable();
            $table->string('status')->default('APPROVED'); // PENDING, APPROVED, REJECTED
        });

        // 5. INVENTORIES (Gudang Terpadu)
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->string('name');
            $table->string('category')->nullable();
            $table->decimal('stock', 10, 2)->default(0);
            $table->string('unit')->default('pcs'); // Kg, Liter, Sak, Pcs
            $table->timestamps();
        });

        // 6. LIVESTOCKS (Ternak)
        Schema::create('livestocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->string('animal_type');
            $table->integer('population_count')->default(0);
            $table->string('health_status')->nullable();
            $table->string('feed_schedule')->nullable();
            $table->timestamps();
        });

        // 7. ALTER PRODUCTIONS (if exists, or rename)
        // Wait, V1.2 already had `production_batches`. Let's just create a new one if we want, or alter it.
        // `production_batches` has: id, batch_number, start_date, end_date, total_raw_material, total_product, status, notes.
        // Let's just add `division_id` to `production_batches`.
        if (Schema::hasTable('production_batches')) {
            Schema::table('production_batches', function (Blueprint $table) {
                $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('set null');
            });
        }

        // 8. CAFE POS
        Schema::create('pos_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->string('name');
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Kasir
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('payment_method')->default('CASH');
            $table->timestamps();
        });

        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pos_transaction_id')->constrained('pos_transactions')->onDelete('cascade');
            $table->foreignId('pos_product_id')->constrained('pos_products')->onDelete('cascade');
            $table->integer('qty')->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transaction_items');
        Schema::dropIfExists('pos_transactions');
        Schema::dropIfExists('pos_products');
        
        if (Schema::hasTable('production_batches')) {
            Schema::table('production_batches', function (Blueprint $table) {
                $table->dropForeign(['division_id']);
                $table->dropColumn('division_id');
            });
        }

        Schema::dropIfExists('livestocks');
        Schema::dropIfExists('inventories');
        
        Schema::table('finances', function (Blueprint $table) {
            $table->dropForeign(['budget_id']);
            $table->dropForeign(['division_id']);
            $table->dropColumn(['division_id', 'budget_id', 'proof_path', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn(['division_id', 'can_view_finances', 'can_manage_division']);
        });

        Schema::dropIfExists('budgets');
        Schema::dropIfExists('divisions');
    }
};
