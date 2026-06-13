<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('motivation')->nullable();
            $table->text('skills')->nullable();
            $table->text('experience')->nullable();
            $table->text('expectations')->nullable();
            $table->string('status')->default('PENDING_REVIEW'); // PENDING_REVIEW, REVIEWED
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_interviews');
    }
};
