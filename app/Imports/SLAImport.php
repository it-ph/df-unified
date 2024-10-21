<?php

namespace App\Imports;

use App\Models\RequestSLA;
use App\Models\RequestType;
use App\Models\RequestVolume;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SLAImport implements ToModel, WithHeadingRow,SkipsEmptyRows {
    private $has_error = array();
    private $row_number = 1;

    public function model(array $row)
    {
        $ctr_error = 0;
        array_push($this->has_error,"Something went wrong, Please check all entries that you have encoded.");

        $this->row_number += 1;

        // Define required columns and their labels
        $requiredFields = [
            'request_type_name' => 'A',
            'num_pages' => 'B',
            'agreed_sla' => 'C',
        ];

        foreach ($requiredFields as $field => $column) {
            if (empty($row[$field])) {
                $ctr_error += 1;
                $this->has_error[] = " Check Cell $column".$this->row_number.", $field is required.";
            }
        }

        $request_type = RequestType::where('name',$row['request_type_name'])->first();
        if (empty($request_type)) {
            $ctr_error += 1;
            array_push($this->has_error, " Check Cell A".$this->row_number.", Request Type: ".$row['request_type_name']." does not exist.");
        }
        else {
            $request_type_id = $request_type->id;
        }

        $request_volume = RequestVolume::where('name',$row['num_pages'])->first();
        if (empty($request_volume)) {
            $ctr_error += 1;
            array_push($this->has_error, " Check Cell B".$this->row_number.", Num Pages: ".$row['num_pages']." does not exist.");
        }
        else {
            $request_volume_id = $request_volume->id;
        }

        if (filter_var($row['agreed_sla'], FILTER_VALIDATE_FLOAT) === false && filter_var($row['agreed_sla'], FILTER_VALIDATE_INT) === false) {
            $ctr_error += 1;
            array_push($this->has_error, " Check Cell C".$this->row_number.", Agreed SLA: ".$row['agreed_sla']." is not a valid number.");
        } else {
            $agreed_sla = $row['agreed_sla'];
        }

        if($ctr_error <= 0)
        {
            // Create or update sla
            RequestSLA::updateOrCreate(
                [
                    'request_type_id' => $request_type_id,
                    'request_volume_id' => $request_volume_id
                ],
                [
                    'agreed_sla' => $agreed_sla,
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]
            );
        }
    }

    public function getErrors()
    {
        return $this->has_error;
    }
}
