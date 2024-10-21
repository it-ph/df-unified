<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class JobLogExport implements FromArray, WithHeadings, WithChunkReading, WithMapping
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

    public function headings(): array
    {
        $headings = [
            'ID',
            'Job Name',
            'Status',
            'Site ID',
            'Platform',
            'Developer',
            'Type of Request',
            'Num of Pages',
            'SLA Agreed',
            'SLA Missed',
            'SLA Miss Reason',
            'Time Taken',
            'QC Round',
            'Salesforce Link',
            'Special Request',
            'Comments for Special Request',
            'Additional Comments',
            'Template Followed',
            'Any Issue with Template',
            'Comments for Issue in Template',
            'Automation Recommended',
            'Comments for Automation Recommendation',
            'Image(s) used from Localstock',
            'Image(s) provided by customer',
            'Num of new images used',
            'Shared Folder Location',
            'Developer Comments',
            'Internal Quality',
            'External Quality',
            'Comments for External Quality',
            'Created On',
            'Start Time',
            'End Time',
            'Closed On',
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

    public function map($job): array
    {
        $data = [
            $job['id'],
            $job['name'],
            $job['status'],
            $job['site_id'],
            $job['platform'],
            $job['developer'],
            $job['request_type'],
            $job['request_volume'],
            $job['requestsla'],
            $job['sla_missed'],
            $job['sla_miss_reason'],
            $job['time_taken'],
            $job['qc_rounds'],
            $job['salesforce_link'],
            $job['special_request'],
            $job['comments_special_request'],
            $job['addon_comments'],
            $job['template_followed'],
            $job['template_issue'],
            $job['comments_template_issue'],
            $job['auto_recommend'],
            $job['comments_auto_recommend'],
            $job['img_localstock'],
            $job['img_customer'],
            $job['img_num'],
            $job['shared_folder_location'],
            $job['dev_comments'],
            $job['internal_quality'],
            $job['external_quality'],
            $job['c_external_quality'],
            $job['created_at'],
            $job['start_at'],
            $job['end_at'],
            $job['end_at'],
            $job['created_by']
        ];

        if ($this->isAdmin) {
            array_splice($data, 2, 0, $job['client']); // Insert client name after job name
        }

        return $data;
    }
}
