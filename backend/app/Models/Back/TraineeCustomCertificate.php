<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraineeCustomCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainee_id',
        'title',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    protected $appends = [
        'issued_at_formatted',
    ];

    /**
     * Get the trainee that owns the certificate.
     */
    public function trainee()
    {
        return $this->belongsTo(Trainee::class)->withTrashed();
    }

    /**
     * Get the issued date formatted for display.
     */
    public function getIssuedAtFormattedAttribute()
    {
        return $this->issued_at ? $this->issued_at->format('Y/m/d') : $this->created_at->format('Y/m/d');
    }
}
