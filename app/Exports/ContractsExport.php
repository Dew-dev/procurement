<?php

namespace App\Exports;

use App\Models\Contract;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ContractsExport
{
    // Header background color (dark slate)
    private const COLOR_HEADER_BG   = 'FF1E293B';
    private const COLOR_HEADER_FONT = 'FFFFFFFF';

    // Section title background (mid slate)
    private const COLOR_SECTION_BG   = 'FF334155';
    private const COLOR_SECTION_FONT = 'FFFFFFFF';

    // Sub-header (column labels) background
    private const COLOR_SUBHEADER_BG   = 'FFE2E8F0';
    private const COLOR_SUBHEADER_FONT = 'FF0F172A';

    public function download(): StreamedResponse
    {
        $contracts = Contract::with([
            'contractItems',
            'rfqs',
            'quotations',
            'purchaseOrders.rfq',
            'purchaseOrders.makerPaymentTerms',
            'purchaseOrders.purchaseOrderItems.contractItem',
            'contractPaymentTerms',
            'bgNumbers',
            'suretyBonds',
        ])->orderBy('created_at')->get();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // remove default empty sheet

        foreach ($contracts as $index => $contract) {
            $sheet = $spreadsheet->createSheet($index);
            $sheetTitle = $this->sanitizeSheetName($contract->contract_number ?? "Contract_{$contract->id}");
            $sheet->setTitle($sheetTitle);

            $row = 1;
            $row = $this->writeContractInfo($sheet, $contract, $row);
            $row = $this->writeContractItems($sheet, $contract, $row);
            $row = $this->writeRfqs($sheet, $contract, $row);
            $row = $this->writeQuotations($sheet, $contract, $row);
            $row = $this->writePurchaseOrders($sheet, $contract, $row);
            $row = $this->writeContractPaymentTerms($sheet, $contract, $row);
            $row = $this->writeBgNumbers($sheet, $contract, $row);
            $this->writeSuretyBonds($sheet, $contract, $row);

            // Auto-size columns A–H
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $writer   = new Xlsx($spreadsheet);
        $filename = 'contracts_export_' . now()->format('Ymd_His') . '.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // -------------------------------------------------------------------------
    // Contract Info Section
    // -------------------------------------------------------------------------

    private function writeContractInfo($sheet, Contract $contract, int $row): int
    {
        // Big title row
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'CONTRACT INFORMATION');
        $this->styleTitle($sheet, "A{$row}:H{$row}");
        $row++;

        $fields = [
            ['Contract Number',   $contract->contract_number],
            ['Buyer Name',        $contract->buyer_name],
            ['Company Name',      $contract->company_name],
            ['RFQ From Buyer',    $this->fmtDate($contract->rfq_from_buyer)],
            ['RFQ Number',        $contract->rfq_number],
            ['Quotation To Buyer',$this->fmtDate($contract->quotation_to_buyer)],
            ['Quotation Number',  $contract->quotation_number],
            ['Contract Date',     $this->fmtDate($contract->contract_date)],
            ['Delivery Date',     $this->fmtDate($contract->delivery_date)],
        ];

        foreach ($fields as [$label, $value]) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->mergeCells("B{$row}:H{$row}");
            $sheet->setCellValue("B{$row}", $value);
            $this->styleInfoLabel($sheet, "A{$row}");
            $this->styleInfoValue($sheet, "B{$row}:H{$row}");
            $row++;
        }

        return $row + 1; // blank gap
    }

    // -------------------------------------------------------------------------
    // Contract Items
    // -------------------------------------------------------------------------

    private function writeContractItems($sheet, Contract $contract, int $row): int
    {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'CONTRACT ITEMS');
        $this->styleSection($sheet, "A{$row}:H{$row}");
        $row++;

        $headers = ['#', 'Item Name', 'Description', 'Qty', 'Unit', 'Unit Price', 'Currency'];
        $this->writeSubHeaders($sheet, $row, $headers, 'A');
        $row++;

        $items = $contract->contractItems;
        if ($items->isEmpty()) {
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", '(No items)');
            $this->styleEmpty($sheet, "A{$row}:H{$row}");
            $row++;
        } else {
            foreach ($items as $i => $item) {
                $sheet->setCellValue("A{$row}", $i + 1);
                $sheet->setCellValue("B{$row}", $item->item_name);
                $sheet->setCellValue("C{$row}", $item->description);
                $sheet->setCellValue("D{$row}", $item->qty);
                $sheet->setCellValue("E{$row}", $item->unit);
                $sheet->setCellValue("F{$row}", $item->unit_price !== null ? (float) $item->unit_price : null);
                $sheet->setCellValue("G{$row}", $item->currency);
                $this->styleBorderedRow($sheet, "A{$row}:G{$row}");
                $row++;
            }
        }

        return $row + 1;
    }

    // -------------------------------------------------------------------------
    // RFQs
    // -------------------------------------------------------------------------

    private function writeRfqs($sheet, Contract $contract, int $row): int
    {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'RFQs');
        $this->styleSection($sheet, "A{$row}:H{$row}");
        $row++;

        $headers = ['#', 'RFQ Number', 'RFQ Date', 'Maker'];
        $this->writeSubHeaders($sheet, $row, $headers, 'A');
        $row++;

        $rfqs = $contract->rfqs;
        if ($rfqs->isEmpty()) {
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", '(No RFQs)');
            $this->styleEmpty($sheet, "A{$row}:H{$row}");
            $row++;
        } else {
            foreach ($rfqs as $i => $rfq) {
                $sheet->setCellValue("A{$row}", $i + 1);
                $sheet->setCellValue("B{$row}", $rfq->rfq_number);
                $sheet->setCellValue("C{$row}", $this->fmtDate($rfq->rfq_date));
                $sheet->setCellValue("D{$row}", $rfq->maker);
                $this->styleBorderedRow($sheet, "A{$row}:D{$row}");
                $row++;
            }
        }

        return $row + 1;
    }

    // -------------------------------------------------------------------------
    // Quotations
    // -------------------------------------------------------------------------

    private function writeQuotations($sheet, Contract $contract, int $row): int
    {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'QUOTATIONS');
        $this->styleSection($sheet, "A{$row}:H{$row}");
        $row++;

        $headers = ['#', 'Quotation Number', 'Quotation Date', 'Maker Name'];
        $this->writeSubHeaders($sheet, $row, $headers, 'A');
        $row++;

        $quotations = $contract->quotations;
        if ($quotations->isEmpty()) {
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", '(No Quotations)');
            $this->styleEmpty($sheet, "A{$row}:H{$row}");
            $row++;
        } else {
            foreach ($quotations as $i => $q) {
                $sheet->setCellValue("A{$row}", $i + 1);
                $sheet->setCellValue("B{$row}", $q->quotation_number);
                $sheet->setCellValue("C{$row}", $this->fmtDate($q->quotation_date));
                $sheet->setCellValue("D{$row}", $q->maker_name);
                $this->styleBorderedRow($sheet, "A{$row}:D{$row}");
                $row++;
            }
        }

        return $row + 1;
    }

    // -------------------------------------------------------------------------
    // Purchase Orders
    // -------------------------------------------------------------------------

    private function writePurchaseOrders($sheet, Contract $contract, int $row): int
    {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'PURCHASE ORDERS');
        $this->styleSection($sheet, "A{$row}:H{$row}");
        $row++;

        $pos = $contract->purchaseOrders;

        if ($pos->isEmpty()) {
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", '(No Purchase Orders)');
            $this->styleEmpty($sheet, "A{$row}:H{$row}");
            return $row + 2;
        }

        foreach ($pos as $i => $po) {
            // PO header label row
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", "PO #" . ($i + 1) . ": " . $po->po_number);
            $this->stylePOHeader($sheet, "A{$row}:H{$row}");
            $row++;

            // PO detail fields
            $poFields = [
                ['PO Number',            $po->po_number],
                ['PO Date',              $this->fmtDate($po->po_date)],
                ['RFQ',                  $po->rfq?->rfq_number],
                ['Payment Term',         $po->po_payment_term],
                ['WIP Status',           $po->wip_status],
                ['Exact Delivery Date',  $this->fmtDate($po->exact_delivery_date)],
                ['Delivered Date',       $this->fmtDate($po->delivered_date)],
                ['Dimension',            $po->dimension],
                ['Weight',               $po->weight],
                ['Incoterm',             $po->incoterm],
                ['Expedite',             $po->expedite],
            ];

            foreach ($poFields as [$label, $value]) {
                $sheet->setCellValue("A{$row}", $label);
                $sheet->mergeCells("B{$row}:H{$row}");
                $sheet->setCellValue("B{$row}", $value);
                $this->styleInfoLabel($sheet, "A{$row}");
                $this->styleInfoValue($sheet, "B{$row}:H{$row}");
                $row++;
            }

            // PO Items sub-section
            $row++;
            $sheet->mergeCells("B{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", 'PO Items');
            $this->styleSubSection($sheet, "A{$row}:H{$row}");
            $row++;

            $poItemHeaders = ['', '#', 'Item Name', 'Description', 'Qty', 'Unit', 'Notes'];
            $this->writeSubHeaders($sheet, $row, $poItemHeaders, 'A');
            $row++;

            $poItems = $po->purchaseOrderItems;
            if ($poItems->isEmpty()) {
                $sheet->mergeCells("B{$row}:H{$row}");
                $sheet->setCellValue("B{$row}", '(No items)');
                $this->styleEmpty($sheet, "B{$row}:H{$row}");
                $row++;
            } else {
                foreach ($poItems as $j => $poItem) {
                    $ci = $poItem->contractItem;
                    $sheet->setCellValue("B{$row}", $j + 1);
                    $sheet->setCellValue("C{$row}", $ci?->item_name);
                    $sheet->setCellValue("D{$row}", $ci?->description);
                    $sheet->setCellValue("E{$row}", $poItem->qty);
                    $sheet->setCellValue("F{$row}", $ci?->unit);
                    $sheet->setCellValue("G{$row}", $poItem->notes);
                    $this->styleBorderedRow($sheet, "B{$row}:G{$row}");
                    $row++;
                }
            }

            // Maker Payment Terms sub-section
            $row++;
            $sheet->mergeCells("B{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", 'Maker Payment Terms');
            $this->styleSubSection($sheet, "A{$row}:H{$row}");
            $row++;

            $mptHeaders = ['', '#', 'Term Code', 'Percentage (%)', 'Invoice Number', 'Invoice Date', 'Paid Date'];
            $this->writeSubHeaders($sheet, $row, $mptHeaders, 'A');
            $row++;

            $mpts = $po->makerPaymentTerms;
            if ($mpts->isEmpty()) {
                $sheet->mergeCells("B{$row}:H{$row}");
                $sheet->setCellValue("B{$row}", '(No payment terms)');
                $this->styleEmpty($sheet, "B{$row}:H{$row}");
                $row++;
            } else {
                foreach ($mpts as $k => $mpt) {
                    $sheet->setCellValue("B{$row}", $k + 1);
                    $sheet->setCellValue("C{$row}", $mpt->term_code);
                    $sheet->setCellValue("D{$row}", $mpt->percentage !== null ? (float) $mpt->percentage : null);
                    $sheet->setCellValue("E{$row}", $mpt->invoice_number);
                    $sheet->setCellValue("F{$row}", $this->fmtDate($mpt->invoice_date));
                    $sheet->setCellValue("G{$row}", $this->fmtDate($mpt->paid_date));
                    $this->styleBorderedRow($sheet, "B{$row}:G{$row}");
                    $row++;
                }
            }

            $row += 2; // gap between POs
        }

        return $row;
    }

    // -------------------------------------------------------------------------
    // Contract Payment Terms
    // -------------------------------------------------------------------------

    private function writeContractPaymentTerms($sheet, Contract $contract, int $row): int
    {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'CONTRACT PAYMENT TERMS');
        $this->styleSection($sheet, "A{$row}:H{$row}");
        $row++;

        $headers = ['#', 'Term Code', 'Percentage (%)', 'Invoice Number', 'Invoice Date', 'Paid Date'];
        $this->writeSubHeaders($sheet, $row, $headers, 'A');
        $row++;

        $terms = $contract->contractPaymentTerms;
        if ($terms->isEmpty()) {
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", '(No payment terms)');
            $this->styleEmpty($sheet, "A{$row}:H{$row}");
            $row++;
        } else {
            foreach ($terms as $i => $term) {
                $sheet->setCellValue("A{$row}", $i + 1);
                $sheet->setCellValue("B{$row}", $term->term_code);
                $sheet->setCellValue("C{$row}", $term->percentage !== null ? (float) $term->percentage : null);
                $sheet->setCellValue("D{$row}", $term->invoice_number);
                $sheet->setCellValue("E{$row}", $this->fmtDate($term->invoice_date));
                $sheet->setCellValue("F{$row}", $this->fmtDate($term->paid_date));
                $this->styleBorderedRow($sheet, "A{$row}:F{$row}");
                $row++;
            }
        }

        return $row + 1;
    }

    // -------------------------------------------------------------------------
    // BG Numbers
    // -------------------------------------------------------------------------

    private function writeBgNumbers($sheet, Contract $contract, int $row): int
    {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'BG NUMBERS');
        $this->styleSection($sheet, "A{$row}:H{$row}");
        $row++;

        $headers = ['#', 'Number', 'Periode', 'Start Date', 'End Date'];
        $this->writeSubHeaders($sheet, $row, $headers, 'A');
        $row++;

        $bgNumbers = $contract->bgNumbers;
        if ($bgNumbers->isEmpty()) {
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", '(No BG Numbers)');
            $this->styleEmpty($sheet, "A{$row}:H{$row}");
            $row++;
        } else {
            foreach ($bgNumbers as $i => $bg) {
                $sheet->setCellValue("A{$row}", $i + 1);
                $sheet->setCellValue("B{$row}", $bg->number);
                $sheet->setCellValue("C{$row}", $bg->periode);
                $sheet->setCellValue("D{$row}", $this->fmtDate($bg->start_date));
                $sheet->setCellValue("E{$row}", $this->fmtDate($bg->end_date));
                $this->styleBorderedRow($sheet, "A{$row}:E{$row}");
                $row++;
            }
        }

        return $row + 1;
    }

    // -------------------------------------------------------------------------
    // Surety Bonds
    // -------------------------------------------------------------------------

    private function writeSuretyBonds($sheet, Contract $contract, int $row): int
    {
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'SURETY BONDS');
        $this->styleSection($sheet, "A{$row}:H{$row}");
        $row++;

        $headers = ['#', 'Number', 'Periode', 'Start Date', 'End Date'];
        $this->writeSubHeaders($sheet, $row, $headers, 'A');
        $row++;

        $bonds = $contract->suretyBonds;
        if ($bonds->isEmpty()) {
            $sheet->mergeCells("A{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", '(No Surety Bonds)');
            $this->styleEmpty($sheet, "A{$row}:H{$row}");
            $row++;
        } else {
            foreach ($bonds as $i => $bond) {
                $sheet->setCellValue("A{$row}", $i + 1);
                $sheet->setCellValue("B{$row}", $bond->number);
                $sheet->setCellValue("C{$row}", $bond->periode);
                $sheet->setCellValue("D{$row}", $this->fmtDate($bond->start_date));
                $sheet->setCellValue("E{$row}", $this->fmtDate($bond->end_date));
                $this->styleBorderedRow($sheet, "A{$row}:E{$row}");
                $row++;
            }
        }

        return $row + 1;
    }

    // =========================================================================
    // Styling helpers
    // =========================================================================

    private function styleTitle($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
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
        $sheet->getRowDimension(
            (int) preg_replace('/[^0-9]/', '', explode(':', $range)[0])
        )->setRowHeight(24);
    }

    private function styleSection($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 11,
                'color' => ['argb' => self::COLOR_SECTION_FONT],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => self::COLOR_SECTION_BG],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'indent'     => 1,
            ],
        ]);
    }

    private function stylePOHeader($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 10,
                'color' => ['argb' => 'FF0F172A'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFCBD5E1'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'indent'     => 1,
            ],
        ]);
    }

    private function styleSubSection($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 10,
                'color' => ['argb' => 'FF1E3A5F'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFDBEAFE'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'indent'     => 2,
            ],
        ]);
    }

    private function writeSubHeaders($sheet, int $row, array $headers, string $startCol): void
    {
        $col = $startCol;
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
    }

    private function styleInfoLabel($sheet, string $cell): void
    {
        $sheet->getStyle($cell)->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF8FAFC'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFCBD5E1'],
                ],
            ],
        ]);
    }

    private function styleInfoValue($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font'    => ['size' => 9],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFCBD5E1'],
                ],
            ],
        ]);
    }

    private function styleBorderedRow($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFCBD5E1'],
                ],
            ],
        ]);
    }

    private function styleEmpty($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['italic' => true, 'color' => ['argb' => 'FF94A3B8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFCBD5E1'],
                ],
            ],
        ]);
    }

    // =========================================================================
    // Utilities
    // =========================================================================

    private function fmtDate($date): ?string
    {
        if (!$date) {
            return null;
        }

        return $date instanceof \DateTimeInterface
            ? $date->format('d/m/Y')
            : $date;
    }

    private function sanitizeSheetName(string $name): string
    {
        // Excel sheet name: max 31 chars, no special chars: \ / ? * [ ]
        $name = preg_replace('/[\\\\\/\?\*\[\]:]+/', '-', $name);
        return mb_substr($name, 0, 31);
    }
}
