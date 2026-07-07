<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Export - {{ now()->format('Y-m-d') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Special rules that only activate when sending to a physical printer */
        @media print {
            body {
                background: white;
                color: black;
                font-size: 12pt;
            }
            /* Hide web components like action buttons */
            .no-print {
                display: none !important;
            }
            /* Force clean page breaks between the Analytics section and the Log section */
            .page-break {
                page-break-before: always;
            }
            .print-card {
                border: 1px solid #e2e8f0 !important;
                box-shadow: none !important;
                background: transparent !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 p-8 print:block">

    <div class="no-print print:block max-w-5xl mx-auto mb-6 flex justify-between items-center bg-blue-50 border border-blue-200 p-4 rounded-xl shadow-sm">
        <span class="text-sm font-medium text-blue-800">System Print Preview Mode</span>
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-1.5 rounded text-sm transition">
            Print Document / Save PDF
        </button>
    </div>

    <div class="max-w-5xl mx-auto bg-white p-8 rounded-2xl shadow-sm border border-gray-100 print-card">
        
        <header class="border-b-2 border-gray-200 pb-6 mb-8">
            <div class="print:block flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Hotel Incident Management Analytics</h1>
                    <p class="text-sm text-gray-500 mt-1">Generated on: {{ now()->toDayDateTimeString() }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs uppercase tracking-wider font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full print-card">Managerial Export</span>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="border border-gray-200 p-4 rounded-xl print-card">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Total Logged Incidents</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalCount ?? 0 }}</p>
                </div>
                <div class="border border-gray-200 p-4 rounded-xl print-card">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Active Pending</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingCount ?? 0 }}</p>
                </div>
                <div class="border border-gray-200 p-4 rounded-xl print-card">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Resolved Cases</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $resolvedCount ?? 0 }}</p>
                </div>
            </div>
        </header>

        <section class="">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Detailed Incident Log Manifest</h2>
            
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50 text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <th class="py-3 px-4">Ticket ID</th>
                        <th class="py-3 px-4">Location / Room</th>
                        <th class="py-3 px-4">Issue Description</th>
                        <th class="py-3 px-4">Current Status</th>
                        <th class="py-3 px-4">Logged Time</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    @forelse($incidents ?? [] as $incident)
                        <tr>
                            <td class="py-3 px-4 font-mono font-bold">#{{ $incident->id }}</td>
                            <td class="py-3 px-4 font-medium text-gray-900">{{ $incident->location ?? $incident->room_number }}</td>
                            <td class="py-3 px-4 text-gray-600">{{ $incident->description }}</td>
                            <td class="py-3 px-4">
                                <span class="font-semibold {{ $incident->status === 'resolved' ? 'text-green-600' : 'text-amber-600' }}">
                                    {{ ucfirst($incident->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-500">{{ $incident->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-400 italic">No incidents recorded within this tracking matrix scope.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <footer class="mt-12 pt-8 border-t border-gray-200 grid grid-cols-2 gap-8 text-xs text-gray-500">
            <div>
                <p>System Authority Verification Hash: <span class="font-mono">{{ md5(now()) }}</span></p>
            </div>
            <div class="text-right border-t border-gray-400 pt-2 w-48 justify-self-end">
                <p class="font-semibold text-gray-700">Manager Signature Approval</p>
            </div>
        </footer>

    </div>

</body>
</html>