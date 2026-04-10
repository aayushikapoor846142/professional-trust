<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class Pages extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','name','short_title','slug','description','added_by'];

    protected $encodedAttributes =['unique_id','name','short_title','slug','description','added_by'];
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    static function deleteRecord($id)
    {
        Pages::where("id", $id)->delete();
    }

    public function seoDetails()
    {
        return $this->hasOne('App\Models\SeoDetails', 'reference_id', 'id')->where("module_type","page");
    }
}
