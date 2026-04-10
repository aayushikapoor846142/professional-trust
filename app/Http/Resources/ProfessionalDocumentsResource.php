<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalDocumentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
    return [
           
            'license' => $this->license,
            'photo_id' => $this->photo_id,
            'professional_id' => $this->professional_id,
            'incorporation_certification' => $this->incorporation_certification,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
