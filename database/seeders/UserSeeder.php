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
        $user = User::updateOrCreate([
            'name' => 'Test User',
            'email' => 'test@test.com',
        ], ['password' => 'test123456']);

        $user->assignRole('admin');
    }
}
