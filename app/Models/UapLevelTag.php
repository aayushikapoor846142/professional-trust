<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UapLevelTag extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ['unique_id', 'uap_id','level_tag_id','added_by','is_ping','level'];

    public function levelTag()
    {
        return $this->belongsTo('App\Models\LevelTag', 'level_tag_id');
    }
}
