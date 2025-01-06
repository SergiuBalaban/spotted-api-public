<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Report $report): Response
    {
        if ($user->id !== $report->user_id) {
            return Response::denyWithStatus(401);
        }

        return Response::allow();
    }
}
