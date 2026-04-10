<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadCase extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'associate_id','client_id','lead_id','case_title','case_description','parent_service_id','sub_service_id','service_type_id','status','added_by'];


    public function asscoiate()
    {
        return $this->belongsTo(User::class, 'associate_id', 'id');
    }
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }

     public function services()
    {
        return $this->belongsTo('App\Models\ImmigrationServices', 'parent_service_id');
    }

    public function subServices()
    {
        return $this->belongsTo('App\Models\ImmigrationServices', 'sub_service_id');
    }

}

