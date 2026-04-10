<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ServiceDocument extends BaseModel
{
    use HasFactory,SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'service_documents';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'added_by',
    ];
    protected $encodedAttributes = [
        'name',
        'slug',
        'description',
        'added_by',
    ];

    /**
     * Deletes a record by the given ID.
     *
     * @param int $id
     * @return bool|null
     */
    public static function deleteRecord($id)
    {
        return self::where('id', $id)->delete();
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