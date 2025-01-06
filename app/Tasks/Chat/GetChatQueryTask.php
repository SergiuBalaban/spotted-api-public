<?php

namespace App\Tasks\Chat;

use App\Models\Chat;
use App\Tasks\User\GetAuthenticatedUserTask;
use Illuminate\Database\Eloquent\Builder;

class GetChatQueryTask
{
    /**
     * @return Builder<Chat>
     */
    public function run(): Builder
    {
        $user = app(GetAuthenticatedUserTask::class)->run();

        return Chat::query()
            ->select([
                'id',
                'owner_id',
                'reporter_id',
                'report_found_id',
                'report_missing_id',
            ])
            ->where(function ($query) use ($user) {
                $query
                    ->where('owner_id', $user->id)
                    ->orWhere('reporter_id', $user->id);
            })
            ->orderByDesc('id')
            ->with('owner:id,name,avatar')
            ->with('reporter:id,name,avatar')
            ->with('reportFound:id,avatar')
            ->with('reportMissing:id,avatar')
            ->with('lastMessage:id,chat_id,data,updated_at');
    }
}
