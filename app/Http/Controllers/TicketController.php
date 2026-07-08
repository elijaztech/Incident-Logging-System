<?php

namespace App\Http\Controllers;
use App\Jobs\CheckStalledTicket;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Using DB facade for quick database inserts
use App\Models\Incident;
use App\Events\TicketStatusUpdated;
use App\Events\TicketCreatedNotification;

class TicketController extends Controller
{
    
    //form stuffs
    
    public function create()
    {
        // render resources/views/createticket.blade.php (its named like that i think)
        return view('createticket');
    }

    
    //store incident ticket in db
     
    public function store(Request $request)
    {
        // validate
        $validated = $request->validate([
            'location'            => ['required', 'string', 'max:255'],
            'department'          => ['required', 'string'],
            'description'         => ['required', 'string', 'max:1000'],
            'compensationtype'    => ['nullable', 'string', 'max:255'],
            'compensationdetails' => ['nullable', 'string'],
            'compensationvalue'   => ['nullable', 'numeric', 'max:1000'],
            'ticket_image'        => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'compensationapproval'=> ['nullable', 'boolean'],
        ]);

        // identify who is logging this ticket
        // it just goes to user1 if no login yet
        $ticket = new Incident();
        $ticket->location = $request->location;
        $ticket->description = $request->description;
        $ticket->user_id = auth()->id();
        $ticket->department = $request->department;
        //compensation stuff
        $ticket->compensationtype = $request->compensationtype;
        $ticket->compensationdetails = $request->compensationdetails;
        $ticket->compensationvalue = $request->compensationvalue;
        $ticket->compensationapproval = $request->boolean('compensationapproval');

        if ($request->hasFile('ticket_image')) {
            $path = $request->file('ticket_image')->store('tickets', 'public');
            $ticket->image_path = $path; 
        }

        $ticket->save();
        broadcast(new TicketCreatedNotification($ticket));

        CheckStalledTicket::dispatch($ticket->ticketid)->delay(now()->addMinutes(30));

        // redirect and success...hopefully
        return redirect()->route('tickets.index')->with('success', 'Incident ticket logged and dispatched successfully!');
    }

    public function index()
    {   
        $user = auth()->user();
        //fetch all incidents from the database ordered by newest first
        $query = Incident::with('user')->orderBy('created_at', 'desc');
        if (!in_array($user->role, ['manager', 'rectifier','admin'])) {
            //normal users are locked down to only their own records
            $query->where('user_id', $user->id);
        }
        //render index.blade.php
        $tickets = $query->get();
        return view('index', compact('tickets'));
    }

    public function showMAD()
    {   
        $authorizedRoles = ['manager','admin'];
        if (!in_array(auth()->user()->role, $authorizedRoles)){
            return back()->withErrors(['state' => 'Access Denied. Management Analytics can only be accessed with management access or higher.']);
        }
        $tickets = DB::table('incidents')
        ->orderBy('created_at', 'desc')
        ->get();
        
        $analytics = [
            'total' => Incident::count(),
            'unreceived' => Incident::where('status', 'unreceived')->count(),
            'received' => Incident::where('status', 'received')->count(),
            'resolved' => Incident::where('status', 'resolved')->count(),
        ];
        return view('manager/mad', compact('tickets','analytics')); 
    }
    public function manage($id)
    {
        //fetch the ticket details along with the author details or give error if not found
        $ticket = Incident::with('user')->findOrFail($id);

        return view('manageticket', compact('ticket'));
    }

