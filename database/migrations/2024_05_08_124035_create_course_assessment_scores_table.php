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
        Schema::create('course_assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_assessment_id');
            $table->integer('student_id');
            $table->integer('course_code');
            $table->foreign('course_assessment_id')->references('id')->on('course_assessments');
            // $table->foreign('StudentID')->references('student_number')->on('students');
            $table->integer('score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_assessment_scores');
    }
};
