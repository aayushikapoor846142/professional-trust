<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ReportSocialMediaGroup extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $table = 'report_social_media_group'; // Define the table name

    protected $fillable = [
        'unique_id',
        'social_media_groups',
        'unauthorised_id',
    ]; 

    protected $encodedAttributes = [
        'unique_id',
        'unauthorised_id',
    ]; 
}
