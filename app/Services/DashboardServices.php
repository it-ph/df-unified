<?php

namespace App\Services;

use DateTime;
use Carbon\Carbon;
use App\Models\Job;
use App\Models\User;
use App\Models\Event;
use App\Models\AuditLog;
use App\Models\JobPause;
use Illuminate\Support\Facades\DB;
use Facades\App\Http\Helpers\TimeElapsedHelper;

class DashboardServices
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

    // DASHBOARD
    public function dashboard($date_from, $date_to, $client_id, $platform, $request_type_id, $developer_id)
    {
        $datastorage = [];
        $jobsQuery = Job::query();
        $audit_logsQuery = AuditLog::with([
            'thejob:id,name,site_id,platform,request_type_id',
            'thejob.thedeveloper:id,first_name,last_name',
            'theauditor:id,first_name,last_name',
        ]);

        // Apply filters conditionally
        if ($client_id !== 'all') {
            $jobsQuery->where('client_id', $client_id);
            $audit_logsQuery->where('client_id', $client_id);
        }

        if ($platform !== 'all') {
            $jobsQuery->where('platform', $platform);
            $audit_logsQuery->whereHas('thejob', function ($query) use ($platform) {
                $query->where('platform', $platform);
            });
        }

        if ($request_type_id !== 'all') {
            $jobsQuery->where('request_type_id', $request_type_id);
            $audit_logsQuery->whereHas('thejob.therequesttype', function ($query) use ($request_type_id) {
                $query->where('id', $request_type_id);
            });
        }

        if ($developer_id !== 'all') {
            $jobsQuery->where('developer_id', $developer_id);
            // $audit_logsQuery->whereHas('thejob.thedeveloper', function ($query) use ($developer_id) {
            //     $query->where('id', $developer_id);
            // });
        }

        // Date range filter
        $jobsQuery->whereBetween('created_at', [
            $date_from . ' 00:00:00',
            $date_to . ' 23:59:59'
        ]);

        $audit_logsQuery->whereBetween('created_at', [
            $date_from . ' 00:00:00',
            $date_to . ' 23:59:59'
        ]);

        // Ordering the results
        $jobsQuery->orderBy('id', 'DESC');
        $audit_logsQuery->orderBy('id', 'DESC');

        // Get roles only when needed
        $roles = $this->getRoles();
        $isAdmin = in_array('admin', $roles);

        // $jobsQuery = $isAdmin ? $jobsQuery : $jobsQuery->clientjobs();
        // $audit_logsQuery = $isAdmin ? $audit_logsQuery : $audit_logsQuery->clientqcs();

        // Clone the base query to avoid mutating the original query
        $totalJobsQuery = clone $jobsQuery;
        $closedJobsQuery = clone $jobsQuery;
        $notStartedJobsQuery = clone $jobsQuery;
        $inProgressJobsQuery = clone $jobsQuery;
        $qcJobsQuery = clone $jobsQuery;
        $internalQualityQueryPass = clone $jobsQuery;
        $externalQualityQueryPass = clone $jobsQuery;
        $internalQualityQueryFail = clone $jobsQuery;
        $externalQualityQueryFail = clone $jobsQuery;
        $qcRoundsQuery = clone $jobsQuery;
        $closeJobsByRequestTypeQuery = clone $jobsQuery;
        $jobsByRequestTypeInternalQualityPassQuery = clone $jobsQuery;
        $jobsByRequestTypeExternalQualityPassQuery = clone $jobsQuery;
        $jobsByRequestTypeInternalQualityFailQuery = clone $jobsQuery;
        $jobsByRequestTypeExternalQualityFailQuery = clone $jobsQuery;
        $devsQuery = clone $jobsQuery;

        // Aggregate data
        $totalJobs = $totalJobsQuery->count();
        $closedJobs = $closedJobsQuery->where('status', 'Closed')->count();
        $notStartedJobs = $notStartedJobsQuery->where('status', 'Not Started')->count();
        $inProgressJobs = $inProgressJobsQuery->where('status', 'In Progress')->count();
        $qcJobs = $qcJobsQuery->where('status', 'Quality Check')->count();
        $jobsSlaMet = 0;
        $jobsSlaMissed = 0;

        $jobsQuery->chunk(500, function ($jobs) use (&$jobsSlaMet, &$jobsSlaMissed) {
            foreach ($jobs as $job) {
                $slaMet = false;
                $request_sla = $job->therequestsla ? $job->therequestsla->agreed_sla : 0;

                if (in_array($job->status, ['In Progress', 'On Hold', 'Sent For QC', 'Bounce Back', 'Quality Check'])) {
                    $start_at = $job->start_at;
                    $end_at = Carbon::now()->format('Y-m-d H:i:s');
                    $shift_start = $job->theclient->start;
                    $shift_end = $job->theclient->end;

                    $pauses = $this->getJobPauses($job->id);
                    $events = $this->getEvents($job->client_id, $start_at, $end_at);

                    $working_hours = TimeElapsedHelper::calculateWorkingTime($start_at, $end_at, $shift_start, $shift_end, $pauses, $events);
                    $slaMet = $working_hours <= $request_sla;
                } else {
                    // For closed jobs, check the already calculated fields
                    $slaMet = !$job->sla_missed;
                }

                if ($slaMet) {
                    $jobsSlaMet++;
                } else {
                    $jobsSlaMissed++;
                }
            }
        });

        // Internal and External Quality %
        $internalQualityPass = $internalQualityQueryPass->where('internal_quality', 'Pass')->count();
        $internalQualityFail = $internalQualityQueryFail->where('internal_quality', 'Fail')->count();
        $externalQualityPass = $externalQualityQueryPass->where('external_quality', 'Pass')->count();
        $externalQualityFail = $externalQualityQueryFail->where('external_quality', 'Fail')->count();

        $totalInternalQuality = $internalQualityPass + $internalQualityFail;
        $totalExternalQuality = $externalQualityPass + $externalQualityFail;

        $internalQualityPercentage = $totalInternalQuality > 0 ? ($internalQualityPass / $totalInternalQuality) * 100 : null;
        $externalQualityPercentage = $totalExternalQuality > 0 ? ($externalQualityPass / $totalExternalQuality) * 100 : null;

        // Group Closed Jobs by Request Type for Donut Chart and Bar Chart
        $jobsByRequestType = $closeJobsByRequestTypeQuery->with([
                'therequesttype:id,name',
            ])
            ->where('status', 'Closed')
            ->select('request_type_id', DB::raw('count(*) as total'))
            ->groupBy('request_type_id')
            ->get()
            ->map(function($job) {
                return [
                    'request_type' => optional($job->therequesttype)->name,
                    'total' => $job->total,
                ];
            })
            ->toArray();

        // Group Jobs by QC Rounds for Donut Chart
        $jobsByQCRounds = $qcRoundsQuery->where('qc_rounds', '<>', null)
            ->select('qc_rounds', DB::raw('count(*) as total'))
            ->groupBy('qc_rounds')
            ->get()
            ->map(function($job) {
                return [
                    'qc_rounds' => $job->qc_rounds,
                    'total' => $job->total,
                ];
            })
            ->toArray();

        // Internal Quality Summary %
        $internalQualitySummaryOutput = $this->getQualitySummaryOutput('internal_quality', $jobsByRequestTypeInternalQualityPassQuery, $jobsByRequestTypeInternalQualityFailQuery);

        // External Quality Summary %
        $externalQualitySummaryOutput = $this->getQualitySummaryOutput('external_quality', $jobsByRequestTypeExternalQualityPassQuery, $jobsByRequestTypeExternalQualityFailQuery);

        // Devs Table
        $devsArray = $this->getDevsTableData($devsQuery);

        // Auditors Table
        $qcsArray = $this->getAuditorsTableData($audit_logsQuery);

        // Prepare the data for the dashboard
        $datastorage = [
            'total_jobs' => $totalJobs,
            'closed_jobs' => $closedJobs,
            'not_started_jobs' => $notStartedJobs,
            'in_progress_jobs' => $inProgressJobs,
            'qc_jobs' => $qcJobs,
            'jobs_sla_met' => $jobsSlaMet,
            'jobs_sla_missed' => $jobsSlaMissed,
            'jobs_qced' => $totalInternalQuality,
            'internal_quality_pass' => $internalQualityPass,
            'internal_quality_fail' => $internalQualityFail,
            'internal_quality_summary' => number_format($internalQualityPercentage, 2) . '%',
            'external_quality_summary' => number_format($externalQualityPercentage, 2) . '%',
            'jobs_by_qc_rounds' => $jobsByQCRounds,
            'jobs_by_request_type' => $jobsByRequestType,
            'internal_qc_summary_by_request_type' => $internalQualitySummaryOutput,
            'external_qc_summary_by_request_type' => $externalQualitySummaryOutput,
            'devs' => $devsArray,
            'auditors' => $qcsArray,
        ];

        return $datastorage;
    }

    // get internal quality summary %
    public function getQualitySummaryOutput($quality, $passQuery, $failQuery)
    {
        // Group and aggregate quality data by request type
        $qualityByRequestType = $passQuery->with([
                'therequesttype:id,name',
            ])->where($quality, 'Pass')
            ->select('request_type_id', DB::raw('count(*) as total_pass'))
            ->groupBy('request_type_id')
            ->get()
            ->keyBy('request_type_id');

        $qualityFailByRequestType = $failQuery->with([
                'therequesttype:id,name',
            ])->where($quality, 'Fail')
            ->select('request_type_id', DB::raw('count(*) as total_fail'))
            ->groupBy('request_type_id')
            ->get()
            ->keyBy('request_type_id');

        // Combine pass and fail counts and prepare output
        $qualitySummaryOutput = [];
        $qualityByRequestType->each(function($passItem) use ($qualityFailByRequestType, &$qualitySummaryOutput) {
            $requestTypeId = $passItem->request_type_id;
            $failItem = $qualityFailByRequestType->get($requestTypeId);
            $totalPass = $passItem->total_pass;
            $totalFail = $failItem ? $failItem->total_fail : 0;
            $totalJobs = $totalPass + $totalFail;
            $percentagePass = $totalJobs > 0 ? ($totalPass / $totalJobs) * 100 : null;

            // Fetch the request type name
            $requestTypeName = $passItem->therequesttype->name;

            // Format output as "request_type_name, summary"
            $qualitySummaryOutput[] = [
                'request_type' => $requestTypeName,
                'summary' => $percentagePass
            ];
        });

        return $qualitySummaryOutput;
    }

    // get devs table data
    public function getDevsTableData($query)
    {
        $devs = $query->with(['thedeveloper:id,first_name,last_name'])
            ->where('status', 'Closed')
            ->select(
                'developer_id',
                DB::raw('COUNT(*) as num_closed_jobs'),
                DB::raw('SUM(CASE WHEN sla_missed = 1 THEN 1 ELSE 0 END) as num_sla_missed'),
                DB::raw('SUM(CASE WHEN internal_quality = "Pass" THEN 1 ELSE 0 END) as job_qc_pass'),
                DB::raw('SUM(CASE WHEN internal_quality = "Fail" THEN 1 ELSE 0 END) as job_qc_fail'),
                DB::raw('SUM(TIME_TO_SEC(time_taken)) as total_seconds'),
                DB::raw('ROUND(SUM(CASE WHEN internal_quality = "Pass" THEN 1 ELSE 0 END) / COUNT(*) * 100, 2) as quality_score')
            )
            ->groupBy('developer_id')
            ->get();

        // Initialize totals array
        $totals = [
            'developer_name' => 'Total',
            'num_closed_jobs' => 0,
            'num_sla_missed' => 0,
            'job_qc_pass' => 0,
            'job_qc_fail' => 0,
            'total_seconds' => 0,
            'quality_score_sum' => 0,  // Sum of all quality scores
            'num_devs' => 0,  // Number of developers
            'total_avg_dev_time' => 0, // Sum of all developers' average time taken
        ];

        // Prepare the final array
        $devsArray = $devs->map(function ($dev) use (&$totals) {
            // Add up totals
            $totals['num_closed_jobs'] += $dev->num_closed_jobs;
            $totals['num_sla_missed'] += $dev->num_sla_missed;
            $totals['job_qc_pass'] += $dev->job_qc_pass;
            $totals['job_qc_fail'] += $dev->job_qc_fail;
            $totals['total_seconds'] += $dev->total_seconds;
            $totals['quality_score_sum'] += $dev->quality_score;
            $totals['num_devs']++;

            // Calculate average development time per developer
            $avgSeconds = $dev->num_closed_jobs > 0 ? $dev->total_seconds / $dev->num_closed_jobs : 0;
            $totals['total_avg_dev_time'] += $avgSeconds;

            // Format the average development time
            $avg_dev_time = $this->formatTime($avgSeconds);

            return [
                'developer_name' => $dev->thedeveloper->full_name,
                'num_closed_jobs' => $dev->num_closed_jobs,
                'num_sla_missed' => $dev->num_sla_missed,
                'job_qc_pass' => $dev->job_qc_pass,
                'job_qc_fail' => $dev->job_qc_fail,
                'quality_score' => $dev->quality_score,
                'total_seconds' => $dev->total_seconds,
                'avg_dev_time' => $avg_dev_time,
            ];
        })->toArray();

        // Calculate overall quality score and average development time for totals
        if ($totals['num_devs'] > 0) {
            $totals['quality_score'] = number_format($totals['quality_score_sum'] / $totals['num_devs'], 2);

            // Calculate the average of all developers' average development times
            $totals['avg_dev_time'] = $totals['num_devs'] > 0 ? $totals['total_avg_dev_time'] / $totals['num_devs'] : 0;

            // Format the total average development time
            $totals['avg_dev_time'] = $this->formatTime($totals['avg_dev_time']);
        }

        // Append totals to the developers' array
        $devsArray[] = $totals;

        return $devsArray;
    }

    // get auditors table data
    public function getAuditorsTableData($query)
    {
        $qcs = $query->where('qc_status', '<>', 'Pending')
            ->select(
                'auditor_id',
                DB::raw('COUNT(*) as num_qc_request'),
                DB::raw('SUM(CASE WHEN qc_status = "Pass" THEN 1 ELSE 0 END) as qc_pass'),
                DB::raw('SUM(CASE WHEN qc_status = "Fail" THEN 1 ELSE 0 END) as qc_fail'),
                DB::raw('SUM(TIME_TO_SEC(time_taken)) as total_seconds')
            )
            ->groupBy('auditor_id')
            ->get();

        // Initialize totals_qc array
        $totals_qc = [
            'auditor_name' => 'Total',
            'num_qc_request' => 0,
            'qc_pass' => 0,
            'qc_fail' => 0,
            'total_seconds' => 0,
            'num_auditors' => 0,  // Number of auditors
            'total_avg_qc_time' => 0, // Sum of all auditors' average time taken
        ];

        // Prepare the final array
        $qcsArray = $qcs->map(function ($qc) use (&$totals_qc) {
            // Add up totals_qc
            $totals_qc['num_qc_request'] += $qc->num_qc_request;
            $totals_qc['qc_pass'] += $qc->qc_pass;
            $totals_qc['qc_fail'] += $qc->qc_fail;
            $totals_qc['total_seconds'] += $qc->total_seconds;
            $totals_qc['num_auditors']++;

            // Calculate average QC time per auditor
            $avgSeconds = $qc->num_qc_request > 0 ? $qc->total_seconds / $qc->num_qc_request : 0;
            $totals_qc['total_avg_qc_time'] += $avgSeconds;

            // Format the average QC time
            $avg_qc_time = $this->formatTime($avgSeconds);

            return [
                'auditor_name' => $qc->theauditor->full_name,
                'num_qc_request' => $qc->num_qc_request,
                'qc_pass' => $qc->qc_pass,
                'qc_fail' => $qc->qc_fail,
                'total_seconds' => $qc->total_seconds,
                'avg_qc_time' => $avg_qc_time,
            ];
        })->toArray();

        // Calculate overall average QC time for totals
        if ($totals_qc['num_auditors'] > 0) {
            $avg_qc_time_seconds = $totals_qc['total_avg_qc_time'] / $totals_qc['num_auditors'];

            // Format the total average QC time
            $totals_qc['avg_qc_time'] = $this->formatTime($avg_qc_time_seconds);
        }

        // Append totals_qc to the QC array
        $qcsArray[] = $totals_qc;

        return $qcsArray;
    }

    // Helper function to format time in HH:MM:SS
    public function formatTime($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
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
