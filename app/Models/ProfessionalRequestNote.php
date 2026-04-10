<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProfessionalRequestNote extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'unique_id','professional_case_request_id','user_id','notes','attachment'
    ];

    public function User()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

}
