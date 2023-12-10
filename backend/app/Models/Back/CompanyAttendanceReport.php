<?php

namespace App\Models\Back;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;

class CompanyAttendanceReport extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    const STATUS_REVIEW = 1;
    const STATUS_APPROVED = 2;

    protected $fillable = [
        'company_id',
        'date_from',
        'date_to',
        'status',
        'to_emails',
        'cc_emails',
        'with_attendance_times',
        'with_logo',
    ];

    protected $casts = [
        'date_from' => 'date:d-m-Y',
        'date_to' => 'date:d-m-Y',
        'approved_at' => 'datetime',
    ];

    protected $appends = [
        'period',
        'updated_at_human',
        'approved_at_human',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->status = self::STATUS_REVIEW;
            $model->number = 'ATR-'.MaxNumber::generateForPrefix('ATR');
            if (auth()->user()) {
                $model->created_by_id = auth()->user()->id;
            }
        });

        if (Str::contains(optional(auth()->user())->email, 'ptc-ksa.com') && auth()->user()->email != 'sara@ptc-ksa.com' && auth()->user()->email != 'mashal.a+1@ptc-ksa.com') {
            static::addGlobalScope('RiyadhBankAccounts', function (Builder $builder) {
                $builder->whereHas('company', function ($query) {
                    $query->whereNull('is_ptc_net');
                })->orWhereDoesntHave('company');
            });
        }

        if (Str::contains(optional(auth()->user())->email, 'ptc-ksa.net')) {
            static::addGlobalScope('RiyadhBankAccounts', function (Builder $builder) {
                $builder->whereHas('company', function ($query) {
                    $query->whereNotNull('is_ptc_net');
                });
            });
        }
    }

    public function trainees()
    {
        return $this
            ->belongsToMany(Trainee::class,'company_attendance_reports_trainees')
            ->withTrashed()
            ->withPivot('active', 'status', 'comment', 'start_date', 'end_date');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function company_attendance_reports_trainee()
    {
        return $this->hasMany(CompanyAttendanceReportsTrainee::class);
    }

    public function approved_by()
    {
        return $this->belongsTo(User::class);
    }

    public function getPeriodAttribute()
    {
        return $this->date_from->format('d-m-Y') .' - '.$this->date_to->format('d-m-Y');
    }

    public function getUpdatedAtHumanAttribute()
    {
        return $this->updated_at->setTimezone('Asia/Riyadh')->format('d-m-Y h:ia');
    }

    public function getApprovedAtHumanAttribute()
    {
        return optional(optional($this->approved_at)->setTimezone('Asia/Riyadh'))->format('d-m-Y h:ia');
    }

    public function activeTraineesCount()
    {
        return CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $this->id)
                ->where('active', true)
                ->count();
    }

    public function TraineesReport()
    {
        return CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $this->id)
            ->where('active', true)
            ->get();
    }

    public function getActiveTrainees()
    {
        return CompanyAttendanceReportsTrainee::where('company_attendance_report_id', $this->id)
                ->where('active', true)
                ->get();
    }

    public function getFallsUnderPtcNetAttribute()
    {
        return $this->company->is_ptc_net;
    }

    public function created_by()
    {
        return $this->belongsTo(User::class);
    }

    public function emails()
    {
        return $this->hasMany(CompanyAttendanceReportsEmail::class);
    }

    public function emails_to()
    {
        return $this->emails()->where('type', 'to');
    }

    public function emails_cc()
    {
        return $this->emails()->where('type', 'cc');
    }
}
