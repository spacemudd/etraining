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
            case 'شعبة 1':
                return 'كل أحد والإثنين الساعة 10:00 الى 3:00 ظهراً';
            case 'شعبة 2':
                return 'كل ثلاثاء والأربعاء الساعة 10:00 الى 12:00 ظهراً';
            case 'شعبة 3':
                return 'كل أحد والإثنين الساعة 10:00 الى 12:00 ظهراً';
            case 'شعبة 4':
                return 'كل ثلاثاء والأربعاء الساعة 1:00 الى 3:00 مساءً';
            case 'شعبة 5':
                return 'كل ثلاثاء والأربعاء الساعة 1:00 الى 3:00 مساءً';
            case 'شعبة 6':
                return 'كل ثلاثاء والأربعاء الساعة 10:00 الى 12:00 ظهراً';
            case 'شعبة 7':
                return 'كل أحد والإثنين الساعة 1:00 الى 3:00 ظهراً';
            case 'شعبة 8':
                return 'كل أحد والإثنين الساعة 10:00 الى 12:00 ظهراً';
            case 'شعبة 9':
                return 'كل احد والإثنين الساعة 800 الى 10:00 صباحًا';
            case 'شعبة 10':
                return 'كل احد والإثنين الساعة 10:00 الى 12:00 ظهراً';
            case 'شعبة 11':
                return 'كل احد والإثنين الساعة 10:00 الى 12:00 ظهراً';
            case 'شعبة 12':
                return 'كل ثلاثاء والأربعاء الساعة 10:00 الى 12:00 ظهراً';
            case 'شعبة 13':
                return 'كل ثلاثاء والأربعاء الساعة 1:00 الى 3:00 مساءً';
        }

        return;
    }
}
