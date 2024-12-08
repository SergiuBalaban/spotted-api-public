<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $users = [
             [
                 'email' => 'sergiu.balaban92@gmail.com',
                 'password' => Hash::make('9hbQSr7Wg8zmG6pP'),
//                 'password' => Hash::make('password'),
                 'phone' => '+40755858442',
                 'phone_prefix' => '+40',
                 'admin' => 1
             ]
         ];

        foreach ($users as $user) {
            $newUser = User::where('email', $user['email'])->firstOrNew();
            if(!isset($newUser->id)) {
                $newUser->email = $user['email'];
                $newUser->password = $user['password'];
                $newUser->phone = $user['phone'];
                $newUser->phone_prefix = $user['phone_prefix'];
                $newUser->admin = $user['admin'];
                $newUser->save();
            }
        }
    }
}
