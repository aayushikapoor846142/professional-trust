<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Languages extends BaseModel
{
    use HasFactory,SoftDeletes;

    // The table associated with the model.
    protected $table = "languages";
    protected $fillable = ['unique_id', 'name'];
    protected $encodedAttributes = ['unique_id', 'name'];

    /**
     * Deletes a language record by its ID.
     *
     * @param int $id The ID of the language record to delete.
     * @return void
     */
    static function deleteRecord($id)
    {
        Languages::where("id", $id)->delete();
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