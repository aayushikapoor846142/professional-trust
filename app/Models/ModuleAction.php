<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ModuleAction extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['module_id', 'action'];
    protected $encodedAttributes = ['module_id', 'action'];
}
