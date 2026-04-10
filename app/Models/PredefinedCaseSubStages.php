<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PredefinedCaseSubStages extends Model
{
    use HasFactory;

    use HasFactory, SoftDeletes;

    static function deleteRecord($id)
    {
        PredefinedCaseSubStages::where("id", $id)->delete();
    }

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
