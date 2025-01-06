<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BannedUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(User $user): void
    {
        $time = match (true) {
            $user->banned_count > 2 => now()->addHours(3),
            default => now()->addHour(),
        };
        $user->update([
            'banned' => true,
            'banned_count' => $user->banned_count + 1,
            'banned_at' => $time,
        ]);
    }
}
