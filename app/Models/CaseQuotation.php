<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseQuotation extends Model
{
    use HasFactory;
    protected $fillable = ['case_id'];

    protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            $object->unique_id = randomNumber();
            $object->receipt_number = generateQuotationNumber();
        });

        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

    public function particulars()
    {
        return $this->hasMany(CaseQuotationItem::class, 'quotation_id', 'id');
    }

    static function deleteRecord($id)
    {
        CaseQuotation::where("id", $id)->delete();
        CaseQuotationItem::where("quotation_id", $id)->delete();
    }

    public function caseComment()
    {
        return $this->hasMany(CaseComment::class, 'case_id', 'case_id');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id', 'id');
    }
}
