<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn(['status', 'due_date']);
            
            $table->string('type')->default('PEMASUKAN');
            $table->text('description')->nullable();
            $table->date('transaction_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn(['type', 'description', 'transaction_date']);
            
            $table->string('status')->default('unpaid');
            $table->date('due_date')->nullable();
        });
    }
};
