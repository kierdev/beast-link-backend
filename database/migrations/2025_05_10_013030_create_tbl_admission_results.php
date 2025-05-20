<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_admission_results', function (Blueprint $table) {
            $table->id('result_id');

            // Foreign key to tbl_applicants
            $table->unsignedBigInteger('applicant_id');
            $table->foreign('applicant_id')
                  ->references('id')->on('tbl_applicants')
                  ->onDelete('cascade');

            // Foreign key to tbl_program
            $table->unsignedBigInteger('program_id');
            $table->foreign('program_id')
                  ->references('program_id')->on('tbl_program')
                  ->onDelete('cascade');

            $table->enum('admission_status', ['PENDING', 'PASSED', 'FAILED'])->default('PENDING');
            $table->enum('letter_status', ['NOT_GENERATED', 'GENERATED', 'SENT'])->default('NOT_GENERATED');

            $table->string('letter_path')->nullable(); // Path to stored PDF
            $table->timestamp('sent_at')->nullable();  // When the email was sent

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_admission_results');
    }
};
