<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Job;
use App\Models\Task;
use App\Models\User;
use App\Models\Event;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Facades\App\Http\Helpers\CredentialsHelper;
use App\Http\Controllers\GlobalVariableController;

class PageController extends GlobalVariableController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function thecredentials()
    {
        return CredentialsHelper::get_set_credentials();
    }

    public function thedevelopers()
    {
        return CredentialsHelper::get_developers();
    }

    public function thedevs()
    {
        return CredentialsHelper::get_devs();
    }

    public function theauditors()
    {
        return CredentialsHelper::get_auditors();
    }

    public function theqcs()
    {
        return CredentialsHelper::get_qcs();
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

    public function updateLoginStatus()
    {
        $user = auth()->user();
        $user->update([
            'last_login_at' => Carbon::now()
        ]);
    }

    /** Dashboard */
    public function showDashboard()
    {
        $this->updateLoginStatus();
        $user = $this->thecredentials();

        $roles = $this->getRoles();
        $isAdmin = in_array('admin', $roles);

        $devs = $isAdmin ? $this->thedevelopers() : $this->thedevs();
        return view('pages.dashboard.index', compact('user','devs'));
    }

    /** Add Job */
    public function addJob()
    {
        $user = $this->thecredentials();
        return view('pages.admin.jobs.create', compact('user'));
    }

    /** Pending Jobs */
    public function showPendingJobs()
    {
        $user = $this->thecredentials();
        return view('pages.admin.jobs.pending', compact('user'));
    }

    /** Jobs */
    public function showJobs()
    {
        $user = $this->thecredentials();
        return view('pages.admin.jobs.all', compact('user'));
    }

    /** My Jobs */
    public function showMyJobs()
    {
        $this->updateLoginStatus();
        $user = $this->thecredentials();
        return view('pages.dev.jobs.list', compact('user'));
    }

    /** Quality Check */
    public function showPendingQC()
    {
        $this->updateLoginStatus();
        $user = $this->thecredentials();
        return view('pages.auditor.jobs.list', compact('user'));
    }

    /** Reallocate Job */
    public function showReallocateJob()
    {
        $user = $this->thecredentials();
        return view('pages.admin.reallocation.job.list', compact('user'));
    }

    /** Reallocate QC */
    public function showReallocateQC()
    {
        $user = $this->thecredentials();
        return view('pages.admin.reallocation.qc.list', compact('user'));
    }

    /** Events */
    public function showEvents()
    {
        $user = $this->thecredentials();
        $events = Event::query();

        $roles = $this->getRoles();
        $isAdmin = in_array('admin', $roles);

        $events = $isAdmin ? $events->get() : $events->clientevents()->get();

        return view('pages.admin.events.calendar', compact('user','events'));
    }

    /** Users */
    public function showUsers(Request $request)
    {
        $user = $this->thecredentials();
        return view('pages.admin.users.list', compact('user'));
    }

    /** JobLogReports */
    public function showJobLogReports(Request $request)
    {
        $user = $this->thecredentials();
        $roles = $this->getRoles();
        $isAdmin = in_array('admin', $roles);

        $developers = $isAdmin ? $this->thedevelopers() : $this->thedevs();
        return view('pages.admin.reports.job-log.index', compact('user','developers'));
    }

    /** AuditLogReports */
    public function showAuditLogReports(Request $request)
    {
        $user = $this->thecredentials();
        $auditors = $this->theauditors();

        $roles = $this->getRoles();
        $isAdmin = in_array('admin', $roles);

        $auditors = $isAdmin ? $this->theauditors() : $this->theqcs();
        return view('pages.admin.reports.audit-log.index', compact('user','auditors'));
    }

    /** DevReports */
    public function showDevReports(Request $request)
    {
        $user = $this->thecredentials();
        $developers = $this->thedevs();
        return view('pages.admin.reports.dev.index', compact('user','developers'));
    }

    /** Configuration */
    public function showConfiguration()
    {
        $user = $this->thecredentials();
        $email_config = Client::query()
            ->where('id', auth()->user()->client_id)
            ->select('id','name','sla_threshold','sla_threshold_to','sla_threshold_cc','sla_missed_to','sla_missed_cc','new_job_cc','qc_send_cc','daily_report_to','daily_report_cc')
            ->first();
        return view('pages.admin.configs.index', compact('user','email_config'));
    }

    /** Clients */
    public function showClients()
    {
        $user = $this->thecredentials();
        return view('pages.admin.clients.list', compact('user'));
    }

    /** Request Types */
    public function showRequestTypes()
    {
        $user = $this->thecredentials();
        return view('pages.admin.request-types.list', compact('user'));
    }

    /** Request Volumes */
    public function showRequestVolumes()
    {
        $user = $this->thecredentials();
        return view('pages.admin.request-volumes.list', compact('user'));
    }

    /** Request SLAs */
    public function showRequestSLAs()
    {
        $user = $this->thecredentials();
        return view('pages.admin.request-slas.list', compact('user'));
    }

    /** Change Password */
    public function showChangePassword()
    {
        $user = $this->thecredentials();
        return view('pages.profile.change-password', compact('user'));
    }
}
