<?php

namespace App\Actions\Chat;

use App\Models\Chat;
use App\Tasks\Chat\GetUserChatTask;

class DeleteUserChatAction
{
    public function run(Chat $chat): void
    {
        app(GetUserChatTask::class)->run($chat);
        $chat->delete();
    }
}
