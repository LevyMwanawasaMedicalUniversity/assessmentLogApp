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
        Schema::create('final_examination_results', function (Blueprint $table) {
            $table->id('final_examination_results_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('final_examinations_id');
            $table->unsignedBigInteger('course_id');
            $table->integer('status')->default(1);
            $table->foreign('final_examinations_id')->references('final_examinations_id')->on('final_examinations')->cascadeOnDelete();
            $table->string('academic_year');
            $table->decimal('cas_score', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_examination_results');
    }
};
