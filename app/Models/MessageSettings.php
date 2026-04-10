<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageSettings extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "msg_settings";
    protected $fillable = ['unique_id','user_id','settings'];
    protected $encodedAttributes = ['unique_id','user_id','settings'];
}
