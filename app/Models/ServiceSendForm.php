<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceSendForm extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'unique_id',
        'user_id',
        'form_id',
        'service_form_id',
        'form_type',
        'form_fields_json',
        'form_name',
        'email',
        'status',
        'added_by'
    ];

    public function form()
    {
        return $this->belongsTo('App\Models\Forms','form_id','id');
    }
	
}
