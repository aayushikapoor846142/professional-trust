<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\Country;
use App\Models\Languages;
use App\Models\Professional;
use App\Models\OtherProfessionalDetail;
use App\Models\CdsProfessionalLicense;
use App\Models\CdsRegulatoryBody;
use App\Models\CdsRegulatoryCountry;
use App\Models\CdsProfessionalDocuments;
use App\Models\DomainVerify;
use App\Models\Types;
use App\Services\UserService;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function edit()
    {
        $viewData['pageTitle'] = "Edit Profile";
        $countries = Country::all();
        $viewData['countries'] = $countries;
   
        $user = User::where("id", \Auth::user()->id)->first();
        $viewData['user'] = $user;

        if (\Auth::user()->role === 'professional') {
            $professionalList = Professional::with(['professionalWebsiteDetail', 'professionalAboutDetail'])
                ->where(['is_linked' => 1, 'linked_user_id' => \Auth::user()->id])
                ->first();
            
            if (empty($professionalList)) {
                $professionalList = Professional::create([
                    'unique_id' => randomNumber(),
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'is_linked' => 1,
                    'linked_user_id' => \Auth::user()->id
                ]);
            }

            $extraDetails = OtherProfessionalDetail::where('professional_id', $professionalList->id)->get();

            if ($extraDetails->isEmpty()) {
                foreach (extraDetails() as $value) {
                    OtherProfessionalDetail::create([
                        'professional_id' => $professionalList->id,
                        'unique_id' => randomNumber(),
                        'meta_key' => $value,
                        'meta_value' => '',
                        'added_by' => \Auth::user()->id
                    ]);
                }
            }

            $license_detail = CdsProfessionalLicense::where('added_by', $user->id)->latest()->first();
           
            if (!empty($license_detail)) {
                $regulatory_bodies = CdsRegulatoryBody::where('regulatory_country_id', $license_detail->regulatory_country_id)->get();
            } else {
                $regulatory_bodies = CdsRegulatoryBody::get();
            }

            $user_details = UserDetails::where('user_id', \Auth::user()->id)->first();
            
            $viewData['professionalList'] = $professionalList;
            $viewData['regulatory_bodies'] = $regulatory_bodies;
            $viewData['regulatory_countries'] = CdsRegulatoryCountry::get();
            
            if ($user->cdsCompanyDetail) {
                $viewData['document'] = CdsProfessionalDocuments::where('company_id', $user->cdsCompanyDetail->id)->get();
            } else {
                $viewData['document'] = collect();
            }
            
            $viewData['license_detail'] = $license_detail; 
            $viewData['types'] = Types::all();
            $viewData['user_details'] = $user_details;
        }

        $viewData['languages'] = Languages::all();
        
        if (auth()->user()->role == "professional") {
            $viewData['domain_data'] = DomainVerify::where("user_id", auth()->user()->id)->first();
            return view('admin-panel.professional-edit-profile', $viewData);
        } else {
            return view('admin-panel.edit-profile', $viewData);
        }       
    }

    public function update(UpdateProfileRequest $request)
    {
        $result = $this->userService->updateProfile(\Auth::user()->id, $request->validated());
        
        return response()->json($result);
    }

    public function changePassword($id)
    {
        $viewData['pageTitle'] = "Change Password";
        $viewData['user'] = User::where("unique_id", $id)->first();
        return view("admin-panel.change-password", $viewData);
    }

    public function updatePassword($id, UpdatePasswordRequest $request)
    {
        $result = $this->userService->updatePassword($id, $request->validated());
        
        return response()->json($result);
    }

    public function show($page = '')
    {
        $viewData['pageTitle'] = "My Profile";
        $user = User::where("id", \Auth::user()->id)->first();
        $viewData['user'] = $user;

        if ($page == '') {
            $page = 'personal';
        }

        $viewData['active_tab'] = $page;
        return view("admin-panel.profile", $viewData);
    }

    public function myProfile()
    {
        $viewData['pageTitle'] = "My Profile";
        $user = User::where("id", \Auth::user()->id)->first();
        $viewData['user'] = $user;
        return view("admin-panel.my-profile", $viewData);
    }
} 