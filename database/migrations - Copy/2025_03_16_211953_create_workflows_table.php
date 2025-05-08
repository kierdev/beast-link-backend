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
        Schema::create('workflows', function (Blueprint $table) {
            $table->id('workflow_id');
            $table->string('workflow_type');
            $table->unsignedBigInteger('test_requirement_id');
            $table->unsignedBigInteger('interviewer_requirement_id');
            $table->foreign('test_requirement_id')->references('test_requirement_id')->on('test_requirements')->onDelete('cascade');
            $table->foreign('interviewer_requirement_id')->references('interviewer_requirement_id')->on('interviewer_requirements')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
