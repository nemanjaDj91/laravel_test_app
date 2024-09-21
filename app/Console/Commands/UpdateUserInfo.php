<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpdateUserInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle():void
    {
        $timezones = ['CET', 'CST', 'GMT+1'];

        User::all()->each(function ($user) use ($timezones) {
            $user->firstname = Str::random(6);
            $user->lastname = Str::random(8);
            $user->timezone = $timezones[array_rand($timezones)];
            $user->save();
        });

        $this->info('Users updated successfully!');
    }
}
