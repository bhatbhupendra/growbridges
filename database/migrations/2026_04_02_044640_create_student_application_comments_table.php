<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_application_comments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('student_school_application_id');
            $table->unsignedBigInteger('user_id');
            $table->text('message');
            $table->timestamps();

            $table->foreign('student_school_application_id', 'sac_app_fk')
                ->references('id')
                ->on('student_school_applications')
                ->cascadeOnDelete();

            $table->foreign('user_id', 'sac_user_fk')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_application_comments');
    }
};