<?php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// Implementing ShouldBroadcastNow sends the message instantly without waiting for a queue handler
class TicketStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $message;

    public function __construct(Incident $ticket)
    {
        $this->ticket = $ticket;
        $this->message = "Your ticket for '{$ticket->location}' has been marked as " . strtoupper($ticket->status) . ".";
    }

    //users can only listen to updates about their own tickets.
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->ticket->user_id),
        ];
    }
}