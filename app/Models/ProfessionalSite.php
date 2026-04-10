<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ProfessionalSite extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "professional_sites";
    protected $fillable = ['unique_id','professional_id','name','site_url','added_by','site_status'];
    protected $encodedAttributes =['unique_id','professional_id','name','site_url','added_by','site_status'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($object) {
            $object->unique_id = randomNumber();
        });
        static::updating(function ($object) {
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

    static function deleteRecord($id){

        $record = ProfessionalSite::where("unique_id",$id)->first();
      
        $directoryPath = storage_path('app/public/company_screenshot/'.$record->unique_id);
       
        $screenshotPath = $directoryPath.'/'. $record->file_name;
   
        // Check if the directory exists, if not, create it
        if (!\File::exists($screenshotPath)) {
            \File::delete($screenshotPath); // Create directory with permissions
        }
    
     CompanySiteScreenshot::where("professional_site_id",$record->id)->delete();
        ProfessionalSite::where("unique_id",$id)->delete();
    }

    public function addedBy()
    {
        return $this->belongsTo('App\Models\User','added_by');
    }


    public function screenshots()
    {
        return $this->hasMany('App\Models\CompanySiteScreenshot', 'professional_site_id', 'id');
  
    }
}
