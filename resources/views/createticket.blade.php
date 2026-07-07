@extends('layouts.app')
@section('extra_head_styles')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Incident Ticket</title>
@endsection
@section('content')
    <div class="flex justify-center items-center">
        <div class="bg-white w-full max-w-md shadow-lg rounded-xl overflow-hidden border border-gray-200">
            <div class="bg-blue-600 p-4 text-white">
                <h2 class="text-xl font-bold">Log New Incident</h2>
                <p class="text-sm text-blue-100 mb-4">Report an issue for resolution</p>
                <a href="{{ route('tickets.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-4 py-2 rounded-lg border border-blue-200 transition">← Back to Dashboard</a>
            </div>
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-xs font-semibold mb-4 space-y-1">
                    <p class="font-bold text-sm mb-1">Ticket Failure: Form contains invalid details</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('tickets.store') }}" enctype="multipart/form-data" method="POST" class="p-6 space-y-4">
                @csrf

                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 p-3 rounded-lg text-xs font-semibold mb-2">
                        ✓ {{ session('success') }}
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Room / Location</label>
                    <input name="location" type="text" placeholder="e.g. Room 402 or Lobby" class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Assign To Department</label>
                    <select name="department" class="w-full border border-gray-300 rounded-lg p-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Front Office</option>
                        <option>Housekeeping</option>
                        <option>Guest Services</option>
                        <option>Engineering (Plumbing, AC)</option>
                        <option>IT (Wi-Fi, TV)</option>
                        <option>Management</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Issue Description</label>
                    <textarea name="description" rows="3" placeholder="Describe the problem briefly..." class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Attach Evidence (Photo)</label>
                    <div class="flex items-center justify-center w-full">
                        <label id="upload-box" class="flex flex-col items-center justify-center w-full h-24 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all duration-200">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                <p id="upload-text" class="text-sm text-gray-500 font-semibold">Tap to upload photo</p>
                            </div>
                            <input type="file" id="ticket_image" name="ticket_image" class="hidden" accept="image/*" />
                        </label>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        
                    <button type="button" onclick="toggleCompensationMenu()" class="w-full bg-gray-50 p-4 flex justify-between items-center hover:bg-gray-100 transition group">
                        <div class="text-left">
                            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                                Compensation Details
                                <span class="text-[10px] bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full border border-amber-200 uppercase font-bold tracking-normal normal-case">Optional</span>
                            </h3>
                            <p class="text-xs text-gray-400 mt-0.5">Attach compensation logs or customer recovery offerings to this incident.</p>
                        </div>
                        <svg id="compensation-arrow" class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div id="compensation-menu" class="hidden border-t border-gray-100 p-6 bg-white space-y-4 transition-all duration-300">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Compensation Type</label>
                                <select name="compensationtype" class="w-full border border-gray-300 rounded-lg p-2 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500 text-gray-800">
                                    <option value="">Select</option>
                                    <option value="room_upgrade">Complimentary Room Upgrade</option>
                                    <option value="meal_voucher">Meal Voucher</option>
                                    <option value="rate_discount">Room Rate Discount</option>
                                    <option value="points">Loyalty Points Credit</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Value</label>
                                <input type="number" step="0.01" name="compensationvalue" placeholder="0.00" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 text-gray-800">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Authorization Note / Reference Context</label>
                            <textarea name="compensationdetails" rows="3" placeholder="Provide manager approval details or voucher receipt numbers..." class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 text-gray-800"></textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg p-3 rounded-lg shadow-md transition duration-200 mt-2">
                    Submit Ticket
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleCompensationMenu() {
            const menu = document.getElementById('compensation-menu');
            const arrow = document.getElementById('compensation-arrow');

            // do the toggle visibility
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                arrow.classList.add('rotate-180'); // rotates arrow up when open
            } else {
                menu.classList.add('hidden');
                arrow.classList.remove('rotate-180'); // rotates arrow down when closed
            }
        }
        document.getElementById('ticket_image').addEventListener('change', function() {
            const uploadBox = document.getElementById('upload-box');
            const uploadText = document.getElementById('upload-text');
            
            // Check if the user actually picked a file
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
    </script>
@endpush