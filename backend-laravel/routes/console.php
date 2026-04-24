<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:create-admin {name} {email} {password} {--phone=}', function () {
    $user = User::query()->updateOrCreate(
        ['email' => (string) $this->argument('email')],
        [
            'name' => (string) $this->argument('name'),
            'phone' => $this->option('phone') ? (string) $this->option('phone') : null,
            'password' => (string) $this->argument('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]
    );

    $this->info("Admin ready: {$user->email}");
})->purpose('Create or update a production administrator account');
