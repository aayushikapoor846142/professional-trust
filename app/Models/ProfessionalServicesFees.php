<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProfessionalServicesFees extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','professional_sub_services_id','schedule_no','price'];
}
