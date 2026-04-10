<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class AwsFile extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['file_key'];
    protected $encodedAttributes =['file_key'];

}
