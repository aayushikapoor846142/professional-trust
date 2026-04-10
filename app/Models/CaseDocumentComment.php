<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseDocumentComment extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            $object->unique_id = randomNumber();
        });

        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

    
    static function deleteRecord($comment_id,$document_id)
    {
        $comment = CaseDocumentComment::where("unique_id",$comment_id)->first();
        $data= mediaDeleteApi(caseDocumentsDir($document_id),$comment->attachments);
        CaseDocuments::where("unique_id", $comment_id)->delete();
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    public function case()
    {
        return $this->belongsTo('App\Models\Cases', 'case_id');
    }
    public function caseFolder()
    {
        return $this->belongsTo('App\Models\CaseFolders', 'folder_id');
    }
    public function caseDocument()
    {
        return $this->belongsTo('App\Models\CaseDocuments', 'document_id');
    }

    public function commentReply()
    {
        return $this->belongsTo('App\Models\CaseDocumentComment', 'parent_id','id');
    }

    public function commentRead()
    {
        return $this->belongsTo('App\Models\CaseDocumentCommentRead', 'id','case_document_comment_id');
    }

}
