<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackTagResource extends JsonResource
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
            'name'=> $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }   

}
