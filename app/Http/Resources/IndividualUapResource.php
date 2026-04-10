<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndividualUapResource extends JsonResource
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
                'first_name' => ($this->first_name?decrypt($this->first_name):''),
                'last_name' => ($this->last_name?decrypt($this->last_name):''),
                'why_uap' =>($this->why_uap?decrypt($this->why_uap):'') ,
                'social_mediumn_link' =>($this->social_mediumn_link?decrypt($this->social_mediumn_link):''), 
                'phone_no' => ($this->phone_no?decrypt($this->phone_no):''),
                'address' => ($this->address?decrypt($this->address):''),
                'city' => ($this->city?decrypt($this->city):''),
                'state' => ($this->state?decrypt($this->state):''),
                'country_code' => ($this->country_code?decrypt($this->country_code):''),
                'google_review' => ($this->google_review?decrypt($this->google_review):''),
                'website' => ($this->website?decrypt($this->website):''),
                'country' =>($this->country?decrypt($this->country):'')       
            ];
        }
    }

