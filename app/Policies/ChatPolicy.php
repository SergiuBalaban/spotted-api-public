<?php

namespace App\Policies;

use App\Exceptions\CustomMessages\ErrorMessageValue;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ChatPolicy
{
    use HandlesAuthorization;

    public function createMessage(User $user, Chat $chat): Response
    {
        if (! $chat->active) {
            return Response::denyWithStatus(401, ErrorMessageValue::ERROR_CHAT_INACTIVE_MESSAGE, 401);
        }

        return Response::allow();
    }

    public function update(User $user, Chat $chat): Response
    {
        if ($user->id !== $chat->owner_id) {
            return Response::denyWithStatus(401, ErrorMessageValue::ERROR_UNAUTHORIZED_MESSAGE, 401);
        }

        if (! $user->missingPets()->exists()) {
            return Response::denyWithStatus(401, ErrorMessageValue::ERROR_UNAUTHORIZED_MESSAGE, 401);
        }

        return Response::allow();
    }

    public function delete(User $user, Chat $chat): Response
    {
        if (! $chat->active) {
            return Response::denyWithStatus(401, ErrorMessageValue::ERROR_CHAT_INACTIVE_MESSAGE, 401);
        }

        if (! $user->missingPets()->exists()) {
            return Response::denyWithStatus(401, ErrorMessageValue::ERROR_UNAUTHORIZED_MESSAGE, 401);
        }

        return Response::allow();
    }
}
