<?php

namespace App\Actions\Chat;

use App\Http\Requests\ChatCreateRequest;
use App\Models\Chat;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Database\Eloquent\Model;

class CreateMessageAction
{
    public function run(ChatCreateRequest $request, Chat $chat): ?Model
    {
        app(GetUserChatAction::class)->run($chat);
        $user = app(GetAuthenticatedUserTask::class)->run();

        try {
            return $chat->messages()->create([
                'data' => $request->data,
                'sender_id' => $user->id,
                'receiver_id' => $request->receiver_id,
            ]);
        } catch (\Exception $exception) {
            report($exception);
        }

        return null;
    }
}
