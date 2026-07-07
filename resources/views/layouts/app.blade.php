<!DOCTYPE html>
<html lang="en">
<head>
    @yield('extra_head_styles')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Hotel Incident System') }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                
                @auth
                <div class="flex items-center gap-4">
                    <span class="font-bold text-lg text-blue-600 tracking-wide">Loggers (Incident Logging System)</span>
                    @if(in_array(auth()->user()->role, ['manager','admin']))
                        <a href="{{ route('mad') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">Management Dashboard</a>
                    @endif
                    <a href="{{ route('tickets.index') }}" class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">Tickets</a>
                    <a href="{{ route('tickets.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition duration-150">
                        + Log New Ticket
                    </a>
                </div>
                
                <div class="flex items-center text-sm text-gray-600">
                    <span class="font-medium bg-gray-50 border border-gray-100 rounded-full px-3 py-1 text-xs text-gray-700 shadow-inner">
                        👤 {{ auth()->user()->name ?? 'Operator' }} 
                        <span class="text-blue-600 font-semibold">({{ ucfirst(auth()->user()->role ?? 'Staff') }})</span>
                    </span>
                    
                    <a href="{{ route('account.edit') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md hover:bg-gray-100 transition duration-150">
                        Manage Account
                    </a>
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md hover:bg-gray-100 transition duration-150">
                        Sign In / Register
                    </a>
                <div>
                @else
                <div class="flex items-center text-sm text-gray-600>
                    <a href="{{ route('login') }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition duration-150">
                        Sign In / Register
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="max-w-4xl mx-auto mt-4 px-4">
            <div class="bg-green-50 border border-green-200 text-green-800 p-4 rounded-xl text-xs font-semibold shadow-sm">
                ✅ {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @yield('content') 
    </main>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            @auth
                const currentUserId = {{ auth()->user()->id }};
                const userRole = "{{ auth()->user()->role }}";
                const protectedRoles = ['manager', 'admin', 'rectifier', 'staff'];

                if (protectedRoles.includes(userRole)) {
                    
                    Echo.channel('management')
                        .listen('TicketCreatedNotification', (event) => {
                            showSystemNotification("New Incident Alert", event.message);
                        })
                        .listen('TicketStalledNotification', (event) => {
                            showSystemNotification("Escalation Warning", event.message);
                        });

                    //user has private channel for their ticket status updates
                    Echo.private(`user.${currentUserId}`)
                        .listen('TicketStatusUpdated', (event) => {
                            showSystemNotification(
                                "Your Ticket Status Changed", 
                                `Ticket #${event.incident.id} has been moved to ${event.incident.status}.`
                            );
                        });
                }
            @endauth
        });

        function showSystemNotification(title, message) {
            if (Notification.permission === "granted") {
                new Notification(title, { body: message });
            } else {
                alert(`${title}\n\n${message}`);
            }
        }
    </script>
</body>
</html>