<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestSLA extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'request_slas';
    protected $guarded = [];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function therequesttype()
    {
        return $this->belongsTo(RequestType::class, 'request_type_id')->withTrashed();
    }

    public function therequestvolume()
    {
        return $this->belongsTo(RequestVolume::class, 'request_volume_id')->withTrashed();
    }

    public function thecreatedby()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function theupdatedby()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
