<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Back\Trainee;

class UkCertificateRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'uk_certificate_id',
        'trainee_id',
        'identity_number',
        'trainee_name',
        'filename',
        'pdf_path',
        'sent_at',
        'status',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    public function ukCertificate()
    {
        return $this->belongsTo(UkCertificate::class);
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}
