<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialMediaUapResource extends JsonResource
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
                'comments' => ($this->comments?decrypt($this->comments):''),
                'social_link' => ($this->social_link?decrypt($this->social_link):'')         
            ];
        }

}
