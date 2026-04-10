<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvidenceComments extends Model
{
    use HasFactory,SoftDeletes;

    static function deleteRecord($id){
        EvidenceComments::where("id",$id)->delete();
    }
}
