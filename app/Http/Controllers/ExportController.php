<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Client;
use App\Mail\DevReportEmail;
use Illuminate\Http\Request;
use App\Exports\JobLogExport;
use App\Services\JobsServices;
use App\Exports\AuditLogExport;
use App\Exports\SLATemplateExport;
use App\Exports\UserTemplateExport;
use App\Services\AuditLogsServices;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\DevelopmentReportExport;
use App\Services\DevelopmentReportServices;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->jobs = new JobsServices();
        $this->audit_logs = new AuditLogsServices();
        $this->devs = new DevelopmentReportServices();
    }

    public function getRoles()
    {
        $user = User::with('theroles:id,user_id,name')->findOrFail(auth()->user()->id);
        $roles = [];
        foreach ($user->theroles as $role) {
            array_push($roles, $role->name);
        }

        return $roles;
    }

    public function exportJobs(Request $request)
    {
        $date_range_selected = explode("to", $request['daterange']);

        $request['date_from'] = trim($date_range_selected[0]);
        $request['date_to'] = trim($date_range_selected[1]);

        $date_from =  Carbon::parse($request['date_from'])->format('Y-m-d');
        $date_to =  Carbon::parse($request['date_to'])->format('Y-m-d');

        // set filename based on date filter
        if($date_from == $date_to)
        {
            $filename = "JOB_LOG_REPORT_". $date_from .".xlsx";
        }else
        {
            $filename = "JOB_LOG_REPORT_". $date_from ." to ".$date_to.".xlsx";
        }

        $jobs = $this->jobs->report($date_from, $date_to, $request->client_id, $request->platform, $request->request_type_id, $request->developer_id, $request->status);

        $roles = $this->getRoles();
        $isAdmin = in_array('admin', $roles);

        return Excel::download(new JobLogExport($jobs, $isAdmin), $filename);
    }

    public function exportAuditLogs(Request $request)
    {
        $date_range_selected = explode("to", $request['daterange']);

        $request['date_from'] = trim($date_range_selected[0]);
        $request['date_to'] = trim($date_range_selected[1]);

        $date_from =  Carbon::parse($request['date_from'])->format('Y-m-d');
        $date_to =  Carbon::parse($request['date_to'])->format('Y-m-d');

        // set filename based on date filter
        if($date_from == $date_to)
        {
            $filename = "AUDIT_LOG_REPORT_". $date_from .".xlsx";
        }else
        {
            $filename = "AUDIT_LOG_REPORT_". $date_from ." to ".$date_to.".xlsx";
        }

        $audit_logs = $this->audit_logs->report($date_from, $date_to, $request->client_id, $request->platform, $request->request_type_id, $request->auditor_id, $request->status);

        $roles = $this->getRoles();
        $isAdmin = in_array('admin', $roles);

        return Excel::download(new AuditLogExport($audit_logs,$isAdmin), $filename);
    }

    public function exportDevReports(Request $request)
    {
        $date_range_selected = explode("to", $request['daterange']);

        $request['date_from'] = trim($date_range_selected[0]);
        $request['date_to'] = trim($date_range_selected[1]);

        $date_from =  Carbon::parse($request['date_from'])->format('Y-m-d');
        $date_to =  Carbon::parse($request['date_to'])->format('Y-m-d');

        // set filename based on date filter
        if($date_from == $date_to)
        {
            $filename = "Web Development_Report_".$request->platform."_".$date_from." as of ".Carbon::now()->format('Y-m-d').".xlsx";
        }else
        {
            $filename = "Web Development_Report_".$request->platform."_".$date_from." to ".$date_to." as of ".Carbon::now()->format('Y-m-d').".xlsx";
        }

        $devs = $this->devs->report($date_from, $date_to, $request->client_id, $request->platform);

        $roles = $this->getRoles();
        $isAdmin = in_array('admin', $roles);

        $total = $devs['total'];
        $completed = $devs['completed'];
        $sla_met = $devs['sla_met'];
        $pending_external_qc =$devs['pending_external_qc'];

        $client_name = auth()->user()->theclient ? auth()->user()->theclient->name : 'ADMIN';

        // Generate and store the Excel file
        $filePath = 'public/exports/'.$client_name.'/'.auth()->user()->email.'/'.$filename;
        Excel::store(new DevelopmentReportExport($devs['devs'],$isAdmin), $filePath, 'local');

        // Retrieve content to display in email
        $content = $devs['devs'];

        $to = $isAdmin && auth()->user()->client_id == null ? auth()->user()->email : $this->getDevReportEmailTORecipients(auth()->user()->client_id);
        $cc = $isAdmin && auth()->user()->client_id == null ? auth()->user()->email : $this->getDevReportEmailCCRecipients(auth()->user()->client_id);

        if($this->is_connected())
        {

            Mail::to($to)
                ->cc($cc)
                ->queue(new DevReportEmail($devs['devs'], $filename, $filePath, $content, $total, $completed, $sla_met, $pending_external_qc, $isAdmin));
        }

        // return Excel::download(new DevelopmentReportExport($devs), $filename);
    }

    // development report email to recipients
    public function getDevReportEmailTORecipients($client_id)
    {
        $to = array();
        $tos = Client::where('id', $client_id)->first();
        if($tos) {
            $recipients = str_replace(' ','', $tos['daily_report_to']);
            $recipients = explode(',',$recipients);
            foreach ($recipients as $recipient) {
                if (!empty($recipient)) {
                    array_push($to, $recipient);
                }
            }
        }

        return $to;
    }

    // development report email cc recipients
    public function getDevReportEmailCCRecipients($client_id)
    {
        $cc = array();
        $ccs = Client::where('id', $client_id)->first();
        if($ccs) {
            $recipients = str_replace(' ','', $ccs['daily_report_cc']);
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

    // User Template
    public function userTemplate() {
        $roles = $this->getRoles();

        return Excel::download(new UserTemplateExport($roles), 'user-upload-template.xlsx');
    }

    // SLA Template
    public function slaTemplate() {
        $roles = $this->getRoles();

        return Excel::download(new SLATemplateExport($roles), 'sla-upload-template.xlsx');
    }
}
