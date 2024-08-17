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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('room_id');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('dl')->nullable();
            $table->date('dob')->nullable();
            $table->string('ssn')->nullable();
            $table->string('gender');
            $table->text('profile')->nullable();
            $table->text('document')->nullable();
            $table->integer('ref_id')->nullable();
            $table->integer('bonus')->nullable();
            $table->integer('address')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
