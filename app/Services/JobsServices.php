<?php

namespace App\Services;

use DateTime;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Event;
use App\Models\JobPause;
use Facades\App\Http\Helpers\TimeElapsedHelper;

class JobsServices
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
    public function report($date_from, $date_to, $client_id, $platform, $request_type_id, $developer_id, $status)
    {
        $datastorage = [];
        $jobsQuery = Job::query()
            ->with([
                'theclient:id,name',
                'therequesttype:id,name',
                'therequestvolume:id,name',
                'therequestsla:id,agreed_sla',
                'thedeveloper:id,first_name,last_name',
                'thecreatedby:id,first_name,last_name',
            ]);

        // Apply filters conditionally
        if ($client_id !== 'all') {
            $jobsQuery->where('client_id', $client_id);
        }

        if ($platform !== 'all') {
            $jobsQuery->where('platform', $platform);
        }

        if ($request_type_id !== 'all') {
            $jobsQuery->where('request_type_id', $request_type_id);
        }

        if ($developer_id !== 'all') {
            $jobsQuery->where('developer_id', $developer_id);
        }

        if ($status !== 'all') {
            $jobsQuery->where('status', $status);
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

        // Process and map data in chunks
        $jobsQuery->chunk(200, function ($jobs) use (&$datastorage) {
            $mappedData = $jobs->map(function ($value) {
                $client = $value->theclient ? $value->theclient->name : '-';
                $developer = $value->thedeveloper ? $value->thedeveloper->full_name : '-';
                $request_type = $value->therequesttype ? $value->therequesttype->name : '-';
                $request_volume = $value->therequestvolume ? $value->therequestvolume->name : '-';
                $request_sla = $value->therequestsla ? $value->therequestsla->agreed_sla : '-';
                $special_request = $value->special_request ? 'Yes' : 'No';
                $template_followed = $value->template_followed ? 'Yes' : 'No';
                $template_issue = $value->template_issue ? 'Yes' : 'No';
                $auto_recommend = $value->auto_recommend ? 'Yes' : 'No';
                $img_localstock = $value->img_localstock ? 'Yes' : 'No';
                $img_customer = $value->img_customer ? 'Yes' : 'No';
                $created_at = $value->created_at ? date('d-M-y h:i:s A', strtotime($value->created_at)) : '';
                $job_start_at = $value->start_at ? date('d-M-y h:i:s A', strtotime($value->start_at)) : '';
                $job_end_at = $value->end_at ? date('d-M-y h:i:s A', strtotime($value->end_at)) : '';
                $job_closed_at = $value->end_at ? date('d-M-y h:i:s A', strtotime($value->end_at)) : '';
                $createdby = $value->thecreatedby ? $value->thecreatedby->full_name : '-';

                // Determine time taken and SLA status
                if (in_array($value->status, ['In Progress', 'On Hold', 'Sent For QC', 'Bounce Back', 'Quality Check'])) {
                    $start_at = $value->start_at;
                    $end_at = Carbon::now()->format('Y-m-d H:i:s');
                    $shift_start = $value->theclient->start;
                    $shift_end = $value->theclient->end;

                    $pauses = $this->getJobPauses($value->id);
                    $events = $this->getEvents($value->client_id, $start_at, $end_at);

                    $working_hours = TimeElapsedHelper::calculateWorkingTime($start_at, $end_at, $shift_start, $shift_end, $pauses, $events);
                    $time_taken = TimeElapsedHelper::convertTime($working_hours);
                    $sla_missed = $working_hours > $request_sla ? 'Yes' : 'No';
                } else {
                    $time_taken = $value->time_taken ?: '00:00:00';
                    $sla_missed = $value->sla_missed ? 'Yes' : 'No';
                }

                return [
                    'id'                        => $value->id,
                    'name'                      => $value->name,
                    'client'                    => $client,
                    'status'                    => $value->status,
                    'site_id'                   => $value->site_id,
                    'platform'                  => $value->platform,
                    'developer'                 => $developer,
                    'request_type'              => $request_type,
                    'request_volume'            => $request_volume,
                    'requestsla'                => $request_sla,
                    'sla_missed'                => $sla_missed,
                    'sla_miss_reason'           => $value->sla_miss_reason,
                    'time_taken'                => $time_taken,
                    'qc_rounds'                 => $value->qc_rounds,
                    'salesforce_link'           => $value->salesforce_link,
                    'special_request'           => $special_request,
                    'comments_special_request'  => $value->comments_special_request,
                    'addon_comments'            => $value->addon_comments,
                    'template_followed'         => $template_followed,
                    'template_issue'            => $template_issue,
                    'comments_template_issue'   => $value->comments_template_issue,
                    'auto_recommend'            => $auto_recommend,
                    'comments_auto_recommend'   => $value->comments_auto_recommend,
                    'img_localstock'            => $img_localstock,
                    'img_customer'              => $img_customer,
                    'img_num'                   => $value->img_num,
                    'shared_folder_location'    => $value->shared_folder_location,
                    'dev_comments'              => $value->dev_comments,
                    'internal_quality'          => $value->internal_quality,
                    'external_quality'          => $value->external_quality,
                    'c_external_quality'        => $value->c_external_quality,
                    'created_at'                => $created_at,
                    'start_at'                  => $job_start_at,
                    'end_at'                    => $job_end_at,
                    'closed_at'                 => $job_closed_at,
                    'created_by'                 => $createdby,
                ];
            });

            // Merge the mapped data into the main array
            $datastorage = array_merge($datastorage, $mappedData->toArray());
        });

        // Return the data as an array
        return $datastorage;
    }

    // VIEW JOB
    public function show($id)
    {
        $value = Job::with([
            'therequesttype:id,name',
            'therequestvolume:id,name',
            'therequestsla:id,agreed_sla',
            'thedeveloper:id,first_name,last_name',
            'theauditlogs:id,job_id,auditor_id,qc_round,qc_status,start_at,end_at,self_qc',
            'theauditlogs.theauditor:id,first_name,last_name'
        ])
        ->where('id',$id);

        $roles = $this->getRoles();

        // ADMIN
        if(in_array('admin',$roles))
        {
            $value = $value->first();
        }
        // TEAM LEAD, MANAGER
        else
        {
            $value = $value->clientjobs()->first();
        }

        if(!$value){
            return null;
        }

        $name = $value->name;
        $client = $value->theclient ? $value->theclient->name : '-';
        $client_id = $value->theclient ? $value->client_id : '-';
        $site_id = $value->site_id;
        $platform = $value->platform;
        $developer = $value->thedeveloper ? $value->thedeveloper->full_name : '-';
        $developer_id = $value->thedeveloper ? $value->developer_id : '-';
        $request_type = $value->therequesttype ? $value->therequesttype->name : '-';
        $request_type_id = $value->therequesttype ? $value->therequesttype->id : '-';
        $request_volume = $value->therequestvolume ? $value->therequestvolume->name : '-';
        $request_volume_id = $value->therequestvolume ? $value->therequestvolume->id : '-';
        $salesforce_link = $value->salesforce_link;
        $special_request_raw = $value->special_request;
        $special_request = $value->special_request ? 'Yes' : 'No';
        $comments_special_request = $value->comments_special_request;
        $addon_comments = $value->addon_comments;
        $agreed_sla = $value->therequestsla ? $value->therequestsla->agreed_sla : '-';

        if(in_array($value->status,['In Progress','On Hold','Sent For QC','Bounce Back','Quality Check']))
        {
            $start_at = $value->start_at;
            $end_at = Carbon::now()->format('Y-m-d H:i:s');
            $shift_start = $value->theclient->start;
            $shift_end = $value->theclient->end;
            $pauses = [];
            $events = [];
            $pauses = $this->getJobPauses($value->id);
            $events = $this->getEvents($value->client_id, $start_at, $end_at);

            $working_hours = TimeElapsedHelper::calculateWorkingTime($start_at, $end_at, $shift_start, $shift_end, $pauses, $events);
            $time_taken = TimeElapsedHelper::convertTime($working_hours);

            $agreed_sla_raw = $value->therequestsla->agreed_sla;
            $sla_missed = $working_hours > $agreed_sla_raw ? 1 : 0;

            $sla_threshold = $value->theclient->sla_threshold/100;
            $sla_percentage = $agreed_sla_raw * $sla_threshold;
            $p_sla_miss = $working_hours > $sla_percentage ? 1 : 0;
        }
        else
        {
            $time_taken = $value->time_taken ? $value->time_taken : '00:00:00';
            $sla_missed = $value->sla_missed ? 1 : 0;
            $p_sla_miss = $value->sla_missed ? 1 : 0;
        }

        $start_at = $value->start_at ? date('d-M-y h:i:s A', strtotime($value->start_at)) : '';
        $end_at = $value->end_at ? date('d-M-y h:i:s A', strtotime($value->end_at)) : '';
        $status = $value->status;

        // additional details
        $template_followed = $value->template_followed ? 'Yes' : 'No';
        $template_issue = $value->template_issue ? 'Yes' : 'No';
        $comments_template_issue = $value->comments_template_issue;
        $auto_recommend = $value->auto_recommend ? 'Yes' : 'No';
        $comments_auto_recommend = $value->comments_auto_recommend;
        $img_localstock = $value->img_localstock ? 'Yes' : 'No';
        $img_customer = $value->img_customer ? 'Yes' : 'No';
        $img_num = $value->img_num;
        $shared_folder_location = $value->shared_folder_location;
        $dev_comments = $value->dev_comments;

        // auditlogs
        $logs = [];
        $audit_logs = $value->theauditlogs ? $value->theauditlogs : '';
        foreach($audit_logs as $log) {
            $qc_round = $log->qc_round;
            $auditor = $log->theauditor ? $log->theauditor->full_name : '-';
            $qc_status = $log->qc_status ? $log->qc_status : '-';
            $qc_start_at = $log->start_at ? date('d-M-y h:i:s a', strtotime($log->start_at)) : '-';
            $qc_end_at = $log->end_at ? date('d-M-y h:i:s a', strtotime($log->end_at)) : '-';
            $self_qc = $log->self_qc ? 'Yes' : 'No';

            $logs[] = [
                'audit_log_id' => $log->id,
                'qc_round' => $qc_round,
                'auditor' => $auditor,
                'qc_status' => $qc_status,
                'qc_start_at' => $qc_start_at,
                'qc_end_at' => $qc_end_at,
                'self_qc' => $self_qc,
            ];
        }

        // internal quality details
        $internal_quality = $value->internal_quality;

        // external quality details
        $external_quality = $value->external_quality;
        $c_external_quality = $value->c_external_quality;

        $job = [
            'id' => $value->id,
            'name' => $name,
            'client' => $client,
            'client_id' => $client_id,
            'site_id' => $site_id,
            'platform' => $platform,
            'developer' => $developer,
            'developer_id' => $developer_id,
            'request_type' => $request_type,
            'request_type_id' => $request_type_id,
            'request_volume' => $request_volume,
            'request_volume_id' => $request_volume_id,
            'salesforce_link' => $salesforce_link,
            'special_request_raw' => $special_request_raw,
            'special_request' => $special_request,
            'comments_special_request' => $comments_special_request,
            'addon_comments' => $addon_comments,
            'agreed_sla' => $agreed_sla,
            'sla_missed' => $sla_missed,
            'start_at' => $start_at,
            'end_at' => $end_at,
            'status' => $status,

            // additional details
            'template_followed' => $template_followed,
            'template_issue' => $template_issue,
            'comments_template_issue' => $comments_template_issue,
            'auto_recommend' => $auto_recommend,
            'comments_auto_recommend' => $comments_auto_recommend,
            'img_localstock' => $img_localstock,
            'img_customer' => $img_customer,
            'img_num' => $img_num,
            'shared_folder_location' => $shared_folder_location,
            'dev_comments' => $dev_comments,

            // auditlogs
            'audit_logs' => $logs,

            // internal quality details
            'internal_quality' => $internal_quality,

            // external quality details
            'external_quality' => $external_quality,
            'c_external_quality' => $c_external_quality,
        ];

        return $job;
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
