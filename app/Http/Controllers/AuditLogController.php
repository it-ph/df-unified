<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\Event;
use App\Models\Client;
use App\Models\AuditLog;
use App\Models\JobPause;
use Illuminate\Http\Request;
use App\Mail\UpdateTaskEmail;
use App\Traits\ResponseTraits;
use App\Mail\UpdateQCTaskEmail;
use App\Services\AuditLogsServices;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\AuditLogStoreRequest;
use Facades\App\Http\Helpers\CredentialsHelper;
use Facades\App\Http\Helpers\TimeElapsedHelper;
use Facades\App\Http\Helpers\JobHistories;

class AuditLogController extends Controller
{
    use ResponseTraits;

    public function __construct()
    {
        $this->model = new AuditLog();
        $this->service = new AuditLogsServices();
    }

    public function thecredentials()
    {
        return CredentialsHelper::get_set_credentials();
    }

    /** AUDITOR */
    public function qualityCheck()
    {
        $result = $this->successResponse('Tasks loaded successfully!');
        try
        {
            $result["data"] =  $this->service->loadPendingQCs();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function pickJob($id)
    {
        $audit_log = AuditLog::findOrfail($id);
        $result = $audit_log->auditor_id ? $this->successResponse('Task is already picked!.') : $result = $this->successResponse("Task picked successfully!");

        try {
            $audit_log->update([
                'auditor_id' => $audit_log->auditor_id ? $audit_log->auditor_id : auth()->user()->id,
                'start_at' => $audit_log->start_at == null ? Carbon::now() : $audit_log->start_at
            ]);

            $job = Job::where('id',$audit_log->job_id)->first();
            $job->update([
                'status' => 'Quality Check'
            ]);

            // email notifs
            if($job->status == 'Quality Check')
            {
                $this->sendJobEmailUpdates($job->id);
            }

            // email notifs
            $this->sendQCEmailUpdates($audit_log->id);

            // create history
            $client_id = $audit_log->client_id;
            $created_by = auth()->user()->id;
            $job_id = $job->id;
            $by = auth()->user()->full_name;
            $activity = "QC Task picked and started by ".$by. ', status set to Quality Check';
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);

        } catch (\Throwable $th) {
            $result = $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function releaseJob($id)
    {
        $result = $this->successResponse("Task released successfully!");
        try {
            $audit_log = AuditLog::findOrfail($id);
            $audit_log->update([
                'auditor_id' => null,
            ]);

            // email notifs
            $this->sendJobEmailUpdates($audit_log->job_id);

            // email notifs
            $this->sendQCEmailUpdates($audit_log->id);


            // create history
            $client_id = $audit_log->client_id;
            $created_by = auth()->user()->id;
            $job_id = $audit_log->job_id;
            $by = auth()->user()->full_name;
            $activity = "QC Task released by ".$by;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);

        } catch (\Throwable $th) {
            $result = $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function viewQC($id)
    {
        $user = $this->thecredentials();
        $job = $this->service->showQC($id);

        if(!$job){
            return view('errors.404');
        }

        return view('pages.admin.jobs.qc.index', compact('user','job'));
    }

    public function submitFeedback(AuditLogStoreRequest $request)
    {
        $result = $this->successResponse("Quality Check saved successfully!");
        try {
            // update audit log
            $request['end_at'] = Carbon::now();
            $audit_log = AuditLog::where('id',$request->edit_id)->first();

            // QC TIME TAKEN
            $start_at = $audit_log->start_at;
            $end_at = Carbon::now()->format('Y-m-d H:i:s');

            // do not deduct outside of work shift, pauses, and events
            $shift_start = '00:00:00';
            $shift_end = '23:59:59';
            $pauses = [];
            $events = [];

            $working_hours = TimeElapsedHelper::calculateWorkingTime($start_at, $end_at, $shift_start, $shift_end, $pauses, $events);
            $time_taken = TimeElapsedHelper::convertTime($working_hours);

            $request['time_taken'] = $time_taken;
            $audit_log->update($request->except('edit_id'));

            // update job
            $job = Job::where('id',$audit_log->job_id)->first();
            $qc_status = $request->qc_status;
            $status = $qc_status == 'Pass' ? 'Closed' : 'Bounce Back';
            $internal_quality = $job->internal_quality ? $job->internal_quality : $qc_status;

            if($status == 'Closed')
            {
                $start_at = $job->start_at;
                $end_at = $request['end_at'];
                $shift_start = $job->theclient->start;
                $shift_end = $job->theclient->end;
                $pauses = [];
                $events = [];
                $pauses = $this->getJobPauses($job->client_id, $start_at, $end_at);
                $events = $this->getEvents($job->client_id, $start_at, $end_at);

                $working_hours = TimeElapsedHelper::calculateWorkingTime($start_at, $end_at, $shift_start, $shift_end, $pauses, $events);
                $time_taken = TimeElapsedHelper::convertTime($working_hours);

                $sla_missed = $working_hours > $job->therequestsla->agreed_sla ? 1 : 0;
                $status = $sla_missed ? 'Info Needed' : 'Closed';

                $sla_threshold = $job->theclient->sla_threshold/100;
                $sla_percentage = $working_hours * $sla_threshold;
                $p_sla_miss = $sla_percentage > $job->therequestsla->agreed_sla ? 1 : 0;
            }
            else
            {
                $end_at = null;
                $time_taken = null;
                $sla_missed = 0;
                $p_sla_miss = 0;
            }

            $job->update([
                'status' => $status,
                'end_at' => $end_at,
                'sla_missed' => $sla_missed,
                'p_sla_miss' => $p_sla_miss,
                'time_taken' => $time_taken,
                'internal_quality' => $internal_quality
            ]);

            // email notifs
            $this->sendJobEmailUpdates($audit_log->job_id);

            // email notifs
            $this->sendQCEmailUpdates($audit_log->id);

            // create history
            $client_id = $audit_log->client_id;
            $created_by = auth()->user()->id;
            $job_id = $job->id;
            $by = auth()->user()->full_name;
            $activity = $by. ' submitted Job feedback, status set to '.$job->status;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);

        } catch (\Throwable $th) {
            $result = $this->errorResponse($th);
        }

        return $this->returnResponse($result);
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

    public function viewQCLog($id)
    {
        $user = $this->thecredentials();
        $job = $this->service->showQC($id);

        if(!$job){
            return view('errors.404');
        }

        return view('pages.admin.jobs.viewqc.index', compact('user','job'));
    }

    // send email updates
    public function sendJobEmailUpdates($job_id)
    {
        $job = Job::query()
            ->with([
                'thedeveloper:id,email',
            ])
            ->select('id','name','client_id','developer_id','status')
            ->where('id',$job_id)
            ->first();

        $to = $job->thedeveloper->email;
        $cc = $this->getJobEmailRecipients($job->client_id);

        if($this->is_connected())
        {
            Mail::to($to)
                ->cc($cc)
                ->queue(new UpdateTaskEmail($job));
        }
    }

    // send email QC
    public function sendQCEmailUpdates($audit_log_id)
    {
        $audit_log = AuditLog::query()
            ->with([
                'thejob:id,name,developer_id',
                'thejob.thedeveloper:id,email',
                'theauditor:id,email'
            ])
            ->select('id','job_id','auditor_id','qc_status')
            ->where('id',$audit_log_id)
            ->first();

        $to = array();
        $audit_log->auditor_id ? array_push($to, $audit_log->theauditor->email) : "";
        array_push($to, $audit_log->thejob->thedeveloper->email);
        $cc = $this->getQCEmailRecipients($audit_log->client_id);

        if($this->is_connected())
        {
            Mail::to($to)
                ->cc($cc)
                ->queue(new UpdateQCTaskEmail($audit_log));
        }
    }

    // job email recipients
    public function getJobEmailRecipients($client_id)
    {
        $cc = array();
        $ccs = Client::where('id', $client_id)->first();
        if($ccs) {
            $recipients = str_replace(' ','', $ccs['new_job_cc']);
            $recipients = explode(',',$recipients);
            foreach ($recipients as $recipient) {
                if (!empty($recipient)) {
                    array_push($cc, $recipient);
                }
            }
        }
        return $cc;
    }

    // qc email recipients
    public function getQCEmailRecipients($client_id)
    {
        $cc = array();
        $ccs = Client::where('id', $client_id)->first();
        if($ccs) {
            $recipients = str_replace(' ','', $ccs['qc_send_cc']);
            $recipients = explode(',',$recipients);
            foreach ($recipients as $recipient) {
                if (!empty($recipient)) {
                    array_push($cc, $recipient);
                }
            }
        }
        return $cc;
    }

    //Check if connected to Internet
    function is_connected()
    {
        $connected = @fsockopen("www.google.com", 80);
         //website, port  (try 80 or 443)
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }
}
