<?php

namespace App\Http\Middleware\Report;

use App\Exceptions\CustomMessages\MaxReportAttemptsException;
use App\Models\Report;
use App\Tasks\User\GetAuthenticatedUserTask;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportLimitMiddleware
{
    /**
     * @throws MaxReportAttemptsException
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        $user = app(GetAuthenticatedUserTask::class)->run();
        $lastDay = now()->subDay();
        $userReports = $user->reportedPets()
            ->orderByDesc('id')
            ->where('created_at', '>', $lastDay)
            ->limit(Report::DEFAULT_USER_REPORTED_PETS)
            ->count();

        if ($userReports >= Report::DEFAULT_USER_REPORTED_PETS) {
            throw new MaxReportAttemptsException;
        }

        return $next($request);
    }
}
