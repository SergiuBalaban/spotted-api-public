<?php

namespace App\Tasks\Chat;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Collection;

class GetUserChatsTask
{
    /**
     * @return Collection<int, Chat>
     */
    public function run(): Collection
    {
        $channelQuery = app(GetChatQueryTask::class)->run();

        return $channelQuery->get();
    }
}
