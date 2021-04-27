<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class AttendanceReportRecord extends Model implements Auditable
{
    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;

    public $guarded = ['id'];

    public $incrementing = false;

    protected $keyType = 'string';

    const STATUS_ABSENT = 0;
    const STATUS_ABSENT_WITH_EXCUSE = 1;
    const STATUS_LATE_TO_CLASS = 2;
    const STATUS_PRESENT = 3;

    protected $appends = [
        'status_name',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($audit) {
            $audit->{$audit->getKeyName()} = (string) Str::uuid();

            if (auth()->user()) {
                $audit->team_id = auth()->user()->current_team_id;
            }
        });

        static::addGlobalScope(new TeamScope());
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    /**
     *
     * @return string
     */
    public function getStatusNameAttribute()
    {
        $status = '';

        switch ($this->status) {
            case self::STATUS_ABSENT:
                $status = 'absent';
                break;
            case self::STATUS_ABSENT_WITH_EXCUSE;
                $status = 'absent-with-excuse';
                break;
            case self::STATUS_LATE_TO_CLASS:
                $status = 'present-but-late';
                break;
            case self::STATUS_PRESENT:
                $status = 'present';
                break;
        }

        return $status;
    }
}
