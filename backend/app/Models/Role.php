<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasPermissions;

class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;
    use HasUuid;

    protected $appends = [
        'display_name',
        'order',
        'role_description',
        'can_manage_users',
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

    public function getOrderAttribute()
    {
        if (Str::contains($this->name, 'admins')) return 1;
        if (Str::contains($this->name, 'finance')) return 2;
        if (Str::contains($this->name, 'chasers')) return 3;
        if (Str::contains($this->name, 'instructors')) return 4;
        if (Str::contains($this->name, 'trainees')) return 5;
        if (Str::contains($this->name, 'services')) return 6;
        if (Str::contains($this->name, 'services_manager')) return 7;
        if (Str::contains($this->name, 'backend')) return 8;
    }

    public function getRoleDescriptionAttribute()
    {
        if (Str::contains($this->name, 'admins')) return __('words.admins-role-info');
        if (Str::contains($this->name, 'finance')) return __('words.finance-role-info');
        if (Str::contains($this->name, 'instructors')) return __('words.instructors-role-info');
        if (Str::contains($this->name, 'trainees')) return __('words.trainees-role-info');
        if (Str::contains($this->name, 'chasers')) return __('words.chasers-role-info');
        if (Str::contains($this->name, 'services')) return __('words.services-role-info');
        if (Str::contains($this->name, 'services_manager')) return __('words.services-manager-role-info');
        if (Str::contains($this->name, 'backend')) return __('words.services-manager-role-info');
    }

    public function getCanManageUsersAttribute()
    {
        if (Str::contains($this->name, 'instructors')) return false;
        if (Str::contains($this->name, 'trainees')) return false;

        return true;
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            User::class,
            'model',
            config('permission.table_names.model_has_roles'),
            'role_id',
            config('permission.column_names.model_morph_key')
        );
    }
}
