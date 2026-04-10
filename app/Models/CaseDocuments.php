<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseDocuments extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id','case_id', 'folder_id','user_id','document_type','file_name','added_by'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    public function caseFolder()
    {
        return $this->belongsTo('App\Models\CaseFolders', 'folder_id');
    }

    static function deleteRecord($id,$case_id,$file_name)
    {
        $data= mediaDeleteApi(caseDocumentsDir($case_id),$file_name);
        CaseDocuments::where("id", $id)->delete();
    }

}
