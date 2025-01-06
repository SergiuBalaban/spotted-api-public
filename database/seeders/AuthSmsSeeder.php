<?php

namespace Database\Seeders;

use App\Models\AuthSms;
use Illuminate\Database\Seeder;

class AuthSmsSeeder extends Seeder
{
    public function run(): void
    {
        AuthSms::factory(10)->create();
    }
}
