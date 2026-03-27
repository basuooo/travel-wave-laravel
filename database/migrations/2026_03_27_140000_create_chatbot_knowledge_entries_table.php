<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_knowledge_entries', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_ar');
            $table->text('question_en')->nullable();
            $table->text('question_ar')->nullable();
            $table->longText('answer_en')->nullable();
            $table->longText('answer_ar')->nullable();
            $table->text('keywords_en')->nullable();
            $table->text('keywords_ar')->nullable();
            $table->string('category_en')->nullable();
            $table->string('category_ar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('priority')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'priority']);
            $table->index(['category_en', 'is_active']);
            $table->index(['category_ar', 'is_active']);
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_knowledge_entries');
    }
};
