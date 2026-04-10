<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FormReply extends Model
{
    use HasFactory,SoftDeletes;

    public function Workspace()
    {
        return $this->belongsTo('App\Models\Forms','form_id','unique_id');
    }
	
	public function User()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
	
    public function SendForm()
    {
        return $this->belongsTo('App\Models\SendForms','form_uuid','uuid');
    }
}
