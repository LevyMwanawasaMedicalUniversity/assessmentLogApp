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
        Schema::create('senate_approved_results', function (Blueprint $table) {
            $table->id();
            $table->integer('student_id');
            $table->integer('academic_year');
            $table->string('course_code');
            $table->float('senate_ca_score')->nullable();
            $table->float('edurole_ca_score')->nullable();
            $table->float('senate_exam_score')->nullable();
            $table->float('edurole_exam_score')->nullable();
            $table->string('senate_grade')->nullable();
            $table->string('edurole_grade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senate_approved_results');
    }
};
