<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportService
{
    /**
     * Get orders filtered by a predefined period keyword
     * or by a custom date range.
     *
     * @param  string  $period  'daily' | 'weekly' | 'monthly' | 'yearly'
     * @return Collection<int, Order>
     */
    public function getOrdersByPeriod(
        string $period = 'monthly',
        ?Carbon $from = null,
        ?Carbon $to = null,
        string $dateColumn = 'order_date'
    ): Collection {
        $query = Order::with(['creator:id,name', 'invoice', 'sizeDetails', 'clothingCategory', 'material', 'sizeDetails.gender']);

        if ($from && $to) {
            $query->whereBetween($dateColumn, [
                $from->startOfDay(),
                $to->endOfDay(),
            ]);
        } else {
            $query->where(function ($q) use ($period, $dateColumn): void {
                match ($period) {
                    'daily'   => $q->whereDate($dateColumn, today()),
                    'weekly'  => $q->whereBetween($dateColumn, [
                        now()->startOfWeek(),
                        now()->endOfWeek(),
                    ]),
                    'monthly' => $q->whereMonth($dateColumn, now()->month)
                                   ->whereYear($dateColumn, now()->year),
                    'yearly'  => $q->whereYear($dateColumn, now()->year),
                    default   => $q->whereMonth($dateColumn, now()->month)
                                   ->whereYear($dateColumn, now()->year),
                };
            });
        }

        return $query->orderByDesc($dateColumn)->get();
    }

    /**
     * Get revenue summary grouped by month for the current year.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function getMonthlyRevenue(): \Illuminate\Support\Collection
    {
        return Invoice::selectRaw(
            'MONTH(created_at) as month,
             YEAR(created_at) as year,
             SUM(grand_total) as total_revenue,
             COUNT(*) as total_invoices'
        )
            ->whereYear('created_at', now()->year)
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) ASC, MONTH(created_at) ASC')
            ->get();
    }

    /**
     * Get order count breakdown by production status for a given period.
     *
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    public function getStatusBreakdown(string $period = 'monthly', string $dateColumn = 'order_date'): \Illuminate\Support\Collection
    {
        $query = Order::selectRaw('current_status, COUNT(*) as total');

        match ($period) {
            'daily'  => $query->whereDate($dateColumn, today()),
            'weekly' => $query->whereBetween($dateColumn, [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]),
            'yearly' => $query->whereYear($dateColumn, now()->year),
            default  => $query->whereMonth($dateColumn, now()->month)
                               ->whereYear($dateColumn, now()->year),
        };

        return $query->groupBy('current_status')->pluck('total', 'current_status');
    }

    /**
     * Generate a formatted XLSX file from a collection of orders.
     * Returns the temp file path — caller is responsible for streaming and deleting it.
     *
     * @param  Collection<int, Order>  $orders
     * @param  string  $period
     */
    public function exportXlsx(Collection $orders, string $period = 'monthly'): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Pesanan');

        // ── Title ─────────────────────────────────────────────────────────────
        $periodLabels = [
            'daily'   => 'Harian',
            'weekly'  => 'Mingguan',
            'monthly' => 'Bulanan',
            'yearly'  => 'Tahunan',
        ];
        $periodLabel = $periodLabels[$period] ?? ucfirst($period);

        $sheet->mergeCells('A1:Q1');
        $sheet->setCellValue('A1', 'LAPORAN PESANAN — ' . strtoupper($periodLabel) . ' — ' . now()->format('d/m/Y'));
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // ── Header row ────────────────────────────────────────────────────────
        $headers = [
            'A' => 'No. Pesanan',
            'B' => 'Nama Pelanggan',
            'C' => 'No. Telepon',
            'D' => 'Jabatan / Custom Nama',
            'E' => 'Alamat Pemesan',
            'F' => 'Nama Produk',
            'G' => 'Tipe / Model',
            'H' => 'Warna',
            'I' => 'Bahan / Material',
            'J' => 'Total Qty',
            'K' => 'Qty Laki-laki',
            'L' => 'Qty Perempuan',
            'M' => 'Qty Anak-anak',
            'N' => 'Total Harga (Rp)',
            'O' => 'Status',
            'P' => 'Tgl. Pesan',
            'Q' => 'Tenggat',
        ];

        foreach ($headers as $col => $label) {
            $sheet->setCellValue("{$col}2", $label);
        }

        $headerRange = 'A2:Q2';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFBFDBFE']]],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        // ── Status labels ─────────────────────────────────────────────────────
        $statusLabels = \App\Models\MasterTrackingStatus::pluck('name', 'code')->toArray();

        // ── Data rows ─────────────────────────────────────────────────────────
        $rowNum = 3;
        foreach ($orders as $order) {
            $isEven  = ($rowNum % 2 === 0);
            $bgColor = $isEven ? 'FFDBEAFE' : 'FFFFFFFF'; // light blue / white alternating

            $sheet->setCellValue("A{$rowNum}", $order->order_number);
            $sheet->setCellValue("B{$rowNum}", $order->customer_name);
            $sheet->setCellValue("C{$rowNum}", $order->customer_phone);
            $sheet->setCellValue("D{$rowNum}", $order->customer_title ?? '');
            $sheet->setCellValue("E{$rowNum}", $order->customer_address ?? '');
            $sheet->setCellValue("F{$rowNum}", $order->product_name);
            $sheet->setCellValue("G{$rowNum}", $order->product_type);
            $sheet->setCellValue("H{$rowNum}", $order->color);
            $sheet->setCellValue("I{$rowNum}", $order->material ?? '');
            $sheet->setCellValue("J{$rowNum}", $order->totalQuantity());
            $sheet->setCellValue("K{$rowNum}", $order->quantityByGender('male'));
            $sheet->setCellValue("L{$rowNum}", $order->quantityByGender('female'));
            $sheet->setCellValue("M{$rowNum}", $order->quantityByGender('child'));
            $sheet->setCellValue("N{$rowNum}", (float) $order->total_price);
            $sheet->setCellValue("O{$rowNum}", $statusLabels[$order->current_status] ?? $order->current_status);
            $sheet->setCellValue("P{$rowNum}", $order->order_date?->format('d/m/Y'));
            $sheet->setCellValue("Q{$rowNum}", $order->deadline?->format('d/m/Y'));

            $rowStyle = [
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFD1D5DB']]],
            ];
            $sheet->getStyle("A{$rowNum}:Q{$rowNum}")->applyFromArray($rowStyle);

            // Currency format for total price column
            $sheet->getStyle("N{$rowNum}")->getNumberFormat()
                ->setFormatCode('#,##0');

            // Center numeric columns
            $sheet->getStyle("J{$rowNum}:M{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowNum++;
        }

        // ── Summary row ───────────────────────────────────────────────────────
        if ($orders->isNotEmpty()) {
            $lastDataRow = $rowNum - 1;

            $sheet->mergeCells("A{$rowNum}:I{$rowNum}");
            $sheet->setCellValue("A{$rowNum}", 'TOTAL');
            $sheet->setCellValue("J{$rowNum}", "=SUM(J3:J{$lastDataRow})");
            $sheet->setCellValue("K{$rowNum}", "=SUM(K3:K{$lastDataRow})");
            $sheet->setCellValue("L{$rowNum}", "=SUM(L3:L{$lastDataRow})");
            $sheet->setCellValue("M{$rowNum}", "=SUM(M3:M{$lastDataRow})");
            $sheet->setCellValue("N{$rowNum}", "=SUM(N3:N{$lastDataRow})");

            $sheet->getStyle("A{$rowNum}:Q{$rowNum}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E3A5F']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF93C5FD']]],
            ]);
            $sheet->getStyle("N{$rowNum}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getRowDimension($rowNum)->setRowHeight(20);
        }

        // ── Column widths ─────────────────────────────────────────────────────
        $columnWidths = [
            'A' => 18, 'B' => 22, 'C' => 16, 'D' => 28, 'E' => 32,
            'F' => 22, 'G' => 16, 'H' => 14, 'I' => 20, 'J' => 11,
            'K' => 13, 'L' => 13, 'M' => 13, 'N' => 20, 'O' => 22,
            'P' => 13, 'Q' => 13,
        ];
        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // Freeze header rows
        $sheet->freezePane('A3');

        // ── Write to temp file ────────────────────────────────────────────────
        $tempPath = tempnam(sys_get_temp_dir(), 'report_') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return $tempPath;
    }
}
