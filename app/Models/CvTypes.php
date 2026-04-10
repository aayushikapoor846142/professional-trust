<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class CvTypes extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $table = "cv_types";
    protected $fillable = ['unique_id', 'name'];
    protected $encodedAttributes = ['unique_id', 'name'];

    /**
     * Deletes the record by the given ID
     * and updates related VisaServices records to set `cv_type` to 0.
     * 
     * @param int $id The ID of the CvTypes record to delete.
     * @return void
     */
    static function deleteRecord($id)
    {
        CvTypes::where("id", $id)->delete();

        // Updating related VisaServices entries where `cv_type` is set to the deleted ID
        VisaServices::where("cv_type", $id)->update(['cv_type' => 0]);
    }

    /**
     * Establishes a one-to-many relationship with the VisaServices model
     * where `cv_type` in VisaServices references the current CvTypes model's ID.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function VisaServices()
    {
        return $this->hasMany('App\Models\VisaServices', 'cv_type');
    }
}
