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
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class TraineeGroup extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasFactory;
    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'company_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function trainees()
    {
        return $this->belongsToMany(Trainee::class, 'trainee_group_trainee');
    }
}
