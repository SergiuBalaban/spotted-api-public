<?php

namespace App\Http\Controllers;

use App\Actions\Chat\ActivateUserChatAction;
use App\Actions\Chat\CreateMessageAction;
use App\Actions\Chat\DeleteUserChatAction;
use App\Actions\Chat\GetUserChatAction;
use App\Http\Requests\ChatCreateRequest;
use App\Models\Chat;
use App\Tasks\Chat\GetUserChatsTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChatController extends Controller
{
    public function getUserChats(Request $request): JsonResponse
    {
        $chats = app(GetUserChatsTask::class)->run();

        return response()->json($chats);
    }

    public function getUserChat(Request $request, Chat $chat): JsonResponse
    {
        $chat = app(GetUserChatAction::class)->run($chat);

        return response()->json($chat);
    }

    public function createChatMessage(ChatCreateRequest $request, Chat $chat): JsonResponse
    {
        $chat = app(CreateMessageAction::class)->run($request, $chat);

        return response()->json($chat);
    }

    public function activateUserChat(Request $request, Chat $chat): Response
    {
        app(ActivateUserChatAction::class)->run($chat);

        return response()->noContent();
    }

    public function deleteUserChat(Request $request, Chat $chat): Response
    {
        app(DeleteUserChatAction::class)->run($chat);

        return response()->noContent();
    }
}
