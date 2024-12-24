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
        Schema::table('ca_and_exam_uploads', function (Blueprint $table) {
            $table->decimal('ca', 8, 2)->nullable()->change();
            $table->decimal('exam', 8, 2)->nullable()->change();
            $table->string('course_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ca_and_exam_uploads', function (Blueprint $table) {
            $table->decimal('ca', 8, 2)->nullable(false)->change();
            $table->decimal('exam', 8, 2)->nullable(false)->change();
            $table->string('course_code')->nullable(false)->change();
        });
    }
};