<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CaseComment extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'case_comments';
    
    protected $fillable = [
        'unique_id',
        'case_id',
        'comments',
        'added_by',
        'sub_service_type_id',
        'status'
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
    /**
     * Get the case that owns the comment.
     */
    public function case()
    {
        return $this->belongsTo(Cases::class, 'case_id');
    }

    /**
     * Get the user who added the comment.
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function professionalSubservice()
    {
        return $this->belongsTo(ProfessionalSubServices::class, 'sub_service_type_id');
    }

    public function caseProposalHistory()
    {
        return $this->belongsTo(CaseProposalHistory::class,'id' ,'case_comment_id');
    }
     
}
