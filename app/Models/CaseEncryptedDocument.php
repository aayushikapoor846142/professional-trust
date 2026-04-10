<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CaseEncryptedDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'case_encrypted_documents';

    protected $fillable = [
        'unique_id',
        'case_id',
        'folder_id',
        'password',
    ];

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

    public function case()
    {
        return $this->belongsTo(CaseDocuments::class, 'case_id','id');
    }

    public function casewithProfessional()
    {
        return $this->belongsTo(CaseWithProfessionals::class, 'case_id','id');
    }
    public function documentFiles()
    {
        return $this->hasMany(CaseDocuments::class, 'case_encrypted_documents_id');
    }
    
        public function getDocumentFileCountAttribute()
{
    return $this->documentFiles->reduce(function ($carry, $doc) {
        $files = array_filter(array_map('trim', explode(',', $doc->file_name)));
        return $carry + count($files);
    }, 0);
}
    // public function folder()
    // {
    //     return $this->belongsTo(DocumentFolder::class, 'folder_id');
    // }
}
