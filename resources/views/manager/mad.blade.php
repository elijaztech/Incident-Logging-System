@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Manager Analytics Dashboard</h1>
    <div class="bg-gray-100 p-6 rounded-lg shadow-sm border border-gray-400">
        <h3 class="text-gray-800 font-semibold uppercase text-sm">Total Incidents</h3>
        <p class="text-3xl font-bold text-gray-900">{{ $analytics['total'] }}</p>
    </div><br>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-red-100 p-6 rounded-lg shadow-sm border border-red-200">
            <h3 class="text-red-800 font-semibold uppercase text-sm">Unreceived</h3>
            <p class="text-3xl font-bold text-red-900">{{ $analytics['unreceived'] }}</p>
        </div>
        <div class="bg-blue-100 p-6 rounded-lg shadow-sm border border-blue-200">
            <h3 class="text-blue-800 font-semibold uppercase text-sm">Active Resolving</h3>
            <p class="text-3xl font-bold text-blue-900">{{ $analytics['received'] }}</p>
        </div>
        <div class="bg-green-100 p-6 rounded-lg shadow-sm border border-green-200">
            <h3 class="text-green-800 font-semibold uppercase text-sm">Resolved Cases</h3>
            <p class="text-3xl font-bold text-green-900">{{ $analytics['resolved'] }}</p>
        </div>
    </div>
    <a href="{{ route('/manager/report') }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm transition duration-150">
        Print Report
    </a>
    <div class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto">
        <h3 class="text-gray-700 font-bold mb-4 text-center">Incident Status Breakdown</h3>
        <canvas id="statusChart"></canvas>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Unreceived','Received', 'Resolved'],
            datasets: [{
                data: [{{ $analytics['unreceived'] }}, {{ $analytics['received'] }}, {{ $analytics['resolved'] }}],
                backgroundColor: ['#C4281C','#80BBDB', '#287F47'],
                borderWidth: .5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endpush