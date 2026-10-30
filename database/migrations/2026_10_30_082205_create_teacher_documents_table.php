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
        Schema::create('teacher_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('General');
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type')->nullable();
            $table->string('file_extension', 20)->default('pdf');
            $table->string('target_audience', 50)->default('teachers');
            $table->unsignedInteger('download_count')->default(0);
            $table->boolean('is_published')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['is_published', 'category']);
            $table->index(['target_audience']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_documents');
    }
};
