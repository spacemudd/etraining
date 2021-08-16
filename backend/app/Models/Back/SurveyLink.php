<?php

namespace App\Models\Back;

use App\Scope\TeamScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

class SurveyLink extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($surveyLink) {
            if (auth()->user()) {
                $surveyLink->team_id = auth()->user()->current_team_id;
            }
        });

        static::addGlobalScope(new TeamScope());
    }
}
