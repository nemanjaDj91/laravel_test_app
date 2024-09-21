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
        $timezones = ['CET', 'CST', 'GMT+1'];
        User::factory()->count(20)->create()->each(function ($user) use ($timezones) {
            $user->time_zone = $timezones[array_rand($timezones)];
            $user->save();
        });
    }
}
