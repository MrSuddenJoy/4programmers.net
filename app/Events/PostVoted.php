<?php

namespace Coyote\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;


class PostVoted extends BroadcastEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public $payload, private int $topicId)
    {
    }

    /**
     * @return Channel|Channel[]
     */
    public function broadcastOn()
    {
        return new Channel('topic:' . $this->topicId);
    }

    /**
     * @return array
     */
    public function broadcastWith()
    {
        return $this->payload;
    }
}
