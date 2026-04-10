<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PredefinedCaseStages extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id','user_id', 'name','stage_type','fees','short_description','added_by','status'];

    static function deleteRecord($id)
    {
        PredefinedCaseStages::where("id", $id)->delete();
        PredefinedCaseSubStages::where('predefined_case_stage_id',$id)->delete();
    }

    public function predefinedCaseSubStages()
    {
        return $this->hasMany(PredefinedCaseSubStages::class, 'predefined_case_stage_id');
    }

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

        public function isEditableBy($userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: can only edit their own records (not professional's)
            return $this->added_by == $userId;
        } else {
            // Professional: can edit their own and their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id')->toArray();
            return $this->added_by == $userId || in_array($this->added_by, $staffIds);
        }
    }
}
