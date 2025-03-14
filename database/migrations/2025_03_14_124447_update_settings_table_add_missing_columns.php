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
        Schema::table('settings', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('settings', 'display_name')) {
                $table->string('display_name')->after('value');
            }
            
            if (!Schema::hasColumn('settings', 'description')) {
                $table->text('description')->nullable()->after('display_name');
            }
            
            if (!Schema::hasColumn('settings', 'type')) {
                $table->string('type')->default('text')->after('description');
            }
            
            if (!Schema::hasColumn('settings', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('type');
            }
            
            if (!Schema::hasColumn('settings', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('updated_at');
            }
            
            if (!Schema::hasColumn('settings', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'display_name',
                'description',
                'type',
                'is_public',
                'created_by',
                'updated_by'
            ]);
        });
    }
};
