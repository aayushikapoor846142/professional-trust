<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
/**
 * Class QuickTipOffs
 * 
 * This model represents the `quick_tip_offs` table in the database.
 * It contains methods for interacting with the quick tip-offs records.
 */
class QuickTipOffs extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table = "quick_tip_offs";
    protected $fillable = ['first_name', 'last_name','info_anonymous','email','category','report_tips','unique_id','added_by'];
    protected $encodedAttributes = ['first_name', 'last_name','info_anonymous','email','category','report_tips','unique_id','added_by'];

    use HasFactory;

    /**
     * Delete a specific quick tip-off record by ID.
     *
     * @param int $id The ID of the record to be deleted.
     * @return void
     */
    public static function deleteRecord($id)
    {
        QuickTipOffs::where('id', $id)->delete();
    }

    /**
     * Get the user that added the quick tip-off.
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
