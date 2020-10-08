<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Permission extends Spatie\Permission\Models\Permission
{
    use HasFactory;
    use HasUuid;

    protected $appends = [
        'display_name',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function getDisplayNameAttribute()
    {
        return __('words.'.$this->name);
    }
}
