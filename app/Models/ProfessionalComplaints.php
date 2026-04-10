<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
/**
 * Class ProfessionalComplaints
 * 
 * Represents a professional complaint in the system.
 * 
 * @package App\Models
 */
class ProfessionalComplaints extends Model
{
    use HasFactory,SoftDeletes;

    /**
     * Deletes a record from the professional complaints table based on the given ID.
     * 
     * @param int $id The ID of the complaint record to delete.
     * @return bool|null Indicates if the deletion was successful.
     */
    public static function deleteRecord(int $id): ?bool
    {
        return self::where("id", $id)->delete();
    }

    /**
     * Defines a relationship to the User model.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The relationship to the User.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    /**
     * Defines a relationship to the ProfessionalComplaintAssigned model.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany The relationship to ProfessionalComplaintAssigned.
     */
    public function assigned()
    {
        return $this->hasMany(ProfessionalComplaintAssigned::class, 'complaint_id');
    }

    /**
     * The "booting" method of the model.
     *
     * This method is called when the model is booted and allows for attaching
     * various model events like `creating` and `updating`.
     *
     * @return void
     */
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
}
