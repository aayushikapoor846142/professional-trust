<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseFolders extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($object) {
            $object->unique_id = randomNumber();
        });
        static::updating(function ($object) {
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }
    protected $fillable = ['case_id', 'name','unique_id','added_by','slug'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    static function deleteRecord($id)
    {
        CaseFolders::where("id", $id)->delete();
    }

    public function documentFiles()
    {
        return $this->hasMany(CaseDocuments::class, 'folder_id')->where('document_type','extra')->orderBy('sort_order', 'asc');
    }

    public function getDocumentFileCountAttribute()
{
    return $this->documentFiles->reduce(function ($carry, $doc) {
        $files = array_filter(array_map('trim', explode(',', $doc->file_name)));
        return $carry + count($files);
    }, 0);
}
}
