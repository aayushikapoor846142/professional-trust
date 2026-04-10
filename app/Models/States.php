<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class States extends BaseModel
{
    use HasFactory,SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "states";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'country_id', 'added_by'];
    protected $encodedAttributes = ['name', 'country_id', 'added_by'];
    /**
     * Defines a relationship where each state belongs to a country.
     * The foreign key is `country_id` that links to the `Country` table.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_id');
    }

    /**
     * Deletes a record from the states table based on the given ID.
     * 
     * @param int $id The ID of the complaint record to delete.
     * @return bool|null Indicates if the deletion was successful.
     */
    public static function deleteRecord(int $id): ?bool
    {
        return self::where("id", $id)->delete();
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
