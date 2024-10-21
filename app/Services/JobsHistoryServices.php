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
                'thejob:id,name',
            ])
            ->where('job_id', $id)
            ->get();

        foreach($histories as $value) {
            $created_by = $value->thecreatedby ? $value->thecreatedby->full_name : '-';
            $job_name = $value->job_id ? $value->thejob->name : '-';
            $created_at = date("d-M-y h:i:s A",strtotime($value->created_at));
            $activity = $value->activity;

            $datastorage[] = [
                'id' => $value->id,
                'created_by' => $created_by,
                'created_at' => $created_at,
                'job_name' => $job_name,
                'activity' => $activity,
            ];
        }

        return $datastorage;
    }
}
