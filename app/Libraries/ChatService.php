<?php

namespace App\Libraries;

use App\Models\TrackedReportedPet;
use App\Models\User;
use Illuminate\Http\Request;

class ChatService
{
    /** @var Request $request */
    private $request;
    /** @var User $user */
    private $user;
    private array $chats = [];
    private array $trackedReportedPetsId = [];

    public function __construct(Request $request) {
        $this->request = $request;
        $this->user = $request->user();
    }

    public function getUserChat()
    {
        $userMissingPet = $this->user->missingPets()->first();
        $userMissingPetId = $userMissingPet->pet_id ?? null;
        $userReportedPetsId = $this->user->reportedPets()->pluck('id')->toArray();

        $trackedReportedPets = TrackedReportedPet::whereIn('reported_pet_id',  $userReportedPetsId)
            ->where('status', TrackedReportedPet::STATUS_IDENTICALLY)
            ->get();

        $trackedMissingPets = TrackedReportedPet::where('pet_id',  $userMissingPetId)
            ->where('status', TrackedReportedPet::STATUS_IDENTICALLY)
            ->get();

        $this->getUserChats($trackedReportedPets);
        $this->getUserChats($trackedMissingPets);

        return $this->chats;
    }

    /**
     * @param $trackedReportedPets
     */
    private function getUserChats($trackedReportedPets)
    {
        foreach ($trackedReportedPets as $trackedReportedPet) {
            if(!in_array($trackedReportedPet->id, $this->trackedReportedPetsId)) {
                $this->trackedReportedPetsId[] = $trackedReportedPet->id;
                $this->chats[] = $this->getFormattedChat($trackedReportedPet, 1);
            }
        }
    }

    /**
     * @param TrackedReportedPet $trackedReportedPet
     * @param false $last
     * @return mixed
     */
    public function getFormattedChat(TrackedReportedPet $trackedReportedPet, $last=false)
    {
        $this->trackedReportedPetsId[] = $trackedReportedPet->id;
        $chat['tracked_reported_pet_id'] = $trackedReportedPet->id;
        $chat['missing_pet'] = $trackedReportedPet->pet;
        $chat['reported_missing_pet'] = $trackedReportedPet->pet->reportedMissingPet()->first();
        $chat['reported_pet'] = $trackedReportedPet->reportedPet;
        $chats = array_reverse($trackedReportedPet->pet->chatWithReportedPet($trackedReportedPet->reported_pet_id)->get()->toArray());
        if($last) {
            $lastChat = $trackedReportedPet->pet->chatWithReportedPet($trackedReportedPet->reported_pet_id)->limit(1)->first();
            $chats = isset($lastChat->id) ? [$lastChat] : [];
        }
        $chat['chat'] = $chats ?? [];
        return $chat;
    }
}
