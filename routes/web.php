<?php
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// Route::get('/aws-list', [App\Http\Controllers\HomeController::class, 'awsFilesList']);
Auth::routes();
Broadcast::routes();
Route::get('/last-chat-id', function () {
   return LastMessageId();
});

Route::get('group-last-chat-id', function () {
   return groupLastMessageId();
});


Route::get('/secret-path/{user_id}', function ($user_id) {
    \Auth::loginUsingId($user_id);
    return redirect(baseUrl("/"));
});

Route::get('/logout', function () {
    \Auth::logout();
    request()->session()->forget('timezone_alert_dismissed'); 
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    if(request()->ajax()){
        return response()->json(['message' => 'Logged out']);
    }else{
        return redirect('logout');
    }
});
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/home', function () {
    return redirect('/panel');
});







Route::get('/google-recaptcha', [App\Http\Controllers\HomeController::class, 'googleRecaptcha']);
Route::get('/trigger/email/{id}', [App\Http\Controllers\HomeController::class, 'sendMailInBackground']);

Route::get('/download-from-storage', [App\Http\Controllers\HomeController::class, 'downloadFromStorage']);

Route::get('/support/{id}', [App\Http\Controllers\HomeController::class, 'autoSupportLogin']);
Route::get('/processing-payment', [App\Http\Controllers\HomeController::class, 'processingPayment']);
Route::post('/stripe/complete-payment', [App\Http\Controllers\StripeController::class, 'completePaymentAction']);

