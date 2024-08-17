<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('player')->default(0);
            $table->integer('set_player')->default(0);
            $table->integer('set_player_point')->default(0);
            $table->integer('machine')->default(0);
            $table->integer('winning')->default(0);
            $table->integer('reading')->default(0);
            $table->integer('chat')->default(0);
            $table->integer('raffle')->default(0);
            $table->integer('staff')->default(0);
            $table->integer('setting')->default(0);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
