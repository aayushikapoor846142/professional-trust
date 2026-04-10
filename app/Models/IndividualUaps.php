<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class IndividualUaps extends BaseModel
{
    use HasFactory,SoftDeletes;
     // The attributes that are mass assignable.
     protected $fillable = ['unique_id', 'unauthorised_id','first_name','last_name','email','country_code','phone_no','address','country','city','state','social_mediumn_link','why_uap','google_review','website'];
     protected $encodedAttributes = ['unique_id', 'unauthorised_id','first_name','last_name','email','country_code','phone_no','address','country','city','state','social_mediumn_link','why_uap','google_review','website'];

    }
