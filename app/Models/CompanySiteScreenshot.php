<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class CompanySiteScreenshot extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'company_site_screenshots';

    protected $fillable = [
        'unique_id',
        'professional_site_id',
        'file_name',
        'added_by',
        'created_at',
        'updated_at',
    ];
    protected $encodedAttributes =[
        'unique_id',
        'professional_site_id',
        'file_name',
        'added_by',
        'created_at',
        'updated_at',
    ];

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
        
        $record = CompanySiteScreenshot::where("unique_id",$id)->first();
  
        $directoryPath = storage_path('app/public/company_screenshot/'.$record->professionalSite->unique_id);
       
        $screenshotPath = $directoryPath.'/'. $record->file_name;
   
        // Check if the directory exists, if not, create it
        if (!\File::exists($screenshotPath)) {
            \File::delete($screenshotPath); // Create directory with permissions
        }
        CompanySiteScreenshot::where("unique_id",$id)->delete();
    }

  
    public function professionalSite()
    {
        return $this->belongsTo(ProfessionalSite::class, 'professional_site_id', 'id');
    }

}
