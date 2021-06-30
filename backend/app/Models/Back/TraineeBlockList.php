<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class TraineeBlockList extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'team_id',
        'name',
        'trainee_id',
        'identity_number',
        'email',
        'phone',
        'phone_additional',
        'reason',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (!auth()->guest()) {
                $model->team_id = auth()->user()->currentTeam()->first()->id;
            }
        });
    }

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }
}
