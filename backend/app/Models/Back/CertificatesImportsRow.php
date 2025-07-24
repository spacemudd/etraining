<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificatesImportsRow extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_id',
        'course_id',
        'sent_at',
        'pdf_path',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class)->withTrashed();
    }
}
