<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfessionalListCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    

    public function toArray($request)
    {
        $professional_other_details = ProfessionalOtherDetailsResource::collection($this->professionalDetail);
        $other_details = array();
        if(!empty($professional_other_details)){
            foreach($professional_other_details  as $detail){
                $other_details[str_slug($detail['meta_key'],'_')] = $detail['meta_value'];
            }
        }

      
        if(!empty($this->userDetails)){
            $company_logo = fetchCompanyLogo($this->userDetails->company_logo);
        }else{
            $company_logo = url("assets/svg/browse.svg");
        }


        return [
            'id' => $this->id,
            'unique_id' => $this->unique_id,
            'view_profile_url' => $this->view_profile_url,
            'college_id' => $this->college_id,
            'name' => $this->name,
            'slug' => str_slug($this->name),
            'company' => $this->company,
            'company_type' => $this->company_type,
            'entitled_to_practise' => $this->entitled_to_practise,
            'entitled_to_practis_college_id' => $this->entitled_to_practis_college_id,
            'suspension_revocation_history' => $this->suspension_revocation_history,
            'employment_company' => $this->employment_company,
            'employment_startdate' => $this->employment_startdate,
            'employment_country' => $this->employment_country,
            'employment_state' => $this->employment_state,
            'employment_city' => $this->employment_city,
            'employment_email' => $this->employment_email,
            'employment_phone' => $this->employment_phone,
            'agentsinfo' => $this->agentsinfo,
            'license_historyclass' => $this->license_historyclass,
            'license_historystartdate' => $this->license_historystartdate,
            'license_historyexpiry_date' => $this->license_historyexpiry_date,
            'license_history_status' => $this->license_history_status,
            'type' => $this->type,
            'professional_website_detail' => $this->professional_website_detail,
            'is_linked' => $this->is_linked,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'pin_code' => $this->pin_code,
            'claim_profile' => $this->claim_profile,
            'professional_address_detail' => $this->professional_address_detail,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'added_by' =>!empty($this->userAdded)?$this->userAdded->first_name." ".$this->userAdded->last_name:"N/A",
            'assigned_to' =>!empty($this->AssignedTo)?$this->AssignedTo->first_name." ".$this->AssignedTo->last_name:"N/A",
            'show_rating' => 0,
            'company_logo' =>$company_logo,
            'category' =>  new CategoryResource($this->category),
            'user' =>  new ProfessionalUserResource($this->user),
            'user_details' =>  new ProfessionalUserDetailResource($this->userDetails),
            'professional_services' => ProfessionalServicesResource::collection($this->services),
            'locations' => ProfessionalCompanyLocationsResource::collection($this->companyLocations),
            'professional_other_details' => $other_details,
        ];
    }   


}
