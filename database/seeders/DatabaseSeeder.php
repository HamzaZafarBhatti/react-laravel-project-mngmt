<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Hamza',
            'email' => 'hamza@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now()
        ]);
        Project::factory()->count(30)->hasTasks(30)->create();
        // User::factory(10)->create();
    }
}
