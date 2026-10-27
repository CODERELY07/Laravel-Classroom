<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * - materials.file becomes nullable (attachments are optional).
 * - Long-form fields move from VARCHAR(255) to TEXT so descriptions and
 *   chat messages are no longer truncated or rejected by the database.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('file')->nullable()->change();
            $table->text('description')->change();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->text('description')->change();
        });

        Schema::table('classroom_chats', function (Blueprint $table) {
            $table->text('message')->change();
            $table->index(['class_id', 'id']);
        });

        Schema::table('class_members', function (Blueprint $table) {
            $table->index(['user_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::table('class_members', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'class_id']);
        });

        Schema::table('classroom_chats', function (Blueprint $table) {
            $table->dropIndex(['class_id', 'id']);
            $table->string('message')->change();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('description')->change();
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->string('description')->change();
            $table->string('file')->nullable(false)->change();
        });
    }
};
