<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'unique_id'=>$this->unique_id,
            'profile_image_url'=>fetchProfileImage($this->unique_id,'r',$this->role),
            'country' => $this->country_id,
            'state' => $this->state,
            'city' => $this->city,
            'country_code' => $this->country_code,
            'phone_no' => $this->phone_no,
            'role' => $this->role,
            'status'=>$this->status,
        ];
    }
}
