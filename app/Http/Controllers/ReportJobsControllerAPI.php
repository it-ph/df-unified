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

class ReportJobsControllerAPI extends Controller
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

    // GET REPORT JOBS
    public function getReportJobs(Request $request)
    {
        if($request->ajax())
        {
            // Get filter values from the request
            $client_id = $request->input('client_id');
            $platform = $request->input('platform');
            $request_type_id = $request->input('request_type_id');
            $developer_id = $request->input('developer_id');
            $status = $request->input('status');
            $date_range = $request->input('date_range');
            $date_range_selected = explode("to", $request->input('date_range'));
            $request['date_from'] = trim($date_range_selected[0]);
            $request['date_to'] = trim($date_range_selected[1]);
            $date_from =  Carbon::parse($request['date_from'])->format('Y-m-d');
            $date_to =  Carbon::parse($request['date_to'])->format('Y-m-d');

            $jobs = Job::query()
                ->with([
                    'theclient:id,name',
                    'therequesttype:id,name',
                    'therequestvolume:id,name',
                    'therequestsla:id,agreed_sla',
                    'thedeveloper:id,first_name,last_name',
                    'thecreatedby:id,first_name,last_name',
                ])
                ->select('tasks.*')
                ->orderBy('tasks.id','DESC');

            // Apply filters based on the values passed in the request
            if ($client_id && $client_id != 'all') {
                $jobs->where('tasks.client_id', $client_id);
            }

            if ($platform && $platform != 'all') {
                $jobs->where('tasks.platform', $platform);
            }

            if ($request_type_id && $request_type_id != 'all') {
                $jobs->where('tasks.request_type_id', $request_type_id);
            }

            if ($developer_id && $developer_id != 'all') {
                $jobs->where('tasks.developer_id', $developer_id);
            }

            if ($status && $status != 'all') {
                $jobs->where('tasks.status', $status);
            }

            if ($date_range) {
                $jobs->whereBetween('created_at', [
                    $date_from . ' 00:00:00',
                    $date_to . ' 23:59:59'
                ]);
            }

            // Continue with roles-based query adjustments
            // $roles = $this->getRoles();
            // $isAdmin = in_array('admin', $roles);

            // $jobs = $isAdmin ? $jobs : $jobs->clientjobs();

            return datatables($jobs)
                ->editColumn('name', (function($value){
                    // return '<a href="'.route('job.view', ['id' => $value->id]).'" rel="noopener noreferrer" target="_blank" class="text-info">'. $value->name .'</a>';
                    return $value->name;
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
                ->editColumn('template_followed', (function($value){
                    return $value->template_followed ? 'Yes' : 'No';
                }))
                ->editColumn('template_issue', (function($value){
                    return $value->template_issue ? 'Yes' : 'No';
                }))
                ->editColumn('auto_recommend', (function($value){
                    return $value->auto_recommend ? 'Yes' : 'No';
                }))
                ->editColumn('img_localstock', (function($value){
                    return $value->img_localstock ? 'Yes' : 'No';
                }))
                ->editColumn('img_customer', (function($value){
                    return $value->img_customer ? 'Yes' : 'No';
                }))
                ->editColumn('created_at', (function($value){
                    return $value->created_at ? date('d-M-y h:i:s a', strtotime($value->created_at)) : '-';
                }))
                ->editColumn('start_at', (function($value){
                    return $value->start_at ? date('d-M-y h:i:s a', strtotime($value->start_at)) : '';
                }))
                ->editColumn('end_at', (function($value){
                    return $value->end_at ? date('d-M-y h:i:s a', strtotime($value->end_at)) : '';
                }))
                ->editColumn('request_sla_id', (function($value){
                    return $value->therequestsla ? TimeElapsedHelper::convertTime($value->therequestsla->agreed_sla) : '-';
                }))
                ->editColumn('time_taken', (function($value){
                    return $this->getTimeTakenSLAMissed($value)['time_taken'];
                }))
                ->editColumn('qc_rounds', (function($value){
                    return $value->qc_rounds ? $value->qc_rounds : 0;
                }))
                ->editColumn('sla_missed', (function($value){
                    return $this->getTimeTakenSLAMissed($value)['sla_missed'];
                }))
                ->editColumn('internal_quality', (function($value){
                    return $value->internal_quality ? '<span class="text-'.($value->internal_quality == 'Pass' ? "success" : "danger").'">'.$value->internal_quality.'</span>' : '-';
                }))
                ->editColumn('external_quality', (function($value){
                    return $value->external_quality ? '<span class="text-'.($value->external_quality == 'Pass' ? "success" : "danger").'">'.$value->external_quality.'</span>' : '-';
                }))
                ->editColumn('developer_id', (function($value){
                    return $value->thedeveloper ? $value->thedeveloper->full_name : '-';
                }))
                ->editColumn('created_by', (function($value){
                    return $value->thecreatedby ? $value->thecreatedby->full_name : '-';
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
                    $action = '<button type="button" class="btn btn-primary btn-sm waves-effect waves-light" title="View History" onclick=JOB.show_history('.$value->id.')><i class="fa fa-history"></i></button>';
                    return $action;
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
        $datestorage = [];
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
        $datestorage = [];
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
