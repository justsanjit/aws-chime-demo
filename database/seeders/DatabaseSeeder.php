<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory()->create([
            'name' => 'Sanjit',
            'email' => 'iamsanjit@hotmail.com',
            'password' => Hash::make('secret')
        ]);

        \App\Models\User::factory()->create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => Hash::make('secret')
        ]);
    }
}
