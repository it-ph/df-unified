<?php

namespace App\Services;

use App\Models\JobHistory;

class JobsHistoryServices {
    public function load($id)
    {
        $datastorage = [];
        $histories = JobHistory::query()
            ->select('id','created_by','job_id','activity','created_at')
            ->with([
                'thecreatedby:id,first_name,last_name',
                'thejob:id,account_no',
            ])
            ->where('job_id', $id)
            ->get();

        foreach($histories as $value) {
            $created_by = $value->thecreatedby ? $value->thecreatedby->full_name : '-';
            $account_no = $value->job_id ? $value->thejob->account_no : '-';
            $created_at = date("d-M-y h:i:s A",strtotime($value->created_at));
            $activity = $value->activity;

            $datastorage[] = [
                'id' => $value->id,
                'created_by' => $created_by,
                'created_at' => $created_at,
                'account_no' => $account_no,
                'activity' => $activity,
            ];
        }

        return $datastorage;
    }
}
