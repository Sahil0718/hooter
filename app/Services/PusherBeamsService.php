<?php

namespace App\Services;

use Pusher\PushNotifications\PushNotifications;

class PusherBeamsService
{
    private PushNotifications $beamsClient;

    public function __construct()
    {
        $this->beamsClient = new PushNotifications([
            'instanceId' => env('PUSHER_BEAMS_INSTANCE_ID'),
            'secretKey' => env('PUSHER_BEAMS_SECRET_KEY'),
        ]);
    }

    public function notifyNewHoot($hoot)
    {
        $this->beamsClient->publishToInterests(['all-users'], [
            'web' => [
                'notification' => [
                    'title' => $hoot->user ? $hoot->user->name . ' just hooted!' : 'New anonymous hoot!',
                    'body' => substr($hoot->message, 0, 100) . (strlen($hoot->message) > 100 ? '...' : ''),
                    'deep_link' => url('/'),
                    'icon' => 'https://avatars.laravel.cloud/' . ($hoot->user ? urlencode($hoot->user->email) : 'anonymous'),
                ],
            ],
        ]);
    }
}