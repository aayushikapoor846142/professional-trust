<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseWithAssociate extends Model
{
    use HasFactory,SoftDeletes;

     protected $fillable = [
        'unique_id',
        'associate_id',
        'case_id',
        'professional_id',
        'lead_case_id',
        'client_id',
        'added_by',
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

    public function asscoiate()
    {
        return $this->belongsTo(User::class, 'associate_id', 'id');
    }
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }
}
