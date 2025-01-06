<?php

namespace App\Tasks\Chat;

use App\Exceptions\UnauthorizedException;
use App\Models\Chat;

class GetUserChatTask
{
    /**
     * @throws UnauthorizedException
     */
    public function run(Chat $chat): Chat
    {
        $channelQuery = app(GetChatQueryTask::class)->run();
        try {
            /** @var Chat $chat */
            $chat = $channelQuery->findOrFail($chat->id);
        } catch (\Exception $exception) {
            throw new UnauthorizedException('You are unauthorized');
        }

        return $chat;
    }
}
