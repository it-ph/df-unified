<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Event;
use App\Models\JobPause;
use Illuminate\Http\Request;
use Facades\App\Http\Helpers\TimeElapsedHelper;

class ReallocationJobControllerAPI extends Controller
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

   // GET ALL PENDING JOBS
    public function getAllPendingJobs(Request $request)
    {
        if($request->ajax())
        {
            $jobs = Job::with([
                'theclient:id,name,start,end,shift_hours,sla_threshold',
                'therequesttype:id,name',
                'therequestvolume:id,name',
                'therequestsla:id,agreed_sla',
                'thedeveloper:id,first_name,last_name',
            ])
            ->select('id','name','client_id','request_type_id','request_volume_id','request_sla_id','special_request','created_at','start_at','time_taken','sla_missed','developer_id','status')
            ->where('status','<>','Closed')
            ->orderBy('created_at','DESC');

            $roles = $this->getRoles();

            // Continue with roles-based query adjustments
            $roles = $this->getRoles();
            $isAdmin = in_array('admin', $roles);

            $jobs = $isAdmin ? $jobs : $jobs->clientjobs();

            return datatables($jobs)
                ->editColumn('name', (function($value){
                    return '<a href="'.route('job.view', ['id' => $value->id]).'" rel="noopener noreferrer" target="_blank" class="text-info">'. $value->name .'</a>';
                }))
                ->editColumn('client_id', (function($value){
                    return $value->theclient ? $value->theclient->name : '-';
                }))
                ->editColumn('request_type_id', (function($value){
                    return $value->therequesttype ? $value->therequesttype->name : '-';
                }))
                ->editColumn('request_volume_id', (function($value){
                    return $value->therequestvolume ? $value->therequestvolume->name : '-';
                }))
                ->editColumn('special_request', (function($value){
                    return $value->special_request ? 'Yes' : 'No';
                }))
                ->editColumn('created_at', (function($value){
                    return $value->created_at ? date('d-M-y h:i:s a', strtotime($value->created_at)) : '-';
                }))
                ->editColumn('request_sla_id', (function($value){
                    return $value->therequestsla ? TimeElapsedHelper::convertTime($value->therequestsla->agreed_sla) : '-';
                }))
                ->editColumn('sla_missed', (function($value){
                    return $this->getTimeTakenSLAMissed($value)['sla_missed'];
                }))
                ->editColumn('p_sla_miss', (function($value){
                    return $this->getTimeTakenSLAMissed($value)['p_sla_miss'];
                }))
                ->editColumn('developer_id', (function($value){
                    return $value->thedeveloper ? $value->thedeveloper->full_name : '-';
                }))
                ->editColumn('status', (function($value){
                    $badge_status = $value->status;
                    switch ($badge_status) {
                        case "Not Started":
                            $badge = 'secondary';
                            break;
                        case "In Progress":
                            $badge = 'primary';
                            break;
                        case "On Hold":
                            $badge = 'warning';
                            break;
                        case "Info Needed":
                            $badge = 'warning';
                            break;
                        case "Sent For QC":
                            $badge = 'dark';
                            break;
                        case "Quality Check":
                            $badge = 'info';
                            break;
                            break;
                        case "Bounce Back":
                            $badge = 'danger';
                            break;
                        case "Closed":
                            $badge = 'success';
                            break;
                    }
                    $status = '<span class="badge bg-'.$badge.'">'.$value->status.'</span>';
                    return $status;
                }))
                ->addColumn('action', (function($value){
                    return '<button type="button" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_reallocate" title="Reallocate Job" onclick=JOB.show('.$value->id.')><i class="fa fa-handshake-o"></i></button>';
                }))
                ->escapeColumns([])
                ->make(true);
        }
    }

    // get Time Taken and SLA Missed
    public function getTimeTakenSLAMissed($value) {
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
