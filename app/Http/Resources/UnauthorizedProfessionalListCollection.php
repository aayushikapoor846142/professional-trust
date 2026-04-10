<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnauthorizedProfessionalListCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    

    public function toArray(Request $request): array
    {
        $uap_details = [];
        
        if ($this->type == 'individual') {
             $uap_details = new IndividualUapResource($this->whenLoaded('individual'));
             //  dd( $uap_details);
        } elseif ($this->type == 'corporate') {  // Fixed typo
            // Pass the uap_details relation to the CorporateUapResource
            $uap_details = new CorporateUapResource($this->whenLoaded('corporate'));
        } elseif ($this->type == 'social_media_handlers') {
           
            // Handle multiple resources for social media handlers
            $uap_details['social_media_uaps'] =  SocialMediaUapResource::collection($this->whenLoaded('socialMediaUap'));
            $uap_details['social_groups_uaps'] =  SocialGroupsUapResource::collection($this->whenLoaded('SocialGroupUap'));
        //  dd($uap_details);
        }

        return [
            'unique_id' => $this->unique_id,
            'type' => $this->type,
            'first_name' => decryptVal($this->first_name),
            'last_name' => decryptVal($this->last_name),
            'email' => decryptVal($this->email),
            'suggestion' => decryptVal($this->suggestion),
            'evidences' => $this->evidences,
            'status' => $this->status,
            'level' => $this->level,
            'uap_details' => $uap_details
        ];
    }

}
