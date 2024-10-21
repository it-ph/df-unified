<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DevReportTableRow extends Component
{
    public $row;
    public $slaColor;
    public $internalQualityColor;
    public $externalQualityColor;
    public $statusColor;
    public $isAdmin;

    public function __construct($row,$isAdmin)
    {
        $this->row = $row;
        $this->isAdmin = $isAdmin;
        $this->slaColor = $this->getColor($row['sla_met'], 'sla');
        $this->internalQualityColor = $this->getColor($row['internal_quality'], 'quality');
        $this->externalQualityColor = $this->getColor($row['external_quality'], 'quality');
        $this->statusColor = $this->getColor($row['status'], 'status');
    }

    public function render()
    {
        return view('components.dev-report-table-row', [
            'row' => $this->row,
            'slaColor' => $this->slaColor,
            'internalQualityColor' => $this->internalQualityColor,
            'externalQualityColor' => $this->externalQualityColor,
            'statusColor' => $this->statusColor,
            'isAdmin' => $this->isAdmin,
        ]);
    }

    public function getColor($status, $type)
    {
        $colors = [
            'sla' => [
                'Not Started' => '#74788D',
                'WIP' => '#FFC600',
                'Yes' => '#28B779',
                'No' => '#E3342F',
            ],
            'quality' => [
                'Not Started' => '#74788D',
                'QC In Progress' => '#FFC600',
                'Pass' => '#28B779',
                'Fail' => '#E3342F',
            ],
            'status' => [
                'Not Started' => '#74788D',
                'WIP' => '#FFC600',
                'Closed' => '#28B779',
            ],
        ];

        return $colors[$type][$status] ?? '#FFFFFF';
    }
}
