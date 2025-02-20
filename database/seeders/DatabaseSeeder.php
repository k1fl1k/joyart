<?php

namespace Database\Seeders;

use k1fl1k\joyart\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(5)->create();
        User::factory(5)->male()->create();
        User::factory(2)->admin()->create();
        User::factory(2)->admin()->male()->create();
    }
}
