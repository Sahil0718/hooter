<?php

namespace App\Events;

use App\Models\Hoot;
use App\Services\PusherBeamsService;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class HootCreated implements ShouldBroadcast
{
    use InteractsWithSockets;

    public Hoot $hoot;

    /**
     * Create a new event instance.
     */
    public function __construct(Hoot $hoot)
    {
        // Ensure user relation is loaded for broadcasting
        $this->hoot = $hoot->load('user');

        // Send push notification
        $beamsService = new PusherBeamsService();
        $beamsService->notifyNewHoot($this->hoot);
    }

    /**
     * Get the channels the event should broadcast on.
     * Using a public channel for feed updates.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('hoots');
    }

    /**
     * Customize the broadcast payload.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'hoot' => [
                'id' => $this->hoot->id,
                'message' => $this->hoot->message,
                'user' => $this->hoot->user ? [
                    'id' => $this->hoot->user->id,
                    'name' => $this->hoot->user->name ?? 'Anonymous',
                ] : null,
                'created_at' => $this->hoot->created_at ? $this->hoot->created_at->toDateTimeString() : now()->toDateTimeString(),
            ],
        ];
    }
}
