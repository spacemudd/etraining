<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Str;

class Course extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'instructor_id',
        'company_id',
        'description',
        'classroom_count',
        'approval_code',
        'days_duration',
        'hours_duration',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = auth()->user()->personalTeam()->id;
            }
        });
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
