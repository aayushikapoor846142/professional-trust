<?php

namespace App\Http\Controllers;

use App\Services\TwilioService;
use Illuminate\Http\Request;

class TwilioController extends Controller
{
    protected $twilio;

    public function __construct(TwilioService $twilio)
    {
        $this->twilio = $twilio;
    }

    public function sendSms(Request $request)
    {
        $to = "+16474823139";
        $message = "Hello from Twilio and Laravel!";

        try {
            $this->twilio->sendSms($to, $message);
            return response()->json(['message' => 'SMS sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send SMS: ' . $e->getMessage()], 500);
        }
    }

    //     public function professionalSignup(Request $request)
    // {
    //     try {

    //         if (!auth()->check()) {
    //             $validator = Validator::make($request->all(), [
    //                 'first_name' => ['required', 'string', new StringLimitRule()],
    //                 'last_name' => ['required', 'string', new StringLimitRule()],
    //                 'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'valid_email'],
    //                 'terms_condition' => ['required'],
    //                 'password' => [
    //                     'required',
    //                     'string',
    //                     'min:8',
    //                     'confirmed',
    //                     'regex:/[a-z]/', // must contain at least one lowercase letter
    //                     'regex:/[A-Z]/', // must contain at least one uppercase letter
    //                     'regex:/[0-9]/', // must contain at least one digit
    //                     'regex:/[!@#$%^&*()\-_=+{};:,<.>ยง~]/', // must contain at least one special character
    //                 ],
    //                 'password_confirmation' => [
    //                     'required',
    //                     'string',
    //                     'min:8',
    //                     'regex:/[a-z]/', // must contain at least one lowercase letter
    //                     'regex:/[A-Z]/', // must contain at least one uppercase letter
    //                     'regex:/[0-9]/', // must contain at least one digit
    //                     'regex:/[!@#$%^&*()\-_=+{};:,<.>ยง~]/', // must contain at least one special character
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
    //             $user_object = TempUser::updateOrCreate(
    //                 [
    //                     'email' => $request->input("email")
    //                     ,
    //                     'type' => 'signup'
    //                 ],
    //                 ['json_data' => json_encode($json_data)]
    //             );
    //             // dd($user_object);

    //                 \Session::put('email', $request->input("email"));

    //                 $otp_object = sendOtp($request->input("email"), "emails.otp-mail");

    //                // $to=$request->country_code . '-' . $request->phone_no;
    //                 $to = "+16474823139";
    //                 $message = "Thank you for choosing ".siteSetting('company_name')." Use the following OTP to complete the procedure to sign up. OTP is
    //                 valid for 5 minutes. Do not share this code with others. Your OTP is  $otp";

    //                 try {
    //                     $this->twilio->sendSms($to, $message);
    //                     return $response['message'] = "SMS sent successfully.";

    //                 } catch (\Exception $e) {
    //                     return $response['message'] = 'Failed to send SMS: ' . $e->getMessage();

    //                 }

    //             $response['status'] = true;
    //             $response['redirct_url'] = route('professional.verify.otp', ['id' => $otp_object->unique_id]);

    //             return response()->json($response);
    //         } else {
    //             $user = \Auth::user();
    //             $validator = Validator::make($request->all(), [
    //                 'g-recaptcha-response' => 'required',
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


    public function makeCall(Request $request)
    {
        $to = $request->input('phone_number');
        $twimlUrl = "http://demo.twilio.com/docs/voice.xml"; // Replace with your own TwiML URL

        try {
            $this->twilio->makeCall($to, $twimlUrl);
            return response()->json(['message' => 'Call initiated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to make call: ' . $e->getMessage()], 500);
        }
    }
}
