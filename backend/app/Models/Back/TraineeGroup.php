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

namespace App\Models\Back;

use App\Models\Company;
use App\Scope\TeamScope;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Str;

class TraineeGroup extends Model implements Auditable
{
    use HasFactory;
    use HasUuid;
    use \OwenIt\Auditing\Auditable;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'company_id',
    ];

    protected $appends = [
        'name_selectable',
        'class_timings'
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new TeamScope());
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
            if (auth()->user()) {
                $model->team_id = $model->team_id = auth()->user()->currentTeam()->first()->id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function trainees()
    {
        return $this->hasMany(Trainee::class);
    }

    public function getNameSelectableAttribute()
    {
        return $this->name;
    }

    public function getClassTimingsAttribute()
    {
        switch ($this->name) {
            case 'شعبة 7':
            case 'شعبة 1':
            case 'شعبة 15':
                return 'كل أحد وإثنين الساعة 11:30 مساءً الى 12:30 صباحا';
            case 'شعبة 6':
            case 'شعبة 12':
            case 'شعبة 2':
                return 'كل ثلاثاء وأربعاء الساعة 10:00 مساءً الى 11:00 مساءً';
            case 'شعبة 8':
            case 'شعبة 10':
            case 'شعبة 11':
            case 'شعبة 3':
                return 'كل أحد وإثنين الساعة 10:00 مساءً الى 11:00 مساءً';
            case 'شعبة 5':
            case 'شعبة 13':
            case 'شعبة 4':
                return 'كل ثلاثاء وأربعاء الساعة 11:30 مساءً الى 12:30 صباحاً';
            case 'شعبة 9':
            case 'شعبة 14':
            case 'شعبة 16':
            case 'شعبة 17':
                return 'كل احد وإثنين الساعة 8:30 مساءً الى 09:30 مساءً';
        }
        return;
    }
}
