<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\AuditLog;
use App\Models\JobPause;
use Illuminate\Http\Request;
use Facades\App\Http\Helpers\TimeElapsedHelper;

class ReallocationQCControllerAPI extends Controller
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

    // GET ALL PENDING QCs
    public function getAllPendingQCs(Request $request)
    {
        if($request->ajax())
        {
            $pending_qcs = AuditLog::with([
                'theclient:id,name',
                'thejob:id,name,request_type_id,request_volume_id,request_sla_id,special_request,time_taken,sla_missed,developer_id',
                'thejob.therequestsla:id,agreed_sla',
                'thejob.therequesttype:id,name',
                'thejob.therequestvolume:id,name',
                'thejob.thedeveloper:id,first_name,last_name',
                'theauditor:id,first_name,last_name',
            ])
            ->select('audit_logs.id','audit_logs.client_id','audit_logs.job_id','audit_logs.qc_round','audit_logs.auditor_id','audit_logs.start_at')
            ->where('auditor_id','<>',null)
            ->where('qc_status','Pending')
            ->orderBy('created_at','DESC');

            // Continue with roles-based query adjustments
            $roles = $this->getRoles();
            $isAdmin = in_array('admin', $roles);

            $pending_qcs = $isAdmin ? $pending_qcs : $pending_qcs->clientqcs();

            return datatables($pending_qcs)
                ->editColumn('job_id', (function($value){
                    return auth()->user()->id == $value->auditor_id ? '<a href="'.route('job.qc', ['id' => $value->id]).'" rel="noopener noreferrer" target="_blank" class="text-info">'. $value->thejob->name .'</a>' : $value->thejob->name;
                }))
                ->editColumn('client_id', (function($value){
                    return $value->theclient ? $value->theclient->name : '-';
                }))
                ->editColumn('request_type_id', (function($value){
                    return $value->thejob->therequesttype ? $value->thejob->therequesttype->name : '-';
                }))
                ->editColumn('request_volume_id', (function($value){
                    return $value->thejob->therequestvolume ? $value->thejob->therequestvolume->name : '-';
                }))
                ->editColumn('special_request', (function($value){
                    return $value->thejob->special_request ? 'Yes' : 'No';
                }))
                ->editColumn('start_at', (function($value){
                    return $value->start_at ? date('d-M-y h:i:s a', strtotime($value->start_at)) : '-';
                }))
                ->editColumn('request_sla_id', (function($value){
                    return $value->thejob->therequestsla ? TimeElapsedHelper::convertTime($value->thejob->therequestsla->agreed_sla) : '-';
                }))
                ->editColumn('sla_missed', (function($value){
                    return $this->getTimeTakenSLAMissed($value)['sla_missed'];
                }))
                ->editColumn('developer_id', (function($value){
                    return $value->thejob->thedeveloper ? $value->thejob->thedeveloper->full_name : '-';
                }))
                ->editColumn('qc_round', (function($value){
                    return $value->qc_round ? $value->qc_round : '-';
                }))
                ->editColumn('auditor_id', (function($value){
                    return $value->theauditor ? $value->theauditor->full_name : '-';
                }))
                ->addColumn('action', (function($value){
                    $action = auth()->user()->id == $value->auditor_id ?
                        '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" id="btn_release_'.$value->id.'" title="Release Job" onclick=JOB.release('.$value->id.')><i class="fa fa-share"></i></button>
                        <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_reallocate'.$value->id.'" title="Reallocate Job" onclick=JOB.show('.$value->id.')><i class="fa fa-handshake-o"></i></button>'
                        : '<button type="button" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_reallocate'.$value->id.'" title="Reallocate Job" onclick=JOB.show('.$value->id.')><i class="fa fa-handshake-o"></i></button>';
                    return $action;
                }))
                ->rawColumns(
                [
                    'action',
                ])
                ->escapeColumns([])
                ->make(true);
        }
    }

    // get Time Taken and SLA Missed
    public function getTimeTakenSLAMissed($value) {
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
