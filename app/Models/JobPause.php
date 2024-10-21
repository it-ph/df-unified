<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPause extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'job_pauses';
    protected $guarded = [];
    protected $dates = ['start','end','created_at','updated_at','deleted_at'];

    public function scopeClientJobPauses($query)
    {
        return $query->where('client_id',auth()->user()->client_id);
    }

    public function thejob()
    {
        return $this->belongsTo(Job::class, 'job_id')->withTrashed();
    }

    public function theclient()
    {
        return $this->belongsTo(Client::class, 'client_id')->withTrashed();
    }

    public function thecreatedby()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }
}
