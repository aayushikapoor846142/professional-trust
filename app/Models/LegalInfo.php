<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class LegalInfo extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table="legal_info";
    protected $fillable = ['unique_id', 'legal_name_of_business','business_reg_no',
    'email','address','professional_id'];
    protected $encodedAttributes = ['unique_id', 'legal_name_of_business','business_reg_no',
    'email','address','professional_id'];

}
