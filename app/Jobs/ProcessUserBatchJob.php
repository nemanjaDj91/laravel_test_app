<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;


class ProcessUserBatchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;

    public function __construct($users)
    {
        $this->users = $users;
    }

    public function handle()
    {
        $cacheKey = 'api_calls_count';
        $currentCallCount = Cache::get($cacheKey, 0);

        // check if the limit has been reached skip doing logic
        if ($currentCallCount >= 50) {
            return;
        }

        // increment the API call count and set a 1-hour expiration
        Cache::put($cacheKey, $currentCallCount + 1, now()->addHour());
        $client = new Client();

        // prepare payload
        $payload = [
            'batches' => [
                'subscribers' => $this->users->map(function ($user) {
                    return [
                        'email' => $user->email,
                        'name' => $user->name,
                        'time_zone' => $user->time_zone,
                    ];
                })->toArray(),
            ]
        ];

        try {
            $response = $client->post('example-third-party-api', [
                'json' => $payload,
                'headers' => [
                    'Authorization' => 'Bearer ' . env('THIRD_PARTY_API_KEY'),
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                foreach ($this->users as $user) {
                    $user->attributes_changed = false;
                    $user->save();
                }
            } else {
                // handle error code
            }
        } catch (\Exception $e) {
            // handle catch
        }
    }
}
