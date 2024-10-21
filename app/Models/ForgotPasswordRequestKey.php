<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgotPasswordRequestKey extends Model
{
    use HasFactory;
    protected $table='forgot_password_request_keys';
    protected $guarded = [];
    protected $dates = ['created_at', 'updated_at'];
}
