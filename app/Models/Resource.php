<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'course_section_id',
        'lesson_id',
        'title',
        'description',
        'resource_type',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'external_url',
        'is_downloadable',
        'order_index',
    ];

    protected $appends = ['formatted_file_size'];

    protected function casts(): array
    {
        return [
            'is_downloadable' => 'boolean',
            'order_index' => 'integer',
            'file_size' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    protected function formattedFileSize(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->file_size) {
                    return null;
                }
                $bytes = $this->file_size;
                $units = ['B', 'KB', 'MB', 'GB'];
                for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
                    $bytes /= 1024;
                }

                return round($bytes, 2).' '.$units[$i];
            }
        );
    }
}
