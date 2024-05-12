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
            $table->id('course_assessment_scores_id');
            $table->unsignedBigInteger('course_assessment_id');
            $table->integer('student_id');
            $table->string('course_code');
            $table->foreign('course_assessment_id')->references('course_assessments_id')->on('course_assessments')->cascadeOnDelete();
            // $table->foreign('StudentID')->references('student_number')->on('students');
            $table->decimal('cas_score',8,2);
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
