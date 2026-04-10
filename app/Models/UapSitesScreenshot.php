<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UapSitesScreenshot extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'uap_sites_screenshots';

    protected $fillable = [
        'unique_id',
        'uap_site_id',
        'file_name',
        'added_by',
        'created_at',
        'updated_at',
    ];

    public function uapProfessionalSites()
    {
        return $this->belongsTo('App\Models\UapProfessionalSites','uap_site_id','id');
    }

    static function deleteRecord($id){
        
        $record = UapSitesScreenshot::where("unique_id",$id)->first();
  
        $directoryPath = storage_path('app/public/uap_sites_screenshot/'.$record->uapProfessionalSites->unique_id);
       
        $screenshotPath = $directoryPath.'/'. $record->file_name;
   
        // Check if the directory exists, if not, create it
        if (!\File::exists($screenshotPath)) {
            \File::delete($screenshotPath); // Create directory with permissions
        }
        UapSitesScreenshot::where("unique_id",$id)->delete();
    }
}
