<?php

namespace App\Events;

use App\Models\Ack as AppAck;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Ack
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message ;
    public $ack ;

    /**
     * Create a new event instance.
     */
    public function __construct(AppAck $ack)
    {
        $this->ack = $ack;
        $this->message = $ack->message;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {

        return [
            new Channel('channel.' . $this->ack->user_id)
        ] ;

        //return [
        //    new PrivateChannel('channel-name'),
        //];
    }

}
