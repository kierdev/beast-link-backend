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
        Schema::create('interviewer_requirements', function (Blueprint $table) {
            $table->id('interviewer_requirement_id');
            $table->integer('passing_percentage');
            $table->unsignedBigInteger('interviewer_id');
            $table->foreign('interviewer_id')->references('interviewer_id')->on('interviewers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviewer_requirements');
    }
};
