<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

    protected $table = 'users';
    protected $dates = ['two_factor_expires_at','deleted_at','created_at','updated_at','last_login_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return ucfirst($this->first_name . " " . $this->last_name);
    }

    public function scopeClientUsers($query)
    {
        return $query->where('client_id',auth()->user()->client_id);
    }

    public function scopeDevelopers($query)
    {
        return $query->where('client_id',auth()->user()->client_id);
    }

    public function scopeAuditors($query)
    {
        return $query->where('client_id',auth()->user()->client_id);
    }

    public function scopeIsActive($query)
    {
        $now = Carbon::today()->toDateString();
        return $query->whereRaw(
            "last_login_at >= ? AND last_login_at <= ?",
            [
                $now." 00:00:00",
                $now." 23:59:59"
            ]
        )
        ->where('status','Active');
    }

    public function hasActiveJob($user_id)
    {
        $hasActiveJob = Job::query()
            ->where('developer_id', $user_id)
            ->where('status','<>','Closed')
            ->count();

        $hasActiveJob = $hasActiveJob ? true : false;

        return $hasActiveJob;
    }

    public function theclient()
    {
        return $this->belongsTo(Client::class, 'client_id')->withTrashed();
    }

    public function theroles()
    {
        return $this->hasMany(Role::class,'user_id');
    }

    public function thetasks()
    {
        return $this->hasMany(Job::class,'developer_id')->withTrashed();
    }

    public function isStatusActive()
    {
        $isActive = $this->status == 'active'? true : false;
        return $isActive;
    }

    /**
     * Generate 6 digits MFA code for the User
     */
    public function generateTwoFactorCode()
    {
        $this->timestamps = false; //Dont update the 'updated_at' field yet
        $this->two_factor_code = rand(100000, 999999);
        $this->two_factor_expires_at = now()->addMinutes(10);
        $this->save();
    }

    /**
     * Reset the MFA code generated earlier
     */
    public function resetTwoFactorCode()
    {
        $this->timestamps = false; //Dont update the 'updated_at' field yet
        $this->two_factor_code = null;
        $this->two_factor_expires_at = null;
        $this->save();
    }
}
