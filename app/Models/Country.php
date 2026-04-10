<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Country extends BaseModel
{
    use HasFactory,SoftDeletes;

    // The attributes that are mass assignable.
    protected $fillable = ['name', 'phonecode', 'sortname'];
    protected $encodedAttributes =['name','phonecode','sortname'];

    /**
     * Deletes a country record by its ID.
     *
     * @param int $id The ID of the country record to delete.
     * @return void
     */
    static function deleteRecord($id)
    {
        Country::where("id", $id)->delete();
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
