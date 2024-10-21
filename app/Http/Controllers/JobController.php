<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\Event;
use App\Models\Client;
use App\Models\AuditLog;
use App\Models\JobPause;
use App\Mail\NewTaskEmail;
use App\Mail\SLAMissEmail;
use App\Mail\SentForQCEmail;
use Illuminate\Http\Request;
use App\Mail\UpdateTaskEmail;
use App\Services\JobsServices;
use App\Traits\ResponseTraits;
use App\Mail\SLAThresholdEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Services\JobsHistoryServices;
use App\Http\Requests\JobStoreRequest;
use App\Http\Requests\JobSendForQCRequest;
use App\Http\Requests\JobSubmitDetailsRequest;
use Facades\App\Http\Helpers\CredentialsHelper;
use Facades\App\Http\Helpers\TimeElapsedHelper;
use Facades\App\Http\Helpers\WorkingHoursHelper;
use Facades\App\Http\Helpers\JobHistories;
use App\Http\Controllers\GlobalVariableController;
use App\Http\Requests\JobUpdateExternalQualityRequest;

class JobController extends GlobalVariableController
{
    use ResponseTraits;

    public function __construct()
    {
        parent::__construct();
        $this->model = new Job();
        $this->service = new JobsServices();
        $this->history = new JobsHistoryServices();
    }

    public function thecredentials()
    {
        return CredentialsHelper::get_set_credentials();
    }

    public function thedevelopers()
    {
        return CredentialsHelper::get_developers();
    }


