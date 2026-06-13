<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_relations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_user_id');
            $table->string('target_type'); // 'user', 'division', 'all'
            $table->unsignedBigInteger('target_id')->nullable(); // user_id or division_id
            $table->timestamps();

            $table->foreign('source_user_id')->references('id')->on('users')->onDelete('cascade');
            // We don't add strict foreign key on target_id because it can be either user or division.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_relations');
    }
};
