<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceFormReply extends Model
{
    use HasFactory,SoftDeletes;

    
    protected $fillable = [
        'unique_id',
        'user_id',
        'form_id',
        'service_send_form_id',
        'first_name',
        'last_name',
        'field_reply',
        'email',
    ];
}
