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
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            // linked login account if role = student
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // admin/agent who created the student
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('student_name');
            $table->string('student_name_jp')->nullable();
            $table->string('email')->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('dob')->nullable();
            $table->string('nationality')->nullable();
            $table->string('phone')->nullable();
            $table->string('passport_number')->nullable();

            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();

            $table->string('highest_qualification')->nullable();
            $table->string('last_institution_name')->nullable();
            $table->string('graduation_year')->nullable();
            $table->integer('academic_gap_years')->nullable()->default(0);

            $table->string('japanese_level')->nullable();
            $table->string('japanese_test_type')->nullable();
            $table->integer('japanese_training_hours')->nullable();

            $table->string('sponsor_name')->nullable();
            $table->string('sponsor_relationship')->nullable();

            $table->string('intake')->nullable();
            $table->string('photo')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('student_name');
            $table->index('email');
            $table->index('passport_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};