<?php

namespace App\Models\Back;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAttendanceReport extends Model
{
    use HasFactory;

    const STATUS_REVIEW = 1;
    const STATUS_APPROVED = 2;

    protected $fillable = [
        'company_id',
        'date_from',
        'date_to',
        'status',
        'to_emails',
        'cc_emails',
    ];

    protected $casts = [
        'date_from' => 'date:d-m-Y',
        'date_to' => 'date:d-m-Y',
    ];

    protected $appends = [
        'period',
        'updated_at_human',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->status = self::STATUS_REVIEW;
            $model->number = 'ATR-'.MaxNumber::generateForPrefix('ATR'); // ATendance Report
            if (auth()->user()) {
                $model->created_by_id = auth()->user()->id;
            }
        });
    }

    public function trainees()
    {
        return $this
            ->belongsToMany(Trainee::class,'company_attendance_reports_trainees')
            ->withPivot('active');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getPeriodAttribute()
    {
        return $this->date_from->format('d-m-Y') .' - '.$this->date_to->format('d-m-Y');
    }

    public function getUpdatedAtHumanAttribute()
    {
        return $this->updated_at->setTimezone('Asia/Riyadh')->format('d-m-Y h:ia');
    }
}
