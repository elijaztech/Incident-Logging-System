@extends('layouts.app')
@section('extra_head_styles')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Incident Logging System - Auth Portal</title>
@endsection

@section('content')
    <div class="w-full py-12 px-4 bg-gray-100 min-h-screen">
        <div class="bg-white w-full max-w-4xl mx-auto rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
            
            
            <div class="bg-blue-700 p-8 text-white flex flex-col justify-between hidden md:flex">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Loggers (Incident Logging System)</h1>
                    <p class="text-blue-200 mt-2 text-sm">Logging In Portal</p>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start gap-3 bg-blue-800/50 p-3 rounded-lg border border-blue-600">
                        <div class="mt-1 font-bold text-green-400">✓</div>
                        <p class="text-xs text-blue-100">Enforced via Encrypted HTTPS Channels for role safety.</p>
                    </div>
                </div>
                <p class="text-xs text-blue-300">2026</p>
            </div>

            <div class="p-8 flex flex-col justify-center">
                
                <div class="flex border-b border-gray-200 mb-6">
                    <button onclick="switchTab('login')" id="loginTabBtn" class="w-1/2 pb-2 text-center text-sm font-bold text-blue-600 border-b-2 border-blue-600 focus:outline-none">Sign In</button>
                    <button onclick="switchTab('register')" id="registerTabBtn" class="w-1/2 pb-2 text-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">Register Account</button>
                </div>

                <form id="loginForm" class="space-y-4" action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                        <input type="email" name="email" placeholder="staff@hotel.com" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                        <input type="password" name="password" class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm p-3 rounded-lg transition shadow-md">
                        Authenticate
                    </button>
                </form>

                <form id="registerForm" class="space-y-4 hidden" action="{{ route('register.submit') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name</label>
                            <input name="name" type="text" placeholder="Name" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Telephone No</label>
                            <input name="phonenumber" type="tel" placeholder="601234567" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email</label>
                        <input name="email" type="email" placeholder="name@domain.com" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">System Role Tier</label>
                            <select name="role" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm bg-white outline-none focus:ring-2 focus:ring-blue-500 text-gray-700">
                                <option>user</option>
                                <option>rectifier</option>
                                <option>manager</option>
                                <option>admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                            <input name="password" type="password" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Confirm</label>
                            <input name="password_confirmation" type="password" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold text-sm p-3 rounded-lg transition shadow-md mt-2">
                        Register New Profile
                    </button>
                </form>
                @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-xs font-semibold mb-4 shadow-sm">
                    <p class="font-bold text-sm mb-1 text-red-800">Authentication Failed:</p>
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function switchTab(mode) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const loginTabBtn = document.getElementById('loginTabBtn');
            const registerTabBtn = document.getElementById('registerTabBtn');

            if(mode === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                loginTabBtn.className = "w-1/2 pb-2 text-center text-sm font-bold text-blue-600 border-b-2 border-blue-600 focus:outline-none";
                registerTabBtn.className = "w-1/2 pb-2 text-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none";
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                loginTabBtn.className = "w-1/2 pb-2 text-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none";
                registerTabBtn.className = "w-1/2 pb-2 text-center text-sm font-bold text-green-600 border-b-2 border-green-600 focus:outline-none";
            }
        }
    </script>
@endpush