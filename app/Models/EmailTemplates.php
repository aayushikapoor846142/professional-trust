<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class EmailTemplates extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'template_name','template_key','subject','mail_body','added_by'];
    protected $encodedAttributes =['unique_id', 'template_name','template_key','subject','mail_body','added_by'];

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');
    }
    
    static function deleteRecord($id)
    {
        EmailTemplates::where("id", $id)->delete();
    }

}
