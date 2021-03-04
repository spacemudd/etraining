<?php

namespace App\Models;

use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use App\Traits\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use JamesMills\LaravelTimezone\Facades\Timezone;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasUuid;
    use HasRoles;
    use Impersonate;

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'last_login_at', 'phone',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'inbox_messages_count',
        'last_login_at_timezone',
    ];

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultProfilePhotoUrl()
    {
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=FFFFFF&background=EA1D3C';
    }

    public function logrocketId()
    {
        return $this->id;
    }

    public function logrocketIdExtra()
    {
        return json_encode([
            'name' => $this->name,
            'email' => $this->email,
        ], JSON_UNESCAPED_UNICODE);
    }

    public function getLastLoginAtTimezoneAttribute()
    {
        if ($this->last_login_at) {
            return Timezone::convertToLocal($this->last_login_at, 'Y-m-d h:i A');
        }
    }

    /**
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale()
    {
        return $this->locale;
    }

    public function getInboxMessagesCountAttribute()
    {
        return $this->inbox_messages_for_me()->unread()->count();
    }

    public function inbox_messages_for_me()
    {
        return $this->hasMany(InboxMessage::class, 'to_id', 'id');
    }

    public function scopeFindByEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    public function isTrainee()
    {
        return Str::contains(optional($this->roles()->first())->name, '_trainees');
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function trainee()
    {
        return $this->hasOne(Trainee::class);
    }

    public function routeNotificationForClickSend()
    {
        return $this->phone;
    }

    public function canImpersonate()
    {
        return $this->hasPermissionTo('can-impersonate');
    }
}
