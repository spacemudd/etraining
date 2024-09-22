<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * This is a class stripped out of relationships for speed optimization.
 *
 */
class TraineeContract extends Model implements Auditable
{
    use HasUuid;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'trainees';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $appends = [
        'trainee_group_object',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function trainee_group()
    {
        return $this->belongsTo(TraineeGroup::class);
    }

    public function getTraineeGroupObjectAttribute()
    {
        if( isset($this->trainee_group[0])) {
            return $this->trainee_group[0];
        }
    }
}
