<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tasks';
    protected $guarded = [];
    protected $dates = ['start_at','end_at','created_at', 'updated_at', 'deleted_at'];

    public function scopeClientJobs($query)
    {
        return $query->where('tasks.client_id', auth()->user()->client_id);
    }

    public function scopeDevs($query)
    {
        return $query->where('developer_id', auth()->user()->id);
    }

    public function theclient()
    {
        return $this->belongsTo(Client::class, 'client_id')->withTrashed();
    }

    public function thedeveloper()
    {
        return $this->belongsTo(User::class, 'developer_id')->withTrashed();
    }

    public function theauditor()
    {
        return $this->belongsTo(User::class, 'auditor_id')->withTrashed();
    }

    public function thecreatedby()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function therequesttype()
    {
        return $this->belongsTo(RequestType::class,'request_type_id')->withTrashed();
    }

    public function therequestvolume()
    {
        return $this->belongsTo(RequestVolume::class, 'request_volume_id')->withTrashed();
    }

    public function therequestsla()
    {
        return $this->belongsTo(RequestSLA::class,'request_sla_id')->withTrashed();
    }

    public function theauditlogs()
    {
        // return $this->hasMany(AuditLog::class,'job_id')->where('qc_status','<>',null);
        return $this->hasMany(AuditLog::class,'job_id');
    }

    public function thehistories()
    {
        return $this->hasMany(JobHistory::class, 'job_id');
    }
}
