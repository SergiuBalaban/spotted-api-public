<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $active
 */
class Chat extends ModelAbstract
{
    const T = 'chats';

    protected $fillable = [
        'owner_id',
        'reporter_id',
        'report_found_id',
        'report_missing_id',
        'active',
    ];

    protected $hidden = [
        'owner_id',
        'reporter_id',
        'report_found_id',
        'report_missing_id',
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function boot(): void
    {
        self::creating(function (Chat $model) {
            $model->active = $model->active ?? 0;
        });

        parent::boot();
    }

    /**
     * @return BelongsTo<User, Chat>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    /**
     * @return BelongsTo<User, Chat>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id', 'id');
    }

    /**
     * @return BelongsTo<Report, Chat>
     */
    public function reportFound(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_found_id', 'id');
    }

    /**
     * @return BelongsTo<Report, Chat>
     */
    public function reportMissing(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'report_missing_id', 'id');
    }

    /**
     * @return HasMany<Message>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'chat_id')->orderByDesc('id');
    }

    /**
     * @return HasOne<Message>
     */
    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'chat_id')->latest();
    }
}
