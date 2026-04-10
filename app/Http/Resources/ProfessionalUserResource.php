<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalUserResource extends JsonResource
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
            'first_name'=> $this->first_name,
            'last_name' => $this->last_name,
            'profile_image' => !empty(userDir($this->unique_id) . '/profile/' . $this->profile_image) ? fetchProfileImage($this->unique_id, 'r', $this->role) : 'public/assets/images/default.jpg',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }   

}
