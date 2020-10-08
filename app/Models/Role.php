<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Role extends Spatie\Permission\Models\Role
{
    use HasFactory;

    protected $appends = [
        'display_name',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function getNameAttribute($value)
    {
        return Str::after($value, '_');
    }

    public function getDisplayNameAttribute()
    {
        return __('words.'.$this->name);
    }
}
