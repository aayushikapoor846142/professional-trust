<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
use App\Scopes\UserIdScope;

class CompanyLocations extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'user_id','location_name','address_1','address_2','country','state','city','pincode','added_by','type_label','is_primary','company_id','unclaimed_id','status','type','location_name'];
    protected $encodedAttributes =['unique_id','location_name', 'user_id','address_1','address_2','country','state','city','pincode','added_by'];

    protected static function boot()
    {
        parent::boot();
        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            $unique_id = randomNumber();
            // $unique_id = checkUniqueId($unique_id,'company_locations');
            $object->unique_id = $unique_id;
        });
        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });

        
    }

     protected static function booted()
        {
            parent::booted();

            static::addGlobalScope(new UserIdScope());
        }

    public function getFullAddressAttribute(){
        $location = array();
        if($this->address_1 != ''){
            $location[] = $this->address_1;
        }
        if($this->address_2 != ''){
            $location[] = $this->address_2;
        }
        if($this->city != ''){
            $location[] = $this->city;
        }
        if($this->state != ''){
            $location[] = $this->state;
        }
        if($this->city != ''){
            $location[] = $this->city;
        }
        return implode(",",$location);
    }
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User','added_by');

    }
    public function company()
    {
        return $this->belongsTo('App\Models\CdsProfessionalCompany','company_id')->withTrashed();

    }
    static function deleteRecord($id)
    {
        CompanyLocations::where("id", $id)->delete();
    }

    public function workingHours()
    {
        return $this->hasMany('App\Models\WorkingHours','location_id');

    }
      public function scopeVisibleToUser($query, $userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: show their own + their professional's records
            return $query->where(function ($q) use ($userId, $professionalId) {
                $q->where('user_id', $userId)
                ->orWhere('user_id', $professionalId);
            });
        } else {
            // Professional: show their own + all their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id');

            return $query->where(function ($q) use ($userId, $staffIds) {
                $q->where('user_id', $userId);

                if ($staffIds->isNotEmpty()) {
                    $q->orWhereIn('user_id', $staffIds);
                }
            });
        }
    }
}
