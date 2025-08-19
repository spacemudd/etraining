<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Back\Course;

class UkCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'status',
        'total_files',
        'matched_count',
        'unmatched_count',
        'sent_count',
        'failed_count',
        'started_at',
        'completed_at',
        'drive_url',
        'progress_percentage',
        'current_file',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_percentage' => 'decimal:2',
    ];

    // Status constants
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_SENDING = 'sending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function rows()
    {
        return $this->hasMany(UkCertificateRow::class);
    }

    public function matchedRows()
    {
        return $this->hasMany(UkCertificateRow::class)->whereNotNull('trainee_id');
    }

    public function unmatchedRows()
    {
        return $this->hasMany(UkCertificateRow::class)->whereNull('trainee_id')->where('status', '!=', UkCertificateRow::STATUS_FAILED);
    }

    public function failedRows()
    {
        return $this->hasMany(UkCertificateRow::class)->where('status', UkCertificateRow::STATUS_FAILED);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($ukCertificate) {
            // Delete all PDF files from S3
            foreach ($ukCertificate->rows as $row) {
                if ($row->pdf_path) {
                    \Storage::disk('s3')->delete($row->pdf_path);
                }
            }
            
            // Delete the original ZIP file from S3
            $zipS3Path = 'uk-certificates/' . $ukCertificate->id . '/original.zip';
            \Storage::disk('s3')->delete($zipS3Path);
            
            // Delete the entire directory
            \Storage::disk('s3')->deleteDirectory('uk-certificates/' . $ukCertificate->id);
        });
    }
}
