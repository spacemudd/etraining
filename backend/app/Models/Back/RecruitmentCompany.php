<?php

namespace App\Models\Back;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Back\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;





class RecruitmentCompany extends Model
{
    use HasFactory;
    use HasUuid;
    // use SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'name_en',
        'created_by_id',
    ];

    // protected $dates = ['deleted_at'];


    public function companies()
    {
        return $this->hasMany(Company::class);
    }
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

  
}
