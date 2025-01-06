<?php

namespace App\Tasks\User;

use App\Models\User;

class GetProfileResponseTask
{
    /**
     * @return array<string, array<string, int|string|null>|int|string|null>
     */
    public function run(User $user): array
    {
        $response = $user->only(User::POSSIBLE_UPDATED_FIELDS);
        $response['id'] = $user->id;

        return $response;
    }
}
