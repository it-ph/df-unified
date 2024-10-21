<?php

namespace App\Services;

use App\Models\Job;
use App\Models\User;
use App\Models\AuditLog;
use Facades\App\Http\Helpers\TaskHelper;

class ReallocationServices
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

    // PENDING JOBS
    public function loadPendingJobs()
    {
        $datastorage = [];

        $jobs = Job::with([
            'therequesttype:id,name',
            'therequestvolume:id,name',
            'therequestsla:id,agreed_sla',
            'thedeveloper:id,first_name,last_name',
        ])
        ->select('id','name','request_type_id','request_volume_id','request_sla_id','special_request','created_at','start_at','time_taken','sla_missed','developer_id','status')
        ->where('status','<>','Closed')
        ->orderBy('created_at','DESC');

        // Continue with roles-based query adjustments
        $roles = $this->getRoles();
        $isAdmin = in_array('admin', $roles);

        $jobs = $isAdmin ? $jobs->get() : $jobs->clientjobs();

        foreach($jobs as $value) {
            $name = '<a href="'.env('APP_URL').'/viewjob/'.$value->id.'" class="text-info">'. $value->name .'</a>';
            $request_type = $value->therequesttype ? $value->therequesttype->name : '-';
            $request_volume = $value->therequestvolume ? $value->therequestvolume->name : '-';
            $special_request = $value->special_request ? 'Yes' : 'No';
            $created_at = $value->created_at ? date('d-M-y h:i:s a', strtotime($value->created_at)) : '-';
            $start_at = $value->start_at ? date('d-M-y h:i:s a', strtotime($value->start_at)) : '-';
            $agreed_sla = $value->therequestsla ? TaskHelper::convertTime($value->therequestsla->agreed_sla) : '-';
            $time_taken = $value->time_taken ? $value->time_taken : '-';
            $sla_missed = $value->sla_missed ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>';
            $p_sla_miss = $value->sla_missed ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>';
            $developer = $value->thedeveloper ? $value->thedeveloper->full_name : '-';

            $badge_status = $value->status;
            switch ($badge_status) {
                case "Not Started":
                    $badge = 'secondary';
                    break;
                case "In Progress":
                    $badge = 'primary';
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

            $action = '<button type="button" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_reallocate" title="Reallocate" onclick=JOB.show('.$value->id.')><i class="fa fa-handshake-o"></i></button>';
            $status = '<span class="badge bg-'.$badge.'">'.$value->status.'</span>';

            $datastorage[] = [
                'id' => $value->id,
                'name' => $name,
                'request_type' => $request_type,
                'request_volume' => $request_volume,
                'special_request' => $special_request,
                'created_at' => $created_at,
                'start_at' => $start_at,
                'agreed_sla' => $agreed_sla,
                'time_taken' => $time_taken,
                'sla_missed' => $sla_missed,
                'p_sla_miss' => $p_sla_miss,
                'developer' => $developer,
                'action' => $action,
                'status' => $status,
            ];
        }

        return $datastorage;
    }

    // PENDING QCS
    public function loadPendingQCs()
    {
        $datastorage = [];

        $pending_qcs = AuditLog::with([
            'thejob:id,name,request_type_id,request_volume_id,request_sla_id,special_request,time_taken,sla_missed,developer_id',
            'thejob.therequestsla:id,agreed_sla',
            'thejob.therequesttype:id,name',
            'thejob.therequestvolume:id,name',
            'thejob.thedeveloper:id,first_name,last_name'
        ])
        ->select('id','job_id','qc_round','qc_round','auditor_id','start_at')
        ->where('auditor_id','<>',null)
        ->where('qc_status','Pending')
        ->orderBy('created_at','DESC');

        // Continue with roles-based query adjustments
        $roles = $this->getRoles();
        $isAdmin = in_array('admin', $roles);

        $pending_qcs = $isAdmin ? $pending_qcs->get() : $pending_qcs->clientjobs();

        foreach($pending_qcs as $value) {
            $name = '<a href="'.env('APP_URL').'/qualitycheck/'.$value->id.'" class="text-info">'. $value->thejob->name .'</a>';
            $request_type = $value->thejob->therequesttype ? $value->thejob->therequesttype->name : '-';
            $request_volume = $value->thejob->therequestvolume ? $value->thejob->therequestvolume->name : '-';
            $special_request = $value->thejob->special_request ? 'Yes' : 'No';
            $start_at = $value->start_at ? date('d-M-y h:i:s a', strtotime($value->start_at)) : '';
            $agreed_sla = $value->thejob->therequestsla ? TaskHelper::convertTime($value->thejob->therequestsla->agreed_sla) : '-';
            $sla_missed = $value->thejob->sla_missed ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>';
            $developer = $value->thejob->thedeveloper ? $value->thejob->thedeveloper->full_name : '-';
            $qc_round = $value->qc_round ? $value->qc_round : '-';
            $auditor = $value->theauditor ? $value->theauditor->full_name : '-';

            $action = auth()->user()->id == $value->auditor_id ? '<button type="button" class="btn btn-info btn-sm waves-effect waves-light" id="btn_release_'.$value->id.'" title="Release Job" onclick=JOB.release('.$value->id.')><i class="fa fa-share"></i></button>
                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_reallocate'.$value->id.'" title="Reallocate Job" onclick=JOB.show('.$value->id.')><i class="fa fa-handshake-o"></i></button>'
                : '<button type="button" class="btn btn-primary btn-sm waves-effect waves-light" id="btn_reallocate'.$value->id.'" title="Reallocate Job" onclick=JOB.show('.$value->id.')><i class="fa fa-handshake-o"></i></button>';

            $datastorage[] = [
                'id' => $value->id,
                'name' => $name,
                'request_type' => $request_type,
                'request_volume' => $request_volume,
                'special_request' => $special_request,
                'start_at' => $start_at,
                'agreed_sla' => $agreed_sla,
                'sla_missed' => $sla_missed,
                'developer' => $developer,
                'qc_round' => $qc_round,
                'auditor' => $auditor,
                'action' => $action,
            ];
        }

        return $datastorage;
    }
}
