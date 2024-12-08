<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BannedUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param User $user
     */
    public function handle(User $user)
    {
        print_r($user->id);die;
        switch ($user->banned_count) {
            case $user->banned_count > 2:
                $time = Carbon::now()->addHours(3);
                break;
            default:
                $time = Carbon::now()->addHour();
        }
        $user->update([
            'banned' => true,
            'banned_count' => $user->banned_count + 1,
            'banned_at' => $time,
        ]);
    }
}
