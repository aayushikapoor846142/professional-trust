<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClaimProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
            'unique_id' => $this->unique_id,
            'reference_id' => $this->reference_id,
            'professional_id' => $this->professional_id,
            'proof_of_identity' => awsFilePreview(config("awsfilepath.claim_profile"). '/' .  $this->proof_of_identity),
            'incorporation_certificate' => awsFilePreview(config("awsfilepath.claim_profile"). '/' .  $this->incorporation_certificate),
            'license' => awsFilePreview(config("awsfilepath.claim_profile"). '/' .  $this->license),
            'alternate_contact_name' => $this->alternate_contact_name,
            'primary_contact_number' => $this->primary_contact_number,
            'registered_domain_name' => $this->registered_domain_name,
            'registered_office_address' => $this->registered_office_address,
            'registered_mailing_address' => $this->registered_mailing_address,
            'added_by' => $this->addedBy->first_name." ".$this->addedBy->last_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'approved_at' => $this->approved_at,
        ];


        
    }
}
