<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property array $gallery
 */
class Pet extends ModelAbstract
{
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

    const CATEGORIES = [
        self::CATEGORY_DOG,
        self::CATEGORY_CAT,
    ];

    const SEX_M = 'M';

    const SEX_F = 'F';

    const SEX = [
        self::SEX_F,
        self::SEX_M,
    ];

    const POSSIBLE_UPDATED_FIELDS = [
        'nickname',
        'sex',
        'category',
        'species',
    ];

    protected $fillable = [
        'nickname',
        'birthday',
        'status',
        'sex',
        'avatar',
        'active',
        'category',
        'species',
        'avatar',
    ];

    protected $casts = [
        'avatar' => 'array',
        'gallery' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    //    public function toArray(): array
    //    {
    //        $array = parent::toArray();
    //        $array['avatar'] = $this->avatar['root'] ?? '';
    //        $array['avatar_thumb'] = $this->avatar['root_thumb'] ?? '';
    //        $galleries = [];
    //        foreach ((array) $this->gallery as $gallery) {
    //            $galleries[] = $gallery['root'];
    //        }
    //        $array['gallery'] = $galleries ?? [];
    //
    //        return $array;
    //    }

    /**
     * @return BelongsTo<User, Pet>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasOne<Report>
     */
    public function report(): HasOne
    {
        return $this->hasOne(Report::class, 'pet_id', 'id')
            ->where('status', Pet::STATUS_MISSING);
    }
}
