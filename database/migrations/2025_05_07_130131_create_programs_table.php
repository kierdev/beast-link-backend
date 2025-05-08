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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_name');
            $table->string('program_college');
            $table->string('program_details')->nullable();
            $table->string('program_code')->unique();
            $table->boolean('program_active')->default(true);
            $table->enum('workflow', ['Interview to Test', 'Test to Interview', 'Interview Only', 'Test Only']);
            $table->integer('no_interviewer')->default(1);
            $table->integer('passing_rate')->default(1);
            $table->text('interview_description')->nullable();
            $table->integer('max_score')->default(1);
            $table->integer('passing_score')->default(1);
            $table->string('document_type');
            $table->timestamps();
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
