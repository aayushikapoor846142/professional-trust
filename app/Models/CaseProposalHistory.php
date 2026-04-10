<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseProposalHistory extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'unique_id',
        'case_id',
        'case_comment_id',
        'added_by',
        'case_quotation_id',
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

    public function caseQuotation()
    {
        return $this->belongsTo(CaseQuotation::class,'case_quotation_id' ,'id');
    }
    

}
