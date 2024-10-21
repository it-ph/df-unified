<?php

namespace App\Services;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\AuditLog;
use App\Models\JobPause;
use Facades\App\Http\Helpers\TimeElapsedHelper;

class AuditLogsServices
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
    public function report($date_from, $date_to, $client_id, $platform, $request_type_id, $auditor_id, $status)
    {
        $datastorage = [];
        $audit_logsQuery = AuditLog::with([
            'theclient:id,name,start,end,shift_hours,sla_threshold',
            'thejob:id,name,site_id,platform,request_type_id,request_volume_id,request_sla_id,special_request,time_taken,sla_missed,developer_id,status,start_at,end_at',
            'thejob.therequestsla:id,agreed_sla',
            'thejob.therequesttype:id,name',
            'thejob.therequestvolume:id,name',
            'thejob.thedeveloper:id,first_name,last_name',
            'theauditor:id,first_name,last_name',
        ])
        ->select('audit_logs.*')
        ->orderBy('audit_logs.id','DESC');

        // Apply filters conditionally
        if ($client_id !== 'all') {
            $audit_logsQuery->where('client_id', $client_id);
        }

        if ($platform !== 'all') {
            $audit_logsQuery->whereHas('thejob', function ($query) use ($platform) {
                $query->where('platform', $platform);
            });
        }

        if ($request_type_id !== 'all') {
            $audit_logsQuery->whereHas('thejob.therequesttype', function ($query) use ($request_type_id) {
                $query->where('id', $request_type_id);
            });
        }

        if ($auditor_id !== 'all') {
            $audit_logsQuery->where('auditor_id', $auditor_id);
        }

        if ($status !== 'all') {
            $audit_logsQuery->where('qc_status', $status);
        }

        // Date range filter
        $audit_logsQuery->whereBetween('created_at', [
            $date_from . ' 00:00:00',
            $date_to . ' 23:59:59'
        ]);

        // Ordering the results
        $audit_logsQuery->orderBy('id', 'DESC');

        // Get roles only when needed
        // $roles = $this->getRoles();
        // $isAdmin = in_array('admin', $roles);

        // $audit_logsQuery = $isAdmin ? $audit_logsQuery : $audit_logsQuery->clientqcs();

        // Process and map data in chunks
        $audit_logsQuery->chunk(200, function ($audit_logs) use (&$datastorage) {
            $mappedData = $audit_logs->map(function ($value) {
                $name = $value->thejob->name;
                $client = $value->theclient ? $value->theclient->name : '-';
                $site_id = $value->thejob->site_id;
                $platform = $value->thejob->platform;
                $developer = $value->thejob->thedeveloper ? $value->thejob->thedeveloper->full_name : '-';
                $request_type = $value->thejob->therequesttype ? $value->thejob->therequesttype->name : '-';
                $request_volume = $value->thejob->therequestvolume ? $value->thejob->therequestvolume->name : '-';
                $self_qc = $value->self_qc ? 'Yes' : 'No';
                $auditor = $value->theauditor ? $value->theauditor->full_name : '';
                $for_rework = $value->for_rework ? 'Yes' : 'No';
                $alignment_aesthetics = $value->alignment_aesthetics ? 'Yes' : 'No';
                $availability_formats = $value->availability_formats ? 'Yes' : 'No';
                $accuracy = $value->accuracy ? 'Yes' : 'No';
                $functionality = $value->functionality ? 'Yes' : 'No';
                $qc_start_at = $value->start_at ? date('d-M-y h:i:s A', strtotime($value->start_at)) : '-';
                $qc_end_at = $value->end_at ? date('d-M-y h:i:s A', strtotime($value->end_at)) : '-';
                $created_at = $value->created_at ? date('d-M-y h:i:s A', strtotime($value->created_at)) : '-';
                $created_by = $value->thecreatedby ? $value->thecreatedby->full_name : '-';

                // Determine time taken and SLA status
                if(in_array($value->thejob->status,['In Progress','Sent For QC','Bounce Back','Quality Check']))
                {
                    // QC TIME TAKEN
                    $start_at = $value->start_at == null ? Carbon::now()->format('Y-m-d H:i:s') : $value->start_at;
                    $end_at = Carbon::now()->format('Y-m-d H:i:s');

                    // do not deduct outside of work shift, pauses, and events
                    $shift_start = '00:00:00';
                    $shift_end = '23:59:59';
                    $pauses = [];
                    $events = [];

                    $working_hours = TimeElapsedHelper::calculateWorkingTime($start_at, $end_at, $shift_start, $shift_end, $pauses, $events);
                    $time_taken = TimeElapsedHelper::convertTime($working_hours);
                }
                else
                {
                    $time_taken = $value->time_taken ? $value->time_taken : '00:00:00';
                }

                return [
                    'id'                        => $value->id,
                    'job_name'                  => $name,
                    'client'                    => $client,
                    'site_id'                   => $site_id,
                    'platform'                  => $platform,
                    'developer'                 => $developer,
                    'request_type'              => $request_type,
                    'request_volume'            => $request_volume,
                    'preview_link'              => $value->preview_link,
                    'self_qc'                   => $self_qc,
                    'dev_comments'              => $value->dev_comments,
                    'time_taken'                => $time_taken,
                    'qc_round'                  => $value->qc_round,
                    'auditor'                   => $auditor,
                    'qc_status'                 => $value->qc_status,
                    'for_rework'                => $for_rework,
                    'num_times'                 => $value->num_times,
                    'alignment_aesthetics'      => $alignment_aesthetics,
                    'c_alignment_aesthetics'    => $value->c_alignment_aesthetics,
                    'availability_formats'      => $availability_formats,
                    'c_availability_formats'    => $value->c_availability_formats,
                    'accuracy'                  => $accuracy,
                    'c_accuracy'                => $value->c_accuracy,
                    'functionality'             => $functionality,
                    'c_functionality'           => $value->c_functionality,
                    'qc_comments'               => $value->qc_comments,
                    'start_at'                  => $qc_start_at,
                    'end_at'                    => $qc_end_at,
                    'created_at'                => $created_at,
                    'created_by'                => $created_by,
                ];
            });

            // Merge the mapped data into the main array
            $datastorage = array_merge($datastorage, $mappedData->toArray());
        });

        // Return the data as an array
        return $datastorage;
    }

    // QUALITY CHECK
    public function showQC($id)
    {
        $value = AuditLog::with([
            'theclient:id,name,start,end,shift_hours,sla_threshold',
            'thejob.therequestsla:id,agreed_sla',
            'thejob.therequesttype:id,name',
            'thejob.therequestvolume:id,name',
            'thejob.thedeveloper:id,first_name,last_name'
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
            $value = $value->clientqcs()->first();
        }

        if(!$value){
            return null;
        }

        $name = $value->thejob->name;
        $client = $value->theclient ? $value->theclient->name : '-';
        $client_id = $value->theclient ? $value->client_id : '-';
        $site_id = $value->thejob->site_id;
        $platform = $value->thejob->platform;
        $developer = $value->thejob->thedeveloper ? $value->thejob->thedeveloper->full_name : '-';
        $developer_id = $value->thejob->thedeveloper ? $value->thejob->developer_id : '-';
        $request_type = $value->thejob->therequesttype ? $value->thejob->therequesttype->name : '-';
        $request_volume = $value->thejob->therequestvolume ? $value->thejob->therequestvolume->name : '-';
        $salesforce_link = $value->thejob->salesforce_link;
        $special_request = $value->thejob->special_request ? 'Yes' : 'No';
        $comments_special_request = $value->thejob->comments_special_request;
        $addon_comments = $value->thejob->addon_comments;
        $agreed_sla = $value->thejob->therequestsla ? $value->thejob->therequestsla->agreed_sla : '-';

        if(in_array($value->thejob->status,['In Progress','Sent For QC','Bounce Back','Quality Check']))
        {
            $start_at = $value->thejob->start_at;
            $end_at = Carbon::now()->format('Y-m-d H:i:s');
            $shift_start = $value->theclient->start;
            $shift_end = $value->theclient->end;
            $pauses = [];
            $events = [];
            $pauses = $this->getJobPauses($value->thejob->id);
            $events = $this->getEvents($value->client_id, $start_at, $end_at);

            $working_hours = TimeElapsedHelper::calculateWorkingTime($start_at, $end_at, $shift_start, $shift_end, $pauses, $events);
            $time_taken = TimeElapsedHelper::convertTime($working_hours);

            $sla_missed = $working_hours > $agreed_sla ? 1 : 0;
        }
        else
        {
            $time_taken = $value->time_taken ? $value->time_taken : '00:00:00';
            $sla_missed = $value->thejob->sla_missed;
        }

        $start_at = $value->thejob->start_at ? date('d-M-y h:i:s A', strtotime($value->thejob->start_at)) : '-';
        $end_at = $value->thejob->end_at ? date('d-M-y h:i:s A', strtotime($value->thejob->end_at)) : '-';
        $status = $value->thejob->status;

        // additional details
        $template_followed = $value->thejob->template_followed ? 'Yes' : 'No';
        $template_issue = $value->thejob->template_issue ? 'Yes' : 'No';
        $comments_template_issue = $value->thejob->comments_template_issue;
        $auto_recommend = $value->thejob->auto_recommend ? 'Yes' : 'No';
        $comments_auto_recommend = $value->thejob->comments_auto_recommend;
        $img_localstock = $value->thejob->img_localstock ? 'Yes' : 'No';
        $img_customer = $value->thejob->img_customer ? 'Yes' : 'No';
        $img_num = $value->thejob->img_num;
        $shared_folder_location = $value->thejob->shared_folder_location;
        $dev_comments = $value->thejob->dev_comments;

        // qc details
        $preview_link = $value->preview_link ? $value->preview_link : '';
        $self_qc = $value->self_qc ? 'Yes' : 'No';
        $audit_dev_comments = $value->dev_comments ? $value->dev_comments : '';
        $qc_round = $value->qc_round ? $value->qc_round : '';

        // qc feedback
        $qc_status= $value->qc_status ? $value->qc_status: null;
        $auditor_id = $value->theauditor ? $value->auditor_id : '';
        $auditor = $value->theauditor ? $value->theauditor->full_name : '';
        $for_rework = $value->for_rework ? 'Yes' : 'No';
        $num_times = $value->num_times;
        $alignment_aesthetics = $value->alignment_aesthetics ? 'Yes' : 'No';
        $c_alignment_aesthetics = $value->c_alignment_aesthetics ? $value->c_alignment_aesthetics : '';
        $availability_formats = $value->availability_formats ? 'Yes' : 'No';
        $c_availability_formats = $value->c_availability_formats ? $value->c_availability_formats : '';
        $accuracy = $value->accuracy ? 'Yes' : 'No';
        $c_accuracy = $value->c_accuracy ? $value->c_accuracy : '';
        $functionality = $value->functionality ? 'Yes' : 'No';
        $c_functionality = $value->c_functionality ? $value->c_functionality : '';
        $qc_comments = $value->qc_comments ? $value->qc_comments : '';
        $qc_start_at = $value->start_at ? date('d-M-y h:i:s A', strtotime($value->start_at)) : '';
        $qc_end_at = $value->end_at ? date('d-M-y h:i:s A', strtotime($value->end_at)) : '';

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
            'request_volume' => $request_volume,
            'salesforce_link' => $salesforce_link,
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

            // qc details
            'preview_link' => $preview_link,
            'self_qc' => $self_qc,
            'audit_dev_comments' => $audit_dev_comments,
            'qc_round' => $qc_round,

            // qc feedback
            'auditor_id' => $auditor_id,
            'auditor' => $auditor,
            'qc_status' => $qc_status,
            'for_rework' => $for_rework,
            'num_times' => $num_times,
            'alignment_aesthetics' => $alignment_aesthetics,
            'c_alignment_aesthetics' => $c_alignment_aesthetics,
            'availability_formats' => $availability_formats,
            'c_availability_formats' => $c_availability_formats,
            'accuracy' => $accuracy,
            'c_accuracy' => $c_accuracy,
            'functionality' => $functionality,
            'c_functionality' => $c_functionality,
            'qc_comments' => $qc_comments,
            'qc_start_at' => $qc_start_at,
            'qc_end_at' => $qc_end_at,
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
