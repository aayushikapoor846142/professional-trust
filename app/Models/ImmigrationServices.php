<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

/**
 * Class ImmigrationServices
 * 
 * Represents the model for the `immigration_services` table.
 * This class provides methods to interact with the `immigration_services` table.
 *
 * @package App\Models
 */
class ImmigrationServices extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','added_by','name','slug','parent_service_id','description','image','specialise_service'];
    protected $encodedAttributes =['unique_id','added_by','name','slug','parent_service_id','description','specialise_service'];


    /**
     * Delete a record from the `immigration_services` table based on the provided ID.
     *
     * @param int $id The ID of the record to delete.
     * @return void
     */
    static function deleteRecord($id)
    {
        ImmigrationServices::where("id", $id)->delete();
    }
    
    public function subServices()
    {
        return $this->hasMany(ImmigrationServices::class, 'parent_service_id');
    }

    // A sub-service belongs to a parent service
    public function parentService()
    {
        return $this->belongsTo(ImmigrationServices::class, 'parent_service_id');
    }
    public function parentServiceCasesForCurrentUser()
    {
        return $this->hasMany(CaseWithProfessional::class, 'parent_service_id')
                    ->where('professional_id', auth()->id()); 
    }
    public function subServiceCasesForCurrentUser()
    {
        return $this->hasMany(CaseWithProfessional::class, 'sub_service_id')
                    ->where('professional_id', auth()->id()); 
    }
    public function subServiceCases()
    {
        return $this->hasMany(CaseWithProfessional::class, 'sub_service_id'); 
    }
    public function parentServiceCases()
    {
        return $this->hasMany(CaseWithProfessional::class, 'parent_service_id'); 
    }
    
    public function getTags()
    {
        return $this->hasMany(ImmigrationServiceTags::class, 'service_id');
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