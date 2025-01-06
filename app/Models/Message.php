<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class Message extends ModelAbstract
{
    const T = 'messages';

    protected $fillable = [
        'data',
        'chat_id',
        'sender_id',
        'receiver_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $hidden = [
        'chat_id',
        'sender_id',
        'receiver_id',
    ];

    /**
     * @return BelongsTo<Chat, Message>
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * @return BelongsTo<User, Message>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, Message>
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Attribute<HtmlString, never>
     */
    public function escapeData(): Attribute
    {
        return Attribute::get(
            fn () => Str::of($this->data)->markdown([
                'html_input' => 'escape',
                'allow_unsafe_links' => false,
                'max_nesting_level' => 5,
            ])->toHtmlString()
        );
    }
}
