<?php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStalledNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ticket;
    public $message;

    public function __construct(Incident $ticket)
    {
        $this->ticket = $ticket;
        $this->message = "⚠️ Warning: Ticket #{$ticket->ticketid} ({$ticket->location}) has been UNRECEIVED for over 30 minutes!";
    }

    public function broadcastOn(): array
    {
        return [new Channel('management')];
    }
}