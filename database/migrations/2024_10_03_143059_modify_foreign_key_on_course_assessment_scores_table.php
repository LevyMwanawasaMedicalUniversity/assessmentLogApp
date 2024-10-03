<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyForeignKeyOnCourseAssessmentScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_assessment_scores', function (Blueprint $table) {
            // Drop the existing foreign key with cascadeOnDelete
            $table->dropForeign(['course_assessment_id']);
            
            // Recreate the foreign key without cascadeOnDelete
            $table->foreign('course_assessment_id')
                  ->references('course_assessments_id')
                  ->on('course_assessments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_assessment_scores', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['course_assessment_id']);

            // Recreate the original foreign key with cascadeOnDelete
            $table->foreign('course_assessment_id')
                  ->references('course_assessments_id')
                  ->on('course_assessments')
                  ->cascadeOnDelete();
        });
    }
}

