<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use SoftDeletes;
    const T = 'chats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'message',
        'sender_user_id',
        'receiver_user_id',
        'reported_pet_id',
        'missing_pet_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function toArray()
    {
        return [
            'id'                => $this->id,
            'message'           => $this->message,
            'sender_user_id'    => $this->sender_user_id,
            'receiver_user_id'  => $this->receiver_user_id,
            'missing_pet_id'    => $this->missing_pet_id,
            'created_at'        => $this->created_at,
        ];
    }

//    public function getCreatedAtAttribute($value){
//        $date = Carbon::parse($value);
//        return $date->format('Y-m-d H:i');
//    }
//
//    public function getUpdatedAtAttribute($value){
//        $date = Carbon::parse($value);
//        return $date->format('Y-m-d H:i');
//    }

    /**
     * @return BelongsTo
     */
    public function senderUser()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function receiverUser()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function reportedPet()
    {
        return $this->belongsTo(ReportedPet::class);
    }

    /**
     * @return BelongsTo
     */
    public function missingPet()
    {
        return $this->belongsTo(Pet::class);
    }
}
