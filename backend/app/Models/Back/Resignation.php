<?php

namespace App\Models\Back;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Resignation extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'company_id',
    ];

    protected $with = [
        'created_by',
    ];

    protected $withCount = [
        'trainees',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->status = 'new';
                $model->team_id = auth()->user()->currentTeam()->first()->id;
                $model->created_by_id = auth()->user()->id;
            }
        });
    }

    public function trainees()
    {
        return $this->belongsToMany(Trainee::class, 'resignation_trainees', 'resignation_id', 'trainee_id') ;
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function created_by()
    {
        return $this->belongsTo(User::class);
    }
}
