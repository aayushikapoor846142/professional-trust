<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DocumentFolder;
use App\Models\BaseModel;

class VisaServices extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "visa_services";
    protected $fillable = ['unique_id','parent_id','name','slug','assessment_price','document_folders','cv_type','eligible_type','added_by'];
    // protected $encodedAttributes = ['unique_id','parent_id','name','slug','assessment_price','document_folders','cv_type','eligible_type','added_by'];
    /**
     * Get the sub-services related to this visa service.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subServices()
    {
        return $this->hasMany('App\Models\VisaServices', 'parent_id');
    }

    /**
     * Get the content related to this visa service.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function visaServiceContent()
    {
        return $this->hasOne('App\Models\VisaServiceContent', 'visa_service_id');
    }

    /**
     * Get the articles related to this visa service.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function articles()
    {
        return $this->hasMany('App\Models\Articles', 'category_id');
    }

    /**
     * Get the webinars related to this visa service.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function webinars()
    {
        return $this->hasMany('App\Models\Webinar', 'category_id');
    }

    /**
     * Get the document folders associated with this visa service.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function documentFolders($id)
    {
        $visa_service = $this->where("id", $id)->first();

        if ($visa_service && $visa_service->document_folders != '') {
            $document_folder_ids = explode(",", $visa_service->document_folders);
            return DocumentFolder::whereIn("id", $document_folder_ids)->get();
        } else {
            return [];
        }
    }

    /**
     * Get the CV type detail associated with this visa service.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cvTypeDetail()
    {
        return $this->belongsTo('App\Models\CvTypes', 'cv_type');
    }

    /**
     * Get the arranged questions related to this visa service.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function arrangeQuestions()
    {
        return $this->hasMany('App\Models\ArrangeQuestions', 'visa_service_id', 'unique_id');
    }

    /**
     * Delete a visa service record and its sub-services.
     *
     * @param int $id
     * @return void
     */
    public static function deleteRecord($id)
    {
        $visa_service = self::where("id", $id)->first();

        if ($visa_service) {
            self::where("id", $id)->delete();
            self::where("parent_id", $id)->delete();
        }
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
