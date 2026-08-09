<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class HppReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService)
    {
    }

    /**
     * Display HPP Report page.
     */
    public function index(Request $request): View
    {
        $period = $request->query('period', 'monthly');

        $from = $request->query('from') ? Carbon::parse($request->query('from')) : null;
        $to = $request->query('to') ? Carbon::parse($request->query('to')) : null;

        $dateColumn = $request->query('date_column', 'order_date');

        $orders = $this->reportService->getOrdersByPeriod($period, $from, $to, $dateColumn);
        
        $totalHpp = $orders->sum('total_cost');

        return view(isMobile() ? 'reports.mobile.hpp' : 'reports.hpp', compact('orders', 'totalHpp', 'period', 'dateColumn'));
    }

    /**
     * Export HPP Report to Excel
     */
    public function export(Request $request)
    {
        $period   = $request->query('period', 'monthly');
        $from     = $request->query('from') ? Carbon::parse($request->query('from')) : null;
        $to       = $request->query('to') ? Carbon::parse($request->query('to')) : null;
        $dateColumn = $request->query('date_column', 'order_date');

        $orders   = $this->reportService->getOrdersByPeriod($period, $from, $to, $dateColumn);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setTitle('Rekap HPP Belanja');
        
        // Headers
        $headers = [
            'No', 'Nomor Order', 'Tanggal', 'Deadline', 'Pelanggan', 'Produk', 'Total HPP / Modal (Rp)'
        ];
        
        foreach (array_values($headers) as $index => $header) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue("{$column}1", $header);
            $sheet->getStyle("{$column}1")->getFont()->setBold(true);
        }

        $row = 2;
        $total = 0;
        foreach ($orders as $index => $order) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->setCellValue("B{$row}", $order->order_number);
            $sheet->setCellValue("C{$row}", $order->order_date?->format('d/m/Y'));
            $sheet->setCellValue("D{$row}", $order->deadline?->format('d/m/Y'));
            $sheet->setCellValue("E{$row}", $order->customer_name);
            $sheet->setCellValue("F{$row}", $order->product_name);
            $sheet->setCellValue("G{$row}", (float) $order->total_cost);
            $total += (float) $order->total_cost;
            $row++;
        }

        // Total Row
        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->mergeCells("A{$row}:F{$row}");
        $sheet->getStyle("A{$row}:F{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
        $sheet->setCellValue("G{$row}", $total);

        // Formatting currency
        $sheet->getStyle("G2:G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
        
        // Auto-size columns
        foreach (range(1, count($headers)) as $col) {
            $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "laporan_hpp_{$period}_" . now()->format('Ymd_His') . ".xlsx";
        $tempPath = storage_path('app/private/temp/' . $filename);
        
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }
        
        $writer->save($tempPath);

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
