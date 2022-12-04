<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificatesImport extends Model
{
    use HasFactory;

    const STATUS_IMPORTING = 1;
    const STATUS_SENDING = 2;
    const STATUS_SENT = 3;

    protected $casts = [
        'failed_rows' => 'array',
    ];

    protected $appends = [
        'can_issue',
        'status_text',
    ];

    public function rows()
    {
        return $this->hasMany(CertificatesImportsRow::class, 'certificates_import_id');
    }

    public function getCanIssueAttribute()
    {
        return $this->status == self::STATUS_IMPORTING;
    }

    public function getStatusTextAttribute()
    {
        switch ($this->status) {
            case self::STATUS_IMPORTING:
                return __('words.processing');
            case self::STATUS_SENDING:
                return __('words.sending-in-progress');
            case self::STATUS_SENT:
                return __('words.sent');
        }
    }
}
