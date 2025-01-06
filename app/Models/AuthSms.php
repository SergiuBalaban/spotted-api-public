<?php

namespace App\Models;

use App\Exceptions\UnauthorizedException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

/**
 * @property string $phone
 * @property int $sms_attempts
 * @property string|null $sms_expired_at
 * @property string|null $sms_blocked_at
 * @property string|null $invite_token
 * @property string|null $sms_code
 */
class AuthSms extends ModelAbstract
{
    use Notifiable;

    const T = 'auth_sms';

    const ROMANIA_COUNTRY_CODE = '+40';

    const DEFAULT_SMS_EXPIRED_AT_IN_MIN = 5;

    const DEFAULT_SMS_MAX_ATTEMPTS = 3;

    protected $fillable = [
        'phone',
        'phone_prefix',
        'sms_code',
        'sms_attempts',
        'sms_created_at',
        'sms_expired_at',
        'sms_blocked_at',
        'invite_token',
    ];

    protected $casts = [
        'sms_created_at' => 'datetime',
        'sms_expired_at' => 'datetime',
        'sms_blocked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public static function boot(): void
    {
        self::creating(function ($model) {
            $model->phone_prefix = self::ROMANIA_COUNTRY_CODE;
            $model->sms_attempts = $model->sms_attempts ?? 0;
            $model->sms_created_at = now();
            $model->sms_expired_at = $model->sms_expired_at ?? now()->addMinutes(self::DEFAULT_SMS_EXPIRED_AT_IN_MIN);
        });

        self::updating(function ($model) {
            if ($model->isDirty(['phone_prefix', 'phone'])) {
                throw new UnauthorizedException('Phone number cannot be updated');
            }

            if ($model->isDirty('sms_attempts')) {
                $model->sms_created_at = now();
                $model->sms_expired_at = now()->addMinutes(self::DEFAULT_SMS_EXPIRED_AT_IN_MIN);
            }
        });

        parent::boot();
    }

    public function routeNotificationForMail(Notification $notification): string
    {
        return 'sergiu.balaban92@gmail.com';
    }

    public function routeNotificationForVonage(Notification $notification): string
    {
        return $this->phone;
    }

    public function getNoAttemptsLeftAttribute(): bool
    {
        return $this->sms_attempts > self::DEFAULT_SMS_MAX_ATTEMPTS;
    }

    public function getSmsExpiredAttribute(): bool
    {
        return now()->greaterThan($this->sms_expired_at);
    }

    public function getIsBLockedAttribute(): bool
    {
        return $this->sms_blocked_at && now()->lessThan($this->sms_blocked_at);
    }
}
