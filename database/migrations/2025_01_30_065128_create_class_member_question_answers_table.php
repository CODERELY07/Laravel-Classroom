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
        Schema::create('class_member_question_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_member_id');
            $table->unsignedBigInteger('question_id');
            $table->foreign('class_member_id')->references('id')->on('class_members')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');  
            $table->text('answer'); 
            $table->integer('grade')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_member_question_answers');
    }
};
