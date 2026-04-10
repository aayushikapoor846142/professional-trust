<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CdsProfessionalLicense extends Model
{
     use HasFactory,SoftDeletes;
    protected $fillable = ['regulatory_country_id','regulatory_body_id','license_number','title','class_level','license_start_date','country_of_practise','license_status','entitled_to_practice','do_you_more_license','user_id','added_by'];

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
        CdsProfessionalLicense::where("unique_id", $id)->delete();
    }

    public function regulatoryBody()
    {
        return $this->belongsTo('App\Models\CdsRegulatoryBody', 'regulatory_body_id');
    }

    public function regulatoryCountry()
    {
        return $this->belongsTo('App\Models\CdsRegulatoryCountry', 'regulatory_country_id');
    }

    public function countryOfPractise()
    {
        return $this->belongsTo('App\Models\Country', 'country_of_practise');
    }

      public function country()
    {
        return $this->belongsTo('App\Models\Country', 'country_of_practise');
    }
    
}
