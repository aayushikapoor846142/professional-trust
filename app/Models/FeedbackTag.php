<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeedbackTag extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'name','slug','added_by'];
        public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    static function deleteRecord($id)
    {
        $action = FeedbackTag::where('id',$id)->first();
        FeedbackTag::where("id", $id)->delete();
    }


}
