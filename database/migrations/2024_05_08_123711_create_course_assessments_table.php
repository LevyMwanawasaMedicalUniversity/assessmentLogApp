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
        Schema::create('course_assessments', function (Blueprint $table) {
            $table->id('course_assessments_id');
            $table->integer('course_code');
            $table->integer('ca_type');
            $table->integer('status')->default(1);
            $table->integer('basic_information_id');
            $table->string('academic_year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_assessments');
    }
};
