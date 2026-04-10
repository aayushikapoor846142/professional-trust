<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffCases extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['case_id', 'staff_id','unique_id','added_by'];

    public function Staff()
    {
        return $this->belongsTo('App\Models\User', 'staff_id');
    }

    public function caseOwner()
    {
        return $this->belongsTo(CaseWithProfessionals::class, 'case_id');
    }
}
