<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class LicenseType extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable=['name','added_by','unique_id','license_category_id'];
    protected $encodedAttributes = ['name','added_by','unique_id'];
    
    public function licenseCategory()
    {
        return $this->belongsTo(CdsLicenseCategory::class, 'license_category_id');
    }

    static function deleteRecord($id)
    {
    
        LicenseType::where("id", $id)->delete();
    }
}
