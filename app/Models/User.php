<?php

namespace App\Models;

use App\Exceptions\UnauthorizedException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property string $phone
 * @property bool $admin
 * @property string $email
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    const T = 'users';

    const AVATAR_TYPE = 'user_avatar';

    const ROMANIA_COUNTRY_CODE = '+40';

    const DEFAULT_PASSWORD = 'pet';

    const POSSIBLE_UPDATED_FIELDS = [
        'name',
        //        'email',
        'avatar',
    ];

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
        'timezone',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'invite_token',
        'email_verified_at',
        'phone_prefix',
        'active',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'avatar' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    //    protected $with = ['pets', 'reportedPets', 'missingReportedPets'];

    public function toArray(): array
    {
        $array = parent::toArray();
        $array['avatar'] = $this->avatar['root'] ?? null;

        return $array;
    }

    public static function boot(): void
    {
        self::creating(function ($model) {
            $model->phone_prefix = self::ROMANIA_COUNTRY_CODE;
            $model->active = 1;
        });

        self::updating(function ($model) {
            if ($model->isDirty(['phone_prefix', 'phone'])) {
                throw new UnauthorizedException('Phone number cannot be updated');
            }
        });

        parent::boot();
    }

    /**
     * @return HasMany<Pet>
     */
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    /**
     * @return HasMany<Pet>
     */
    public function missingPets(): HasMany
    {
        return $this->pets()->where('status', Pet::STATUS_MISSING);
    }

    /**
     * @return HasMany<Report>
     */
    public function allReportedPets(): HasMany
    {
        return $this->hasMany(Report::class)->orderByDesc('id');
    }

    /**
     * @return HasMany<Report>
     */
    public function reportedPets(): HasMany
    {
        return $this->allReportedPets()->where('status', Report::STATUS_REPORTED);
    }

    /**
     * @return HasMany<Report>
     */
    public function missingReportedPets(): HasMany
    {
        return $this->allReportedPets()->where('status', Pet::STATUS_MISSING);
    }

    /**
     * @return HasMany<Chat>
     */
    public function ownerChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'owner_id')->orderByDesc('id');
    }

    /**
     * @return HasMany<Chat>
     */
    public function collaboratorChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'user_id')->orderByDesc('id');
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * @return array<string, array<string, int|string>>
     */
    public function getJWTCustomClaims(): array
    {
        $user = [
            'id' => $this->id,
            'phone' => $this->phone,
        ];
        if ($this->admin) {
            $user = array_merge($user, [
                'email' => $this->email,
            ]);
        }

        return ['user' => $user];
    }
}
