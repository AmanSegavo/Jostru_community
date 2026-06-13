<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->string('creditor_name'); // Nama peminjam atau pemberi pinjaman
            $table->unsignedBigInteger('member_id')->nullable(); // Jika anggota
            $table->decimal('amount', 15, 2);
            $table->decimal('remaining_amount', 15, 2);
            $table->enum('type', ['HUTANG', 'PIUTANG'])->default('HUTANG'); // Hutang (kita pinjam), Piutang (kita kasih pinjam)
            $table->enum('status', ['BELUM LUNAS', 'LUNAS'])->default('BELUM LUNAS');
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id'); // Yang menginput data
            $table->timestamps();

            $table->foreign('member_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('debts');
    }
};
