<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class Action extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id', 'name','slug','added_by'];
    protected $encodedAttributes = ['unique_id', 'name','slug','added_by'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    static function deleteRecord($id)
    {
        $action = Action::where('id',$id)->first();
        ModuleAction::where('action',$action->slug)->delete();
        Action::where("id", $id)->delete();
    }

}
