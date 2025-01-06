<?php

namespace App\Tasks\Pet;

use App\Models\Pet;
use App\Models\Report;

class GetPetReportTask
{
    public function run(Pet $pet): ?Report
    {
        return $pet->report()->withTrashed()->first();
    }
}
