@extends('layouts.app')
@section('extra_head_styles')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Account Security</title>
@endsection
@section('content')

    <header class="bg-white border-b border-gray-200 p-4 sticky top-0 z-10 shadow-sm">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-gray-400">User Control Center</span>
                <h1 class="text-2xl font-bold text-gray-800 mt-1">Account Profile Settings</h1>
            </div>
            <a href="{{ route('tickets.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-4 py-2 rounded-lg border border-blue-200 transition">
                ← Dashboard
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto p-4 sm:p-6 space-y-6">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl text-sm font-semibold">
                ✓ {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl text-sm font-semibold">
                Error: {{ $errors->first() }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Personal Identity Context</h3>
                <p class="text-xs text-gray-400 mt-1">Update your system credentials.</p>
            </div>

            <form action="{{ route('account.update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Full User Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 text-gray-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Communication Target</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 text-gray-800">
                    </div>
                </div>

                <hr class="border-gray-100">

                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Authentication Security Modifiers</h4>
                    <p class="text-xs text-amber-600 font-medium italic">Leave these fields blank if you do not intend to modify your current password</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">New Security Password</label>
                            <input type="password" name="password" placeholder="••••••••" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 text-gray-800">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Confirm Security Password</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-500 text-gray-800">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-6 py-3 rounded-lg transition shadow-md">
                        Commit Profile Settings Update
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection