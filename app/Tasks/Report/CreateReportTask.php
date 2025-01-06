<?php

namespace App\Tasks\Report;

use App\Exceptions\CreateResourceFailedException;
use App\Models\Report;
use App\Tasks\User\GetAuthenticatedUserTask;
use App\Traits\CoordinationTrait;

class CreateReportTask
{
    use CoordinationTrait;

    /**
     * @param  array<string, string>  $data
     *
     * @throws CreateResourceFailedException
     */
    public function run(array $data = []): Report
    {
        try {
            $user = app(GetAuthenticatedUserTask::class)->run();
            /** @var Report $newReport */
            $newReport = $user->reportedPets()->create($data);
        } catch (\Exception $exception) {
            report($exception);
            throw (new CreateResourceFailedException('Error Creating Reported Pet'));
        }

        return $newReport;
    }
}
