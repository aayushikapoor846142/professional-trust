<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class Subscriber extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table = 'subscribers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'unique_id',
        'name',
        'email'
    ];
    protected $encodedAttributes = [
        'name',
        'email',
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

    static function deleteRecord($id)
    {
        Subscriber::where("unique_id", $id)->delete();
    }
}
