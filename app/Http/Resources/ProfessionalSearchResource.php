<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalSearchResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'unique_id' => $this->unique_id,
            'view_profile_url' => $this->view_profile_url,
            'college_id' => $this->college_id,
            'name' => $this->name,
            'slug' => str_slug($this->name),
            'company' => $this->company,
            'company_type' => $this->company_type,
            'user' =>  new ProfessionalUserResource($this->user),
            'user_details' =>  new ProfessionalUserDetailResource($this->userDetails),
        ];
    }   


}
