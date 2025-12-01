<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Create some default users with use of faked data
     */
    public function run(): void
    {
        \App\Models\User::factory()->count(10)->create();
    }
}
