<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UapComment extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'uap_id','comment','email','added_by'];
}
