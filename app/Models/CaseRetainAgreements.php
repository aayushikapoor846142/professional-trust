<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseRetainAgreements extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['professional_case_id', 'unique_id','agreement','additional_details','status','added_by','posted_on','posted_by','submitted_on','is_accept','accepted_date','title','signature_type'];

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

    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    public function case()
    {
        return $this->belongsTo('App\Models\CaseWithProfessionals', 'professional_case_id');
    }
}
