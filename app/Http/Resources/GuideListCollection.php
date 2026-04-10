<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuideListCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */

    public function toArray($request)
    {
        $images= explode(',',$this->images);
        // Build the full image URLs (assuming storage is in 'public/storage/images/')
        $imageUrls = array_map(function($image) {
            return guideDir('t').$image; // Or wherever your images are stored
        }, $images);

            return [
            'name' => $this->name,
            'unique_id' => $this->unique_id,
            'slug' => $this->slug,
            'images' => $imageUrls,
            'description' => $this->description,
            'summary' => $this->summary,
            'reading_time' => $this->reading_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'added_by' =>!empty($this->userAdded)?$this->userAdded->first_name." ".$this->userAdded->last_name:"N/A",
            'category' => $this->category ? new CategoryResource($this->category) : "N/A",  // Corrected handling of category
        ];
    }   

}
