<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_document_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('crm_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_document_category_id')->constrained('crm_document_categories')->cascadeOnDelete();
            $table->morphs('documentable');
            $table->string('title');
            $table->string('original_file_name');
            $table->string('stored_file_name');
            $table->string('disk')->default('local');
            $table->string('directory')->nullable();
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->string('extension', 20)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('status')->default('uploaded');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_required')->default(false);
            $table->text('note')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();

            $table->index(['crm_document_category_id', 'uploaded_at'], 'crm_documents_category_uploaded_idx');
            $table->index(['uploaded_by', 'uploaded_at'], 'crm_documents_uploader_uploaded_idx');
            $table->index(['status', 'expiry_date'], 'crm_documents_status_expiry_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_documents');
        Schema::dropIfExists('crm_document_categories');
    }
};
