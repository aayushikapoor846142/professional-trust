<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ReportProfile extends BaseModel
{
    use HasFactory,SoftDeletes;
    // The attributes that are mass assignable.
    protected $fillable = ['unique_id', 'added_by','status','evidences','reason','subject','professional_id','email'];
    protected $encodedAttributes = ['unique_id', 'added_by','status','evidences','reason','subject','professional_id','email'];


    public function addedBy()
    {
        return $this->belongsTo('App\Models\User','added_by','id');
    }

    public function Professional()
    {
        return $this->belongsTo('App\Models\Professional','professional_id','id');
    }

}
