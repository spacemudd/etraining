<?php
/*
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Models\Back;

use Str;
use App\Scope\TeamScope;

class Audit extends \OwenIt\Auditing\Models\Audit
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $appends = [
        'created_at_human',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($audit) {
            $audit->{$audit->getKeyName()} = (string) Str::uuid();

            if (auth()->user()) {
                $audit->team_id = auth()->user()->team_id;
            }
        });

        static::addGlobalScope(new TeamScope());
    }

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->toDateTimeString();
    }
}
