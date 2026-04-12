<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_strengths', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->unique()
                ->constrained()
                ->onDelete('cascade');

            $table->unsignedTinyInteger('overall')->default(0);
            $table->unsignedTinyInteger('hiragana')->default(0);
            $table->unsignedTinyInteger('katagana')->default(0);
            $table->unsignedTinyInteger('numbers')->default(0);
            $table->unsignedTinyInteger('interview')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_strengths');
    }
};