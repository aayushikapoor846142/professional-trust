<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class UapReconsider extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id', 'uap_id','description','evidences','added_by','status','assigned_to'];
    protected $encodedAttributes =['unique_id', 'uap_id','description','evidences','added_by','status','assigned_to'];
    public function uapProfessional()
    {
        return $this->belongsTo('App\Models\UnauthorisedProfessional','id');

    }

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');

    }
}
