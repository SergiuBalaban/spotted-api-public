<?php

namespace App\Models;

use Akuechler\Geoly;
use App\Traits\CoordinationTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Report extends ModelAbstract
{
    use CoordinationTrait;
    use Geoly;

    const T = 'reports';

    const DEFAULT_USER_REPORTED_PETS = 4;

    const DEFAULT_DAYS_BEFORE_DELETE = 2;

    const DEFAULT_RADIUS_IN_KM = 10; // 10 km

    const DEFAULT_AVATAR_ORIGIN_IN_PIXEL = 350;

    const DEFAULT_AVATAR_THUMB_IN_PIXEL = 100;

    const FOUND_ON_APP = 'found_on_app';

    const FOUND_OUTSIDE_APP = 'found_outside_app';

    const AVATAR_TYPE = 'pet_reported_avatar';

    const STATUS_MISSING = 'missing';

    const STATUS_REPORTED = 'reported';

    const STATUS_FOUND = 'found';

    const STATUS_PENDING = 'closed';

    const STATUSES_ON_MAP = [self::STATUS_MISSING, self::STATUS_REPORTED];

    protected $fillable = [
        'user_id',
        'pet_id',
        'status',
        'category',
        'latitude',
        'longitude',
        'country',
        'city',
        'formatted_address',
        'dms_location',
        'avatar',
        'message',
        'found_in_app',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'avatar' => 'array',
        'found_in_app' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'pet_id',
        'user_id',
    ];

    //    public function toArray(): array
    //    {
    //        $array = parent::toArray();
    //        $array['avatar'] = $this->avatar['root'] ?? $this->pet->avatar['root'] ?? '';
    //        $array['avatar_thumb'] = $this->avatar['root_thumb'] ?? $this->pet->avatar['root_thumb'] ?? '';
    //
    //        return $array;
    //    }

    public static function boot(): void
    {
        self::creating(function (Report $model) {
            $user = Auth::user();
            //            $model->user_id = $model->user_id ?? ($user?->id ?? null);
            $model->status = $model->status ?? self::STATUS_REPORTED;
            $model->found_in_app = $model->found_in_app ?? 0;
        });

        parent::boot();
    }

    /**
     * @return BelongsTo<User, Report>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo<Pet, Report>
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'id');
    }

    /**
     * @return HasMany<Chat>
     */
    public function missingPetChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'report_missing_id', 'id');
    }

    /**
     * @return HasMany<Chat>
     */
    public function reportChats(): HasMany
    {
        return $this->hasMany(Chat::class, 'report_found_id', 'id');
    }
}
