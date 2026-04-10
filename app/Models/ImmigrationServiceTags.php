<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class ImmigrationServiceTags extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table = "immigration_service_tags";
    protected $fillable = ['unique_id','tag_id','service_id','added_by'];
    protected $encodedAttributes = ['unique_id', 'tag_id','service_id','added_by'];

}
