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
        Schema::create('total_marks_for_assement_type_in_courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('course_id')->unsigned();
            $table->bigInteger('assessment_type_id')->unsigned();
            $table->foreign('user_id', 'fk_user_id')->references('id')->on('users')->onDelete('cascade');            
            $table->foreign('assessment_type_id', 'fk_assessment_type_id')->references('id')->on('assessment_types')->onDelete('cascade');
            $table->float('total_marks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('total_marks_for_assement_type_in_courses');
    }
};
