<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permission_delegations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delegator_id'); // User who gives access
            $table->unsignedBigInteger('delegatee_id'); // User who receives access
            $table->string('permission'); // e.g. 'can_manage_finances'
            $table->enum('scope', ['community', 'division'])->default('community'); // All community or specific division
            $table->boolean('requires_approval')->default(true);
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->timestamps();

            $table->foreign('delegator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('delegatee_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permission_delegations');
    }
};
