<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\Action;
use App\Models\Category;
use App\Models\UserDetails;

class ReviewerController extends Controller
{
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    /**
     * Show the form for creating a new action.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function add()
    {
        $viewData['pageTitle'] = "Verify Reviewer";
        return view('admin-panel.reviewer.add', $viewData);
    }

    /**
     * Store a newly created action in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo_id' => 'required',
        ]);

        if ($validator->fails()) {
            $response['status'] = false;
            $error = $validator->errors()->toArray();
            $errMsg = array();

            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }
       
        $user_detail = UserDetails::where('user_id',\Auth::user()->id)->first();

        if(!empty($user_detail))
        {
            UserDetails::where('user_id',\Auth::user()->id)->update([ 
                'photo_for_verification' => $request->photo_id,
                'is_verified_reviewer' => 1
            ]);
        }else{
            UserDetails::create([
                'unique_id' => randomNumber(), 
                'user_id' => \Auth::user()->id,
                'photo_for_verification' => $request->photo_id,
                'is_verified_reviewer' => 1
            ]);
        }
        

        $response['status'] = true;
        $response['message'] = "Record added successfully";

        return response()->json($response);
    }

    public function UploadFiles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpg,jpeg,pdf',
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

        $evidencesName = "";
        if ($file = $request->file){

            $fileName        = $file->getClientOriginalName();
            $extension       = $file->getClientOriginalExtension() ?: 'png';
            $evidencesName        = mt_rand(1,99999)."-".$fileName;
            $source_url = $file->getPathName();

            $destinationPath = uapDir();
            if($file->move($destinationPath, $evidencesName)){

            }
        }
        $response['status'] = true;
        $response['filename'] = $evidencesName;
        $response['message'] = "Record added successfully";

        return response()->json($response);
    }
}
