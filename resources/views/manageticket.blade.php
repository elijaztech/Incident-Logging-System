@extends('layouts.app')
@section('extra_head_styles')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ticket Details</title>
@endsection
@section('content')

    <header class="bg-white border-b border-gray-200 p-4 sticky top-0 z-10 shadow-sm">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Incident Registry</span>
                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-1 rounded-full border border-blue-200">
                        {{ $ticket->status }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mt-1">Ticket #{{ $ticket->ticketid }}: {{ $ticket->department }} Issue</h1>
            </div>
            <a href="{{ route('tickets.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-4 py-2 rounded-lg border border-blue-200 transition">← Back to Dashboard</a>
        </div>
    </header>

    @if($errors->any())
        <div class="max-w-6xl mx-auto mt-4 px-4">
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm font-semibold">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    <main class="max-w-6xl mx-auto p-4 sm:p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <section class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 grid grid-cols-2 sm:grid-cols-3 gap-4">
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase">Target Location</h4>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ $ticket->location ?? 'Unknown'}}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase">Assigned Unit</h4>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ $ticket->department ?? 'Unknown'}}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase">Reporter Account</h4>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ $ticket->user->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase">Reporter Phone Number</h4>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ $ticket->user->phonenumber ?? 'Unknown' }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase">Ticket ID</h4>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ $ticket->ticketid ?? 'Unknown'}}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-400 uppercase">Logged At</h4>
                    <p class="text-sm font-bold text-gray-800 mt-1">{{ date('d M Y, h:i A', strtotime($ticket->created_at)) }}</p>
                </div>
                <p class="text-sm text-gray-700">
                    <strong>Time to Resolution:</strong><br>
                    @if($ticket->ttr && $ticket->ttr !== '00:00:00')
                        {{ \Carbon\CarbonInterval::createFromFormat('H:i:s', $ticket->ttr)->forHumans(['join' => true]) }}
                    @else
                        <span class="text-gray-400 italic">Not resolved yet</span>
                    @endif
                </p>
            </div>
            @if((($ticket->status === 'unreceived') && ((auth()->user()->id == $ticket->user_id)||(auth()->user()->role === 'admin'))) || (($ticket->status !== 'unreceived') && ($ticket->status !== 'resolved') && ($ticket->status !== 'denied') && (in_array(auth()->user()->role, ['manager','admin', 'rectifier']))))
                <form action="{{ route('tickets.update', $ticket->ticketid) }}" enctype="multipart/form-data" method="POST" class="space-y-6">
                    @csrf
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
                        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Modify Log Context</h3>
                        <div>
                            @if(!empty($ticket->image_path))
                                <div class="mt-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                                        📸 Attached Visual Evidence
                                    </h4>
                                    <div class="overflow-hidden rounded-lg border border-gray-300 bg-white shadow-sm max-w-md">
                                        <a href="{{ asset('storage/' . $ticket->image_path) }}" target="_blank" title="Click to view full image">
                                            <img src="{{ asset('storage/' . $ticket->image_path) }}" 
                                                alt="Incident Evidence Photo" 
                                                class="w-full h-auto object-cover hover:opacity-90 transition cursor-zoom-in">
                                        </a>
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-2 italic">
                                        * Click the photo preview to review or download the high-resolution file.
                                    </p>
                                </div>
                            @else
                                <div class="mt-6 bg-gray-50 p-4 rounded-xl border border-gray-200 border-dashed text-center">
                                    <p class="text-xs text-gray-400 font-medium">
                                        No image attachment was submitted alongside this incident profile.
                                    </p>
                                </div>
                            @endif
                            <label id="upload-box" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-200">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                    <p id="upload-text" class="text-sm text-gray-500 font-semibold">Tap to upload photo</p>
                                </div>
                                <input type="file" id="ticket_image" name="ticket_image" class="hidden" accept="image/*" />
                            </label>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Room / Location</label>
                            <input type="text" name="location" value="{{ old('location', $ticket->location) }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Department</label>
                            <select name="department" class="w-full border border-gray-300 rounded-lg p-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Engineering" {{ $ticket->department == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                <option value="IT" {{ $ticket->department == 'IT' ? 'selected' : '' }}>IT</option>
                                <option value="Housekeeping" {{ $ticket->department == 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1">Issue Description</label>
                            <textarea name="description" rows="4" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">{{ old('description', $ticket->description) }}</textarea>
                        </div>
                        
                        @if($ticket->compensationtype !== null)
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Compensation Type</label>
                                <select name="compensationtype" class="w-full border border-gray-300 rounded-lg p-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500 text-gray-800">
                                    <option  value="{{ old('compensationtype', $ticket->compensationtype) }}">Current:{{ old('compensationtype', $ticket->compensationtype) }}</option>
                                    <option value="room_upgrade">Complimentary Room Upgrade</option>
                                    <option value="meal_voucher">Meal Voucher</option>
                                    <option value="rate_discount">Room Rate Discount</option>
                                    <option value="points">Loyalty Points Credit</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Compensation Value</label>
                                <input type="compensationvalue" name="compensationvalue" value="{{ old('compensationvalue', $ticket->compensationvalue) }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 mb-1">Compensation Details</label>
                                <input type="compensationdetails" name="compensationdetails" value="{{ old('compensationdetails', $ticket->compensationdetails) }}" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                @if(in_array(auth()->user()->role, ['manager','admin']))
                                    <label class="block text-xs font-bold text-gray-500 mb-1">Compensation Approval</label>
                                    <select name="compensationapproval" class="w-full border border-gray-300 rounded-lg p-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500 text-gray-800">
                                        <option value="{{ old('compensationapproval', $ticket->compensationapproval) }}">Current:{{ old('compensationapproval', $ticket->compensationapproval) }}</option>
                                        <option value="true">Yes</option>
                                        <option value="false">No</option>
                                    </select>
                                @endif
                            </div>
                            
                        @endif
                        @if(in_array(auth()->user()->role, ['manager','admin']))
                            <a href="{{ route('manager.printticket', $ticket->ticketid) }}" target="_blank" class="mt-4 inline-block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm p-3 rounded-lg transition shadow-md">
                                Print Ticket Details
                            </a>
                        @endif
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm p-3 rounded-lg transition shadow-md">
                        Save Modification Changes
                        </button>
                    </div>
                </form>
            @else    
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 space-y-4">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Log Context (Read-Only Mode)</h3>
                    @if(!empty($ticket->image_path))
                        <div class="mt-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
                                Attached Visual Evidence
                            </h4>
                            <div class="overflow-hidden rounded-lg border border-gray-300 bg-white shadow-sm max-w-md">
                                <a href="{{ asset('storage/' . $ticket->image_path) }}" target="_blank" title="Click to view full image">
                                    <img src="{{ asset('storage/' . $ticket->image_path) }}" 
                                        alt="Incident Evidence Photo" 
                                        class="w-full h-auto object-cover hover:opacity-90 transition cursor-zoom-in">
                                </a>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-2 italic">
                                * Click the photo preview to review or download the high-resolution file.
                            </p>
                        </div>
                    @else
                        <div class="mt-6 bg-gray-50 p-4 rounded-xl border border-gray-200 border-dashed text-center">
                            <p class="text-xs text-gray-400 font-medium">
                                No image attachment was submitted alongside this incident profile.
                            </p>
                        </div>
                    @endif
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-600 space-y-2">
                        <p><strong>Compensation Type:</strong> {{ $ticket->compensationtype }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-600 space-y-2">
                        <p><strong>Compensation Value:</strong> {{ $ticket->compensationvalue }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-600 space-y-2">
                        <p><strong>Compensation Details:</strong> {{ $ticket->compensationdetails }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-600 space-y-2">
                        <p><strong>Approval?:</strong> {{ ((bool) $ticket->compensationapproval ? 'True' : 'False') }}</p>
                    </div>
                    @if(($ticket->rating !== null) || $ticket->ratingdetails !== null)
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-600 space-y-2">
                            <p><strong>Rating:</strong> {{ $ticket->rating }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-600 space-y-2">
                            <p><strong>Rating Details:</strong> {{ ((bool) $ticket->ratingdetails ? 'True' : 'False') }}</p>
                        </div>
                    @endif
                    @if(in_array(auth()->user()->role, ['manager','admin']))
                        <a href="{{ route('manager.printticket', $ticket->ticketid) }}" target="_blank" class="mt-4 inline-block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm p-3 rounded-lg transition shadow-md">
                            Print Ticket Details
                        </a>
                    @endif
                </div>
            @endif
            
            @if((auth()->user()->id == $ticket->user_id) && ($ticket->status == 'resolved'))
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-600 space-y-2">
                    <form action="{{ route('tickets.updaterating', $ticket->ticketid) }}" method="POST" class="space-y-4">
                        @csrf
                        <h2 class="font-semibold">Share Your Feedback</h2>
                        <div>
                            <label for="rating">Rating (Out of 10):</label>
                            <input type="range" min="0" max="10" value="5" id="rating" name="rating" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label for="ratingdetails">Feedback Details</label>
                            <textarea id="ratingdetails" name="ratingdetails" rows="4" rows="4" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm p-3 rounded-lg transition shadow-md">
                            Submit Feedback
                        </button>
                    </form>
                </div>
            @endif
        </section>
        @if (in_array(auth()->user()->role ?? '', ['manager','admin']))
            <aside class="space-y-6">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 sticky top-24">
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Status Control Terminal</h3>
                    @if($ticket->status === 'unreceived')
                        <p class="text-xs text-gray-400 italic mb-4">Awaiting manager validation before workflow routing becomes available.<br>You are unable to edit logs once they have been received.</p>
                    @endif
                    
                        <form action="{{ route('tickets.updateStatus', $ticket->ticketid) }}" method="POST" class="space-y-4">
                            @csrf
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Modify Ticket Status State</label>
                                        <select name="status" class="w-full border border-gray-300 rounded-lg p-3 bg-white text-sm font-semibold focus:ring-2 focus:ring-blue-500 text-gray-800 outline-none">
                                            <option value="{{ old('status', $ticket->status) }}">Current:{{ old('status', $ticket->status) }}</option>
                                            <option value="unreceived" {{ $ticket->status === 'Unreceived' ? 'selected' : '' }}>Unreceived</option>
                                            <option value="received" {{ $ticket->status === 'Received' ? 'selected' : '' }}>Received (Approved & Dispatched)</option>
                                            <option value="denied" {{ $ticket->status === 'Denied' ? 'selected' : '' }}>Not Received (Denied)</option>
                                            <option value="inprogress" {{ $ticket->status === 'In Progress' ? 'selected' : '' }}>In Progress (Under Inspection)</option>
                                            <option value="resolved" {{ $ticket->status === 'Resolved' ? 'selected' : '' }}>Resolved (Fixed & Cleaned)</option>
                                            <option value="onhold" {{ $ticket->status === 'On Hold' ? 'selected' : '' }}>On Hold</option>
                                    </select>
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm p-3 rounded-lg transition shadow-md">
                                        Commit Status State Change
                                    </button>
                                </div>
                        </form>
                        
                </div>
            </aside>
        @endif
    </main>
@endsection

@push('scripts')
    <script>
        document.getElementById('ticket_image').addEventListener('change', function() {
            const uploadBox = document.getElementById('upload-box');
            const uploadText = document.getElementById('upload-text');
            
            //check if the user actually picked a file
            if (this.files && this.files.length > 0) {
                const fileName = this.files[0].name;

                // gray to green
                uploadBox.classList.remove('border-gray-300', 'border-dashed', 'bg-gray-50', 'hover:bg-gray-100');
                uploadBox.classList.add('border-green-500', 'border-solid', 'bg-green-50', 'hover:bg-green-100');

                //gray to green and filename
                uploadText.classList.remove('text-gray-500');
                uploadText.classList.add('text-green-700');
                uploadText.innerHTML = `
                    <span class="block text-base mb-0.5">Photo Attached!</span>
                    <span class="block text-xs font-mono font-normal opacity-80">${fileName}</span>
                `;
            } else {
                uploadBox.classList.remove('border-green-500', 'border-solid', 'bg-green-50', 'hover:bg-green-100');
                uploadBox.classList.add('border-gray-300', 'border-dashed', 'bg-gray-50', 'hover:bg-gray-100');
                
                uploadText.classList.remove('text-green-700');
                uploadText.classList.add('text-gray-500');
                uploadText.innerText = "Tap to upload photo";
            }
        });
@endpush