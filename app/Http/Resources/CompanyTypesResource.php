<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class CompanyTypesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */


     public function toArray($request)
    {
        return [
            'unique_id' => $this->unique_id,
            'name' => $this->name,
            'added_by' => $this->added_by,
            'user_added' => $this->userAdded->first_name." ". $this->userAdded->last_name,
            'created_at' => dateFormat($this->created_at),
            'updated_at' => dateFormat($this->updated_at),
        ];
    }   

}
