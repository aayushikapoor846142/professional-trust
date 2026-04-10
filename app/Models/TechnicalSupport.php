<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TechnicalSupport extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "technical_supports";
    protected $fillable = ['unique_id','who_are_you','subject','describe_issue','email','attachment'];

    static function deleteRecord($id)
    {
        TechnicalSupport::where("id", $id)->delete();
    }

}
