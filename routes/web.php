<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\JobControllerAPI;
use App\Http\Controllers\ReportController;;
use App\Http\Controllers\UserControllerAPI;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClientControllerAPI;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RequestSLAController;
use App\Http\Controllers\AuditlogControllerAPI;
use App\Http\Controllers\RequestTypeController;
use App\Http\Controllers\ReallocationController;
use App\Http\Controllers\ReportJobsControllerAPI;
use App\Http\Controllers\RequestSLAControllerAPI;
use App\Http\Controllers\RequestVolumeController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\RequestTypeControllerAPI;
use App\Http\Controllers\RequestVolumeControllerAPI;
use App\Http\Controllers\ReallocationQCControllerAPI;
use App\Http\Controllers\ReallocationJobControllerAPI;
use App\Http\Controllers\ReportAuditLogsControllerAPI;

// LOGIN
Auth::routes(['register' => false]);


Route::get('/', function () {
    return redirect()->guest('/login');
});

// SSO
// Route::group(['middleware' => ['web', 'guest']], function(){
//     Route::get('login', [AuthController::class, 'login'])->name('login')->middleware('csp');
//     Route::get('connect', [AuthController::class, 'connect'])->name('connect');
// });

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::get('unauthorized', function () {
    return view('errors.401');
})->name('unauthorized');

Route::get('404', function () {
    return view('errors.404');
})->name('404');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

/**
 *
 * REDIS CACHE CLEAR
 */
Route::GET('redis/clear-cache', function () {
    Redis::flushdb();
    echo 'redis cache cleared successfully!';
});

// 2FA
Route::GET('verify/resend', [TwoFactorController::class, 'resend'])->name('verify.resend');
Route::GET('verify', [TwoFactorController::class, 'index'])->name('verify.index');;
Route::POST('verify', [TwoFactorController::class, 'store'])->name('verify.store');;

// FORGOT PASSWORD
Route::GET('forgot-password', [ForgotPasswordController::class,'forgotPassword'])->name('forgot-password');
Route::POST('forgot-password', [ForgotPasswordController::class,'submitForgotPassword'])->name('forgot.password.submit');
Route::GET('forgot-password-verify/{request_key}', [ForgotPasswordController::class,'verifyForgotPassword'])->name('forgot.password.verify');
Route::GET('successful-reset/{userId}/{password}/{request_key}', [ForgotPasswordController::class,'successfulForgotPassword'])->name('successful.verify.forgot.password');

// SEND SLA MISS AND P_SLA MISS
Route::GET('jobs/sla_miss_notifs', [JobController::class, 'sendSLAMissNotifs']);

// GET TIMETAKE HELPER TEST
Route::GET('jobs/timetaken', [JobControllerAPI::class, 'getTimeTaken']);

/**
 *  START OF AUTHORIZE & ACTIVE USERS
 */

