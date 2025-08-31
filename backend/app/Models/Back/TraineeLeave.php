<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JamesMills\LaravelTimezone\Facades\Timezone;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TraineeLeave extends Model implements Auditable, HasMedia
{
    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;
    use InteractsWithMedia;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $appends = [
        'created_at_timezone',
        'from_date_formatted',
        'to_date_formatted',
        'has_file',
        'leave_file_url',
        'leave_file_name',
    ];

    protected $fillable = [
        'trainee_id',
        'leave_type',
        'from_date',
        'to_date',
        'status',
        'notes',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'from_date',
        'to_date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();

            if (auth()->user()) {
                $model->team_id = auth()->user()->current_team_id;
                $model->status = 'pending';
            }
        });

        static::addGlobalScope(new TeamScope());
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function getCreatedAtTimezoneAttribute()
    {
        if ($this->created_at) {
            return Timezone::convertToLocal($this->created_at, 'Y-m-d h:i A');
        }
    }

    public function getFromDateFormattedAttribute()
    {
        if ($this->from_date) {
            return $this->from_date->format('Y-m-d');
        }
    }

    public function getToDateFormattedAttribute()
    {
        if ($this->to_date) {
            return $this->to_date->format('Y-m-d');
        }
    }

    public function getHasFileAttribute()
    {
        return $this->hasMedia('leave_file');
    }

    public function getLeaveFileUrlAttribute()
    {
        $media = $this->getFirstMedia('leave_file');
        if ($media) {
            $url = $media->getUrl();
            // إذا كان التطبيق يعمل على منفذ مختلف عن 80، أضف المنفذ للرابط
            if (config('app.env') === 'local') {
                // تأكد من أن الرابط يحتوي على المنفذ 8000
                $url = str_replace('://localhost/', '://localhost:8000/', $url);
            }
            return $url;
        }
        return null;
    }

    public function getLeaveFileNameAttribute()
    {
        $media = $this->getFirstMedia('leave_file');
        return $media ? $media->name : null;
    }
}

