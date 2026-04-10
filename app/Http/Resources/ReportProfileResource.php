<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $evidences = [];
        if($this->evidences != ''){
            foreach(explode(',',$this->evidences) as $value){
                $evidences[] = awsFilePreview(config("awsfilepath.report_profile"). '/' .  $value);
            }
        }
        return [
            'unique_id' => $this->unique_id,
            'professional_id' => $this->professional_id,
            'evidences' => $evidences,
            'subject' => $this->subject,
            'reason' => $this->reason,
            'status' => $this->status,
            'added_by' => $this->addedBy->first_name." ".$this->addedBy->last_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];


        
    }
}
