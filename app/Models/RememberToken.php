<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RememberToken extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'token','user_id','expiry_time'];
}
