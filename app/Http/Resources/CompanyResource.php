<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


    public function toArray($request)
    {
        return [
            'unique_id' => $this->unique_id,
            'college_id' => $this->college_id,
            'employment_startdate' => $this->employment_startdate,
            'company_type' => $this->company_type,
            'entitled_to_practise' => $this->entitled_to_practise,
            'company' => $this->company,
            'employment_email' => $this->employment_email,
            'employment_city' => $this->employment_city,
            'type' => $this->type,
            'pin_code' => $this->pin_code,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'is_linked' => $this->is_linked,
            'linked_user_id' => $this->linked_user_id,
            'entitled_to_practis_college_id' => $this->entitled_to_practis_college_id
        ];
    }   

}
