<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup app for local';

    public function handle(): void
    {
        // copy .env.example to .env
        if (! file_exists('.env')) {
            copy('.env.example', '.env');
        }

        // do not run in production
        if (! app()->environment('local', 'dev')) {
            $this->error('You can only run this command locally or in dev.');

            return;
        }

        $this->call('key:generate');
        $this->call('jwt:secret');
        $this->call('config:cache');

        $this->call('migrate:fresh', [
            '--seed' => true,
        ]);
    }
}
