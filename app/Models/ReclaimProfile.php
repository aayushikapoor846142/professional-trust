<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ReclaimProfile extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'added_by','professional_id','user_id','status'];
    protected $encodedAttributes = ['unique_id', 'added_by','professional_id','user_id','status'];
    public function Professional()
    {
        return $this->belongsTo('App\Models\Professional','professional_id','id');
    }

    public function User()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }

    public function ClaimProfile()
    {
        return $this->belongsTo('App\Models\ClaimProfile','unique_id','reference_id');
    }

}
