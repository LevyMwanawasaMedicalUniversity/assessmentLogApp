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
        Schema::create('ca_and_exam_uploads', function (Blueprint $table) {
            $table->id('ca_and_exam_uploads_id');
            $table->integer('student_id');
            $table->string('course_code');
            $table->string('delivery_mode');
            $table->integer('study_id');
            $table->integer('type_of_exam');
            $table->integer('status')->default(1);
            $table->integer('course_id');
            $table->string('academic_year');
            $table->decimal('ca',8,2)->nullable();
            $table->decimal('exam', 8,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ca_and_exam_uploads');
    }
};
