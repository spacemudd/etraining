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
        'source',
        'source_ref',
        'sent_at',
        'status',
        'error_message',
        'mailgun_message_id',
        'delivered_at',
        'failed_at',
        'delivery_failure_reason',
        'delivery_status',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    // Delivery status constants
    const DELIVERY_STATUS_PENDING = 'pending';
    const DELIVERY_STATUS_DELIVERED = 'delivered';
    const DELIVERY_STATUS_FAILED = 'failed';

    public function ukCertificate()
    {
        return $this->belongsTo(UkCertificate::class);
    }

    public function trainee()
    {
        return $this->belongsTo(\App\Models\Back\Trainee::class)->withTrashed();
    }
}
