<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class CompanyComplaints extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['service_provided', 'individual_name'];
    protected $encodedAttributes = ['service_provided', 'individual_name'];

    /**
     * Delete a specific company complaint record by its ID.
     *
     * @param int $id The ID of the complaint record to delete.
     * @return void
     */
    static function deleteRecord($id)
    {
        CompanyComplaints::where('id', $id)->delete();
    }

    /**
     * Define a relationship between the company complaint and the user who created it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
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
