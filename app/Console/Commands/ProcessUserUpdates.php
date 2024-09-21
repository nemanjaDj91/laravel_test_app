<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Jobs\ProcessUserBatchJob;
use Illuminate\Console\Command;

class ProcessUserUpdates extends Command
{
    protected $signature = 'users:process-updates';
    protected $description = 'Process user attribute updates in batches and dispatch jobs';

    public function handle()
    {
        // get users with changed attributes
        $users = User::where('attributes_changed', true)->get();
        $users->chunk(1000)->each(function ($batch) {
            ProcessUserBatchJob::dispatch($batch);
        });

        $this->info('User update jobs dispatched successfully!');
    }
}

