<?php

namespace App\Models;

use Akuechler\Geoly;
use App\Events\petMarkedAsEvent;
use App\Traits\CoordinationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportedPet extends Model
{
    use Geoly;
    use SoftDeletes;
    use CoordinationTrait;
    const T = 'reported_pets';

    const DEFAULT_USER_REPORTED_PETS = 4;
    const DEFAULT_DAYS_BEFORE_DELETE = 2;
    const DEFAULT_RADIUS_IN_KM = 10; // 10 km
    const DEFAULT_AVATAR_ORIGIN_IN_PIXEL = 350;
    const DEFAULT_AVATAR_THUMB_IN_PIXEL = 100;

    const FOUND_ON_APP = "found_on_app";
    const FOUND_OUTSIDE_APP = "found_outside_app";

    const AVATAR_TYPE = "pet_reported_avatar";
    const STATUS_MISSING = "missing";
    const STATUS_REPORTED = "reported";
    const STATUS_FOUND = "found";
    const STATUS_PENDING = "closed";
    const STATUSES_ON_MAP = [self::STATUS_MISSING, self::STATUS_REPORTED];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'details',
        'found_in_app',
    ];

    protected $casts = [
        'latitude'  => 'double',
        'longitude' => 'double',
        'avatar'    => 'array',
        'details'   => 'array',
    ];

    protected $hidden = [
        'pet_id',
        'user_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $with = ['pet'];

    public static function boot()
    {
        self::creating(function (ReportedPet $model) {
            $model->category = $model->pet->category ?? $model->details['category'];
            $model->message = $model->details['message'];
        });

        self::created(function (ReportedPet $model) {
            switch ($model->status) {
                case ReportedPet::STATUS_MISSING:
                    event(new petMarkedAsEvent($model));
                    break;
            }
        });

        self::updated(function (ReportedPet $model) {
            switch ($model->status) {
                case ReportedPet::STATUS_FOUND:
                    event(new petMarkedAsEvent($model));
                    break;
            }
        });

        self::deleting(function (ReportedPet $model) {
            $model->trackedPets()->delete();
        });

        parent::boot();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['avatar'] = $this->avatar['root'] ?? $this->pet->avatar['root'] ?? '';
        $array['avatar_thumb'] = $this->avatar['root_thumb'] ?? $this->pet->avatar['root_thumb'] ?? '';
        return $array;
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function trackedPets()
    {
        return $this->hasMany(TrackedReportedPet::class, 'reported_pet_id', 'id');
    }

    /**
     * @param $petId
     * @return HasMany
     */
    public function trackedPet($petId)
    {
        return $this->trackedPets()->where('pet_id', $petId);
    }

    /**
     * @return HasMany
     */
    public function chatWithMissingPet($missingPetId)
    {
        return $this->hasMany(Chat::class, 'reported_pet_id', 'id')->where('missing_pet_id', $missingPetId)->orderByDesc('created_at');
    }
}
