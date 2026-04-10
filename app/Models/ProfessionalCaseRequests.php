<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProfessionalCaseRequests extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'unique_id','user_id','case_id','form_id','title','attachment','status','request_type','message_body','completed_by','completed_at','additional_detail','reply'
    ];

    public function cases()
    {
        return $this->belongsTo('App\Models\CaseWithProfessionals', 'case_id');
    }

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


    static function deleteRecord($id)
    {
        ProfessionalCaseRequests::where("id", $id)->delete();
    }
}
