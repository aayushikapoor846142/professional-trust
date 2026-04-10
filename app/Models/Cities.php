<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class Cities extends BaseModel
{
    use HasFactory,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'state_id', 'added_by'];
    protected $encodedAttributes = ['name', 'state_id', 'added_by'];

    /**
     * Defines a relationship where each city belongs to a state.
     * The foreign key is `state_id` that links to the `states` table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo('App\Models\States', 'state_id');
    }

    /**
     * Deletes a city record by its ID.
     * If the city has any related data that needs to be handled before deletion,
     * it should be managed here.
     *
     * @param int $id The ID of the city to be deleted.
     * @return void
     */
    static function deleteRecord($id)
    {
        // Delete the city record by its ID
        Cities::where("id", $id)->delete();
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