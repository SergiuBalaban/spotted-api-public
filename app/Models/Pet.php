<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use SoftDeletes;
    const T = 'pets';

    const MAX_PET_UPLOADS_LIMIT = 6;

    const STATUS_LOST = 'lost';
    const STATUS_MISSING = 'missing';
    const STATUS_FOUND = 'found';
    const STATUS_NORMAL = 'normal';
    const STATUSES = [self::STATUS_MISSING, self::STATUS_FOUND, self::STATUS_LOST];

    const AVATAR_TYPE = 'pet_avatar';
    const GALLERY_TYPE = 'pet_gallery';

    const CATEGORY_DOG = 'Dog';
    const CATEGORY_CAT = 'Cat';
    const SEX_M = 'M';
    const SEX_F = 'F';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nickname',
        'birthday',
        'status',
        'sex',
        'avatar_id',
        'active',
        'category',
        'species',
        'avatar',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'avatar'    => 'array',
        'gallery'   => 'array',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function toArray()
    {
        $array = parent::toArray();
        $array['avatar'] = $this->avatar['root'] ?? '';
        $array['avatar_thumb'] = $this->avatar['root_thumb'] ?? '';
        $galleries = [];
        foreach ((array)$this->gallery as $gallery) {
            $galleries[] = $gallery['root'];
        }
        $array['gallery'] = $galleries ?? [];
        return $array;
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function reports()
    {
        return $this->hasMany(ReportedPet::class, 'pet_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function reportedMissingPet()
    {
        return $this->hasOne(ReportedPet::class, 'pet_id', 'id')->where('status', Pet::STATUS_MISSING);
    }

    /**
     * @return HasMany
     */
    public function reportedFoundPet()
    {
        return $this->hasMany(ReportedPet::class, 'pet_id', 'id')
            ->where('status', Pet::STATUS_FOUND);
    }

    /**
     * @return HasMany
     */
    public function trackedReportedPets()
    {
        return $this->hasMany(TrackedReportedPet::class, 'pet_id', 'id');
    }

    /**
     * @param $reportedPetId
     * @return HasMany
     */
    public function trackedReportedPet($reportedPetId)
    {
        return $this->trackedReportedPets()->where('reported_pet_id', $reportedPetId);
    }

    /**
     * @return HasMany
     */
    public function trackedReportedPetMarkedAsIdentical()
    {
        return $this->trackedReportedPets()->where('status', TrackedReportedPet::STATUS_IDENTICALLY);
    }

    /**
     * @return HasMany
     */
    public function trackedReportedPetMarkedAsNotIdentical()
    {
        return $this->trackedReportedPets()->where('status', '=', TrackedReportedPet::STATUS_NOT_IDENTICALLY);
    }

    /**
     * @param $reportedPetId
     * @return HasMany
     */
    public function chatWithReportedPet($reportedPetId)
    {
        return $this->hasMany(Chat::class, 'missing_pet_id', 'id')->where('reported_pet_id', $reportedPetId)->orderByDesc('created_at');
    }
}