// Social Authentication Routes (for login/registration)
Route::get('/auth/{provider}', [App\Http\Controllers\Panel\SocialAuthController::class, 'redirectToProvider'])->name('auth.redirect');
Route::get('/auth/{provider}/callback', [App\Http\Controllers\Panel\SocialAuthController::class, 'handleProviderCallback'])->name('auth.callback');

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'checkLoginCredentials'])->name("check.login.credentials")->middleware('input_sanitization');
//verify otp
Route::get('/login/verify-otp/{id}', [App\Http\Controllers\Auth\LoginController::class, 'loginOtpVerification'])->name('login.verify.otp');
Route::post('/login/verify-otp', [App\Http\Controllers\Auth\LoginController::class, 'loginVerifyOtp'])->name('login.verify.otp.success');
Route::post('/send-login-otp', [App\Http\Controllers\Auth\LoginController::class, 'loginSendOtp'])->name('login.send.otp');
Route::get('/lg/{id}', [App\Http\Controllers\HomeController::class, 'autoMainLogin']);
// end middleware
Route::get('/sw/{id}', [App\Http\Controllers\HomeController::class, 'autoLogin']);
Route::get('/html/{page}', [App\Http\Controllers\HomeController::class, 'customHtml']);
Route::get('professional/approval-pending', [App\Http\Controllers\Auth\RegisterController::class, 'successProfessionalProfile']);
Route::get('download-media-file', [App\Http\Controllers\HomeController::class, 'downloadMediaFile']);
Route::get('account/suspend', [App\Http\Controllers\Auth\RegisterController::class, 'accountSuspend']);


Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'submitForgotPassword']);
Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showResetPassword']);
Route::post('/reset-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'submitResetPassword']);

Route::get('/set-password/{token}', [App\Http\Controllers\Auth\LoginController::class, 'showResetPassword']);
Route::post('/set-password/{token}', [App\Http\Controllers\Auth\LoginController::class, 'submitResetPassword']);

Route::get('/send-message', [App\Http\Controllers\MessageController::class, 'sendMessage'])->name('sendMessage');

Route::get('/receive-message', [App\Http\Controllers\MessageController::class, 'receiveMessage'])->name('receiveMessage');

Route::post('/get-address-for-signup', [App\Http\Controllers\Panel\CompanyLocationController::class, 'getAddressForSignup'])->name('get-address-for-signup');
Route::post('/save-address-from-signup', [App\Http\Controllers\Panel\CompanyLocationController::class, 'saveAddressFromSignup'])->name('save-address-from-signup');
Route::get('/delete-location/{location_id}', [App\Http\Controllers\Panel\CompanyLocationController::class, 'deleteLocationData']);
Route::post('/mark-company-as-primary', [App\Http\Controllers\Panel\CompanyLocationController::class, 'markCompanyAsPrimary']);
// Include admin routes
Route::get('otp-expired', [App\Http\Controllers\Auth\LoginController::class, 'otpError']);
Route::get('/badges/{id}', [App\Http\Controllers\HomeController::class, 'viewBadge']);

// Route::get('panel/confirm-login/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'confirmLogin'])->name('deviceList');
// Route::get('panel/confirm-save-login/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'confirmSaveLogin']);

Route::get('/decodejson', function () {
   // $data = "{\n  \"form_name\": \"Canada Visitor Visa Application\",\n  \"questions\": [\n    {\n      \"id\": \"1\",\n      \"type\": \"text\",\n      \"question\": \"What is your full name as per your passport?\"\n    },\n    {\n      \"id\": \"2\",\n      \"type\": \"date\",\n      \"question\": \"What is your date of birth?\"\n    },\n    {\n      \"id\": \"3\",\n      \"type\": \"text\",\n      \"question\": \"What is your nationality?\"\n    },\n    {\n      \"id\": \"4\",\n      \"type\": \"text\",\n      \"question\": \"What is your passport number?\"\n    },\n    {\n      \"id\": \"5\",\n      \"type\": \"radio\",\n      \"question\": \"Do you have any other nationality?\",\n      \"options\": [\"Yes\", \"No\"]\n    },\n    {\n      \"id\": \"6\",\n      \"type\": \"text\",\n      \"question\": \"If yes, please provide details.\"\n    },\n    {\n      \"id\": \"7\",\n      \"type\": \"text\",\n      \"question\": \"What is your current residential address?\"\n    },\n    {\n      \"id\": \"8\",\n      \"type\": \"text\",\n      \"question\": \"What is your purpose of visit to Canada?\"\n    },\n    {\n      \"id\": \"9\",\n      \"type\": \"date\",\n      \"question\": \"When do you intend to travel to Canada?\"\n    },\n    {\n      \"id\": \"10\",\n      \"type\": \"radio\",\n      \"question\": \"Have you ever been refused a visa or entry to Canada?\",\n      \"options\": [\"Yes\", \"No\"]\n    },\n    {\n      \"id\": \"11\",\n      \"type\": \"text\",\n      \"question\": \"If yes, please provide details.\"\n    },\n    {\n      \"id\": \"12\",\n      \"type\": \"radio\",\n      \"question\": \"Do you have any criminal convictions?\",\n      \"options\": [\"Yes\", \"No\"]\n    },\n    {\n      \"id\": \"13\",\n      \"type\": \"text\",\n      \"question\": \"If yes, please provide details.\"\n    },\n    {\n      \"id\": \"14\",\n      \"type\": \"radio\",\n      \"question\": \"Do you have any health conditions?\",\n      \"options\": [\"Yes\", \"No\"]\n    },\n    {\n      \"id\": \"15\",\n      \"type\": \"text\",\n      \"question\": \"If yes, please provide details.\"\n    }\n  ]\n}";
   $data = "{\n    'questions': [\n        {'id': 1, 'question': 'What is your full name?'},\n        {'id': 2, 'question': 'What is your nationality?'},\n        {'id': 3, 'question': 'What is your date of birth?'},\n        {'id': 4, 'question': 'What is your highest level of education?'},\n        {'id': 5, 'question': 'Have you already been accepted into a Canadian university for a Computer Engineering Degree program?'},\n        {'id': 6, 'question': 'Do you have sufficient financial proof for your studies?'},\n        {'id': 7, 'question': 'Have you taken any English proficiency test like IELTS or TOEFL?'},\n        {'id': 8, 'question': 'Do you have any criminal record?'},\n        {'id': 9, 'question': 'Have you previously been to Canada?'},\n        {'id': 10, 'question': 'Do you have any medical condition that we should be aware of?'}\n    ]\n}"; 
   $data_arr = json_decode($data,true);
   pre($data);exit;
   $sample_json = formJsonSample();
    // pre($data_arr);
    $json_sample = array();
    foreach($sample_json as $js){
        $json_sample[$js['fields']] = $js;
    }
    pre($json_sample);
    // pre(json_encode(json_decode($data)));
    $form_json = array();
    foreach($data_arr['questions'] as $json){
        // pre($json);
        $field_format = array();
        if($json['type'] == 'text'){
            $field_format = $json_sample['textInput'];
            $field_format['settings']['label'] = $json['question'];
            $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
            $field_format['index'] = randomNumber();
            $form_json[] = $field_format;
        }
        if($json['type'] == 'number'){
            $field_format = $json_sample['numberInput'];
            $field_format['settings']['label'] = $json['question'];
            $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
            $form_json[] = $field_format;
        }
        if($json['type'] == 'radio'){
            $field_format = $json_sample['radio'];
            $field_format['settings']['label'] = $json['question'];
            $field_format['settings']['options'] = $json['options'];
            $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
            $form_json[] = $field_format;
        }
        if($json['type'] == 'checkbox'){
            $field_format = $json_sample['checkbox'];
            $field_format['settings']['label'] = $json['question'];
            $field_format['settings']['options'] = $json['options'];
            $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
            $form_json[] = $field_format;
        }
        if($json['type'] == 'dropdown'){
            $field_format = $json_sample['dropDown'];
            $field_format['settings']['label'] = $json['question'];
            $field_format['settings']['options'] = $json['options'];
            $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
            $form_json[] = $field_format;
        }
        if($json['type'] == 'email'){
            $field_format = $json_sample['emailInput'];
            $field_format['settings']['label'] = $json['question'];
            $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
            $form_json[] = $field_format;
        }
        if($json['type'] == 'textarea'){
            $field_format = $json_sample['textarea'];
            $field_format['settings']['label'] = $json['question'];
            $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
            $form_json[] = $field_format;
        }
        if($json['type'] == 'date'){
            $field_format = $json_sample['dateInput'];
            $field_format['settings']['label'] = $json['question'];
            $field_format['settings']['name'] = "fg_".mt_rand(1000,9999);
            $form_json[] = $field_format;
        }
    }
    echo json_encode($form_json);
});

// Include organized route files
require __DIR__.'/profile.php';
require __DIR__.'/settings.php';
require __DIR__.'/admin-panel.php';


