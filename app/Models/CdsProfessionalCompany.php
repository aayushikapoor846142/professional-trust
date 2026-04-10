<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CdsProfessionalCompany extends Model
{
       use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id', 'user_id','company_name','owner_type','company_type','about_company','company_logo','banner_image','is_primary'];
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
        CdsProfessionalCompany::where("unique_id", $id)->delete();
    }

    public function CompanyLocation()
    {
        return $this->hasOne('App\Models\CompanyLocations','unclaimed_id');
    }

    public function CdsUnclaimProfile()
    {
        return $this->belongsTo('App\Models\CdsUnclaimProfile','unclaimed_id');
    }
}
