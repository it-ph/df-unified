<?php

namespace App\Http\Helpers;

use App\Models\JobHistory;

class JobHistories {
    public function addNewHistory($client_id, $created_by, $job_id, $activity)
    {
        JobHistory::query()
            ->create([
                'client_id' => $client_id,
                'created_by' => $created_by,
                'job_id' => $job_id,
                'activity' => $activity,
            ]);
    }
}
