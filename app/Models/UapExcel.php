<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class UapExcel extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['title', 'message','files','type','submitted_from'];

    protected $encodedAttributes = ['title', 'message','files','type','submitted_from'];
    static function deleteRecord($id)
    {
        UapExcel::where("id", $id)->delete();
    }
}
