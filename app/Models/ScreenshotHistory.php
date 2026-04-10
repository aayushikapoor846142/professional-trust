<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ScreenshotHistory extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'capture_id',
        'file_name',
        'added_by',
    ];

    protected $encodedAttributes = [
        'capture_id',
        'file_name',
        'added_by',
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
        $record = ScreenshotHistory::where("unique_id",$id)->first();
        $directoryPath = storage_path('app/public/screenshots/'.$record->screenCapture->unique_id);
        $screenshotPath = $directoryPath.'/'. $record->file_name;
        // Check if the directory exists, if not, create it
        if (!\File::exists($screenshotPath)) {
            \File::delete($screenshotPath); // Create directory with permissions
        }
        ScreenshotHistory::where("unique_id",$id)->delete();
    }

    public function screenCapture()
    {
        return $this->belongsTo('App\Models\ScreenCaptures','capture_id');
    }
}
