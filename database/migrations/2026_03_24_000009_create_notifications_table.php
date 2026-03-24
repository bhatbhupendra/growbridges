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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->string('notification_type');
            $table->string('title');
            $table->text('message');

            // notification target user
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('student_documents')->nullOnDelete();

            // who triggered the notification
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->boolean('is_read')->default(false);
            $table->string('redirect_url')->nullable();

            $table->timestamps();

            $table->index('notification_type');
            $table->index('is_read');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};