<?php

namespace App\Models;

use App\Support\CrmDocumentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CrmDocument extends Model
{
    use HasFactory;

    public const STATUS_UPLOADED = 'uploaded';

    protected $fillable = [
        'crm_document_category_id',
        'documentable_type',
        'documentable_id',
        'title',
        'original_file_name',
        'stored_file_name',
        'disk',
        'directory',
        'path',
        'mime_type',
        'extension',
        'size',
        'status',
        'issue_date',
        'expiry_date',
        'is_required',
        'note',
        'meta',
        'uploaded_by',
        'uploaded_at',
    ];

    protected $casts = [
        'crm_document_category_id' => 'integer',
        'documentable_id' => 'integer',
        'size' => 'integer',
        'is_required' => 'boolean',
        'meta' => 'array',
        'uploaded_by' => 'integer',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'uploaded_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(CrmDocumentCategory::class, 'crm_document_category_id');
    }

    public function documentable()
    {
        return $this->morphTo();
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getNormalizedPathAttribute(): string
    {
        return str_replace('\\', '/', ltrim((string) $this->path, '/'));
    }

    public function getExistsAttribute(): bool
    {
        return Storage::disk($this->disk ?: 'local')->exists($this->normalized_path);
    }

    public function getEntityTypeAttribute(): string
    {
        return CrmDocumentService::documentableAlias($this->documentable_type);
    }

    public function localizedEntityType(): string
    {
        return CrmDocumentService::localizedEntityLabel($this->entity_type);
    }

    public function linkedRecordLabel(): string
    {
        return CrmDocumentService::documentableLabel($this->documentable);
    }

    public function formattedFileSize(): string
    {
        $size = (int) ($this->size ?? 0);

        if ($size < 1024) {
            return $size . ' B';
        }

        if ($size < 1048576) {
            return number_format($size / 1024, 1) . ' KB';
        }

        return number_format($size / 1048576, 2) . ' MB';
    }

    public function isPreviewable(): bool
    {
        $mime = (string) $this->mime_type;

        return str_starts_with($mime, 'image/') || $mime === 'application/pdf';
    }
}
