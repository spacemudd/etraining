<?php

namespace App\Models\Back;

use App\Models\Team;
use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Str;

class ZoomAccount extends Model implements AuditableContract
{
    use HasFactory;
    use Auditable;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->currentTeam()->first()->id;
            } else {
                // TODO: Identify the tenant later via the domain.
                $model->team_id = Team::first()->id;
            }
        });
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }
}
