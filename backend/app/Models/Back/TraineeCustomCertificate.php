<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TraineeCustomCertificate extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

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
        'has_certificate_file',
        'certificate_file_name',
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

    public function getHasCertificateFileAttribute(): bool
    {
        return $this->hasMedia('certificate_file');
    }

    public function getCertificateFileNameAttribute(): ?string
    {
        $media = $this->getFirstMedia('certificate_file');

        return $media ? $media->name : null;
    }
}