    /** PENDING JOBS */
    public function pendingJob()
    {
        $result = $this->successResponse('Jobs loaded successfully!');
        try
        {
            $result["data"] =  $this->service->loadPendingJobs();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    /** DEV */
    public function myJob()
    {
        $result = $this->successResponse('Jobs loaded successfully!');
        try
        {
            $result["data"] =  $this->service->loadDevJobs();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function view($id)
    {
        $user = $this->thecredentials();
        $job = $this->service->show($id);

        if(!$job){
            return view('errors.404');
        }

        return view('pages.admin.jobs.view.index', compact('user','job'));
    }

    public function startJob($id)
    {
        $result = $this->successResponse("Job started successfully!");
        try {
            $job = Job::findOrfail($id);
            $status = 'In Progress';

            $job->update([
                'status' => $status,
                'start_at' => Carbon::now(),
            ]);

            // email notifs
            $this->sendJobEmailUpdates($job->id);

            // create history
            $client_id = $job->client_id;
            $created_by = auth()->user()->id;
            $job_id = $job->id;
            $by = auth()->user()->full_name;
            $to = $job->thedeveloper->full_name;
            $activity = "Job started by ".$by. ', status set to '.$status;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);

        } catch (\Throwable $th) {
            $result = $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function pauseJob($id)
    {
        $result = $this->successResponse("Job paused successfully!");
        try {
            $job = Job::findOrfail($id);
            $last_status = $job->status;
            $status = 'On Hold';

            // create job pauses
            $job_pause = JobPause::create([
                'job_id' => $job->id,
                'client_id' => $job->client_id,
                'start' => Carbon::now(),
                'end' => null,
                'created_by' => auth()->user()->id,
            ]);

            $job->update([
                'last_status' => $last_status,
                'status' => $status,
            ]);

            // email notifs
            $this->sendJobEmailUpdates($job->id);

            // create history
            $client_id = $job->client_id;
            $created_by = auth()->user()->id;
            $job_id = $job->id;
            $by = auth()->user()->full_name;
            $activity = "Job paused by ".$by. ', status set to '.$status;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);

        } catch (\Throwable $th) {
            $result = $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function resumeJob($id)
    {
        $result = $this->successResponse("Job resumed successfully!");
        try {
            $job = Job::findOrfail($id);
            $status = $job->last_status;

            // stop job pause
            $job_pause = JobPause::latest()->where('job_id',$job->id)->first();
            $job_pause->update([
                'end' => Carbon::now(),
            ]);

            $job->update([
                'status' => $status,
            ]);

            // email notifs
            $this->sendJobEmailUpdates($job->id);

            // create history
            $client_id = $job->client_id;
            $created_by = auth()->user()->id;
            $job_id = $job->id;
            $by = auth()->user()->full_name;
            $activity = "Job resumed by ".$by. ', status set to '.$status;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);

        } catch (\Throwable $th) {
            $result = $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function submitDetails(JobSubmitDetailsRequest $request)
    {
        $result = $this->successResponse('Job details saved successfully!');
        try {
            Job::findOrfail($request->edit_id)->update($request->except('edit_id'));
        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    public function sendForQC(JobSendForQCRequest $request)
    {
        $result = $this->successResponse('Job Sent for QC successfully!');
        try {
            // update job status to Sent For QC
            $job = Job::findOrfail($request->edit_id);

            // increment qc round
            $qc_round = $job->qc_rounds <> null ? $job->qc_rounds + 1 : 1;

            $job->update([
                'status' => 'Sent For QC',
                'dev_comments' => $request->dev_comments,
                'qc_rounds' => $qc_round,
            ]);

            // create audit log
            AuditLog::create([
                'created_by' => auth()->user()->id,
                'client_id' => auth()->user()->client_id,
                'job_id' => $request->edit_id,
                'preview_link' => $request->preview_link,
                'self_qc' => $request->self_qc,
                'dev_comments' => $request->dev_comments,
                'qc_round' => $qc_round,
                'qc_status' => 'Pending',
            ]);

            // email notifs
            $this->sendJobEmailUpdates($job->id);

            // create history
            $client_id = $job->client_id;
            $created_by = auth()->user()->id;
            $job_id = $job->id;
            $by = auth()->user()->full_name;
            $activity = "Job sent for QC by ".$by;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);

        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->successResponse('Jobs loaded successfully!');
        try
        {
            $result["data"] =  $this->service->load();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function showHistory($id)
    {
        $result = $this->successResponse('Job History loaded successfully!');
        try {
            $result["data"] = $this->history->load($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function store(JobStoreRequest $request)
    {
        $result = $this->successResponse('Job created successfully!');
        try{
            if($request->edit_id === null)
            {
                $request['request_sla_id'] = $request->request_sla_id;
                $client_id = $request['client_id'];
                $request['created_by'] = auth()->user()->id;
                $job = Job::create($request->except(['edit_id','agreed_sla']));

                // email notifs
                $job = $job::query()
                ->with([
                    'therequesttype:id,name',
                    'therequestvolume:id,name',
                    'therequestsla:id,agreed_sla',
                    'thedeveloper:id,first_name,last_name,email',
                    'thecreatedby:id,first_name,last_name,email',
                ])
                ->select('id','name','client_id','developer_id','request_type_id','request_volume_id','request_sla_id','special_request','created_by','salesforce_link','status')
                ->where('id',$job->id)
                ->first();

                $to = $job->thedeveloper->email;
                $cc = $this->getJobEmailRecipients($client_id);

                if($this->is_connected())
                {
                    Mail::to($to)
                        ->cc($cc)
                        ->queue(new NewTaskEmail($job));
                }

                // create history
                $created_by = auth()->user()->id;
                $job_id = $job->id;
                $by = auth()->user()->full_name;
                $to = $job->thedeveloper->full_name;
                $activity = "Created by ".$by. " and assigned to ".$to;
                JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);
            }
            else
            {
                $result = $this->update($request, $request->edit_id);
            }

        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function showJob($id)
    {
        $result = $this->successResponse('Job retrieved successfully!');
        try {
            $result["data"] = Job::findOrfail($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    // edit
    public function show($id)
    {
        $user = $this->thecredentials();

        $job = $this->service->show($id);
        if(!$job){
            return view('errors.404');
        }

        return view('pages.admin.jobs.edit', compact('user','id'));
    }

    public function getData($id)
    {
        $result = $this->successResponse('Job retrieved successfully!');
        try {
            $result["data"] = $this->service->show($id);
        } catch (\Throwable $th) {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
    }

    public function update($request, $id)
    {
        $result = $this->successResponse('Job updated successfully!');
        try {
            $job = Job::where('id',$id)->first();
            $job->update($request->except(['edit_id','agreed_sla','developer','request_type','request_volume']));

            // email notifs

            // create history
            $client_id = $job->client_id;
            $created_by = auth()->user()->id;
            $job_id = $job->id;
            $by = auth()->user()->full_name;
            $activity = "Job details updated by ".$by;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);

        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    public function updateExternalQuality(JobUpdateExternalQualityRequest $request)
    {
        $result = $this->successResponse('External Quality details saved successfully!');
        try {
            $job = Job::where('id', $request->edit_id)->first();
            $job->update([
                'external_quality' => $request->external_quality,
                'c_external_quality' => $request->c_external_quality
            ]);

            // email notifs

            // create history
            $client_id = $job->client_id;
            $created_by = auth()->user()->id;
            $job_id = $job->id;
            $by = auth()->user()->full_name;
            $activity = "External Quality updated by ".$by;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);

        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    public function addSLAMissReason(Request $request)
    {
        $result = $this->successResponse('SLA Miss Reason saved successfully!');
        try {
            $job = Job::findOrfail($request->edit_id);
            $job->update([
                'sla_miss_reason' => $request->sla_miss_reason,
                'status' => 'Closed'
            ]);

            // email notifs
            $this->sendJobEmailUpdates($job->id);

            // create history
            $client_id = $job->client_id;
            $created_by = auth()->user()->id;
            $job_id = $job->id;
            $by = auth()->user()->full_name;
            $activity = "SLA Miss Reason added by ".$by;
            JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);

        } catch (\Throwable $th)
        {
            $result = $this->errorResponse($th);
        }

        return $result;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $job = Job::findOrfail($id);
        $result = $this->successResponse('Job deleted successfully!');
        try {
            $job->delete();
        } catch (\Throwable $th)
        {
            return $this->errorResponse($th);
        }

        return $this->returnResponse($result);
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

    // send sla threshold email
    public function sendSLAThresholdEmail($job_id)
    {
        $job = Job::query()
            ->with([
                'thedeveloper:id,email',
            ])
            ->select('id','name','developer_id','client_id','status')
            ->where('id',$job_id)
            ->first();

        $to = $this->getSLAThresholdEmailTORecipients($job->client_id);
        array_push($to, $job->thedeveloper->email);
        $cc = $this->getSLAThresholdEmailCCRecipients($job->client_id);

        if($this->is_connected())
        {
            Mail::to($to)
                ->cc($cc)
                ->queue(new SLAThresholdEmail($job));
        }
    }

    // sla threshold email to recipients
    public function getSLAThresholdEmailTORecipients($client_id)
    {
        $to = array();
        $tos = Client::where('id', $client_id)->first();
        if($tos) {
            $recipients = str_replace(' ','', $tos['sla_threshold_to']);
            $recipients = explode(',',$recipients);
            foreach ($recipients as $recipient) {
                if (!empty($recipient)) {
                    array_push($to, $recipient);
                }
            }
        }

        return $to;
    }

    // sla threshold email cc recipients
    public function getSLAThresholdEmailCCRecipients($client_id)
    {
        $cc = array();
        $ccs = Client::where('id', $client_id)->first();
        if($ccs) {
            $recipients = str_replace(' ','', $ccs['sla_threshold_cc']);
            $recipients = explode(',',$recipients);
            foreach ($recipients as $recipient) {
                if (!empty($recipient)) {
                    array_push($cc, $recipient);
                }
            }
        }

        return $cc;
    }

    // send sla miss email
    public function sendSLAMissEmail($job_id)
    {
        $job = Job::query()
            ->with([
                'thedeveloper:id,email',
            ])
            ->select('id','name','client_id','developer_id','status')
            ->where('id',$job_id)
            ->first();

        $to = $this->getSLAMissEmailTORecipients($job->client_id);
        array_push($to, $job->thedeveloper->email);
        $cc = $this->getSLAMissEmailCCRecipients($job->client_id);

        if($this->is_connected())
        {
            Mail::to($to)
                ->cc($cc)
                ->queue(new SLAMissEmail($job));
        }
    }

    // sla miss email to recipients
    public function getSLAMissEmailTORecipients($client_id)
    {
        $to = array();
        $tos = Client::where('id', $client_id)->first();
        if($tos) {
            $recipients = str_replace(' ','', $tos['sla_missed_to']);
            $recipients = explode(',',$recipients);
            foreach ($recipients as $recipient) {
                if (!empty($recipient)) {
                    array_push($to, $recipient);
                }
            }
        }

        return $to;
    }

    // sla miss email cc recipients
    public function getSLAMissEmailCCRecipients($client_id)
    {
        $cc = array();
        $ccs = Client::where('id', $client_id)->first();
        if($ccs) {
            $recipients = str_replace(' ','', $ccs['sla_missed_cc']);
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

    // use for testing purposes
    public function getWorkingHours()
    {

        $shift_start = "07:00:00";
        $shift_end = "07:00:00";
        $start = strtotime("08-08-2024 14:00:00");
        $end = strtotime("11-08-2024 16:28:22");
        $shift_hours = 9;
        // d-m-Y H:i:s format

        // if job is not closed, set it to current time
        // $end = null;
        // $end = $end ? $end : strtotime(Carbon::now());

        $working_hours = WorkingHoursHelper::getWorkingHours($start, $end, $shift_start, $shift_end, $shift_hours);
        $time_taken = WorkingHoursHelper::convertTime($working_hours);

        return $time_taken;
    }

    // SLA MISS NOTIFS
    public function sendSLAMissNotifs()
    {
        $ctr_sla_threshold = 0;
        $ctr_sla_miss = 0;

        $jobs = Job::with([
            'therequestsla:id,agreed_sla',
            'theclient:id,name,start,end,shift_hours,sla_threshold',
        ])
        ->select('id','request_sla_id','client_id','start_at','end_at','time_taken','sla_missed','send_p_sla_miss','send_sla_missed','status')
        ->where('status','<>','Closed')
        ->get();

        foreach($jobs as $value) {
            $start_at = $value->start_at ? date('d-M-y h:i:s a', strtotime($value->start_at)) : '';
            $end_at = $value->end_at ? date('d-M-y h:i:s a', strtotime($value->end_at)) : '';
            $agreed_sla_raw = $value->therequestsla ? $value->therequestsla->agreed_sla : '-';
            $agreed_sla = $value->therequestsla ? TimeElapsedHelper::convertTime($value->therequestsla->agreed_sla) : '-';

            if(in_array($value->status,['In Progress','Sent For QC','Bounce Back','Quality Check']))
            {
                $start_at = $value->start_at;
                $end_at = Carbon::now()->format('Y-m-d H:i:s');
                $shift_start = $value->theclient->start;
                $shift_end = $value->theclient->end;
                $pauses = [];
                $events = [];
                $pauses = $this->getJobPauses($value->client_id, $start_at, $end_at);
                $events = $this->getEvents($value->client_id, $start_at, $end_at);

                $working_hours = TimeElapsedHelper::calculateWorkingTime($start_at, $end_at, $shift_start, $shift_end, $pauses, $events);
                $time_taken = TimeElapsedHelper::convertTime($working_hours);

                $sla_missed = $working_hours > $agreed_sla_raw ? '<span class="text-danger">Yes</span>' : '<span class="text-success">No</span>';

                // send email
                $sla_threshold = $value->theclient->sla_threshold/100;
                $sla_percentage = $agreed_sla_raw * $sla_threshold;
                $p_sla_miss = $working_hours > $sla_percentage ? 1 : 0;
                if($p_sla_miss && $value->send_p_sla_miss == 0)
                {
                    $this->sendSLAThresholdEmail($value->id);
                    $value->update(['send_p_sla_miss' => 1]);
                    $ctr_sla_threshold += 1;

                    // create history
                    $client_id = $value->client_id;
                    $created_by = 0;
                    $job_id = $value->id;
                    $activity = "Reached / Exceeded SLA Threshold";
                    JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);
                }

                $sla_miss = $working_hours > $agreed_sla_raw ? 1 : 0;
                if($sla_miss && $value->send_sla_missed == 0)
                {
                    $this->sendSLAMissEmail($value->id);
                    $value->update(['send_sla_missed' => 1]);
                    $ctr_sla_miss += 1;

                    // create history
                    $client_id = $value->client_id;
                    $created_by = 0;
                    $job_id = $value->id;
                    $activity = "Missed Agreed SLA";
                    JobHistories::addNewHistory($client_id, $created_by, $job_id, $activity);
                }
            }
        }

        return 'Total SLA Threshold Email Sent: '.$ctr_sla_threshold.'<br>Total SLA Miss Email Sent: ' .$ctr_sla_miss;
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
