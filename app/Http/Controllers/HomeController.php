<?php

namespace App\Http\Controllers;

use App\Models\EmailLog;
use Illuminate\Http\Request;
use App\Models\States;
use App\Models\Cities;
use App\Models\User;
use App\Models\Media;
use App\Models\RememberToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class HomeController extends Controller
{
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
  
    public function stateList(Request $request)
    {

        $country_id = $request->input("country_id");
        $states = States::where("country_id", $country_id)->get();
        $options = '<option value="">Select State</option>';
        foreach ($states as $state) {
            $options .= '<option value="' . $state->id . '">' . $state->name . '</option>';
        }
        $response['options'] = $options;
        $response['status'] = true;
        return response()->json($response);
    }

    public function cityList(Request $request)
    {
        $state_id = $request->input("state_id");
        $cities = Cities::where("state_id", $state_id)->get();

        $options = '<option value="">Select City</option>';
        foreach ($cities as $city) {
            $options .= '<option value="' . $city->id . '">' . $city->name . '</option>';
        }
        $response['options'] = $options;
        $response['status'] = true;
        return response()->json($response);
    }

 
    public function sendMailInBackground($unique_id)
    {

        $mail_data = EmailLog::where("unique_id", $unique_id)->first();
        $parameter = json_decode($mail_data->mail_data, true);
        $response = triggerMail($parameter);
        if ($response['status']) {
            EmailLog::where("unique_id", $unique_id)->delete();
        }
    }

    public function awsFilesList(){
        $files = awsFetchFiles();
        pre($files);
        exit;
        foreach ($files as $fileKey) {
            // Delete each file
            $common  = "https://trustvisory.s3.amazonaws.com/";
            $file = str_replace($common,"",$fileKey);            
            awsDeleteFile($file);
            exit;
        }
    }

    public function googleRecaptcha(Request $request){
        $view = view("components.google-recaptcha");
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;

        return response()->json($response);
    }

    public function fetchRegulatoryBody(Request $request){
        $country_id = $request->input("country_id");
        $bodies = CdsRegulatoryBody::where("regulatory_country_id",$country_id)->get();
        $options = '<option value="">None</option>';
        foreach($bodies as $body){
            $options .= '<option data-prefix="'.$body->license_prefix.'" value="'.$body->id.'">'.$body->name.'</option>';
        }

        $response['status'] = true;
        $response['options'] = $options;

        return response()->json($response);
    }
  
    public function downloadProfessionalDocuments(Request $request){
    
        $filekey = $request->file;
        return awsFileDownload(config('awsfilepath.professional_document') . '/' .$filekey);
    }

    public function autoLogin(Request $request,$token)
    {
       
        $login_token =  \DB::table('auto_login_tokens')->where('token',decryptVal($token))->first();
        if(!empty($login_token)){
            $user = User::where('id',$login_token->user_id)->first();
            \Auth::loginUsingId($user->id);
            return redirect(baseUrl("/tracking"));
        }else{
            return redirect(baseUrl("/"));
        }   

    }
    public function customHtml($page)
    {
        return view("html." . $page);
    }

    public function autoMainLogin(Request $request,$token)
    {
       
        $login_token =  \DB::table('auto_login_tokens')->where('token',decryptVal($token))->first();
        if(!empty($login_token)){
            $user = User::where('id',$login_token->user_id)->first();
            \Auth::loginUsingId($user->id);
            return redirect(baseUrl("/"));
        }else{
            return redirect(baseUrl("/"));
        }   

    }

    public function downloadMediaFile(Request $request){
        $file_name = $request->file_name;
        $dir = $request->dir;
        if($request->size){
            $size = $request->size;
        }
        if(downloadMediaFile($file_name,$dir,$size='') == ''){
            return redirect()->back()->with("error","File not exists");
        }else{
            return redirect(downloadMediaFile($file_name,$dir,$size=''));
        }
       
    }

    public function sendTestMessage(){
        event(new \App\Events\MessageSent('Hello, World!'));

    }

    public function editorShortcode(){
        $viewData['pageTitle'] = "ShortCodes";
        $images = Media::whereIn('file_type',array("png","jpg","jpeg"))->get();
        $images_arr = array();
        foreach($images as $image){
            $images_arr[] = [
                'name' => $image->file_name,
                'file_url' => mediaDirUrl($image->file_name,'t'),
                'shortcode' => $image->file_name.":m",
            ];
        }
        $viewData['images'] = json_encode($images_arr);
        $view = view("components.editor-shortcode",$viewData);
        $response['contents'] = $view->render();
        $response['status'] = true;
        return response()->json($response);
    }


    
    public function autoSupportLogin(Request $request,$token)
    {
       
        $login_token =  \DB::table('auto_login_tokens')->where('token',decryptVal($token))->first();
        if(!empty($login_token)){
            $user = User::where('id',$login_token->user_id)->first();

            if($request->has('token')){
              
                $remeber_token = RememberToken::where('unique_id',decryptVal($request->get('token')))->where('user_id',$login_token->user_id)->first();

                if(!empty($remeber_token)){
                   
                    \Auth::loginUsingId($user->id);
                    RememberToken::where('unique_id',decryptVal($request->get('token')))->where('user_id',$login_token->user_id)->delete();
                    \Session::forget('support_token',encryptVal($remeber_token->unique_id));
                    return redirect( baseUrl('support/thankyou'));
                }else{
                    Auth::logout(); 
                    $url = mainTrustvisoryUrl().'/support/thankyou';
                    return redirect($url);
                }
            }
        }else{
            return redirect(baseUrl("/"));
        }   

    }

    public function supportThankyou()
    {
        if(auth()->check()){
            return view("thank-you-support");
        }else{
            return redirect('/');
        }
      
    }
    
    public function goToSupport()
    {
        $encryptVal = encryptVal(randomString());

        $token = RememberToken::where('user_id',auth()->user()->id)->first();

        if(!empty($token)){
            $rememberToken = RememberToken::where('user_id',auth()->user()->id)->first();
        }else{
            $rememberToken = new RememberToken;
        }
        
        $rememberToken->unique_id = randomNumber();
        $rememberToken->token = $encryptVal;
        $rememberToken->user_id = auth()->user()->id;
        $rememberToken->expiry_time = Carbon::now()->addMinutes(15);
        $rememberToken->save();
        \Session::put('support_token',encryptVal($rememberToken->unique_id));

        $url =  mainTrustvisoryUrl().'/support-our-initiative?ref='.encryptVal(auth()->user()->unique_id).'&token='.encryptVal($rememberToken->unique_id);
        return redirect($url);
    }

    public function viewBadge($uid)
    {
        $uid = decrypt($uid);
        $user = User::where("unique_id",$uid)->first();
        
        $badgeData['user'] = $user;
        $badgeData['showDownload'] = true;
        $badge = supportBadge(pointEarns($user->id),'data');
        $badgeData['badge'] = $badge;
        $viewData['pageTitle'] = "View Badge";
        $viewData['badge_html'] = view('components.badges', $badgeData)->render();
        return view('admin-panel.09-utilities.transactions.points-earned.99-02-badges.view-badge', $viewData);
    }

    public function notAccess()
    {
         return handleUnauthorizedAccess('You are not authorized to edit this Page');
    }
    
    public function processingPayment(Request $request){
        $viewData = array();
        $view = view("components.payment-processing-modal",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;
        return response()->json($response);
    }

     public function downloadFromStorage(Request $request)
    {
        $fileName = $request->query('file');
        $filePath = $request->query('file_path');
        // $size = '';

        // if ($request->s) {
        //     if ($request->s == 'm') {
        //         $size = 'medium/';
        //     } elseif ($request->s == 't') {
        //         $size = 'thumb/';
        //     } elseif ($request->s == 's') {
        //         $size = 'small/';
        //     }
        // }

        $fullPath = $filePath . '/' . $fileName;

        // Check if the file exists
        if ($fileName && Storage::disk('public')->exists($fullPath)) {
            $fileContent = Storage::disk('public')->get($fullPath);
            $mimeType = Storage::disk('public')->mimeType($fullPath);
            return response($fileContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        } else {
            return response()->json(['error' => 'File not found.'], 404);
        }
    }
}
