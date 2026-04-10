<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class RolePrevilege extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $table = 'role_previleges';
    protected $fillable = ['role', 'module','action','added_by'];
    protected $encodedAttributes = ['role', 'module','action','added_by'];
}
