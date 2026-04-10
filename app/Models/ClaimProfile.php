<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;

class ClaimProfile extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id', 'professional_id','proof_of_identity','incorporation_certificate','license','alternate_contact_name','primary_contact_number','registered_domain_name','registered_office_address','registered_mailing_address','status','added_by','approved_at','approved_by','reference_id','alt_country_code','pri_country_code'];
    // protected $encodedAttributes = ['unique_id', 'professional_id','alternate_contact_name','primary_contact_number','registered_domain_name','registered_office_address','registered_mailing_address','status'];

    public function professional()
    {
        return $this->belongsTo('App\Models\Professional','professional_id','id');
    }

    public function addedBy()
    {
        return $this->belongsTo('App\Models\User','added_by','id');
    }
}
