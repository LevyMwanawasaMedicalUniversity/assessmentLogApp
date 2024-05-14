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
        Schema::create('students_continous_assessments', function (Blueprint $table) {
            $table->id('students_continous_assessment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('course_assessment_id');
            // $table->foreign('course_assessment_id')->references('course_assessments_id')->on('course_assessments');
            $table->string('academic_year');
            $table->decimal('sca_score', 8, 2);
            $table->integer('ca_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students_continous_assessments');
    }
};
