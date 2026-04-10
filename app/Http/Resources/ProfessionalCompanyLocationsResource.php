<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalCompanyLocationsResource extends JsonResource
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
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'pincode' => $this->pincode
        ];
    }   

}
