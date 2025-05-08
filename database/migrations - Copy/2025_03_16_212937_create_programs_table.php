<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id('program_id');
            $table->string('program_name');
            $table->string('program_details');
            $table->unsignedBigInteger('test_requirement_id')->nullable();
            $table->unsignedBigInteger('interviewer_requirement_id')->nullable();
            $table->unsignedBigInteger('workflow_id')->nullable();
            $table->unsignedBigInteger('academic_id')->nullable();
            $table->timestamps();
            $table->foreign('test_requirement_id')->references('test_requirement_id')->on('test_requirements')->onDelete('cascade');
            $table->foreign('interviewer_requirement_id')->references('interviewer_requirement_id')->on('interviewer_requirements')->onDelete('cascade');
            $table->foreign('workflow_id')->references('workflow_id')->on('workflows')->onDelete('cascade');
            $table->foreign('academic_id')->references('academic_id')->on('academic_years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
