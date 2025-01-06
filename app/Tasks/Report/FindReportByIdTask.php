<?php

namespace App\Tasks\Report;

use App\Exceptions\NotFoundException;
use App\Models\Report;

class FindReportByIdTask
{
    /**
     * @throws NotFoundException
     */
    public function run(int $id): Report
    {
        try {
            return Report::query()->withTrashed()->findOrFail($id);
        } catch (\Exception $exception) {
            report($exception);
            throw (new NotFoundException('Error Finding Report'));
        }
    }
}
