<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RcicFeedbacks extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'name','designation','company','comment','status','added_by','first_name','last_name','email','rcic_number','is_anonymous'];

    static function deleteRecord($id)
    {
        RcicFeedbacks::where("id", $id)->delete();
    }

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');
    }
}
