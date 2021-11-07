<?php

namespace App\Models;

use App\Models\Back\Instructor;
use App\Models\Back\Trainee;
use App\Traits\HasUuid;
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
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
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
    use \OwenIt\Auditing\Auditable;

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

    public function logrocketEnabled()
    {
        return in_array($this->email, [
            'sara@ptc-ksa.com',
            'leena@ptc-ksa.com',
        ]);
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

    public function team()
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    public function routeNotificationForClickSend()
    {
        return $this->cleanUpThePhoneNumber($this->phone);
    }

    public function canImpersonate()
    {
        return $this->hasPermissionTo('can-impersonate');
    }

    public function cleanUpThePhoneNumber($phone)
    {
        $convertPhone = $this->arabicE2w($phone);

        if (! Str::startsWith($convertPhone, '966')) {
            $convertPhone = Str::replaceFirst('05', '9665', $convertPhone);
        }

        if (Str::startsWith($convertPhone, '5')) {
            $convertPhone = Str::replaceFirst('5', '9665', $convertPhone);
        }


        if (Str::length($convertPhone) != 12) { // KSA number.
            return null;
        }

        return $convertPhone;
    }

    public function arabicE2w($str) {
        $arabic_eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $arabic_western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($arabic_eastern, $arabic_western, $str);
    }
}
