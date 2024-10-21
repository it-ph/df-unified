<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class AuditLogExport implements FromArray, WithHeadings, WithChunkReading, WithMapping
{
    private $audit_logs,$isAdmin;

    public function __construct($audit_logs,$isAdmin)
    {
        $this->audit_logs = $audit_logs;
        $this->isAdmin = $isAdmin;
    }

    public function array(): array
    {
        return $this->audit_logs;
    }

    public function headings(): array
    {
        $headings = [
            'ID',
            'Job Name',
            'Site ID',
            'Platform',
            'Developer',
            'Type of Request',
            'Num of Pages',
            'Preview Link',
            'Self QC Performed',
            'Developer Comment',
            'Time Taken',
            'QC Round',
            'QC Auditor',
            'QC Status',
            'Call For Rework',
            'Num of Times',
            'Alignment & Aesthetics',
            'Comments for Alignment & Asthetics',
            'Availability and Formats',
            'Comments for Availability and Formats',
            'Accuracy',
            'Comments for Accuracy',
            'Functionality',
            'Comments for Functionality',
            'QC Comments',
            'QC Start Time',
            'QC End Time',
            'Created On',
            'Created By',
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

    public function map($audit_log): array
    {
        $data = [
            $audit_log['id'],
            $audit_log['job_name'],
            $audit_log['site_id'],
            $audit_log['platform'],
            $audit_log['developer'],
            $audit_log['request_type'],
            $audit_log['request_volume'],
            $audit_log['preview_link'],
            $audit_log['self_qc'],
            $audit_log['dev_comments'],
            $audit_log['time_taken'],
            $audit_log['qc_round'],
            $audit_log['auditor'],
            $audit_log['qc_status'],
            $audit_log['for_rework'],
            $audit_log['num_times'],
            $audit_log['alignment_aesthetics'],
            $audit_log['c_alignment_aesthetics'],
            $audit_log['availability_formats'],
            $audit_log['c_availability_formats'],
            $audit_log['accuracy'],
            $audit_log['c_accuracy'],
            $audit_log['functionality'],
            $audit_log['c_functionality'],
            $audit_log['qc_comments'],
            $audit_log['start_at'],
            $audit_log['end_at'],
            $audit_log['created_at'],
            $audit_log['created_by'],
        ];

        if ($this->isAdmin) {
            array_splice($data, 2, 0, $audit_log['client']); // Insert client name after job name
        }

        return $data;
    }
}
