<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherUapDetails extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id','uap_id','meta_key','meta_value','added_by'];
    
}
