<?php

namespace App\Models\Back;

use App\Mail\TraineeCertificateMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class TraineeCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'trainee_id',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class)->withTrashed();
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function send_email()
    {
        Mail::to($this->trainee->email)
            ->queue(new TraineeCertificateMail($this->id));

    }
}
