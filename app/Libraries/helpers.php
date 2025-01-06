<?php

use App\Models\Pet;

function getPhoneNumber(): string
{
    $phoneRandom = rand(50000000, 59999999);

    return '+407'.$phoneRandom;
}

function generateSmsCode(): string
{
    $result = '';
    for ($i = 0; $i < 6; $i++) {
        $result .= mt_rand(0, 9);
    }

    return $result;
}

function parseText(string $textToFind, string $textToReplace, string $message): string
{
    return str_replace($textToFind, $textToReplace, $message);
}

function getPetCategory(): string
{
    return Pet::CATEGORIES[array_rand(Pet::CATEGORIES)];
}

function getPetSex(): string
{
    return Pet::SEX[array_rand(Pet::CATEGORIES)];
}

/**
 * @param  array<string, string>  $fields
 * @param  array<string, string>  $requestData
 * @return array<string, string>
 */
function getFilledDataFromRequest(array $fields, array $requestData): array
{
    $data = [];
    foreach ($fields as $key) {
        if (isset($requestData[$key])) {
            $data[$key] = $requestData[$key];
        }
    }

    return $data;
}
