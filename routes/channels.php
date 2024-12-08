<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

//Event: markedAs
Broadcast::channel('pet', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//Event: missing_pet
Broadcast::channel('pet.{pet_id}.missing', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//Event: sendMessage
Broadcast::channel('chat.reportedPet.{reported_pet_id}.missingPet.{missing_pet_id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

//Event: sendMessage
Broadcast::channel('chat.reportedPet.{reported_pet_id}.missingPet.{missing_pet_id}.typing', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
