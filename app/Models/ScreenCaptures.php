<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ScreenCaptures extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $table = "screen_captures";
    
    protected $fillable = ['unique_id', 'name','site_url','added_by','site_status'];
    protected $encodedAttributes = ['unique_id', 'name','site_url','added_by','site_status'];

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
        $record = ScreenCaptures::where("unique_id",$id)->first();
        ScreenshotHistory::where("capture_id",$record->id)->delete();
        $directoryPath = storage_path('app/public/screenshots/'.$record->unique_id);
        // Check if the directory exists, if yes, delete it
        if (!\File::exists($directoryPath)) {
            \File::deleteDirectory($directoryPath); 
        }
        ScreenCaptures::where("unique_id",$id)->delete();
    }

    public function addedBy()
    {
        return $this->belongsTo('App\Models\User','added_by');
    }

    public function screenshotHistory()
    {
        return $this->hasMany('App\Models\ScreenshotHistory','capture_id');
    }
}
