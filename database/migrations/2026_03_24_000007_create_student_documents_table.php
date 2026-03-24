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
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('doc_type_id')->constrained('document_types')->cascadeOnDelete();

            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();

            // pending, approved, disapproved
            $table->string('verify_status')->default('pending');
            $table->text('verify_message')->nullable();

            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index(['student_id', 'school_id']);
            $table->index('verify_status');

            // optional: only one current document per type per student per school
            // remove this if later you want multiple uploads/history per same doc type
            $table->unique(['student_id', 'school_id', 'doc_type_id'], 'student_school_doc_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};