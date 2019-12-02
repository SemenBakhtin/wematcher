<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class VideoCallEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $from;
    public $to;
    public $end_url;
    public $accept_url;
    public $reject_url;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
        $this->end_url = route('videochat.dating.end', ['to' => $from->id]);
        $this->accept_url = route('videochat.dating.accept', ['to' => $from->id]);
        $this->reject_url = route('videochat.dating.reject', ['to' => $from->id]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('Dating.'.$this->to);
    }

    public function broadcastAs()
    {
        return 'Call';
    }
}
