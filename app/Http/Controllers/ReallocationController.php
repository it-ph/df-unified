<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Client;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use App\Traits\ResponseTraits;
use App\Mail\ReallocateTaskEmail;
use App\Mail\ReallocateQCTaskEmail;
use Illuminate\Support\Facades\Mail;
use App\Services\ReallocationServices;
use App\Http\Requests\ReallocateQCRequest;
use App\Http\Requests\ReallocateJobRequest;
use Facades\App\Http\Helpers\CredentialsHelper;
use Facades\App\Http\Helpers\JobHistories;

class ReallocationController extends Controller
{
    use ResponseTraits;

    public function __construct()
    {
        $this->service = new ReallocationServices();
    }

    public function thecredentials()
    {
        return CredentialsHelper::get_set_credentials();
    }

    /** PENDING JOBS */
    public function pendingJobs()
    {
        $result = $this->successResponse('Tasks loaded successfully!');
        try
        {
            $result["data"] =  $this->service->loadPendingJobs();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function showJob($id)
    {
        $result = $this->successResponse('Task retrieved successfully!');
        try {
            $result["data"] = Job::query()
                ->where('id',$id)->select('id','client_id')
                ->first();
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function reallocateJob(ReallocateJobRequest $request)
    {
        $result = $this->successResponse('Task reallocated successfully!');
        try {
            $job = Job::findOrfail($request->edit_id);
            $job->update($request->except('edit_id'));

            // email notifs
            $this->sendJobReallocationEmail($job->id);

            // create history
            $client_id = $job->client_id;
            $created_by = auth()->user()->id;
            $job_id = $job->id;
            $by = auth()->user()->full_name;
            $to = $job->thedeveloper->full_name;
            $activity = "Job reallocated by ".$by. ' and assigned to '.$to;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);
        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    /** PENDING QCS */
    public function pendingQCs()
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

    public function showQC($id)
    {
        $result = $this->successResponse('QC Task retrieved successfully!');
        try {
            $result["data"] = AuditLog::query()
                ->where('id',$id)->select('id','client_id')
                ->first();
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function reallocateQC(ReallocateQCRequest $request)
    {
        $result = $this->successResponse('QC Task reallocated successfully!');
        try {
            $audit_log = AuditLog::findOrfail($request->edit_id);
            $audit_log->update($request->except('edit_id'));

            // email notifs
            $this->sendQCJobReallocationEmail($audit_log->id);

            // create history
            $client_id = $audit_log->client_id;
            $created_by = auth()->user()->id;
            $job_id = $audit_log->job_id;
            $by = auth()->user()->full_name;
            $to = $audit_log->theauditor->full_name;
            $activity = "QC Task reallocated by ".$by. ' and assigned to '.$to;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);
        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    // send reallocate job email
    public function sendJobReallocationEmail($job_id)
    {
        $job = Job::query()
            ->with([
                'thedeveloper:id,first_name,last_name,email',
            ])
            ->select('id','name','developer_id','status')
            ->where('id',$job_id)
            ->first();

        $to = $job->thedeveloper->email;
        $cc = $this->getJobEmailRecipients($job->client_id);

        if($this->is_connected())
        {
            Mail::to($to)
                ->cc($cc)
                ->queue(new ReallocateTaskEmail($job));
        }
    }

    // send email QC
    public function sendQCJobReallocationEmail($audit_log_id)
    {
        $audit_log = AuditLog::query()
            ->with([
                'thejob:id,name,developer_id',
                'thejob.thedeveloper:id,first_name,last_name,email',
                'theauditor:id,email,first_name,last_name'
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
                ->queue(new ReallocateQCTaskEmail($audit_log));
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
