<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ReportSocialMediaContent extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $table = 'report_social_media_content'; // Define the table name

    protected $fillable = [
        'unique_id',
        'social_link',
        'comments',
        'unauthorised_id',
    ];

    protected $encodedAttributes = [
        'unique_id',
        'unauthorised_id',
    ]; 
   
}
