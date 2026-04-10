<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class SocialMediaUap extends Model
{
    use HasFactory,SoftDeletes;

     // The attributes that are mass assignable.
     protected $fillable = ['unique_id', 'unauthorised_id','social_link','comments'];
     
}
