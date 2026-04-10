<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class Module extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','slug' ,'name','added_by'];
    protected $encodedAttributes = ['unique_id','slug' ,'name','added_by'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    public function moduleAction()
    {
        return $this->hasMany('App\Models\ModuleAction','module_id','id');

    }
    public function membershipPlans()
    {
        return $this->belongsToMany(MembershipPlan::class, 'membership_plan_module', 'module_id', 'membership_plan_id');
    }
    
    public function membershipPlanModules()
    {
        return $this->hasMany(MembershipPlanModule::class);
    }

    static function deleteRecord($id)
    {
        Module::where("id", $id)->delete();
        ModuleAction::where('module_id',$id)->delete();
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
