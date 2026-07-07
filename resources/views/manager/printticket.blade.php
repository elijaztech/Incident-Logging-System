<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incident Voucher - #{{ $incident->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; color: black; font-size: 11pt; }
            .print-card { border: 1px solid #cbd5e1 !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-gray-50 p-6 print:block">

    <div class="no-print print:block max-w-5xl mx-auto mb-6 flex justify-between items-center bg-blue-50 border border-blue-200 p-4 rounded-xl shadow-sm">
        <span class="text-sm font-medium text-blue-800">Ticket Print Mode</span>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-1.5 rounded text-sm transition">
            Print / Save PDF
        </button>
    </div>

    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-200 print-card">
        
        <div class="border-b pb-4 mb-6 print:block flex justify-between items-start">
            <div>
                <h1 class="text-xl font-bold text-gray-900">HOTEL INCIDENT WORK ORDER</h1>
                <p class="text-xs text-gray-500 mt-1">Logged via IncidentLogging System</p>
            </div>
            <div class="text-right">
                <span class="text-lg font-mono font-bold block text-gray-800">#{{ $incident->id }}</span>
                <span class="text-xs uppercase tracking-wider font-semibold px-2.5 py-0.5 rounded-full {{ $incident->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $incident->status }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-lg border print-card">
            <div>
                <p class="text-xs font-semibold uppercase text-gray-400">Location / Room</p>
                <p class="text-base font-bold text-gray-900">{{ $incident->location ?? $incident->room_number }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-400">Reported Date/Time</p>
                <p class="text-sm text-gray-700">{{ $incident->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-xs font-semibold uppercase text-gray-400 mb-1">Issue Description</p>
            <div class="border p-4 rounded-lg text-sm text-gray-800 bg-white min-h-[100px] leading-relaxed">
                {{ $incident->description }}
            </div>
        </div>

        <div class="border-t pt-6 grid grid-cols-2 gap-6 text-sm">
            <div>
                <p class="text-xs font-semibold uppercase text-gray-400">Assigned Staff / Technician</p>
                <p class="text-sm font-medium text-gray-800 mt-1">
                    {{ $incident->assigned_user->name ?? '_______________________' }}
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase text-gray-400">Rectification Date Completion</p>
                <p class="text-sm font-medium text-gray-800 mt-1">
                    {{ $incident->resolved_at ? $incident->resolved_at->format('Y-m-d H:i') : '____ / ____ / 2026' }}
                </p>
            </div>
        </div>

        <div class="mt-12 pt-6 border-t border-dashed border-gray-300 grid grid-cols-2 text-xs text-gray-400">
            <div>
                <p>Security Footprint Verification:</p>
                <p class="font-mono mt-0.5">{{ md5($incident->id . $incident->created_at) }}</p>
            </div>
            <div class="text-right flex flex-col items-end justify-end">
                <div class="border-b border-gray-400 w-36 h-8"></div>
                <p class="mt-1 pr-2">Staff Sign-Off Signature</p>
            </div>
        </div>

    </div>

</body>
</html>