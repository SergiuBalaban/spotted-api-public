<?php

use App\Models\Pet;

//Event: getMissingPets
Broadcast::channel('city.{city}.missingPets', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//Event: reportCreated
//Event: reportDeleted
Broadcast::channel('{category}.{city}.report', function ($user) {
    return $user->pets()->where('status', Pet::STATUS_MISSING)->exists();
});
