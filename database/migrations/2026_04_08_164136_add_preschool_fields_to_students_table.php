<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('pre_school_status')->default('new')->after('photo');
            $table->text('admin_review_notes')->nullable()->after('pre_school_status');
            $table->timestamp('admin_reviewed_at')->nullable()->after('admin_review_notes');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'pre_school_status',
                'admin_review_notes',
                'admin_reviewed_at',
            ]);
        });
    }
};