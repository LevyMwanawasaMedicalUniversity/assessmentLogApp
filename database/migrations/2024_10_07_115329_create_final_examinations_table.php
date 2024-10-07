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
        Schema::create('final_examinations', function (Blueprint $table) {
            $table->id('final_examinations_id');
            $table->integer('student_id');
            $table->string('course_code');
            $table->integer('status')->default(1);
            $table->integer('course_id');
            $table->string('academic_year');
            $table->decimal('cas_score',8,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_examinations');
    }
};
