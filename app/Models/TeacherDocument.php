<?php

namespace App\Models;

use Database\Factories\TeacherDocumentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherDocument extends Model
{
    /** @use HasFactory<TeacherDocumentFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uploaded_by',
        'title',
        'description',
        'category',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'file_extension',
        'target_audience',
        'download_count',
        'is_published',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'download_count' => 'integer',
        'is_published' => 'boolean',
    ];

    protected $appends = [
        'formatted_file_size',
        'is_pdf',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForAudience(Builder $query, string $role): Builder
    {
        if (in_array($role, ['super_admin', 'lms_admin'])) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($role) {
            $q->where('target_audience', 'all')
                ->orWhere('target_audience', 'teachers');

            if ($role === 'facilitator') {
                $q->orWhere('target_audience', 'facilitators');
            }
        });
    }

    protected function formattedFileSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                $bytes = (int) $this->file_size;
                if ($bytes >= 1048576) {
                    return round($bytes / 1048576, 2).' MB';
                }
                if ($bytes >= 1024) {
                    return round($bytes / 1024, 1).' KB';
                }

                return $bytes.' B';
            }
        );
    }

    protected function isPdf(): Attribute
    {
        return Attribute::make(
            get: fn () => strtolower((string) $this->file_extension) === 'pdf' || $this->mime_type === 'application/pdf'
        );
    }
}
