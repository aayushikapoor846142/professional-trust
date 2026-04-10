<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CdsProfessionalLicense;
use App\Models\CdsRegulatoryCountry;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\CdsRegulatoryBody;
use App\Models\OtpVerify;
use App\Models\Professional;
use App\Models\ProfessionalDocuments;
use App\Models\CdsProfessionalCompany;
use App\Models\TempUser;
use App\Models\UserDetails;
use App\Models\Types;
use App\Models\LicenseType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;  
use Illuminate\Support\Facades\Session;
use View;
use App\Rules\StringLimitRule;
use App\Models\CompanyLocations;
use App\Models\CdsProfessionalDocuments;

use App\Models\AppointmentBooking;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'first_name' => $data['name'],
//          'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }


    public function showNormalRegistraion()
    {
        return redirect('/');
        $data['countries'] = Country::all();
        return view('auth.register', $data);
    }
    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
      
        // return redirect('/');
        if(auth()->check()){
            if(auth()->user()->role != 'professional'){
                return redirect('/');
            }
        }

        if(auth()->check()){
            $personal_address = CompanyLocations::where(function ($query) {
                $query->where('user_id',\Auth::user()->id);
            })
            ->orderBy('id', "desc")
            ->where('type_label','personal')
            ->count();

            $company_location = CompanyLocations::where(function ($query) {
                $query->where('user_id',\Auth::user()->id);
            })
            ->orderBy('id', "desc")
            ->where('type_label','company')
            ->count();
        }else{
            $personal_address =  0;
            $company_location = 0;
        }
        
        $viewData['countries'] = Country::all();
        $viewData['personal_address'] = $personal_address;
        $viewData['company_location'] = $company_location;
        $viewData['company_type'] = Types::all();
        $viewData['license_type'] = LicenseType::all();
        $viewData['regulatory_countries'] = CdsRegulatoryCountry::get();
        $viewData['countries'] = Country::get();
        $regulatory_bodies = array();

        if(auth()->check()){
            $user = User::where("id",auth()->user()->id)->first();
            $viewData['user'] = $user;
            $viewData['user_detail'] = $user->userDetail;
            $viewData['company_detail'] = $user->cdsCompanyDetail;
            $license_number = '';
            $claimed_profile = array();
            $profile_type = $user->userDetail->profile_type?? '';

            if($profile_type == 'claim_profile'){
                $check = CdsProfessionalCompany::where("user_id",auth()->user()->id)->where('is_primary',1)->first();
                if($request->has('profile_id')){
                    $claimed_profile = Professional::where('unique_id',$request->get('profile_id'))->first();
                }else{
                    $claimed_profile = Professional::where('id',$check->claimed_company_id)->first();
                }
                $license_number = $claimed_profile->college_id??'';
            }
            $license_detail= CdsProfessionalLicense::where("user_id",auth()->user()->id)->latest()->first();
           
            if(!empty($license_detail)){
                $regulatory_bodies = CdsRegulatoryBody::where('regulatory_country_id',$license_detail->regulatory_country_id)->get();
            }

         
            $viewData['claim_profile'] = $claimed_profile;
            $viewData['license_number'] = $license_number;
            $viewData['regulatory_bodies'] = $regulatory_bodies;
            $viewData['license_detail'] = CdsProfessionalLicense::where("user_id",auth()->user()->id)->first();
            $viewData['identifyDocuments'] = CdsProfessionalDocuments::where("user_id",auth()->user()->id)->where('document_type','proof_of_identify')->get();
            $viewData['incorporationDocuments'] = CdsProfessionalDocuments::where("user_id",auth()->user()->id)->where('document_type','incorporation_certificate')->get();
            $viewData['licenseDocuments'] = CdsProfessionalDocuments::where("user_id",auth()->user()->id)->where('document_type','license')->get();
        }
        if($request->has('profile_type')){
            $viewData['profile_type'] = $request->query('profile_type');
        }else{
            $viewData['profile_type'] = '';
        }

        if($request->has('profile_id')){
            $viewData['profile_id'] = $request->query('profile_id');
        }else{
            $viewData['profile_id'] = '';
        }
       
        return view('auth.professional-registers', $viewData);
    }

    // public function showRegistrationForms()
    // {
        
    //     $data['countries'] = Country::all();
    //     $data['company_type'] = Types::all();
    //     $data['license_type'] = LicenseType::all();
    //     return view('auth.professional-registers', $data);
    // }

    

    public function normalUserSignup(Request $request)
    {
        
    
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users','valid_email'],
            'gender' =>  ['required'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[a-z]/', // must contain at least one lowercase letter
                'regex:/[A-Z]/', // must contain at least one uppercase letter
                'regex:/[0-9]/', // must contain at least one digit
                'regex:/[!@#$%^&*()\-_=+{};:,<.>ยง~]/', // must contain at least one special character
            ],
            'password_confirmation' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/', // must contain at least one lowercase letter
                'regex:/[A-Z]/', // must contain at least one uppercase letter
                'regex:/[0-9]/', // must contain at least one digit
                'regex:/[!@#$%^&*()\-_=+{};:,<.>ยง~]/', // must contain at least one special character
            ],
            'g-recaptcha-response' => ['required']
        ], [
            'password.regex' => 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
            'password_confirmation.regex' => 'Password confirmation must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
            'g-recaptcha-response.required' => 'Please complete the CAPTCHA to proceed.',
            // 'g-recaptcha-response.captcha' => 'CAPTCHA verification failed. Please try again.',
        ]);

       
        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = [];

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }

        $json_data = $request->all();
        $json_data['role'] = "client";
        $user_object = TempUser::updateOrCreate(['email'=>$request->input("email"),'type'=>'signup'],['json_data'=>json_encode($json_data)]);
        \Session::put('email', $request->input("email"));
        $otp_object = sendOtp($request->input("email"),"emails.otp-mail",$request->input('type'));
       // dd( $otp_object);
        $response['status'] = true;
        $response['redirct_url'] = url('register/verify-otp', ['id' => $otp_object->unique_id]);
        $response['message'] = "OTP sent to your email.";
        return response()->json($response);

       
    }

    public function professionalSignup(Request $request)
    {
        try {

            if (!auth()->check()) {
             
                $validator = Validator::make($request->all(), [
                    'first_name' => ['required', 'string', new StringLimitRule()],
                    'last_name' => ['nullable', 'string', new StringLimitRule()],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'valid_email'],
                    'terms_condition' => ['required'],
                    'password' => [
                        'required',
                        'string',
                        'confirmed',
                        'password_validation'
                    ],
                    'password_confirmation' => [
                        'required',
                        'string',
                       'password_validation'
                    ],
                    'g-recaptcha-response' => ['required']
                ], [
                    'user_country_code.required' => 'Country code is required',
                    'user_phone_no.required' => 'Phone number is required',
                    'password.regex' => 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
                    'password_confirmation.regex' => 'Password confirmation must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
                    'g-recaptcha-response.required' => 'Please complete the CAPTCHA to proceed.',
                    // 'g-recaptcha-response.captcha' => 'CAPTCHA verification failed. Please try again.',
                ]);


                if ($validator->fails()) {
                    $response['status'] = false;
                    $error = $validator->errors()->toArray();
                    $errMsg = [];

                    foreach ($error as $key => $err) {
                        $errMsg[$key] = $err[0];
                    }
                    $response['message'] = $errMsg;
                    return response()->json($response);
                }

                $json_data = $request->all();
                $json_data['role'] = "professional";
                $json_data['user_location'] = detectUserLocation();
               
                $user_object = TempUser::updateOrCreate(
                    [
                        'email' => $request->input("email"),
                        'type' => 'signup'
                    ],
                    ['json_data' => json_encode($json_data)]
                );
                // dd($user_object);

                \Session::put('email', $request->input("email"));

                $otp_object = sendOtp($request->input("email"), "emails.otp-mail");

                $response['status'] = true;
                $response['redirct_url'] = route('professional.verify.otp', ['id' => $otp_object->unique_id, 'profile_type' => $request->profile_type,'profile_id' => $request->profile_id]);
                $response['message'] = "OTP sent to your email.";

                return response()->json($response);
            } else {
                

                // choose option
                if($request->input("form_type") == 'choose_type'){
                    $validator = Validator::make($request->all(), [
                        'choose_option' => 'required',
                        'license_number' => 'required_if:choose_option,claim_profile',
                    ],
                    [
                        'choose_option.required' => "Select the option",
                        'license_number.required_if' => "License number is required"
                    ]);

                    if ($validator->fails()) {
                        $response['status'] = false;
                        $error = $validator->errors()->toArray();
                        $errMsg = [];
    
                        foreach ($error as $key => $err) {
                            $errMsg[$key] = $err[0];
                        }
                        $response['message'] = $errMsg;
                        return response()->json($response);
                    }
                    if($request->input("choose_option") == 'new_signup'){
                        $user = User::find(auth()->user()->id);
                        if($user->completed_step < 2){
                            $user->completed_step = '2';
                        }
                        $user->save();
    
                        $userDetail = UserDetails::where("user_id",auth()->user()->id)->first();
                        $userDetail->profile_type = 'new_signup';
                        $userDetail->save();
                    }
                    if($request->input("choose_option") == 'claim_profile'){
      
                        $claimed_profile = Professional::where("id",$request->license_number)->first();
                        // pre($claimed_profile->toArray());
                        // exit;
                        $owner_full_name = $claimed_profile->name;
                        $owner_name = explode(" ",$claimed_profile->name);
                        $user = User::find(auth()->user()->id);
                        $user->first_name = $owner_name[0]??'';
                        $user->last_name = !empty($owner_name[1]??'')?(str_replace($owner_name[0],"",$owner_full_name)):'';
                        if($user->completed_step < 2){
                            $user->completed_step = '2';
                        }
                        $user->save();

                        $userDetail = UserDetails::where("user_id",auth()->user()->id)->first();
                        $userDetail->profile_type = 'claim_profile';
                        $userDetail->save();

                        

                        $claimed_profile = Professional::where("id",$request->license_number)->first();
                        // pre($claimed_profile->toArray());
                        // exit;
                        $owner_full_name = $claimed_profile->name;
                        $owner_name = explode(" ",$claimed_profile->name);
                        $user = User::find(auth()->user()->id);
                        $user->first_name = $owner_name[0]??'';
                        $user->last_name = !empty($owner_name[1]??'')?(str_replace($owner_name[0],"",$owner_full_name)):'';
                        if($user->completed_step < 2){
                            $user->completed_step = '2';
                        }
                        $user->save();

                        $other_profiles = Professional::where("college_id",$claimed_profile->college_id)->get();
                        // Compamy Info
                        foreach($other_profiles as $profile){
                            $check = CdsProfessionalCompany::where("user_id",auth()->user()->id)
                                            ->where('claimed_company_id',$profile->id)
                                            ->first();
                            if(!empty($check)){
                                $cds_company = CdsProfessionalCompany::find($check->id);
                            }else{
                                $cds_company = new CdsProfessionalCompany();
                            }
                            $cds_company->user_id = auth()->user()->id;
                            $cds_company->company_name = $profile->company;
                            $cds_company->is_claimed  = 1;
                            $cds_company->claimed_company_id = $profile->id;
                            if($request->license_number == $profile->id){
                                $cds_company->is_primary = 1;
                            }else{
                                $cds_company->is_primary = 0;
                            }
                            $cds_company->save();

                            $address = CompanyLocations::where(function ($query) {
                                $query->where('user_id',\Auth::user()->id);
                            })
                            ->where('company_id',$cds_company->id)
                            ->where('type_label','company')
                            ->orderBy('id', "desc")
                            ->first();
    
                            if(!empty($address)){
                                CompanyLocations::where('id',$address->id)->update([
                                    'user_id' => \Auth::user()->id,
                                    'address_1' => $address->address_1??'Missing Info',
                                    'address_2' => $address->address_1??'Missing Info',
                                    'country' => $address->address_1??$claimed_profile->employment_country,
                                    'state' => $address->address_1??$claimed_profile->employment_state,
                                    'city' => $address->address_1??$claimed_profile->employment_city,
                                    'pincode' => $address->address_1??'Missing Info',
                                    'added_by' => \Auth::user()->id,
                                    'type_label' => 'company'
                                ]);
                            }else{
                                $checkPrimary = CdsProfessionalCompany::where("user_id",auth()->user()->id)
                                ->where('is_primary',1)
                                ->first();
                              
                                $is_company_primary = 0;
                                if(($checkPrimary->id ?? '') == $cds_company->id){
                                    $is_company_primary = 1;
                                }
                                
                                CompanyLocations::create([
                                    'unique_id' => randomNumber(),
                                    'user_id' => \Auth::user()->id,
                                    'address_1' => 'Missing Info',
                                    'address_2' => 'Missing Info',
                                    'country' => $claimed_profile->employment_country,
                                    'state' => $claimed_profile->employment_state,
                                    'city' => $claimed_profile->employment_city,
                                    'pincode' => 'Missing Info',
                                    'added_by' => \Auth::user()->id,
                                    'type_label' => 'company',
                                    'company_id' => $cds_company->id,
                                    'is_primary' => $is_company_primary
                                ]);
                            }
                        }
                        $regulatory_country = CdsRegulatoryCountry::where("name",$claimed_profile->employment_country)->first();
                        $regulatory_body = array();
                        $license_number = '';
                        $prefix = '';
                        $license_no = $claimed_profile->college_id;
                        if(!empty($regulatory_country)){
                            preg_match('/([A-Za-z]+)([0-9]+)/', $license_no, $matches);
                            $prefix = $matches[1]; // Contains the letters part (e.g., "R")
                            $license_number = $matches[2];
                            $regulatory_body = CdsRegulatoryBody::where("regulatory_country_id",$regulatory_country->id)->where('license_prefix',$prefix)->first();
                        }
                        
                        $check = CdsProfessionalLicense::where("user_id",auth()->user()->id)->first();
                        if(!empty($check)){
                            $cds_license = CdsProfessionalLicense::find($check->id);
                        }else{
                            $cds_license = new CdsProfessionalLicense();
                        }
                        $cds_license->user_id = auth()->user()->id;
                        $cds_license->regulatory_country_id =  $regulatory_country->id??'0';
                        $cds_license->regulatory_body_id = $regulatory_body->id??'0';
                        $cds_license->entitled_to_practice  = $request->entitled_to_pratice;
                        $cds_license->license_prefix  = $prefix;
                        $cds_license->license_number  = $license_number;
                        $cds_license->country_of_practice  = '';
                        $cds_license->license_status  = '';
                        $cds_license->added_by = auth()->user()->id;
                        $cds_license->do_you_more_license  = 0;
                        $cds_license->save();
                    }
                    $response['status'] = true;
                    $response['message'] = "Data saved successfully";
                    return response()->json($response);
                }


                // personal detail
                if($request->input("form_type") == 'personal_detail'){
                    $validator = Validator::make($request->all(), [
                        'first_name' => ['required', 'string','string_limit'],
                        'last_name' => ['nullable', 'string','string_limit'],
                        'country_code' => ['required'],
                        'phone_no' => ['required','phone_validation'],
                        'gender' => ['required'],
                    ]);

                    if ($validator->fails()) {
                        $response['status'] = false;
                        $error = $validator->errors()->toArray();
                        $errMsg = [];
    
                        foreach ($error as $key => $err) {
                            $errMsg[$key] = $err[0];
                        }
                        $response['message'] = $errMsg;
                        $response['error_type'] = "validation";
                        return response()->json($response);
                    }
                    $personalLocations =  CompanyLocations::where(function ($query) {
                        $query->where('user_id',\Auth::user()->id);
                    })
                    ->orderBy('id', "desc")
                    ->where('type_label','personal')
                    ->count();
            
                    if($personalLocations == 0){
                        $response['status'] = false;
                        $response['error_type'] = 'address';
                        $response['message'] = 'Please add address';
                        return response()->json($response);
                    } 
                    $user = User::find(auth()->user()->id);
                    $user->first_name = $request->input("first_name");
                    $user->last_name = $request->input("last_name");
                    $user->country_code = $request->input("country_code");
                    $user->phone_no = $request->input("phone_no");
                    $user->date_of_birth = $request->input("date_of_birth");
                    $user->gender = $request->input("gender");
                    if($user->completed_step < 3){
                        $user->completed_step = '3';
                    }
                    $user->save();
                   
                    $response['status'] = true;
                    $response['message'] = "Data saved successfully";
                    return response()->json($response);
                }

                if($request->input("form_type") == 'company_detail'){
                    $validator = Validator::make($request->all(), [
                        'company_name' => ['required', 'string'],
                        'owner_type' => ['required'],
                        'company_type' => 'required_if:owner_type,Self Employed',
                    ]);

                    if ($validator->fails()) {
                        $response['status'] = false;
                        $error = $validator->errors()->toArray();
                        $errMsg = [];
    
                        foreach ($error as $key => $err) {
                            $errMsg[$key] = $err[0];
                        }
                        $response['message'] = $errMsg;
                        $response['error_type'] = "validation";
                        return response()->json($response);
                    }
                    $personalLocations =  CompanyLocations::where(function ($query) {
                        $query->where('user_id',\Auth::user()->id);
                    })
                    ->orderBy('id', "desc")
                    ->where('type_label','company')
                    ->count();
            
                    if($personalLocations == 0){
                        $response['status'] = false;
                        $response['error_type'] = 'address';
                        $response['message'] = 'Please add address';
                        return response()->json($response);
                    } 
                    $user = User::find(auth()->user()->id);
                    
                    if($user->completed_step < 4){
                        $user->completed_step = '4';
                    }
                    $user->save();
                    $check = CdsProfessionalCompany::where("user_id",auth()->user()->id)->where('is_primary',1)->first();
                    if(!empty($check)){
                        $cds_company = CdsProfessionalCompany::find($check->id);
                    }else{
                        $cds_company = new CdsProfessionalCompany();
                    }
                    $cds_company->user_id = auth()->user()->id;
                    $cds_company->company_name = $request->company_name;
                    $cds_company->owner_type  = $request->owner_type;
                    $cds_company->company_type  = $request->company_type;
                    if($request->owner_type == 'Employed' && $request->currently_working){
                        $cds_company->currently_working  = $request->currently_working??0;
                    }
                    $cds_company->save();

                    $response['status'] = true;
                    $response['message'] = "Data saved successfully";
                    return response()->json($response);
                }
                if($request->input("form_type") == 'license_detail'){
                    $validator = Validator::make($request->all(), [
                        'regulatory_country_id' => ['required'],
                        'regulatory_body_id' => ['required'],
                        'license_number' => ['required','numeric'],
                        'license_start_date' => ['required'],
                        'license_status' =>['required'],
                        'entitled_to_practice' => ['required']
                    ]);

                    if ($validator->fails()) {
                        $response['status'] = false;
                        $error = $validator->errors()->toArray();
                        $errMsg = [];
    
                        foreach ($error as $key => $err) {
                            $errMsg[$key] = $err[0];
                        }
                        $response['message'] = $errMsg;
                        $response['error_type'] = "validation";
                        return response()->json($response);
                    }
                    $user = User::find(auth()->user()->id);
                    if($user->completed_step < 5){
                        $user->completed_step = '5';
                    }
                    $user->save();  
                    $check = CdsProfessionalLicense::where("user_id",auth()->user()->id)->latest()->first();
                    if(!empty($check)){
                        $cds_license = CdsProfessionalLicense::find($check->id);
                    }else{
                        $cds_license = new CdsProfessionalLicense();
                    }
                    $cds_license->user_id = auth()->user()->id;
                    $cds_license->regulatory_country_id = $request->regulatory_country_id;
                    $cds_license->regulatory_body_id = $request->regulatory_body_id;
                    $cds_license->entitled_to_practice  = $request->entitled_to_practice;
                    $cds_license->license_prefix  = licensePrefix($request->regulatory_body_id);
                    $cds_license->license_number  = $request->license_number;
                    $cds_license->country_of_practice  = $request->country_of_practice;
                    $cds_license->license_status  = $request->license_status;
                    $cds_license->license_start_date  = $request->license_start_date;
                    $cds_license->added_by = auth()->user()->id;
                    $cds_license->do_you_more_license  = $request->do_you_more_license??0;
                    $cds_license->save();

                    $response['status'] = true;
                    $response['message'] = "Data saved successfully";
                    return response()->json($response);
                }

                // document detail
                if($request->input("form_type") == 'document_detail'){
                  
                    $validator = Validator::make($request->all(), [
                        'proof_of_identify' => ['required'],
                        'incorporation_certificate' => ['required'],
                        'license' => ['required','numeric'],
                    ]);

                    if ($validator->fails()) {
                        $response['status'] = false;
                        $error = $validator->errors()->toArray();
                        $errMsg = [];
    
                        foreach ($error as $key => $err) {
                            $errMsg[$key] = $err[0];
                        }
                        $response['message'] = $errMsg;
                        $response['error_type'] = "validation";
                        return response()->json($response);
                    }

                    
                    $user = User::find(auth()->user()->id);
                    // if($user->completed_step < 5){
                    //     $user->completed_step = '5';
                    // }
                    $user->status = 'pending';
                    $user->save();  
                   
                    $response['status'] = true;
                    $response['redirect_back'] = url('/professional/approval-pending');
                    $response['message'] = "Data saved successfully";
                    return response()->json($response);
                }
            }

            

        } catch (\Exception $e) {
            $response['status'] = false;
            $response['message'] = $e->getMessage()." LINE: ".$e->getLine();

            return response()->json($response);
        }

    }
    // public function professionalSignupOld(Request $request)
    // {
    //     try {

    //         if (!auth()->check()) {
    //             $validator = Validator::make($request->all(), [
    //                 'first_name' => ['required', 'string', new StringLimitRule()],
    //                 'last_name' => ['nullable', 'string', new StringLimitRule()],
    //                 'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'valid_email'],
    //                 'terms_condition' => ['required'],
    //                 'password' => [
    //                     'required',
    //                     'string',
    //                     'confirmed',
    //                     'password_validation'
    //                 ],
    //                 'password_confirmation' => [
    //                     'required',
    //                     'string',
    //                    'password_validation'
    //                 ],
    //                 'g-recaptcha-response' => ['required']
    //             ], [
    //                 'user_country_code.required' => 'Country code is required',
    //                 'user_phone_no.required' => 'Phone number is required',
    //                 'password.regex' => 'Password must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
    //                 'password_confirmation.regex' => 'Password confirmation must contain at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.',
    //                 'g-recaptcha-response.required' => 'Please complete the CAPTCHA to proceed.',
    //                 // 'g-recaptcha-response.captcha' => 'CAPTCHA verification failed. Please try again.',
    //             ]);


    //             if ($validator->fails()) {
    //                 $response['status'] = false;
    //                 $error = $validator->errors()->toArray();
    //                 $errMsg = [];

    //                 foreach ($error as $key => $err) {
    //                     $errMsg[$key] = $err[0];
    //                 }
    //                 $response['message'] = $errMsg;
    //                 return response()->json($response);
    //             }

    //             $json_data = $request->all();
    //             $json_data['role'] = "professional";
    //             $json_data['user_location'] = detectUserLocation();
    //             $user_object = TempUser::updateOrCreate(
    //                 [
    //                     'email' => $request->input("email")
    //                     ,
    //                     'type' => 'signup'
    //                 ],
    //                 ['json_data' => json_encode($json_data)]
    //             );
    //             // dd($user_object);

    //             \Session::put('email', $request->input("email"));

    //             $otp_object = sendOtp($request->input("email"), "emails.otp-mail");

    //             $response['status'] = true;
    //             $response['redirct_url'] = route('professional.verify.otp', ['id' => $otp_object->unique_id]);
    //             $response['message'] = "OTP sent to your email.";

    //             return response()->json($response);
    //         } else {
    //             $user = \Auth::user();
    //             if($request->input("form_type") == 'choose_type'){
    //                 $validator = Validator::make($request->all(), [
    //                     'choose_option' => 'required',
    //                     'license_number' => 'required_if:choose_option,claim_profile',
    //                 ],
    //                 [
    //                     'choose_option.required' => "Select the option",
    //                     'license_number.required_if' => "License number is required"
    //                 ]);
    //             }

    //             if ($validator->fails()) {
    //                 $response['status'] = false;
    //                 $error = $validator->errors()->toArray();
    //                 $errMsg = [];

    //                 foreach ($error as $key => $err) {
    //                     $errMsg[$key] = $err[0];
    //                 }
    //                 $response['message'] = $errMsg;
    //                 return response()->json($response);
    //             }
    //             if($request->input("choose_option") == 'new_signup'){
                    
    //             }
    //             $user->country_code = $request->user_country_code;
    //             $user->phone_no = $request->user_phone_no;
    //             $user->gender = $request->gender;
    //             $user->date_of_birth = $request->date_of_birth;
    //             $user->completed_step = '3';
    //             $user->save();

    //             $check_user_detail = UserDetails::where("user_id",\Auth::user()->id)->first();
    //             if(!empty($check_user_detail)){
    //                 $userDetails = UserDetails::find($check_user_detail->id);
    //             }else{
    //                 $userDetails = new UserDetails();
    //                 $userDetails->user_id = $user->id;
    //             }
    //             $userDetails->country_id = $request->country;
    //             $userDetails->state_id = $request->state;
    //             $userDetails->city_id = $request->city;
    //             $userDetails->address = $request->u_address_line_1;
    //             $userDetails->address_2 = $request->address_2;
    //             $userDetails->zip_code = $request->u_pin_code;
    //             $userDetails->save();
                
                
    //             $professional = new Professional();
    //             //  $professional->unique_id = $unique_id;
    //             $professional->college_id = $request->license_no;
    //             $professional->company = $request->company_name;
    //             $professional->name = $request->owner_name;
    //             $professional->entitled_to_practis_college_id = $request->license_no;
    //             $professional->entitled_to_practise = $request->entitled_to_practice;
    //             $professional->type = $request->company_type;
    //             $professional->employment_startdate = $request->employment_start_date;
    //             $professional->employment_country = $request->company_country;
    //             $professional->employment_state = $request->company_state;
    //             $professional->employment_city = $request->company_city;
    //             $professional->employment_email = $request->company_email;
    //             $professional->address_line_1 = $request->address_line_1;
    //             $professional->address_line_2 = $request->address_line_2;
    //             $professional->pin_code = $request->pin_code;
    //             $professional->company_type = $request->license_type;
    //             $professional->employment_phone = $request->country_code . '-' . $request->phone_no;
    //             $professional->added_by = \Auth::user()->id;
    //             $professional->is_linked = 1;
    //             $professional->linked_user_id = \Auth::user()->id;
    //             $professional->save();

    //             $record = Professional::find($professional->id);

    //             $prof_document = new ProfessionalDocuments();
    //             // $unique_id = randomNumber();
    //             //  $prof_document->unique_id = $unique_id;
    //             $prof_document->professional_id = $record->id;
    //             $prof_document->proof_of_identity = $request->pf_evidenence;
    //             $prof_document->incorporation_certification = $request->ic_evidenence;
    //             $prof_document->license = $request->lc_evidenence;
    //             $prof_document->save();
    //             session()->forget('activeStep');
    //             $mailData = ['name' => auth()->user()->first_name. " ".auth()->user()->last_name];
    //             $view = \View::make('emails.professional-welcome-mail', $mailData);
    //             $message = $view->render();

    //             $parameter = [
    //                 'to' => auth()->user()->email,
    //                 'to_name' => auth()->user()->first_name,
    //                 'message' => $message,
    //                 'subject' =>"Welcome to ".siteSetting('company_name'),
    //                 'view' => 'emails.professional-welcome-mail',
    //                 'data' => $mailData,
    //             ];
    //             // Send the email
    //             $mailRes = sendMail($parameter);

    //             $staffs  = User::where("role","admin")->get();
    //             foreach($staffs as $staff){
    //                 $mailData  = array();
    //                 $mailData = ['name' => auth()->user()->first_name. " ".auth()->user()->last_name,'action'=>"new","professional"=>auth()->user()];
    //                 $view = \View::make('emails.new-professional-added', $mailData);
    //                 $message = $view->render();

    //                 $parameter = [
    //                     'to' => $staff->email,
    //                     'to_name' => $staff->first_name,
    //                     'message' => $message,
    //                     'subject' =>siteSetting("company_name").": New Professional has signup",
    //                     'view' => 'emails.new-professional-added',
    //                     'data' => $mailData,
    //                 ];
    //                 // Send the email
    //                 $mailRes = sendMail($parameter);
    //             }
                
    //             User::where('id', \Auth::user()->id)->update(['status' => 'pending']);
    //             \Session::flash("signup_success", "Signup Process successfull");
    //             $response['status'] = true;
    //             $response['redirct_url'] = url('/professional/approval-pending');
    //             $response['message'] = "Signup Successfully";

    //             return response()->json($response);
    //         }

    //     } catch (\Exception $e) {
    //         $response['status'] = false;
    //         $response['message'] = $e->getMessage()." LINE: ".$e->getLine();

    //         return response()->json($response);
    //     }

    // }

    public  function successProfessionalProfile(){
        if(auth()->check()){
            if(auth()->user()->status == 'active'){
                return redirect(baseUrl('/'));
            }elseif(auth()->user()->status == 'pending'){
                return view('auth.professional-profile-success');
            }elseif(auth()->user()->status == 'draft'){
                return redirect('/register/professional');
            }else{
                return redirect('/');
            }
            
        }else{
            return redirect('/');
        }
    }

    public  function accountSuspend(){
        return view('auth.user-accout-suspend');
    }
    public function checkCompanyDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_country_code' => ['nullable'],
            'gender' => ['required'],
            'user_phone_no' => ['required'],
            'company_phone_no' => ['nullable'],
            'country' => ['nullable'],
            'state' => ['nullable'],
            'city' => ['nullable'],
            'u_address_line_1' => ['nullable'],
            'u_pin_code' => ['nullable', 'numeric'],
            'country_code' => ['nullable'],
            'phone_no' => ['nullable'],
            'company_type' => ['required', 'string', 'max:50'],
            'company_country' => ['nullable'],
            'company_state' => ['nullable'],
            'company_city' => ['nullable'],
            'address_line_1' => ['nullable'],
            'date_of_birth' => ['required'],
            // 'address_line_2' => ['required'],
            'pin_code' => ['nullable', 'numeric'],
            'u_pin_code' => ['nullable'],
            'company_email' => ['nullable', 'email', 'max:255','valid_email'],
            'employment_start_date' => ['nullable', 'date'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'entitled_to_practice' => ['required','in:Yes,No'],
            'license_type' => ['required'],
            'license_no' => ['nullable','input_sanitize'],
            // 'incorporation_certification' => ['required','mimes:jpeg,png,jpg,gif,bmp,svg,webp,pdf'],
            // 'license_file' => ['required','mimes:jpeg,png,jpg,gif,bmp,svg,webp,pdf'],
            // 'proof_of_identity' => ['required','mimes:jpeg,png,jpg,gif,bmp,svg,webp,pdf'],
        ],
        [
            'user_country_code.required'=>"Country code required",
            'user_phone_no.required'=>"Phone No. required",
            'u_address_line_1.required'=>"Address is required",
            'u_pin_code.required'=>"Pin code is required",
        ]
        );

        // Handle validation failure
        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = [];

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }
       
        $response['status'] = true;
        return response()->json($response);
    }

    // professional verify otp route
    public function professionalOtpVerification(Request $request, $token)
    {

        if (!\Session::get("email")) {
            return redirect("/register/professional");
        }

        $otpVerify = OtpVerify::where('unique_id',$token)->first();

        $otpLocation  = json_decode($otpVerify->user_location,true);
        if(empty($otpVerify)){
            return redirect("/register/professional");
        }
        $viewData['token'] = $token;
        $viewData['otpVerify'] = $otpVerify;
        $viewData['timezone'] = $otpLocation['timezone']??'UTC';
        $viewData['send_otp_url'] = url('register/send-otp');
        $viewData['verify_otp_url'] = route('professional.verify.otp.success');
        return view("auth.professional-verify-otp", $viewData);
    }
    
    public function professionalVerifyOtp(Request $request)
    {

         // Validate the input
         $validator = Validator::make($request->all(), [
            'otp1' => 'required|numeric|digits:1',
            'otp2' => 'required|numeric|digits:1',
            'otp3' => 'required|numeric|digits:1',
            'otp4' => 'required|numeric|digits:1',
            'otp5' => 'required|numeric|digits:1',
            'otp6' => 'required|numeric|digits:1',
        ], [
            'otp6.required' => 'The OTP field is required.',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $errorMessages = [
                'otp6' => 'The OTP field is required.'
            ];

            $response['message'] = $errorMessages;

            return response()->json($response);
        }
       

        // If validation passes, continue processing
        $otpInputs = $request->only(['otp1', 'otp2', 'otp3', 'otp4', 'otp5', 'otp6']);

        // Process the OTP inputs
        // Example: Concatenating the OTP inputs into a single string
        $otp = implode('', $otpInputs);

        $token = $request->input('token');
        

        // Find the OTP record in the database using the token
    
        $otpRecord = OtpVerify::where('unique_id', $token)->first();
        $checkProfile = TempUser::where('email',$otpRecord->email)->latest()->first();
        $chek_json_data = json_decode($checkProfile->json_data,true);
        $response = [];
        // Check if the OTP record exists and is not expired
        if ($otpRecord && $otpRecord->otp === $otp) {
            // Check if the OTP is not expired
            if (Carbon::now()->lessThanOrEqualTo(Carbon::parse($otpRecord->otp_expiry_time))) {
                // OTP is valid
                $temp_user = TempUser::where('email',$otpRecord->email)->latest()->first();
                if(!empty($temp_user)){
                    $json_data = json_decode($temp_user->json_data,true);
                    $object = new User();
                    $object->first_name = $json_data['first_name'];
                    $object->last_name = $json_data['last_name'];
                    $object->email = $json_data['email'];
                    $object->password = bcrypt($json_data['password']);
                    $object->role = $json_data['role'];
                    if($json_data['role']=="professional"){
                        $object->status = "draft";
                        $object->is_verified  = 0;
                        $object->is_active = 0;
                    }elseif($json_data['role']=="client"){
                        $object->status = "active";
                        $object->is_verified  = 1;
                        $object->is_active = 1;
                    }
                    $object->completed_step = 1;
                    $object->added_by = 0;
                    $object->save();
                    $userDetails = new UserDetails();
                    $userDetails->user_id = $object->id;
                    if(isset( $json_data['terms_condition'])){
                        $userDetails->terms_condition = $json_data['terms_condition'];
                    }
                    if(isset( $json_data['profile_type']) &&  $json_data['profile_type'] != ''){
                        $userDetails->profile_type = $json_data['profile_type'];
                    }
                    // claimed_profile save in user det
                    $userDetails->save();
                    TempUser::where("email",$json_data['email'])->delete();
                    OtpVerify::where("email",$json_data['email'])->delete();
                   
                    Auth::login($object);
               
                    $response['status'] = true;
                    $response['message'] = 'Registraion successful.';
                    if($json_data['role'] == "client")
                    {
                        $response['url'] = url('/panel'); // Redirect URL after successful OTP verification
                       
                    }else{
                        session(['ActiveStep' => 2]);
                        Session::forget('email');

                        if($chek_json_data['profile_type'] != ''){
                            $response['url'] = url('/register/professional?profile_type='.$chek_json_data['profile_type'].'&profile_id='.$chek_json_data['profile_id']); // Redirect URL after successful OTP verification
                        }   else{
                            $response['url'] = url('/register/professional'); // Redirect URL after successful OTP verification
                        }
                      
                    }
                  

                    return response()->json($response);
                }else{
                    $response['status'] = false;
                    $response['message'] = 'Sign Up token invalid';
                   
                    return response()->json($response);
                }
             
            } else {
                // OTP expired
                $response['status'] = false;
                $response['message'] = 'The OTP has expired. Please request a new one.';
            }
        } else {

            if ($otpRecord) {
                // OTP is invalid
                $updateOtp = OtpVerify::where("id",$otpRecord->id)->latest()->first();
                $lastAttempt = $updateOtp->attempt;
            
                if($lastAttempt < 2){
                    $updateOtp->increment('attempt');
                    $updateOtp->save();

                    $response['status'] = false;
                    $response['message'] = 'Sign Up token invalid,'.(3-($lastAttempt+1)).' Attempts are left';

                }else{
                    TempUser::where("email",$otpRecord->email)->delete();
                    OtpVerify::where("email",$otpRecord->email)->delete();
                    $response['status'] = false;
                    if($chek_json_data['profile_type'] != ''){
                        $response['redirect_back'] = url('/register/professional?profile_type='.$chek_json_data['profile_type'].'&profile_id='.$chek_json_data['profile_id']);
                    }else{
                        $response['redirect_back'] = url('/register/professional');
                    }
                   
                    $response['message'] = 'Your OTP verification has failed due to multiple incorrect attempts';
                    \Session::flash("error","Your OTP verification has failed due to multiple incorrect attempts");
                }
                return response()->json($response);
            }else{
                $response['status'] = false;
                $response['message'] = 'Invalid OTP. Please try again.';
            }
            
           
        }

        return response()->json($response);
    }

    public function otpVerification(Request $request, $token)
    {
        if (!\Session::get("email")) {
            return redirect("/register");
        }
       //  dd( $otpRecord);
        $viewData['token'] = $token;
        $viewData['send_otp_url'] = url('register/send-otp');
        $viewData['verify_otp_url'] = url('register/verify-otp');
        return view("auth.verify-otp", $viewData);
    }

    public function verifyOtp(Request $request)
    {

        
        $token = $request->input('token');
        $otp = $request->input('otp');

        // Find the OTP record in the database using the token
     
        $otpRecord = OtpVerify::where('unique_id', $token)->first();
        
        $email = Session::get('email');
        $user = User::where('email', $email)->first();
        $response = [];
       
        // Check if the OTP record exists and is not expired
        if ($otpRecord && $otpRecord->otp === $otp) {
            // Check if the OTP is not expired
            if (Carbon::now()->lessThanOrEqualTo(Carbon::parse($otpRecord->otp_expiry_time))) {
                // OTP is valid
     
                if ($user->role == 'user') {
                    $user->status = 'active';
                    $user->is_active = 1;
                    $user->save();
                }
                Auth::login($user);
               
                Session::forget('email');
                $response['status'] = true;
                $response['url'] = baseUrl('/'); // Redirect URL after successful OTP verification
                $response['message'] = 'Registraion successful.';
             
            } else {
                // OTP expired
                $response['status'] = false;
                $response['message'] = 'The OTP has expired. Please request a news one.';
            }
        } else {
            // OTP is invalid
            $response['status'] = false;
            $response['message'] = 'Invalid OTP. Please try again.';
        }

        return response()->json($response);
    }

    public function sendOtp(Request $request)
    {
        
        $token = $request->input('token');
      // Find the existing record by the unique token
        $verifyOtp = OtpVerify::where('unique_id', $token)->first();
        
        if ($verifyOtp) {
            $attempt = $verifyOtp->resend_attempt;

            if($attempt < 2){
                $otp_object = sendOtp($verifyOtp->email,"emails.otp-mail",$request->input("type"));
                return response()->json(['status' => true, 'message' => 'OTP sent successfully.']);
            }else{
                TempUser::where("email",$verifyOtp->email)->delete();
                OtpVerify::where("email",$verifyOtp->email)->delete();
                $response['status'] = false;
                $response['redirect_back'] = url('register/professional');
                $response['message'] = 'Maximum OTP verification attempts reached';
                \Session::flash("error","Maximum OTP verification attempts reached");
                return response()->json($response);
            }
           
        } else {
            // No record found with the given token
            return response()->json(['status' => false, 'message' => 'OTP record not found.']);
        }
    
        // return response()->json(['status' => true, 'message' => 'OTP sent successfully.']);
    }

    public function uploadProfessionalDocument(Request $request)
    {
        
        $evidencesName = "";
        $count = 0;
        if ($file = $request->file){

            $fileName        = $file->getClientOriginalName();
            $extension       = $file->getClientOriginalExtension() ?: 'png';
            $evidencesName        = mt_rand(1,99999)."-".$fileName;
            $source_url = $file->getPathName();

            $destinationPath = prfessionalDocumentDir();
            if($file->move($destinationPath, $evidencesName)){
                $res = awsFileUpload(config('awsfilepath.professional_document')."/".$evidencesName,$destinationPath.'/'.$evidencesName);
                unlink($destinationPath.'/'.$evidencesName);

                $company = CdsProfessionalCompany::where("user_id",auth()->user()->id)->where('is_primary',1)->first();

                $documents = new CdsProfessionalDocuments;
                $documents->user_id = auth()->user()->id;
                $documents->document_type = $request->document_type;
                $documents->file_name = $evidencesName;
                $documents->company_id = $company->id;
                $documents->added_by = auth()->user()->id;
                $documents->save();
                
                $count = CdsProfessionalDocuments::where('user_id',auth()->user()->id)->where('document_type',$request->document_type)->count();
            }
        }
        $response['status'] = true;
        $response['filename'] = $evidencesName;
        $response['count'] = $count;
        $response['message'] = "Record added successfully";

        return response()->json($response);
    }

    
    public function claimOtpVerification(Request $request, $token)
    {

        if (!\Session::get("email")) {
            return redirect("/register");
        }

        $viewData['token'] = $token;
        $viewData['send_otp_url'] = url('register/send-otp');
        $viewData['verify_otp_url'] = route('claim.verify.otp.success');
        return view("auth.claim-verify-otp", $viewData);
    }
    
    public function claimVerifyOtp(Request $request)
    {

         // Validate the input
         $validator = Validator::make($request->all(), [
            'otp1' => 'required|numeric|digits:1',
            'otp2' => 'required|numeric|digits:1',
            'otp3' => 'required|numeric|digits:1',
            'otp4' => 'required|numeric|digits:1',
            'otp5' => 'required|numeric|digits:1',
            'otp6' => 'required|numeric|digits:1',
        ], [
            'otp6.required' => 'The OTP field is required.',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $errorMessages = [
                'otp6' => 'The OTP field is required.'
            ];

            $response['message'] = $errorMessages;

            return response()->json($response);
        }
       

        // If validation passes, continue processing
        $otpInputs = $request->only(['otp1', 'otp2', 'otp3', 'otp4', 'otp5', 'otp6']);

        // Process the OTP inputs
        // Example: Concatenating the OTP inputs into a single string
        $otp = implode('', $otpInputs);

        $token = $request->input('token');
        

        // Find the OTP record in the database using the token
    
        $otpRecord = OtpVerify::where('unique_id', $token)->first();
       
        $response = [];

        // Check if the OTP record exists and is not expired
        if ($otpRecord && $otpRecord->otp === $otp) {
         
            // Check if the OTP is not expired
            if (Carbon::now()->lessThanOrEqualTo(Carbon::parse($otpRecord->otp_expiry_time))) {
                // OTP is valid
                $temp_user = TempUser::where('email',$otpRecord->email)->latest()->first();
                if(!empty($temp_user)){
                    $json_data = json_decode($temp_user->json_data,true);
         
                    $object = new User();
                    $object->first_name = $json_data['first_name'];
                    $object->last_name = $json_data['last_name'];
                    $object->email = $json_data['email'];
                    if(isset($json_data['country_code'])){
                        $object->country_code = $json_data['country_code'];
                    }
                    if(isset($json_data['phone_no'])){
                        $object->phone_no = $json_data['phone_no'];
                    }
                    $object->password = bcrypt($json_data['password']);
                    $object->role = $json_data['role'];
                    $object->status = "draft";
                    $object->is_verified  = 0;
                    $object->is_active = 0;
                    $object->added_by = 0;
                    $object->completed_step = 1;
                    $object->save();
                    $userDetails = new UserDetails();
                    $userDetails->user_id = $object->id;
                    if(isset( $json_data['terms_condition'])){
                        $userDetails->terms_condition = $json_data['terms_condition'];
                    }
                    $userDetails->save();
                    TempUser::where("email",$json_data['email'])->delete();
                    OtpVerify::where("email",$json_data['email'])->delete();
                   
                    Auth::login($object);
             
                    $response['status'] = true;
                    $response['message'] = 'Registraion successful.';
                    $response['url'] = \Session::get("claim_redirect_back"); 
                    \Session::forget("claim_redirect_back"); 

                    return response()->json($response);
                }else{
                    $response['status'] = false;
                    $response['message'] = 'Sign Up token invalid';
                    return response()->json($response);
                }
             
            } else {
                // OTP expired
                $response['status'] = false;
                $response['message'] = 'The OTP has expired. Please request a new one.';
            }
        } else {
            // OTP is invalid
            $response['status'] = false;
            $response['message'] = 'Invalid OTP. Please try again.';
        }

        return response()->json($response);
    }

    public function reportOtpVerification(Request $request, $token)
    {

        if (!\Session::get("email")) {
            return redirect("/register");
        }

        $viewData['token'] = $token;
        $viewData['send_otp_url'] = url('register/send-otp');
        $viewData['verify_otp_url'] = route('report.verify.otp.success');
        return view("auth.report-verify-otp", $viewData);
    }

    public function reportVerifyOtp(Request $request)
    {

         // Validate the input
         $validator = Validator::make($request->all(), [
            'otp1' => 'required|numeric|digits:1',
            'otp2' => 'required|numeric|digits:1',
            'otp3' => 'required|numeric|digits:1',
            'otp4' => 'required|numeric|digits:1',
            'otp5' => 'required|numeric|digits:1',
            'otp6' => 'required|numeric|digits:1',
        ], [
            'otp6.required' => 'The OTP field is required.',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $errorMessages = [
                'otp6' => 'The OTP field is required.'
            ];

            $response['message'] = $errorMessages;

            return response()->json($response);
        }
       

        // If validation passes, continue processing
        $otpInputs = $request->only(['otp1', 'otp2', 'otp3', 'otp4', 'otp5', 'otp6']);

        // Process the OTP inputs
        // Example: Concatenating the OTP inputs into a single string
        $otp = implode('', $otpInputs);

        $token = $request->input('token');
        

        // Find the OTP record in the database using the token
    
        $otpRecord = OtpVerify::where('unique_id', $token)->first();
       
        $response = [];

        // Check if the OTP record exists and is not expired
        if ($otpRecord && $otpRecord->otp === $otp) {
         
            // Check if the OTP is not expired
            if (Carbon::now()->lessThanOrEqualTo(Carbon::parse($otpRecord->otp_expiry_time))) {
                // OTP is valid
                $temp_user = TempUser::where('email',$otpRecord->email)->latest()->first();
                if(!empty($temp_user)){
                    $json_data = json_decode($temp_user->json_data,true);
                    $object = new User();
                    
                    $object->first_name = $json_data['first_name'];
                    $object->last_name = $json_data['last_name'];
                    $object->email = $json_data['email'];
                    $object->password = bcrypt($json_data['password']);
                   
                    $object->role = $json_data['role'];
                    $object->status = "draft";
                    $object->is_verified  = 0;
                    $object->is_active = 0;
                    $object->added_by = 0;
                    $object->completed_step = 1;
                    $object->save();
                    $userDetails = new UserDetails();
                    $userDetails->user_id = $object->id;
                    if(isset( $json_data['terms_condition'])){
                        $userDetails->terms_condition = $json_data['terms_condition'];
                    }
                    $userDetails->save();
                    TempUser::where("email",$json_data['email'])->delete();
                    OtpVerify::where("email",$json_data['email'])->delete();
                   
                    Auth::login($object);
               
                    $response['status'] = true;
                    $response['message'] = 'Registraion successful.';
                    $response['url'] = \Session::get("report_redirect_back"); 
                    \Session::forget("report_redirect_back"); 

                    return response()->json($response);
                }else{
                    $response['status'] = false;
                    $response['message'] = 'Sign Up token invalid';
                    return response()->json($response);
                }
             
            } else {
                // OTP expired
                $response['status'] = false;
                $response['message'] = 'The OTP has expired. Please request a new one.';
            }
        } else {
            // OTP is invalid
            $response['status'] = false;
            $response['message'] = 'Invalid OTP. Please try again.';
        }

        return response()->json($response);
    }

    public function saveProfessionalPersonal(Request $request)
    {

        $personalLocations =   CompanyLocations::where(function ($query) {
            $query->where('user_id',\Auth::user()->id);
        })
        ->orderBy('id', "desc")
        ->where('type_label','personal')
        ->count();

        if($personalLocations == 0){
            $response['status'] = false;
            $response['error_type'] = 'address';
            $response['message'] = 'Please add address';
            return response()->json($response);
        }   

         $validator = Validator::make($request->all(), [
            'date_of_birth' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'user_phone_no' => ['required'],
            'gender' => ['required']
        ]);

         // // Handle validation failure
        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = [];

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['error_type'] = 'validation';
            $response['message'] = $errMsg;
            return response()->json($response);
        }
       
        $response['status'] = true;
        return response()->json($response);
    }

    public function searchClaimProfile(Request $request)
    {
        $search =  $request->term;

        
        if(strlen($search) > 3){
           return $professionals = Professional::where('college_id', 'LIKE', "%{$search}%")
            ->orderBy('id', 'asc')->limit(50)
            ->get()->toArray();
        }
       
    }

    public function getCompanyList(Request $request){
        
        $records = CompanyLocations::where(function ($query) {
                $query->where('user_id',\Auth::user()->id);
            })
            ->orderBy('id', "desc")
            ->where('type_label','company')
            ->get();
        $viewData['records'] = $records;
        $view = View::make('auth.companies-list', $viewData);
        
        $contents = $view->render();
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function updateCompany(Request $request){
        $validator = Validator::make($request->all(), [
            'company_name' => ['required', 'string'],
            'owner_type' => ['required'],
            'company_type' => 'required_if:owner_type,Self Employed',
            'address_line_1' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pin_code' => 'required'
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = [];

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            $response['error_type'] = "validation";
            return response()->json($response);
        }
        $cds_company = CdsProfessionalCompany::where("unique_id",$request->company_id)->first();
        $cds_company->user_id = auth()->user()->id;
        $cds_company->company_name = $request->company_name;
        $cds_company->owner_type  = $request->owner_type;
        $cds_company->company_type  = $request->company_type;
        if($request->owner_type == 'Employed' && $request->currently_working){
            $cds_company->currently_working  = $request->currently_working??0;
        }
        $cds_company->save();

        CompanyLocations::where('unique_id',$request->location_id)->update([
            'user_id' => \Auth::user()->id,
            'address_1' => $request->input('address_line_1'),
            'address_2' => $request->input('address_line_2'),
            'country' => $request->input('country'),
            'state' => $request->input('state'),
            'city' => $request->input('city'),
            'pincode' => $request->input('pin_code'),
        ]);
        $response['status'] = true;
        $response['message'] = 'Company information updated';
        return response()->json($response);
    }

    public function markCompanyAsPrimary(Request $request){
        CdsProfessionalCompany::where("user_id",auth()->user()->id)->update(['is_primary'=>0]);
        CdsProfessionalCompany::where("user_id",auth()->user()->id)->where("unique_id",$request->company_id)->update(['is_primary'=>1]);

        CompanyLocations::where("user_id",auth()->user()->id)->update(['is_primary'=>0]);
        CompanyLocations::where("user_id",auth()->user()->id)->where("unique_id",$request->location_id)->update(['is_primary'=>1]);

        $response['status'] = true;
        $response['message'] = 'Company set as primary';
        return response()->json($response);
    }

    public function deleteCompanyData($company_id, $location_id){
        CdsProfessionalCompany::where("user_id",auth()->user()->id)->where("unique_id",$company_id)->delete();
        CompanyLocations::where("user_id",auth()->user()->id)->where("unique_id",$location_id)->delete();

        
        return redirect()->back()->with("success","Company deleted successfully");
    }

    public function deleteLocationData($location_id){
        CompanyLocations::where("user_id",auth()->user()->id)->where("unique_id",$location_id)->delete();
        return redirect()->back()->with("success","Location deleted successfully");
    }


    public function deleteProfessionalLocationData($location_id)
    {
        $location = CompanyLocations::where("user_id", auth()->user()->id)
            ->where("unique_id", $location_id)
            ->first();

        if (!$location) {
            return response()->json([
                'status' => false,
                'message' => 'Location not found.',
            ]);
        }

        // Block deletion if any upcoming appointments have these statuses
        $blockedStatuses = ['approved', 'awaiting'];

        $hasBlockedAppointments = AppointmentBooking::where('location_id', $location->id)
            ->where('appointment_date', '>=', now())
            ->whereIn('status', $blockedStatuses)
            ->exists();

        if ($hasBlockedAppointments) {
            return response()->json([
                'status' => false,
                'message' => 'Cannot delete location: It has upcoming appointments in restricted statuses.',
            ]);
        }

        // Safe to delete
        CompanyLocations::deleteRecord($location->id);

        return response()->json([
            'status' => true,
            'message' => 'Record deleted successfully.',
        ]);
    }


}
