<?php

namespace App\Http\Controllers;

use App\Events\ChatNewMessageEvent;
use App\Exceptions\UserHaveNoMissingPetsException;
use App\Http\Requests\ChatCreateRequest;
use App\Libraries\ChatService;
use App\Models\Chat;
use App\Models\ReportedPet;
use App\Models\TrackedReportedPet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $chatService = new ChatService($request);
        $chats = $chatService->getUserChat();

        return response()->json($chats, 200);
    }

    /**
     * @param Request $request
     * @param TrackedReportedPet $trackedReportedPet
     * @return JsonResponse
     */
    public function getChat(Request $request, TrackedReportedPet $trackedReportedPet)
    {
        $chatService = new ChatService($request);
        $chat = $chatService->getFormattedChat($trackedReportedPet);
        return response()->json($chat, 200);
    }

    /**
     * @param ChatCreateRequest $request
     * @param TrackedReportedPet $trackedReportedPet
     * @return JsonResponse
     * @throws UserHaveNoMissingPetsException
     */
    public function store(ChatCreateRequest $request, TrackedReportedPet $trackedReportedPet)
    {
        /** @var User $user */
        $user = $request->user();
        if($trackedReportedPet->pet->status != ReportedPet::STATUS_MISSING) {
            throw new UserHaveNoMissingPetsException();
        }

        $chat = [
            'message' => $request->message,
            'sender_user_id' => $user->id,
            'receiver_user_id' => $trackedReportedPet->reportedPet->user_id,
            'reported_pet_id' => $trackedReportedPet->reported_pet_id,
            'missing_pet_id' => $trackedReportedPet->pet_id,
        ];

        Chat::create($chat);
        event(new ChatNewMessageEvent($trackedReportedPet, $chat));
        return response()->json($chat, 200);
    }
}
