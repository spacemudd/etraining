<?php

namespace App\Models\Back;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    use HasUuid;

    use HasUuid;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'name_ar',
        'name_en',
        'cr_number',
        'contact_number',
        'company_rep',
        'company_rep_mobile',
        'email',
        'address',
    ];
}
