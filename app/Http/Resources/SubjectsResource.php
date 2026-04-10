<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectsResource extends JsonResource
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
            'added_by' => $this->added_by,
            'user_added' => $this->userAdded->first_name." ". $this->userAdded->last_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }   

}
