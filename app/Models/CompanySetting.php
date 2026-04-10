<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CompanySetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'company_settings';

    protected $fillable = [
        'unique_id',
        'company_name',
        'logo',
        'email',
        'updated_by',
    ];

}
