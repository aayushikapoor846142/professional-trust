<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseStages extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id','case_id','user_id', 'name','stage_type','fees','short_description','added_by','sort_order','predefined_case_stage_id','status'];

    static function deleteRecord($id)
    {
        CaseStages::where("id", $id)->delete();
    }

    public function caseSubStages()
    {
        return $this->hasMany(CaseSubStages::class, 'stage_id')->orderBy('sort_order','asc');
    }

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
}
