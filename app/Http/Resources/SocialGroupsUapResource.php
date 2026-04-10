<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialGroupsUapResource extends JsonResource
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
                'social_media_groups' => ($this->social_media_groups?decrypt($this->social_media_groups):'') ,
            ];
        }
}
