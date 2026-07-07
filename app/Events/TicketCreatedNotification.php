<?php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCreatedNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $message;

    public function __construct(Incident $ticket)
    {
        $this->ticket = $ticket;
        $this->message = "New Ticket Filed: A new incident has been reported at {$ticket->location} for the {$ticket->department} department.";
    }

    public function broadcastOn(): array
    {
        // public channel named 'management' so any logged-in manager/rectifier can grab it
        return [new Channel('management')];
    }
}