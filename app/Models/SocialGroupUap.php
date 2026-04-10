<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class SocialGroupUap extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id', 'unauthorised_id','social_media_groups'];
   
}