    public function update(Request $request, $id)
    {
        $ticket = Incident::findOrFail($id);
        $user = auth()->user();
        $authorizedRoles = ['manager', 'rectifier', 'admin'];

        //status
        if ($request->has('status') && !$request->has('description') && !$request->has('location')) {
            
            
            if (!in_array($user->role, $authorizedRoles)) {
                return back()->withErrors(['state' => 'Access Denied. Only managers, admins, or rectifiers can modify status tracking states.']);
            }

            $validated = $request->validate([
                'status' => ['required', 'string'],
            ]);

            $ticket->update([
                'status' => $validated['status']
            ]);

            return redirect()->route('tickets.index')->with('success', 'Ticket status committed successfully.');
        } 

        //details
        
        if ($ticket->status === 'unreceived') {
            // unreceived tickets can only be modified by the creator or an admin
            if ($user->id !== $ticket->user_id && $user->role !== 'admin') {
                return back()->withErrors(['state' => 'Access Denied. You cannot edit someone else\'s unreceived logs.']);
            }
        } else {
            //active tickets can only be modified by staff roles
            if (!in_array($user->role, $authorizedRoles)) {
                return back()->withErrors(['state' => 'Access Denied. Active tickets can only be adjusted by authorized personnel.']);
            }
        }

        //validation
        $rules = [
            'location'            => ['required', 'string', 'max:255'],
            'department'          => ['required', 'string'],
            'description'         => ['required', 'string', 'max:1000'],
            'compensationtype'    => ['nullable', 'string', 'max:255'],
            'compensationdetails' => ['nullable', 'string', 'max:1000'],
            'compensationvalue'   => ['nullable', 'numeric', 'max:1000'],
            'compensationapproval'=> ['nullable', 'string', 'max:10'], // Added from your form fields
            'rating'              => ['nullable', 'numeric', 'max:10'],
            'ratingdetails'       => ['nullable', 'string', 'max:1000'],
        ];

        if ($request->hasFile('ticket_image')) {
            $rules['ticket_image'] = ['image', 'mimes:jpeg,png,jpg', 'max:2048'];
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('ticket_image')) {
            $validated['image_path'] = $request->file('ticket_image')->store('tickets', 'public');
        }

        $ticket->update($validated);

        return redirect()->route('tickets.index')->with('success', 'Ticket log details updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Incident::findOrFail($id);
        $user = auth()->user();

        //ensure only managers or rectifiers can change status if it's received
        if ($ticket->status !== 'unreceived') {
            if (!$user || !in_array($user->role, ['manager', 'rectifier','admin'])) {
                return back()->withErrors(['status' => 'Unauthorized. Only managers and rectifiers can modify processed tickets.']);
            }
        }

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:unreceived,received,denied,inprogress,resolved,onhold'],
        ]);

        if ($validated['status'] === 'resolved') {
            $startTime = $ticket->created_at;
            $endTime = now();

            // ttr based on difference betwwen start and end time
            $totalSeconds = $startTime->diffInSeconds($endTime);

            //hh::mm::ss
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds / 60) % 60);
            $seconds = $totalSeconds % 60;

            $ttrString = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $ticket->ttr = $ttrString;
        }

        $ticket->status = $validated['status'];
        $ticket->save();
        broadcast(new TicketStatusUpdated($ticket));
        
        return redirect()->route('tickets.manage',$id)->with('success', 'Ticket status successfully updated to ' . $validated['status']);
    }

    public function updaterating(Request $request, $id)
    {
        $ticket = Incident::findOrFail($id);
        $user = auth()->user() ?? \App\Models\User::find(0); //default if empty
        
        if ($user->id !== (int)$ticket->user_id) {
            return back()->withErrors(['rating' => 'Access Denied. Only the logging operator can rate this ticket.']);
        }

        if (auth()->user()->id == $ticket->user_id) {

            //validate modification updates are allowed for users
            $rules = [
                'rating' => ['nullable', 'numeric', 'max:10'],
                'ratingdetails' => ['nullable', 'string', 'max:1000'],
            ];

            $validated = $request->validate($rules);

            $ticket->update($validated);
                return redirect()->route('tickets.index')->with('success', 'Ticket rating updated successfully.');
            }

        $ticket->update([
            'rating' => $validated['rating'],
            'ratingdetails' => $validated['ratingdetails']
        ]);

        return redirect()->route('tickets.index')->with('success', 'Ticket status committed successfully.');
    }
    public function destroy($id)
    {
        $ticket = Incident::findOrFail($id);
        
        // only admin/maanger or ticket owner can delete
        if (!in_array(auth()->user()->role, ['manager','admin']) && auth()->user()->id !== $ticket->user_id) {
            return back()->withErrors(['state' => 'Unauthorized action.']);
        }

        $ticket->delete();

        return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
    }
}