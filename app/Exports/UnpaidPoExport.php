<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UnpaidPoExport
{
    private const COLOR_HEADER_BG   = 'FF1E293B';
    private const COLOR_HEADER_FONT = 'FFFFFFFF';
    private const COLOR_SUBHEADER_BG   = 'FFE2E8F0';
    private const COLOR_SUBHEADER_FONT = 'FF0F172A';

    public function download(): StreamedResponse
    {
        // POs that have at least one maker payment term not yet paid
        $purchaseOrders = PurchaseOrder::with('contract')
            ->whereHas('makerPaymentTerms', fn ($q) => $q->whereNull('paid_date'))
            ->orderBy('contract_id')
            ->orderBy('id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Unpaid PO Payment Terms');

        $row = 1;

        // Title
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", 'PO WITH UNPAID PAYMENT TERMS');
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 13,
                'color' => ['argb' => self::COLOR_HEADER_FONT],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => self::COLOR_HEADER_BG],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(24);
        $row++;

        // Column headers
        $headers = ['#', 'Contract Number', 'PO Number'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue("{$col}{$row}", $header);
            $sheet->getStyle("{$col}{$row}")->applyFromArray([
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => self::COLOR_SUBHEADER_FONT],
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => self::COLOR_SUBHEADER_BG],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFCBD5E1'],
                    ],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $col++;
        }
        $row++;

        // Data rows
        if ($purchaseOrders->isEmpty()) {
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->setCellValue("A{$row}", '(No unpaid PO payment terms found)');
            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                'font'      => ['italic' => true, 'color' => ['argb' => 'FF94A3B8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders'   => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFCBD5E1'],
                    ],
                ],
            ]);
        } else {
            foreach ($purchaseOrders as $i => $po) {
                $sheet->setCellValue("A{$row}", $i + 1);
                $sheet->setCellValue("B{$row}", $po->contract->contract_number ?? '-');
                $sheet->setCellValue("C{$row}", $po->po_number);
                $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFCBD5E1'],
                        ],
                    ],
                ]);
                $row++;
            }
        }

        // Auto-size columns
        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'unpaid_po_' . now()->format('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
