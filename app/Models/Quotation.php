<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;
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
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');
    }
    public function service()
    {
        return $this->belongsTo(ImmigrationServices::class, 'service_id', 'id');
    }

    public function particulars()
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id', 'id');
    }

    static function deleteRecord($id)
    {
        Quotation::where("id", $id)->delete();
        QuotationItem::where("quotation_id", $id)->delete();
    }
}
