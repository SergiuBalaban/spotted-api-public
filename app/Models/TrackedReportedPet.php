<?php

namespace App\Models;

use App\Events\petMissingEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrackedReportedPet extends Model
{
    use SoftDeletes;
    const T = 'tracked_reported_pets';

    const STATUS_RESEMBLE = 'resemble';
    const STATUS_IDENTICALLY = 'identically';
    const STATUS_NOT_IDENTICALLY = 'not_identically';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reported_pet_id',
        'pet_id',
        'category',
        'is_identically',
        'status',
    ];

    protected $casts = [];

    protected $hidden = [
        'reported_pet_id',
        'pet_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $with = ['reportedPet'];

    public static function boot()
    {
        self::created(function (TrackedReportedPet $model) {
            event(new petMissingEvent($model));
        });

        self::deleting(function (TrackedReportedPet $model) {
            event(new petMissingEvent($model));
        });

        parent::boot();
    }

    /**
     * @return BelongsTo
     */
    public function reportedPet()
    {
        return $this->belongsTo(ReportedPet::class, 'reported_pet_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'id');
    }
}
