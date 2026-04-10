<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SendForms extends Model
{
    use HasFactory,SoftDeletes;

    public function formReply()
    {
        return $this->belongsTo('App\Models\FormReply','uuid' ,'form_uuid');
    }

}
