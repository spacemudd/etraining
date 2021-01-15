<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasPermissions;

class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;
    use HasUuid;

    protected $appends = [
        'display_name',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function getNameAttribute($value)
    {
        return $value;
        return Str::after($value, '_');
    }

    public function getDisplayNameAttribute()
    {
        return __('words.'.Str::after($this->name, '_'));
    }
}
