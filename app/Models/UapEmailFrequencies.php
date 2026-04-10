<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class UapEmailFrequencies extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','subject','template_name','mail_content','mail_to_send_on','mail_sequence','added_by'];

    protected $encodedAttributes =  ['unique_id','subject','template_name','mail_content','mail_to_send_on','mail_sequence','added_by'];

    static function deleteRecord($id)
    {
        UapEmailFrequencies::where("id", $id)->delete();
    }
}
