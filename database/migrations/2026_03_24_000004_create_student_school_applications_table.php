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
        Schema::create('student_school_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();

            // pending, accepted, rejected, enrolled
            $table->string('status')->default('pending');

            // admin/agent who assigned school to student
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();

            // who applied: agent/student/admin
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('applied_at')->nullable();

            $table->timestamps();

            // prevent duplicate application to same school
            $table->unique(['student_id', 'school_id']);

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_school_applications');
    }
};