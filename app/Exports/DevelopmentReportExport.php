<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DevelopmentReportExport implements WithTitle, FromArray, WithHeadings, WithChunkReading, WithMapping, WithStyles, ShouldAutoSize
{
    private $jobs,$isAdmin;

    public function __construct($jobs,$isAdmin)
    {
        $this->jobs = $jobs;
        $this->isAdmin = $isAdmin;
    }

    public function array(): array
    {
        return $this->jobs;
    }

    public function title(): string
    {
        return 'Web Dev Report';
    }

    public function headings(): array
    {
        $headings = [
            'Name',
            'Type of Request',
            'Num Pages',
            'Date',
            'Start Time',
            'End Time',
            'Agreed SLA',
            'Time Taken',
            'SLA Met',
            'Internal QC',
            'External QC',
            'Status',
        ];

        if ($this->isAdmin) {
            array_splice($headings, 2, 0, 'Client Name'); // Insert 'Client Name' after 'Job Name'
        }

        return $headings;
    }

    public function chunkSize(): int
    {
        return 200;
    }

    public function map($job): array
    {
        $data = [
            $job['job_name'],
            $job['request_type'],
            $job['request_volume'],
            $job['created_at'],
            $job['start_at'],
            $job['end_at'],
            $job['agreed_sla'],
            $job['time_taken'],
            $job['sla_met'],
            $job['internal_quality'],
            $job['external_quality'],
            $job['status'],
        ];

        if ($this->isAdmin) {
            array_splice($data, 2, 0, $job['client']); // Insert client name after job name
        }

        return $data;
    }

    public function columnFormats(): array
    {
        $formats = [
            'D' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => NumberFormat::FORMAT_DATE_TIME4,
            'F' => NumberFormat::FORMAT_DATE_TIME4,
            'G' => NumberFormat::FORMAT_NUMBER_00,
            'H' => NumberFormat::FORMAT_NUMBER_00,
        ];

        // If the user is an admin, apply additional formatting to the 'Client Name' column (if present)
        if ($this->isAdmin) {
            $formats['C'] = NumberFormat::FORMAT_TEXT; // Example: set 'Client Name' column to text format
        }

        return $formats;
    }

    public function styles(Worksheet $sheet)
    {
        // Base header and border styles
        $headerStyle = [
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00599D'], // Background color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'], // Border color
                ],
            ],
        ];

        // Apply header styles and border styles
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray($headerStyle);
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray($borderStyle);

        // Apply conditional formatting based on user role
        if ($this->isAdmin) {
            // Apply additional styles if the user is an admin
            $sheet->getStyle('C')->applyFromArray([
                'font' => [
                    'color' => ['rgb' => '000000'], // Example: Set font color for 'Client Name' column
                ],
            ]);
        }

        // Apply conditional formatting based on user role
        if ($this->isAdmin) {
            // Apply styles for admin users
            for ($row = 2; $row <= $highestRow; $row++) {
                $slaMet = $sheet->getCell("J{$row}")->getValue();
                $internalQC = $sheet->getCell("K{$row}")->getValue();
                $externalQC = $sheet->getCell("L{$row}")->getValue();
                $status = $sheet->getCell("M{$row}")->getValue();

                $slaMetColor = $this->getColorForStatus($slaMet);
                $internalQCColor = $this->getColorForStatus($internalQC);
                $externalQCColor = $this->getColorForStatus($externalQC);
                $statusColor = $this->getColorForStatus($status);

                $sheet->getStyle("J{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($slaMetColor);
                $sheet->getStyle("K{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($internalQCColor);
                $sheet->getStyle("L{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($externalQCColor);
                $sheet->getStyle("M{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($statusColor);
            }
        } else {
            // Apply styles for non-admin users
            for ($row = 2; $row <= $highestRow; $row++) {
                $slaMet = $sheet->getCell("I{$row}")->getValue();
                $internalQC = $sheet->getCell("J{$row}")->getValue();
                $externalQC = $sheet->getCell("K{$row}")->getValue();
                $status = $sheet->getCell("L{$row}")->getValue();

                $slaMetColor = $this->getColorForStatus($slaMet);
                $internalQCColor = $this->getColorForStatus($internalQC);
                $externalQCColor = $this->getColorForStatus($externalQC);
                $statusColor = $this->getColorForStatus($status);

                $sheet->getStyle("I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($slaMetColor);
                $sheet->getStyle("J{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($internalQCColor);
                $sheet->getStyle("K{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($externalQCColor);
                $sheet->getStyle("L{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($statusColor);
            }
        }

        $columns = $this->isAdmin ? range('B', 'M') : range('B', 'L');
        foreach ($columns as $column) {
            $sheet->getStyle("{$column}1:{$column}" . $sheet->getHighestRow())->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        return [
            1 => $headerStyle,
            'A1:' . $highestColumn . $highestRow => $borderStyle,
        ];
    }

    private function getColorForStatus($status): string
    {
        switch ($status) {
            case "Not Started":
                return '74788D';
            case "WIP":
            case "QC In Progress":
                return 'FFC600';
            case "Yes":
            case "Pass":
            case "Closed":
                return '28B779';
            case "No":
            case "Fail":
                return 'E3342F';
            default:
                return 'FFFFFF'; // Default color if none of the above matches
        }
    }

}
