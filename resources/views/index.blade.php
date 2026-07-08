@extends('layouts.app')
@section('extra_head_styles')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Incident Dashboard</title>
@endsection
@section('content')
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                @auth
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">Active Incident Tickets Matrix</h2>
                    <h3 class="text-2xl font-semibold leading-7 text-gray-900 sm:text-3xl sm:truncate">Welcome, {{ ucfirst(auth()->user()->role) }} {{auth()->user()->name}}</h3>
                    <p class="text-sm text-gray-500 mt-1">Real-time status tracking of hotel work orders and room maintenance.</p>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-500 hover:text-blue-600 transition">Sign In/Register</a>
                @endauth
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl text-sm font-medium mb-6 shadow-sm flex items-center gap-2">
                <span>✓</span> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-2 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ticket ID</th>
                            <th class="px-2 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-2 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Department</th>
                            <th class="px-2 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Issue Description</th>
                            <th class="px-2 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-2 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Logged At</th>
                            <th class="px-2 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Username</th>
                            <th class="px-2 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Telephone Number</th>
                            <th class="px-2 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 text-sm text-gray-700">
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-2 py-4 whitespace-nowrap font-mono text-xs font-bold text-gray-400">
                                    #{{ $ticket->ticketid }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap font-semibold text-gray-900">
                                    {{ $ticket->location }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium {{ $ticket->department == 'Engineering' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-purple-50 text-purple-800 border border-purple-200' }}">
                                        {{ $ticket->department }}
                                    </span>
                                </td>
                                <td class="px-2 py-4 max-w-md truncate text-gray-600">
                                    {{ str($ticket->description)->limit(20) }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-black-800 animate-pulse">
                                        ● {{ $ticket->status }}
                                    </span>
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap text-xs text-gray-400">
                                    {{ date('d M Y, h:i A', strtotime($ticket->created_at)) }}
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold text-black-800">
                                        {{ $ticket->user->name ?? 'Unknown Username'}}
                                    </span>
                                </td>
                                 <td class="px-2 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold text-black-800">
                                        {{ $ticket->user->phonenumber ?? 'Unknown Phone Number'}}
                                    </span>
                                </td>
                                <td class="px-2 py-4 whitespace-nowrap">
                                    <div class="flex gap-2">
                                        <a href="{{ route('tickets.manage', $ticket->ticketid) }}" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Manage Status</a>

                                        @if(in_array(auth()->user()->role, ['manager','admin']) || auth()->user()->id === $ticket->user_id)
                                            <form action="{{ route('tickets.destroy', $ticket->ticketid) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket? This action cannot be undone.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs px-3 py-2 rounded transition shadow-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-sm font-medium text-gray-400 bg-gray-50">
                                    No active incident tickets found. All rooms are operational!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection