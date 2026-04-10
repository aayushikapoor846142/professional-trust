<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\TempUser;
use App\Models\OtpVerify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ChatInvitation;
use App\Models\ChatRequest;
use App\Models\StaffUser;
use App\Models\UserLoginActivity;
use App\Models\UserLocationAccessibility;
use Illuminate\Support\Facades\Session;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/panel';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    protected function authenticated($request, $user)
    {
        // Check user role
        if (($user->role !== 'professional' && $user->isStaffUser && getUserRole($user->isStaffUser->added_by)=="professional")|| $user->role=="professional") { 
            return redirect()->intended($this->redirectPath());
            
        }else{
            auth()->logout(); // Logout the user
            return redirect('/login')->with('error', 'Access restricted to your role.');
        }
    
        // Allow login for specific roles
    }

    public function checkLoginCredentials(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|valid_email',
            'password' => 'required',
            'g-recaptcha-response' => 'required',
        ]);
        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = 'validation';
            $error = $validator->errors()->toArray();
            $errMsg = array();

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }

        $user = User::where('email', $request->email)->first();
            if (!$user) {
             return response()->json(['status' => false, 'message' => 'No user found with this email']);
}
        $parameter = [
            'user_id' => $user->unique_id,
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];
        $response = checkUserSecurity($parameter);
      
        if (($response['status'] ?? 'failed') !== 'success') {
            return response()->json(['status' => false, 'message' => 'Email or Password does not match.']);
        }
        
        if ($user && Hash::check($request->password, $user->password)) {
        
            if (($user->role == 'professional') || ($user->role !== 'professional' && $user->isStaffUser && getUserRole($user->isStaffUser->added_by)=="professional")) {
                \Session::forget('chat_message');
                //dd('hello');
                $json_data = $request->all();
                $user_object = TempUser::updateOrCreate(['email' => $request->input("email"), 'type' => 'signin'], ['json_data' => json_encode($json_data)]);
                $otp_object = sendOtp($request->input("email"), "emails.otp-mail");
                $response['status'] = true;
                $response['user_token'] = $user_object->unique_id;
                $response['otp_token'] = $otp_object->unique_id;
                $response['redirct_url'] = route('login.verify.otp', ['id' => $otp_object->unique_id]);
                return response()->json($response);
            }else {
                $response['status'] = false;
                $response['message'] = 'Invalid Email or Password!';
                return response()->json($response);
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'Invalid Email or Password!';
            return response()->json($response);
        }
    }


    public function loginOtpVerification(Request $request, $token)
    {

     

        $otpVerify = OtpVerify::where('unique_id', $token)->first();

        $otpVerify = OtpVerify::where('unique_id',$token)->first();
        if(empty($otpVerify)){
            return redirect("/otp-expired");
        }


        $otpLocation  = json_decode($otpVerify->user_location, true);
      
        $viewData['token'] = $token;
        $viewData['otpVerify'] = $otpVerify;
        $viewData['email'] = $otpVerify->email;
        $viewData['timezone'] = $otpLocation['timezone'] ?? 'UTC';
        
        // $viewData['timezone'] = 'UTC';
        $viewData['send_otp_url'] = url('send-login-otp');

        $viewData['verify_otp_url'] = route('login.verify.otp.success');
        return view("auth.login-otp-verify", $viewData);
    }

    public function loginVerifyOtp(Request $request)
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

        $verify_otp = OtpVerify::where("email", $request->input("email"))
            ->where("unique_id", $request->input("otp_token"))
            ->where("otp", $otp)
            ->first();

        //  dd( $verify_otp);
        if (!empty($verify_otp)) {

            $expiry_time = $verify_otp->otp_expiry_time;
            $current_time = Carbon::now();

            if ($current_time < $expiry_time) {

                if ($verify_otp->otp == $otp) {

                    $temp_user = TempUser::where("email", $verify_otp->email)->latest()->first();
                    if (!empty($temp_user)) {
                        $json_data = json_decode($temp_user->json_data, true);
                        $user = User::where('email', $json_data['email'])->first();

                        $chatInvitation = ChatInvitation::where('email', $json_data['email'])->first();

                        if ($chatInvitation) {
                            $existingRequest = ChatRequest::where('sender_id', $chatInvitation->added_by)
                                ->where('receiver_id', $user->id)
                                ->get();
                            if (count($existingRequest)<1) {
                                // Flash message to the dashboard (optional)
                                $chat_message = 'You have one chat request pending. Please check it.';
                                \Session::put('chat_message',$chat_message);
                           
                                // Create a new ChatRequest entry
                                ChatRequest::create([
                                    'unique_id' => randomNumber(),
                                    'sender_id' => $chatInvitation->added_by,
                                    'receiver_id' => $user->id,
                                    'is_accepted' => 0,
                                ]);
                            }
                        }

                       

                        if ($user && Hash::check($json_data['password'], $user->password)) {
                            $user->is_login = 1;
                            $user->save();

                            if($user->temporary_password == 1){
                                $url = url("set-password/".$user->unique_id);
                            }else{
                                      \Auth::loginUsingId($user->id);
                                             if (Session::has('login_activity')) {
                                                $session_value = Session::get('login_activity');
                                                Session::forget('login_activity');
                                                    $url =  $session_value;
                                                     } else {    
                                                        $url = url('/home'); 
                                            }
                            }
                        }
                 
                       
                        $response['status'] = true;
                        $response['success'] = "Login successfully";
                        $response['message'] = "Login successfully";
                        $response['url'] = $url;
                        // if ($request->session()->has('url.intended')) {
                        //     $response['status'] = true;
                        //     $response['success'] = "Login successfully";
                        //     $response['message'] = "Login successfully";
                        //     $response['url'] = $request->session()->get('url.intended');
                        
                        //     $request->session()->forget('url.intended'); 
                        // } else {
                        //     $response['status'] = true;
                        //     $response['success'] = "Login successfully";
                        //     $response['message'] = "Login successfully";
                        //     $response['url'] = url('/home'); 
                        // }
                         $currentLogin = !empty($verify_otp->user_location) ? json_decode($verify_otp->user_location, true) : detectUserLocation();

                        $signupActivity = UserLocationAccessibility::where('user_id', $user->id)
                            ->where('is_signup_location', 1)
                            ->get();
                        $matched = false;
                        if (is_array($currentLogin)) {
                            $currentLogin = json_decode(json_encode($currentLogin));
                        }

                        foreach ($signupActivity as $signup) {
                          
                            if (checkLocationMatches($currentLogin, $signup)) {
                                $matched = true;
                                break;
                            }
                        }
                 
                        if (!$matched) {
                     
                            $loginMessage = 'New login detected from: '
                                . ($currentLogin->city ?? 'Unknown City') . ', '
                                . ($currentLogin->country ?? 'Unknown Country')
                                . ' using ' . ($currentLogin->device_type ?? 'unknown device')
                                . ' and ' . ($currentLogin->browser ?? 'unknown browser') . '.';

                            $mailData = [
                                'user' => $user,
                                'locationType' => 'Unrecognized Login Location or Device',
                                'currentLogin' => $currentLogin,
                                'signupLogin' => $signupActivity->first(),
                                'loginMessage' => $loginMessage
                            ];
                            sendMail([
                                'to' => $user->email,
                                'to_name' => $user->first_name . ' ' . $user->last_name,
                                'message' => view('emails.login-alert', $mailData)->render(),
                                'subject' => siteSetting("company_name") . ": Suspicious Login Activity Detected",
                                'view' => 'emails.login-alert',
                                'data' => $mailData,
                            ]);

                           
                        }
                      
                     storeLoginActivity($currentLogin);
                        $temp_user->delete();
                        $verify_otp->delete();
                        return response()->json($response);
                    } else {
                        $response['status'] = false;
                        $response['message'] = 'Login token invalid';
                        return response()->json($response);
                    }
                }
            } else {
                $response['status'] = false;
                $response['message'] = 'Otp has been expired';
                return response()->json($response);
            }
        } else {
            $response['status'] = false;
            $response['message'] = 'Otp is not valid';
            return response()->json($response);
        }
    }

    public function loginSendOtp(Request $request)
    {

        $token = $request->input('token');
        // Find the existing record by the unique token
        $verifyOtp = OtpVerify::where('unique_id', $token)->first();

        if ($verifyOtp) {
            $attempt = $verifyOtp->resend_attempt;

            if ($attempt < 2) {
                $otp_object = sendOtp($verifyOtp->email, "emails.otp-mail", $request->input("type"));
                return response()->json(['status' => true, 'message' => 'OTP sent successfully.']);
            } else {
                TempUser::where("email", $verifyOtp->email)->delete();
                OtpVerify::where("email", $verifyOtp->email)->delete();
                $response['status'] = false;
                $response['redirect_back'] = url('login');
                $response['message'] = 'Maximum OTP verification attempts reached';
                \Session::flash("error", "Maximum OTP verification attempts reached");
                return response()->json($response);
            }
        } else {
            // No record found with the given token
            return response()->json(['status' => false, 'message' => 'OTP record not found.']);
        }

        // return response()->json(['status' => true, 'message' => 'OTP sent successfully.']);
    }
    public function   otpError()
    {
        return view('errors.otp-error');
    }

    public function showResetPassword($user_id)
    {
        $user = User::where("unique_id",$user_id)->first();
        if(!empty($user) && $user->temporary_password == 1){
            return view('auth.set-password', ['user' => $user, 'pageTitle' => 'Reset Password']);
        }else{
            return redirect("/login")->with("error","Not allowed to reset password");
        }
       
    }


    /**
     * Handle the password reset submission.
     * 
     * This method validates the incoming request for password reset, checks if the 
     * provided reset token is valid, and updates the user's password if everything 
     * is correct. It then deletes the reset token from the `password_resets` table 
     * and redirects the user to the login page with a success message.
     *
     * @param \Illuminate\Http\Request $request The request instance containing the email, password, and token.
     * @return \Illuminate\Http\RedirectResponse Redirects back to the login page with a success or error message.
     */
    public function submitResetPassword($user_id,Request $request)
    {
        $request->validate([
            'email' => 'required|max:255|email|exists:users',
            'password' => 'required|string|password_validation|confirmed',
            'password_confirmation' => 'required|password_validation',
        ]);
        
        $user = User::where("unique_id",$user_id)->first();
        if (!$user) {
            return back()->withInput()->with('error', 'User is not exists');
        }

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->temporary_password = 0;
            $user->save();
        }
        // User::where('email', $request->email)->update(['password' => Hash::make($request->password),'temporary_password'=>0]);
        $user->storePasswordHistory();
        \Auth::loginUsingId($user->id);
       
        return redirect(baseUrl('/'))->with('message', 'Your password has been changed! You can login now');
        
    }

}