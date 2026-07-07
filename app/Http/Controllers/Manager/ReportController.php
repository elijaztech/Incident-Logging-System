<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function exportCsv(Request $request)
    {
        //filter incidents based on manager's request input dates
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());

        $incidents = Incident::whereBetween('created_at', [$startDate, $endDate])->get();

        //browser headers
        $fileName = 'incident_report_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        //file row by row
        $callback = function() use($incidents) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Room/Location', 'Description', 'Status', 'Created At']);

            foreach ($incidents as $incident) {
                fputcsv($file, [
                    $incident['id'],
                    $incident['location'] ?? $incident['room_number'], 
                    $incident['description'],
                    $incident['status'],
                    $incident['created_at']
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}