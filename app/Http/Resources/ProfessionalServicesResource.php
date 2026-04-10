<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalServicesResource extends JsonResource
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
            'parent_service_id'=> $this->parent_service_id,
            'service_id' => $this->service_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'service_name'=>$this->ImmigrationServices->name??'',
            'immigration_services'=> new ImmigrationServicesResources($this->ImmigrationServices) 
        ];
    }   

}
