<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Event;
use App\Models\JobPause;
use Illuminate\Http\Request;
use Facades\App\Http\Helpers\TimeElapsedHelper;

class ReportAuditLogsControllerAPI extends Controller
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

    // GET REPORT AUDITLOGS
    public function getReportAuditLogs(Request $request)
    {
        if($request->ajax())
        {
            // Get filter values from the request
            $client_id = $request->input('client_id');
            $platform = $request->input('platform');
            $request_type_id = $request->input('request_type_id');
            $auditor_id = $request->input('auditor_id');
            $status = $request->input('status');
            $date_range = $request->input('date_range');
            $date_range_selected = explode("to", $request->input('date_range'));
            $request['date_from'] = trim($date_range_selected[0]);
            $request['date_to'] = trim($date_range_selected[1]);
            $date_from =  Carbon::parse($request['date_from'])->format('Y-m-d');
            $date_to =  Carbon::parse($request['date_to'])->format('Y-m-d');

            $qcs = AuditLog::with([
                'theclient:id,name,start,end,shift_hours,sla_threshold',
                'thejob:id,name,site_id,platform,request_type_id,request_volume_id,request_sla_id,special_request,time_taken,sla_missed,developer_id,status,start_at,end_at',
                'thejob.therequestsla:id,agreed_sla',
                'thejob.therequesttype:id,name',
                'thejob.therequestvolume:id,name',
                'thejob.thedeveloper:id,first_name,last_name',
                'theauditor:id,first_name,last_name',
                'thecreatedby:id,first_name,last_name'
            ])
            ->select('audit_logs.*')
            ->orderBy('audit_logs.id','DESC');

            // Apply filters based on the values passed in the request
            if ($client_id && $client_id != 'all') {
                $qcs->where('audit_logs.client_id', $client_id);
            }

            if ($platform && $platform != 'all') {
                $qcs->whereHas('thejob', function ($query) use ($platform) {
                    $query->where('platform', $platform);
                });
            }

            if ($request_type_id && $request_type_id != 'all') {
                $qcs->whereHas('thejob.therequesttype', function ($query) use ($request_type_id) {
                    $query->where('id', $request_type_id);
                });
            }

            if ($auditor_id && $auditor_id != 'all') {
                $qcs->where('auditor_id', $auditor_id);
            }

            if ($status && $status != 'all') {
                $qcs->where('qc_status', $status);
            }

            if ($date_range) {
                $qcs->whereBetween('audit_logs.created_at', [
                    $date_from . ' 00:00:00',
                    $date_to . ' 23:59:59'
                ]);
            }

            // Continue with roles-based query adjustments
            // $roles = $this->getRoles();
            // $isAdmin = in_array('admin', $roles);

            // $qcs = $isAdmin ? $qcs : $qcs->clientqcs();

            return datatables($qcs)
                ->editColumn('job_id', (function($value){
                    // return '<a href="'.route('job.qc', ['id' => $value->id]).'" rel="noopener noreferrer" target="_blank" class="text-info">'. $value->thejob->name .'</a>';
                    return $value->thejob->name;
                }))
                ->editColumn('client_id', (function($value){
                    return $value->theclient ? $value->theclient->name : '-';
                }))
                ->editColumn('developer_id', (function($value){
                    return $value->thejob->thedeveloper ? $value->thejob->thedeveloper->full_name : '-';
                }))
                ->editColumn('request_type_id', (function($value){
                    return $value->thejob->therequesttype ? $value->thejob->therequesttype->name : '-';
                }))
                ->editColumn('request_volume_id', (function($value){
                    return $value->thejob->therequestvolume ? $value->thejob->therequestvolume->name : '-';
                }))
                ->editColumn('self_qc', (function($value){
                    return $value->self_qc ? 'Yes' : 'No';
                }))
                ->editColumn('request_sla_id', (function($value){
                    return $value->thejob->therequestsla ? TimeElapsedHelper::convertTime($value->thejob->therequestsla->agreed_sla) : '-';
                }))
                ->editColumn('time_taken', (function($value){
                    return $this->getTimeTakenSLAMissed($value)['time_taken'];
                }))
                ->editColumn('qc_round', (function($value){
                    return $value->qc_round ? $value->qc_round : '-';
                }))
                ->editColumn('auditor_id', (function($value){
                    return $value->theauditor ? $value->theauditor->full_name : '-';
                }))
                ->editColumn('qc_status', (function($value){
                    $badge_status = $value->qc_status;
                    switch ($badge_status) {
                        case "Pending":
                            $badge = 'warning';
                            break;
                        case "Pass":
                            $badge = 'success';
                            break;
                        case "Fail":
                            $badge = 'danger';
                            break;
                    }
                    $status = '<span class="badge bg-'.$badge.'">'.$value->qc_status.'</span>';
                    return $status;
                }))
                ->editColumn('for_rework', (function($value){
                    return $value->for_rework ? 'Yes' : 'No';
                }))
                ->editColumn('alignment_aesthetics', (function($value){
                    return $value->alignment_aesthetics ? 'Yes' : 'No';
                }))
                ->editColumn('availability_formats', (function($value){
                    return $value->availability_formats ? 'Yes' : 'No';
                }))
                ->editColumn('accuracy', (function($value){
                    return $value->accuracy ? 'Yes' : 'No';
                }))
                ->editColumn('functionality', (function($value){
                    return $value->functionality ? 'Yes' : 'No';
                }))
                ->editColumn('start_at', (function($value){
                    return $value->start_at ? date('d-M-y h:i:s a', strtotime($value->start_at)) : '-';
                }))
                ->editColumn('end_at', (function($value){
                    return $value->end_at ? date('d-M-y h:i:s a', strtotime($value->end_at)) : '-';
                }))
                ->editColumn('created_at', (function($value){
                    return $value->created_at ? date('d-M-y h:i:s a', strtotime($value->created_at)) : '-';
                }))
                ->editColumn('created_by', (function($value){
                    return $value->thecreatedby ? $value->thecreatedby->full_name : '-';
                }))
                ->escapeColumns([])
                ->make(true);
        }
    }

    // get Time Taken and SLA Missed
    public function getTimeTakenSLAMissed($value) {
        if(in_array($value->thejob->status,['In Progress','On Hold','Sent For QC','Bounce Back','Quality Check']))
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
            $agreed_sla_raw = $value->thejob->therequestsla->agreed_sla;
            $sla_missed = $working_hours > $agreed_sla_raw ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>';

            $sla_threshold = $value->theclient->sla_threshold/100;
            $sla_percentage = $agreed_sla_raw * $sla_threshold;
            $p_sla_miss = $working_hours > $sla_percentage ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>';
        }
        else
        {
            $time_taken = $value->time_taken ? $value->time_taken : '00:00:00';
            $sla_missed = $value->sla_missed ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>';
            $p_sla_miss = $value->sla_missed ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>';
        }

        return $result = array(
            'time_taken' => $time_taken,
            'sla_missed' => $sla_missed,
            'p_sla_miss' => $p_sla_miss,
        );
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
