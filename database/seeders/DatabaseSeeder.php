<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UsersSeeder::class,
            WorldsSeeder::class,
            TopicsSeeder::class,
            IslandSeeder::class
        ]);

        \App\Models\User::factory(20)->create();
        \App\Models\Item::factory(40)->create();
        \App\Models\Ship::factory(40)->create();
    }
}
