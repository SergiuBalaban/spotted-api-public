<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    const T = 'users';
    const AVATAR_TYPE = 'user_avatar';
    const ROMANIA_COUNTRY_CODE = '+40';
    const DEFAULT_PASSWORD = 'pet';
    const DEFAULT_VALID_EXPIRATION_CODE_IN_MIN = 5;

    const DEFAULT_PHONES_FOR_TESTING = [
        '+40711111111',
        '+40711111112',
        '+40711111113',
        '+40711111114',
        '+40711111115',
        '+40711111116',
        '+40711111117',
        '+40711111118',
        '+40711111119',
        '+40755858442',
        '+40749096820',
        '+40747832443',
        '+40747832443',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'password',
        'phone',
        'phone_prefix',
        'email',
        'email_verified_at',
        'invite_token',
        'avatar',
        'admin',
        'sms_code',
        'sms_code_expiration',
        'timezone',
        'active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'invite_token',
        'sms_code',
        'sms_code_expiration',
        'email_verified_at',
        'phone_prefix',
        'active',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at'     => 'datetime',
        'sms_code_expiration'   => 'datetime',
        'avatar'                => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'email_verified_at',
        'sms_code_expiration',
    ];

    protected $with = ['pets', 'reportedPets', 'missingPets'];

    public function toArray()
    {
        $array = parent::toArray();
        $array['avatar'] = $this->avatar['root'] ?? null;
        return $array;
    }

    public function isAdmin()
    {
        return $this->belongsTo(self::class)->where('admin', 1);
    }

    /**
     * @return HasMany
     */
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * @return HasMany
     */
    public function allReportedPets()
    {
        return $this->hasMany(ReportedPet::class)->orderByDesc('id');
    }

    /**
     * @return HasMany
     */
    public function reportedPets()
    {
        return $this->allReportedPets()->where('status', ReportedPet::STATUS_REPORTED);
    }

    /**
     * @return HasMany
     */
    public function missingPets()
    {
        return $this->allReportedPets()->where('status', Pet::STATUS_MISSING);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        $user = [
            'id' => $this->id,
            'phone' => $this->phone
        ];
        if($this->admin) {
            $user = array_merge($user, [
                'email' => $this->email
            ]);
        }
        return ['user' => $user];
    }
}
