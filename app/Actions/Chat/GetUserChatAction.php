<?php

namespace App\Actions\Chat;

use App\Models\Chat;
use App\Tasks\Chat\GetUserChatTask;

class GetUserChatAction
{
    public function run(Chat $chat): ?Chat
    {
        return app(GetUserChatTask::class)->run($chat);
    }
}
