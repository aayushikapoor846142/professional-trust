<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;
class Cases extends Model
{
    use HasFactory,SoftDeletes;
    
    protected $table = 'cases';
    protected $fillable = [
        'unique_id',
        'title',
        'description',
        'parent_service_id',
        'sub_service_id',
        'status',
        'added_by',
    ];
    protected $encodedAttributes = ['unique_id',
    'title',
    'description',
    'parent_service_id',
    'sub_service_id',
    'status',
    'added_by',];

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
        return $this->belongsTo('App\Models\User', 'added_by');
    }
      public function services()
    {
        return $this->belongsTo('App\Models\ImmigrationServices', 'parent_service_id');
    }

    public function subServices()
    {
        return $this->belongsTo('App\Models\ImmigrationServices', 'sub_service_id');
    }

    static function deleteRecord($id)
    {
        Cases::where("id", $id)->delete();
    }

    public function comments()
    {
        return $this->hasOne('App\Models\CaseComment', 'case_id');
    }

    public function ownComments()
    {
        return $this->hasOne('App\Models\CaseComment', 'case_id')->where('added_by',auth()->user()->id)->where('status','pending');
    }


    public function ProfessionalServices()
    {
        return $this->belongsTo(ProfessionalServices::class, 'sub_service_id', 'service_id');
    }

    public function submitProposal()
    {
        return $this->hasMany('App\Models\CaseComment', 'case_id','id');
    }

    public function ProfessionalCaseViewed()
    {
        return $this->hasOne('App\Models\ProfessionalCaseViewed','case_id','id');
    }

    public function professionalFavouriteCase()
    {
        return $this->hasOne('App\Models\ProfessionalFavouriteCase','case_id','id');
    }

    public function professionalCaseViewedCount()
    {
        return $this->hasMany('App\Models\ProfessionalCaseViewed','case_id','id');
    }

    public function clientLocation()
    {
        return $this->belongsTo('App\Models\CompanyLocations','added_by');
    }
}
