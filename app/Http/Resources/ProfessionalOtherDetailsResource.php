<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalOtherDetailsResource extends JsonResource
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
            'meta_key'=> $this->meta_key,
            'meta_value' => $this->meta_value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }   

}
