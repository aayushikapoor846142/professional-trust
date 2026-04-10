<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use App\Models\BaseModel;
class ProfessionalDocuments extends Model
{
    use HasFactory,SoftDeletes;
    protected $table="professional_documents";

    protected $fillable = ['unique_id','incorporation_certification','license','proof_of_identity','professional_id'];
   // protected $encodedAttributes =[];
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

}
