<?php

use App\Models\ReportedPet;

/**
 * @return string
 */
function generateSmsCode()
{
    $result = '';
    for($i = 0; $i < 6; $i++) {
        $result .= mt_rand(0, 9);
    }
    return $result;
}

/**
 * @param $reportedPets
 * @param $currentLatitude
 * @param $currentLongitude
 * @return mixed
 */
function getReportedPetsByLocation($reportedPets, $currentLatitude, $currentLongitude)
{
    if (isset($currentLatitude) && isset($currentLongitude)) {
        $radius = ReportedPet::DEFAULT_RADIUS_IN_KM;
        return $reportedPets->radius($currentLatitude, $currentLongitude, $radius);
    }
    return $reportedPets;
}
