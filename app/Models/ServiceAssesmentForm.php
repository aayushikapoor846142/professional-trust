<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceAssesmentForm extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','service_id','form_id','added_by','professional_service_id'];

    public function forms(){

    	return $this->belongsTo('App\Models\Forms', 'form_id');

    }
}
