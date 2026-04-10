<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class ApiKey extends Model

{
    use HasFactory,SoftDeletes;

    protected $fillable = ['api_key', 'api_value','group_id','unique_id'];

    public function apiGroup()
    {
        return $this->belongsTo('App\Models\ApiGroups', 'group_id');
    }

    static function deleteRecord($id)
    {
        ApiKey::where("id", $id)->delete();
    }

}
