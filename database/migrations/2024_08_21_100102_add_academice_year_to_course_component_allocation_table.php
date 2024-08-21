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
        Schema::table('course_component_allocations', function (Blueprint $table) {
            $table->unsignedBigInteger('academic_year')->after('delivery_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_component_allocations', function (Blueprint $table) {
            //
        });
    }
};
