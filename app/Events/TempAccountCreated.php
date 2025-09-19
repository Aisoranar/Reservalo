<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TempAccountCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $createdBy;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, int $createdBy)
    {
        $this->user = $user;
        $this->createdBy = $createdBy;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('temp-accounts'),
        ];
    }
}
