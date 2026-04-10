<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CorporateUapResource extends JsonResource
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
                'company_name' => ($this->company_name?decrypt($this->company_name):''),
                'first_name' => ($this->first_name?decrypt($this->first_name):''),
                'last_name' => ($this->last_name?decrypt($this->last_name):''),
                'why_uap' => ($this->why_uap?decrypt($this->why_uap):''),
                'phone_no' => ($this->phone_no?decrypt($this->phone_no):''),
                'address' => ($this->address?decrypt($this->address):''),
                'city' => ($this->city?decrypt($this->city):''),
                'state' => ($this->state?decrypt($this->state):''),
                'registration_number' => ($this->registration_number?decrypt($this->registration_number):''),
                'country_code' => ($this->country_code?decrypt($this->country_code):''),
                'country' =>($this->country?decrypt($this->country):'') ,
                'social_mediumn_link' => ($this->social_mediumn_link?$this->social_mediumn_link:''),
       
            ];
        }
}
