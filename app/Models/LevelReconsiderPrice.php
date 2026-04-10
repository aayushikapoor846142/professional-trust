<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;


class LevelReconsiderPrice extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'level_1','level_2','level_3','added_by'];
    protected $encodedAttributes = ['level_1','level_2','level_3','added_by'];
}
