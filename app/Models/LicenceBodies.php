<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class LicenceBodies extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "licence_bodies";

    protected $fillable = ['unique_id ', 'country_id','name','added_by'];
    // protected $encodedAttributes = ['unique_id ', 'country_id','name','added_by'];
    /**
     * Defines a relationship with the Countries model.
     * Each LicenceBodies record belongs to a single country,
     * referenced by the `country_id` foreign key.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function CountryName()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    /**
     * Deletes the LicenceBodies record by its ID.
     *
     * @param int $id The ID of the LicenceBodies record to delete.
     * @return void
     */
    static function deleteRecord($id)
    {
        LicenceBodies::where("id", $id)->delete();
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
