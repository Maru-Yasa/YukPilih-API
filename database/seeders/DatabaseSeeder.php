<?php

namespace Database\Seeders;

use App\Models\Devision;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $hr = Devision::create([
        //     "name" => 'HR'
        // ]);

        User::create([
            'username' => 'hr_1',
            'password' => Hash::make('hr_1'),
            'role' => 'admin',
            'devision_id' => 1
        ]);
    }
}
