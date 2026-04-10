<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseSubStages extends Model
{
    use HasFactory, SoftDeletes;

    static function deleteRecord($id)
    {
        CaseSubStages::where("id", $id)->delete();
    }

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

}
