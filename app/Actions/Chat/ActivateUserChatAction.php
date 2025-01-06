<?php

namespace App\Actions\Chat;

use App\Models\Chat;

class ActivateUserChatAction
{
    public function run(Chat $chat): void
    {
        $chat->update([
            'active' => true,
        ]);
    }
}
