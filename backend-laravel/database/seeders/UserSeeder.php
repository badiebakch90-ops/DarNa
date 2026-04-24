<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        User::query()->updateOrCreate(
            ['email' => 'admin@darna.test'],
            [
                'name' => 'DarNa Admin',
                'phone' => '+212600000001',
                'password' => 'password',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'member@darna.test'],
            [
                'name' => 'DarNa Member',
                'phone' => '+212600000002',
                'password' => 'password',
                'role' => 'member',
                'email_verified_at' => now(),
            ]
        );
    }
}
