<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomTemplates extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id', 'template_name', 'file_name','template_for','added_by'];

    public function addedBy()
    {
        return $this->belongsTo('App\Models\User','added_by','id');
    }

    static function deleteRecord($id)
    {
        CustomTemplates::where("id", $id)->delete();
    }
}
