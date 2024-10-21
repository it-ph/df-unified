<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'events';
    protected $guarded = [];
    protected $dates = ['start','end','created_at', 'updated_at', 'deleted_at'];

    public function scopeClientEvents($query)
    {
        return $query->where('client_id',auth()->user()->client_id);
    }

    public function theclient()
    {
        return $this->belongsTo(Client::class, 'client_id')->withTrashed();
    }
}
