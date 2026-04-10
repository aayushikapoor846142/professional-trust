<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AssociateEducation extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'associate_education';
    protected $fillable = ['unique_id', 'user_id','degree','university','passout_year','added_by'];

    public function degrees()
    {
        return $this->belongsTo(Degree::class, 'degree');
    }
}
