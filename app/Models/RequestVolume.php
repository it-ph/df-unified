<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestVolume extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'request_volumes';
    protected $guarded = [];
    protected $dates = ['created_at','updated_at','deleted_at'];

    public function thecreatedby()
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function theupdatedby()
    {
        return $this->belongsTo(User::class, 'updated_by')->withTrashed();
    }
}
