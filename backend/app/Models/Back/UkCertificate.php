<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;

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
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PROCESSING = 'processing';
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
        return $this->hasMany(UkCertificateRow::class)->whereNull('trainee_id');
    }
}
