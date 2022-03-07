<?php
/**
 * Copyright (c) 2020 - Clarastars, LLC  - All Rights Reserved.
 *
 * Unauthorized copying of this file via any medium is strictly prohibited.
 * This file is a proprietary of Clarastars LLC and is confidential / educational purpose only.
 *
 * https://clarastars.com - info@clarastars.com
 * @author Shafiq al-Shaar <shafiqalshaar@gmail.com>
 */

namespace App\Models;

use App\Scope\TeamScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Str;

class Media extends \Spatie\MediaLibrary\MediaCollections\Models\Media
{
    use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $appends = [
        'download_url',
        'created_at_timezone',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = auth()->user()->currentTeam()->first()->id;
            }
        });
    }

    public function getDownloadUrlAttribute()
    {
        return route('back.media.download', $this->id);
    }

    public function getCreatedAtTimezoneAttribute()
    {
        if (auth()->user()) {
            return Timezone::convertToLocal($this->created_at, 'Y-m-d h:i A');
        }
    }

}
