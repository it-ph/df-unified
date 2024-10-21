<?php

namespace App\Services;

use DateTime;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Event;
use App\Models\AuditLog;
use App\Models\JobPause;
use Facades\App\Http\Helpers\TimeElapsedHelper;
use Facades\App\Http\Helpers\WorkingHoursHelper;

class DevelopmentReportServices
{
    public function getRoles()
    {
        $user = User::with('theroles:id,user_id,name')->findOrFail(auth()->user()->id);
        $roles = [];
        foreach ($user->theroles as $role) {
            array_push($roles, $role->name);
        }

        return $roles;
    }

    // REPORTS
    public function report($date_from, $date_to, $client_id, $platform)
    {
        $datastorage = [];
        $jobsQuery = Job::query()
            ->with([
                'theclient:id,name',
                'therequesttype:id,name',
                'therequestvolume:id,name',
                'therequestsla:id,agreed_sla',
            ]);

        // Apply filters conditionally
        if ($client_id !== 'all') {
            $jobsQuery->where('client_id', $client_id);
        }

        if ($platform !== 'All') {
            $jobsQuery->where('platform', $platform);
        }

        // Date range filter
        $jobsQuery->whereBetween('created_at', [
            $date_from . ' 00:00:00',
            $date_to . ' 23:59:59'
        ]);

        // Ordering the results
        $jobsQuery->orderBy('id', 'DESC');

        // Get roles only when needed
        // $roles = $this->getRoles();
        // $isAdmin = in_array('admin', $roles);

        // $jobsQuery = $isAdmin ? $jobsQuery : $jobsQuery->clientjobs();

        // Clone the base query to avoid mutating the original query
        $totalQuery = clone $jobsQuery;
        $completedQuery = clone $jobsQuery;
        $sla_met_ctrQuery = clone $jobsQuery;
        $pending_external_qcQuery = clone $jobsQuery;

        // Process and map data in chunks
        $jobsQuery->chunk(200, function ($jobs) use (&$datastorage) {
            $mappedData = $jobs->map(function ($value) {
                $request_type = $value->therequesttype ? $value->therequesttype->name : '-';
                $request_volume = $value->therequestvolume ? $value->therequestvolume->name : '-';
                $request_sla = $value->therequestsla ? $value->therequestsla->agreed_sla : '-';
                $created_at = $value->created_at ? date('d-M-y', strtotime($value->created_at)) : '';
                $job_start_at = $value->start_at ? date('d-M-y h:i:s a', strtotime($value->start_at)) : 'Not Started';
                $job_end_at = $value->end_at ? date('d-M-y h:i:s a', strtotime($value->end_at)) : ($value->status == 'Not Started' ? 'Not Started' : 'WIP');
                $internal_quality = $value->internal_quality <> null ? $value->internal_quality : ($value->status <> 'Quality Check' ? 'Not Started' : 'QC In Progress');
                $external_quality = $value->external_quality <> null ? $value->external_quality : ($value->status <> 'Closed' ? 'Not Started' : 'QC In Progress');

                if($value->status == 'Closed') {
                    $time_taken = $value->time_taken;
                    $sla_missed = $value->sla_missed ? 'Yes' : 'No';
                    $sla_met = $sla_missed == 'Yes' ? 'No' : 'Yes';
                    $status = 'Closed';
                }
                elseif($value->status == 'Not Started') {
                    $time_taken = 'Not Started';
                    $sla_missed = 'Not Started';
                    $sla_met = 'Not Started';
                    $status = 'Not Started';
                }
                else {
                    $time_taken = 'WIP';
                    $sla_missed = 'WIP';
                    $sla_met = 'WIP';
                    $status = 'WIP';
                }

                return [
                    'job_name'          => $value->name,
                    'client'            => $value->theclient->name,
                    'request_type'      => $request_type,
                    'request_volume'    => $request_volume,
                    'created_at'        => $created_at,
                    'start_at'          => $job_start_at,
                    'end_at'            => $job_end_at,
                    'agreed_sla'        => $request_sla,
                    'time_taken'        => $time_taken,
                    'sla_missed'        => $sla_missed,
                    'sla_met'           => $sla_met,
                    'internal_quality'  => $internal_quality,
                    'external_quality'  => $external_quality,
                    'status'            => $status,
                ];
            });

            // Merge the mapped data into the main array
            $datastorage = array_merge($datastorage, $mappedData->toArray());
        });

        $total = $totalQuery->count();
        $completed = $completedQuery->where('status', 'Closed')->count();
        $sla_met_ctr = $sla_met_ctrQuery->where('sla_missed', 0)->count();
        $pending_external_qc = $pending_external_qcQuery->where('external_quality', null)->count();

        // Return the data as an array
        return [
            'devs' => $datastorage,
            'total' => $total,
            'completed' => $completed,
            'sla_met' => $sla_met_ctr,
            'pending_external_qc' => $pending_external_qc
        ];
    }

    // get job pauses
    public function getJobPauses($job_id) {
        $pauses = JobPause::query()
            ->select('id','job_id','start','end')
            ->where('job_id', $job_id)
            ->get();

        if($pauses->count() > 0)
        {
            foreach($pauses as $value)
            {
                $datastorage[] = [
                    'start' => new DateTime($value->start),
                    'end' => new DateTime($value->end)
                ];
            }
            return $datastorage;
        }
        else
        {
            return [];
        }
    }

    // get events
    public function getEvents($client_id,$start_at,$end_at) {
        $events = Event::query()
            ->select('id','client_id','start','end')
            ->where('client_id', $client_id)
            ->where('start','<=',$end_at)
            ->where('end','>=',$start_at)
            ->get();

        if($events->count() > 0)
        {
            foreach($events as $value)
            {
                $datastorage[] = [
                    'start' => new DateTime($value->start),
                    'end' => new DateTime($value->end)
                ];
            }
            return $datastorage;
        }
        else
        {
            return [];
        }
    }
}