Auth::routes();
Route::group(['middleware' => ['auth','twofactor','web','active.user']],function () {

    // CHANGE PASSWORD
    Route::GET('change-password', [PageController::class,'showChangePassword']);
    Route::POST('change-password', [UserController::class,'changePassword'])->name('change.password');

    Route::group(['middleware' => ['changepassword']], function ()
    {
        // DASHBOARD
        Route::get('home', [PageController::class, 'showDashboard'])->name('home');
        Route::get('index', [PageController::class, 'showDashboard'])->name('index');

        Route::group(['prefix' => 'dashboard'],
        function ()
        {
            Route::post('index', [DashboardController::class,'index'])->name('dashboard.index');
        });

        Route::get('recipients', [JobController::class, 'getJobEmailRecipients'])->name('index');

        // VIEW JOB
        Route::get('/viewjob/{id}', [JobController::class, 'view'])->name('job.view');
        Route::get('/job/show/history/{id}', [JobController::class,'showHistory'])->name('job.show.history');

        // QUALITY CHECK
        Route::get('/viewqualitycheck/{id}', [AuditLogController::class, 'viewQCLog'])->name('view.qc');

        // MY JOBS - DEV ACCESS
        Route::group(['middleware' => ['role:admin,designer']], function ()
        {
            Route::get('/myjobs', [PageController::class, 'showMyJobs'])->name('myjobs.index');
            Route::group(['prefix' => 'myjob'],
            function ()
            {
                Route::get('api/all', [JobControllerAPI::class,'getAllDevJobs'])->name('api.get.alldevjobs');
                Route::get('/all', [JobController::class,'myJob'])->name('myjob.index');
                Route::get('/start/{id}', [JobController::class,'startJob'])->name('myjob.start');
                Route::get('/pause/{id}', [JobController::class,'pauseJob'])->name('myjob.pause');
                Route::get('/resume/{id}', [JobController::class,'resumeJob'])->name('myjob.resume');
                Route::post('/submitdetails', [JobController::class,'submitDetails'])->name('myjob.submit-details');
                Route::post('/sendforqc', [JobController::class,'sendforqc'])->name('myjob.send-for-qc');
                // SLA MISS REASON
                Route::post('/slamissreason', [JobController::class,'addSLAMissReason'])->name('job.update.slamissreason');
            });
        });

        // QUALITY CHECK - AUDITOR ACCESS
        Route::group(['middleware' => ['role:admin,proofreader']], function ()
        {
            Route::get('/qualitycheck', [PageController::class, 'showPendingQC'])->name('qualitycheck.index');
            Route::get('/qualitycheck/{id}', [AuditLogController::class, 'viewQC'])->name('job.qc');
            Route::group(['prefix' => 'pendingqc'],
            function ()
            {
                Route::get('api/all', [AuditlogControllerAPI::class,'getAllPendingQCs'])->name('api.get.allpendingqcs');
                Route::get('/all', [AuditLogController::class,'qualityCheck'])->name('pendingqc.index');
                Route::get('/pick/{id}', [AuditLogController::class,'pickJob'])->name('pendingqc.pick');
                Route::get('/release/{id}', [AuditLogController::class,'releaseJob'])->name('pendingqc.realease');
                Route::post('/submitfeedback', [AuditLogController::class,'submitFeedback'])->name('pendingqc.submit-eedback');
            });
        });

        Route::group(['middleware' => ['role:admin,manager,supervisor']], function ()
        {
            // JOBS
            Route::get('/jobs', [PageController::class, 'showJobs'])->name('jobs.index');
            Route::group(['prefix' => 'job'],
            function ()
            {
                Route::get('api/all', [JobControllerAPI::class,'getAllJobs'])->name('api.get.alljobs');
                Route::get('/all', [JobController::class,'index'])->name('job.index');
                Route::get('/create', [PageController::class,'addJob'])->name('job.create');
                Route::post('/store', [JobController::class,'store'])->name('job.store');
                Route::get('/show/{id}', [JobController::class,'show'])->name('job.show');
                Route::get('/get_data/{id}', [JobController::class,'getData'])->name('job.get_data');
                Route::post('/update/{id}', [JobController::class,'update'])->name('job.update');
                Route::post('/delete/{id}', [JobController::class,'destroy'])->name('job.delete');
                Route::post('/externalquality', [JobController::class,'updateExternalQuality'])->name('job.update.externalquality');
                Route::get('/working-hours', [JobController::class,'getWorkingHours']);

                // GET DEVELOPERS
                Route::get('get_devs/{client_id}', [UserController::class,'getDevs'])->name('users.get_devs');
            });

            // PENDING JOBS
            Route::get('/pendingjobs', [PageController::class, 'showPendingJobs'])->name('pendingjobs.index');
            Route::group(['prefix' => 'pendingjob'],
            function ()
            {
                Route::get('api/all', [JobControllerAPI::class,'getAllPendingJobs'])->name('api.get.allpendingjobs');
                Route::get('/all', [JobController::class,'pendingjob'])->name('pendingjob.index');
                Route::get('/show/{id}', [JobController::class,'show'])->name('pendingjob.show');
            });

            // REALLOCATION
            Route::group(['prefix' => 'reallocation'],
            function ()
            {
                // JOB
                Route::get('/job', [PageController::class, 'showReallocateJob'])->name('reallocate.job.index');
                Route::group(['prefix' => 'jobs'],
                function ()
                {
                    Route::get('api/all', [ReallocationJobControllerAPI::class,'getAllPendingJobs'])->name('api.get.reallocate.pendingjobs');
                    Route::get('/all', [ReallocationController::class,'pendingJobs'])->name('reallocate.pendingjobs');
                    Route::get('/show/{id}', [ReallocationController::class,'showJob'])->name('reallocate.show.job');
                    Route::post('/update', [ReallocationController::class,'reallocateJob'])->name('reallocate.reallocate.job');
                });

                // QC
                Route::get('/qc', [PageController::class, 'showReallocateQC'])->name('reallocate.qc.index');
                Route::group(['prefix' => 'qcs'],
                function ()
                {
                    Route::get('api/all', [ReallocationQCControllerAPI::class,'getAllPendingQCs'])->name('api.get.reallocate.pendingqcs');
                    Route::get('/all', [ReallocationController::class,'pendingQCs'])->name('reallocate.pendingqcs');
                    Route::get('/show/{id}', [ReallocationController::class,'showQC'])->name('reallocate.show.qc');
                    Route::post('/update', [ReallocationController::class,'reallocateQC'])->name('reallocate.reallocate.qc');
                });

                // GET DEVELOPERS
                Route::get('get_devs/{client_id}', [UserController::class,'getDevs'])->name('users.get_devs');

                // GET AUDITORS
                Route::get('get_auditors/{client_id}', [UserController::class,'getAuditors'])->name('users.get_auditors');
            });

            // EVENTS
            Route::get('events', [PageController::class, 'showEvents']);
            Route::group(['prefix' => 'event'],
            function ()
            {
                Route::get('/all', [EventController::class,'index'])->name('event.index');
                Route::post('/store', [EventController::class,'store'])->name('event.store');
                Route::get('/show/{id}', [EventController::class,'show'])->name('event.show');
                Route::post('/update/{id}', [EventController::class,'update'])->name('event.update');
                Route::post('/delete/{id}', [EventController::class,'destroy'])->name('event.delete');
                Route::get('/isconflict', [EventController::class,'isconflict'])->name('event.isconflict');
            });

            // CLIENTS for TL / MANAGER
            Route::get('/configuration', [PageController::class, 'showConfiguration'])->name('configuration.index');
            Route::get('config/show/{id}', [ClientController::class,'show'])->name('client.config.show');
            Route::post('client/updateEmailConfig', [ClientController::class,'updateEmailConfig'])->name('client.updateEmailConfig');


            // REQUEST
            Route::group(['prefix' => 'request'],
            function ()
            {
                // TYPES
                Route::get('/types', [PageController::class, 'showRequestTypes'])->name('request-types.index');
                Route::group(['prefix' => 'type'],
                function ()
                {
                    Route::get('api/all', [RequestTypeControllerAPI::class,'getAllTypes'])->name('api.get.types');
                    Route::get('/all', [RequestTypeController::class,'index'])->name('request-type.index');
                    Route::post('/store', [RequestTypeController::class,'store'])->name('request-type.store');
                    Route::get('/show/{id}', [RequestTypeController::class,'show'])->name('request-type.show');
                    Route::post('/update/{id}', [RequestTypeController::class,'update'])->name('request-type.update');
                    Route::post('/delete/{id}', [RequestTypeController::class,'destroy'])->name('request-type.delete');
                });

                // VOLUMES
                Route::get('/volumes', [PageController::class, 'showRequestVolumes'])->name('request-volumes.index');
                Route::group(['prefix' => 'volume'],
                function ()
                {
                    Route::get('api/all', [RequestVolumeControllerAPI::class,'getAllVolumes'])->name('api.get.volumes');
                    Route::get('/all', [RequestVolumeController::class,'index'])->name('request-volume.index');
                    Route::post('/store', [RequestVolumeController::class,'store'])->name('request-volume.store');
                    Route::get('/show/{id}', [RequestVolumeController::class,'show'])->name('request-volume.show');
                    Route::post('/update/{id}', [RequestVolumeController::class,'update'])->name('request-volume.update');
                    Route::post('/delete/{id}', [RequestVolumeController::class,'destroy'])->name('request-volume.delete');
                });

                // SLAS
                Route::get('/slas', [PageController::class, 'showRequestSLAs'])->name('request-slas.index');
                Route::group(['prefix' => 'sla'],
                function ()
                {
                    Route::get('api/all', [RequestSLAControllerAPI::class,'getAllSLAs'])->name('api.get.slas');
                    Route::get('/all', [RequestSLAController::class,'index'])->name('request-sla.index');
                    Route::post('/store', [RequestSLAController::class,'store'])->name('request-sla.store');
                    Route::get('/show/{id}', [RequestSLAController::class,'show'])->name('request-sla.show');
                    Route::get('/get/{typeId}/{volumeId}', [RequestSLAController::class,'get'])->name('request-sla.get');
                    Route::post('/update/{id}', [RequestSLAController::class,'update'])->name('request-sla.update');
                    Route::post('/delete/{id}', [RequestSLAController::class,'destroy'])->name('request-sla.delete');
                });
            });

            // REPORTS
            Route::group(['prefix' => 'reports'],
            function ()
            {
                Route::get('/joblogs', [PageController::class, 'showJobLogReports'])->name('reports.joblogs');
                Route::get('/auditlogs', [PageController::class, 'showAuditLogReports'])->name('reports.auditlogs');
                Route::get('/development', [PageController::class, 'showDevReports'])->name('reports.development');

                Route::group(['prefix' => 'export'],
                function ()
                {
                    // job logs export
                    Route::post('api/jobs', [ReportJobsControllerAPI::class,'getReportJobs'])->name('api.get.report.jobs');
                    Route::post('/jobs',[ExportController::class, 'exportJobs'])->name('export.jobs');

                    // audit logs export
                    Route::post('api/auditlogs', [ReportAuditLogsControllerAPI::class,'getReportAuditLogs'])->name('api.get.report.auditlogs');
                    Route::post('/auditlogs',[ExportController::class, 'exportAuditLogs'])->name('export.auditlogs');

                    // development reports
                    Route::post('/devs',[ExportController::class, 'exportDevReports'])->name('export.devs');
                });
            });
        });

        // USERS
        Route::group(['middleware' => ['role:admin,manager']], function ()
        {
            Route::get('users', [PageController::class, 'showUsers'])->name('users.index');
            Route::group(['prefix' => 'user'],
            function ()
            {
                Route::get('api/all', [UserControllerAPI::class,'getAllUsers'])->name('api.get.users');
                Route::get('/all', [UserController::class,'index'])->name('user.index');
                Route::post('/store', [UserController::class,'store'])->name('user.store');
                Route::get('/show/{id}', [UserController::class,'show'])->name('user.show');
                Route::post('/update/{id}', [UserController::class,'update'])->name('user.update');
                Route::post('/delete/{id}', [UserController::class,'destroy'])->name('user.delete');
            });
        });

        // IMPORTS / EXPORTS
        Route::group(['middleware' => ['role:admin,manager']], function ()
        {
            // USERS
            Route::get('user/export/template', [ExportController::class,'userTemplate'])->name('user.export.template');
            Route::post('user/import', [ImportController::class, 'importUser'])->name('user.import');

            // REQUST TYPES
            Route::get('sla/export/template', [ExportController::class,'slaTemplate'])->name('sla.export.template');
            Route::post('sla/import', [ImportController::class, 'importsla'])->name('sla.import');
        });

        // CLIENTS for ADMIN ONLY
        Route::group(['middleware' => ['role:admin,manager']], function ()
        {
            Route::get('/clients', [PageController::class, 'showClients'])->name('clients.index');
            Route::group(['prefix' => 'client'],
            function ()
            {
                Route::get('api/all', [ClientControllerAPI::class,'getAllClients'])->name('api.get.clients');
                Route::get('/all', [ClientController::class,'index'])->name('client.index');
                Route::post('/store', [ClientController::class,'store'])->name('client.store');
                Route::get('/show/{id}', [ClientController::class,'show'])->name('client.show');
                Route::post('/update/{id}', [ClientController::class,'update'])->name('client.update');
                Route::post('/delete/{id}', [ClientController::class,'destroy'])->name('client.delete');
            });
        });
    });

});
/**
 * END OF AUTHORIZE & ACTIVE USERS
 *
 */

//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);
