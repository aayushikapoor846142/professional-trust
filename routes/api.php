<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\ReportUapController;
use App\Http\Controllers\OpenAIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfessionalController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\PublicUapReportController;
use App\Http\Controllers\Api\UapProfessionalController;
use App\Http\Controllers\Api\UapFormsController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::prefix('auth')->group(function () {
// Route::group(['middleware' => []], function () {
//   Route::group(array('prefix' => 'auth'), function () {
//     Route::post('user-signup',  [AuthController::class,'userSignUp']);
//     Route::post('professional-signup',  [AuthController::class,'professionalSignUp']);
//     Route::post('resend/signup-otp',  [AuthController::class,'resendSignUpOtp']);
//     Route::post('verify/signup-otp',  [AuthController::class,'verifySignUpOtp']);
//     Route::post('login',  [AuthController::class,'login']);
//     Route::post('verify/login-otp',  [AuthController::class,'verifyLoginOtp']);
//   });
//   Route::post('fetch-dropdown-values',  [CommonController::class,'fetchDropdownValues']);
//   Route::post('professional-list',  [ProfessionalController::class,'getProfessionalsList']);
//   Route::post('professional-profile',  [ProfessionalController::class,'getCompanyProfile']);
//   Route::post('/save-professional-documents', [ProfessionalController::class, 'saveProfessionalDocuments']);
//   Route::post('professional-search',  [ProfessionalController::class,'getProfessionalsSearch']);

//   // uap professional new Api
//   Route::post('uap-professional-list',  [ProfessionalController::class,'getUapProfessionalsList']);
//   Route::post('uap-professional-profile',  [ProfessionalController::class,'getUapProfessionalProfile']);
//   // Route::post('unauthorised-professional-profile/{unique_id}',  [ProfessionalController::class,'getUnauthrorizedProfile']);
//   // Route::post('unauthorised-professional-detail/{uniqueid}', [ProfessionalController::class, 'getUnauthrorizedProfile']);
//   Route::post('uap-city-autosuggestion',  [ProfessionalController::class,'getUapCityAutoSuggestion']);
//   Route::post('save-uap-feedback',  [ProfessionalController::class,'saveUapFeedback']);
//   Route::post('save-uap-voilations',  [ProfessionalController::class,'saveUapVoilations']);
//   Route::post('get-uap-feedback',  [ProfessionalController::class,'getUapFeedback']);
//   Route::post('get-uap-voilations',  [ProfessionalController::class,'getUapVoilations']);
//   // Route::post('unauthrorized-professional-profile/{unique_id}',  [ProfessionalController::class,'getUnauthrorizedProfile']);
//   // Route::post('unauthrorized-professional-detail/{uniqueid}', [ProfessionalController::class, 'getUnauthrorizedProfile']);

//   // 

//   Route::post('article-list',  [ArticleController::class,'getArticlesList']);
//   Route::post('article-details',  [ArticleController::class,'getArticleDetails']);

//   Route::post('guide-list',  [GuideController::class,'getGuidesList']);
//   Route::post('guide-details',  [GuideController::class,'getGuideDetails']);


//   Route::post('report-uap',  [ReportUapController::class,'reportUap']);
//   Route::post('report-excel-profile',  [ReportUapController::class,'reportExcelProfile']);
//     // get subject list for report profile
//   Route::get('get-subject',  [ReportUapController::class,'getSubjects']);
//   // API for token redirect after login 
 


// })->withoutMiddleware(['auth:sanctum']);
// Route::group(array('prefix' => 'professional'), function () {
//   Route::post('/save-company', [ProfessionalController::class, 'saveProfessionalCompany']);
// });

// // url to access after login
// Route::group(array('middleware' => ['auth:sanctum']), function () {
//   Route::post('delete-user',  [AuthController::class,'deleteUser']);
  
//   Route::post('login-redirect', [AuthController::class, 'loginRedirect']);
//   Route::post('logout', [AuthController::class, 'logout']);
//   // claim profile
//   Route::post('claim-profile-submit',  [AuthController::class,'claimProfileSubmit']);
//   Route::post('report-profile',  [ReportUapController::class,'reportProfile']);
// });

// // Route::post('logout', [AuthController::class, 'logout']);

// Route::post('report-company', [ReportUapController::class, 'companyReport']);
// Route::post('report-professional', [ReportUapController::class, 'professionalReport']);
// Route::post('report-quick-tip-offs', [ReportUapController::class, 'quickTipOffsReport']);


// //public report route
// Route::post('public-report-company', [PublicUapReportController::class, 'publicCompanyReport']);
// Route::post('pubic-report-professional', [PublicUapReportController::class, 'publicProfessionalReport']);
// Route::post('pubic-report-social-media', [PublicUapReportController::class, 'publicSocialMediaReport']);

// Route::post('profile-signup-url',  [AuthController::class,'profileSignupUrl']);
// Route::post('verify-profile-otp',  [AuthController::class,'verifyProfileOtp']);

// // Route::post('claim-account-signup',  [AuthController::class,'claimAccountSignup']);
// // Route::post('verify-claim-otp',  [AuthController::class,'verifyClaimOtp']);

// // send reset password email
// Route::post('reset-password-email',  [AuthController::class,'resetPasswordEmail']);
// Route::post('submit-reset-password',  [AuthController::class,'submitResetPassword']);

// // check social login user exist or not 
// Route::post('exist-social-login-user',  [AuthController::class,'existSocialLoginUser']);
// Route::post('signup-social-login-user',  [AuthController::class,'signupSocialLoginUser']);

// // save scribe alerts on home page
// Route::post('save-subscribe-alerts',  [CommonController::class,'saveSubscribeAlerts']);



// // APIs routes for publish Uap related transfer data
// Route::post('/save-uap-professional', [UapProfessionalController::class, 'saveUapProfessionals']);


Route::post('/generate-form', [OpenAIController::class, 'generateForm']);
Route::post('/generate-case-detail', [OpenAIController::class, 'generateVisaCase']);

Route::post('/start-conversation', [OpenAIController::class, 'startConversation']);
Route::post('/process-response', [OpenAIController::class, 'processResponse']);