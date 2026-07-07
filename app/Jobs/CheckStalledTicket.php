<?php

namespace App\Jobs;
use App\Jobs\CheckStalledTicket;

use App\Models\Incident;
use App\Events\TicketStalledNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Queue\SerializesModels;

class CheckStalledTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, Queueable, SerializesModels;

    protected $ticketId;

    public function __construct($ticketId)
    {
        $this->ticketId = $ticketId;
    }

    public function handle(): void
    {
        //fetch database state of this ticket 30 minutes later
        $ticket = Incident::find($this->ticketId);

        //if it was deleted, or if someone already picked it up, stop immediately
        if (!$ticket || $ticket->status !== 'unreceived') {
            return;
        }

        //if it's still unreceived, warn the management channel
        broadcast(new TicketStalledNotification($ticket));
    }
}