<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use View;
use DB;
use Illuminate\Support\Str;

class TrackingController extends Controller
{
    /**
     * Display a listing of the professionals.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Tracking Data";
        return view('admin-panel.09-utilities.tracking.lists', $viewData);
    }


    /**
     * Get the professionals list via AJAX with search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $apiData['user_id'] = auth()->user()->id;
        $records =  investgateApiCall('fetch-client-tracking-data', $apiData);
       
        if(!empty($records['status']) != 0){
            $viewData['records'] = $records['result']['data'];
            $response['last_page'] = $records['result']['last_page'];
            $response['current_page'] =$records['result']['current_page'];
            $response['total_records'] = $records['result']['total'];
        }else{
            $viewData['records'] = $records['result'];
            $response['last_page'] = 0;
            $response['current_page'] =0;
            $response['total_records'] = 0;
        }
        
        $view = View::make('admin-panel.09-utilities.tracking.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
       

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified professional.
     *
     * @param string $uid
     * @return \Illuminate\View\View
     */

    public function view($id)
    {
        $apiData['unique_id'] = $id;
        $apiData['user_id'] = auth()->user()->id;
        $records =  investgateApiCall('fetch-client-tracking-detail', $apiData);
        $viewData['pageTitle'] = "View Detail";
        $viewData['record'] = $records['result'];
        $viewData['uap_id'] = $records['uap_id'];
        $viewData['ref_user_id'] = $records['ref_user_id'];
        return view('admin-panel.09-utilities.tracking.view',$viewData);
    }

    public function saveUapComment(Request $request)
    {

        $apiData = $request->all();
        $apiData['added_by'] = auth()->user()->id;
        $apiData['added_by_name'] = auth()->user()->first_name.' '.auth()->user()->last_name;
        $records = investgateApiCall('save-uap-comment', $apiData);

        $response['message'] = $records['message'];
        $response['status'] = $records['status'];

        return response()->json($response);
    }

    public function getUapComment(Request $request)
    {
        $apiData = $request->all();
        $apiData['uap_id'] = $request->uap_id;
        $records = investgateApiCall('get-uap-comment', $apiData);
        
        $viewData['records'] = $records['data']['records'];
        $view = View::make('admin-panel.09-utilities.tracking.comment-ajax-list', $viewData);
        $contents = $view->render();


        $response['contents'] = $contents;
        $response['last_page'] = $records['data']['last_page'];
        $response['current_page'] = $records['data']['current_page'];
        $response['total_records'] = $records['data']['total_records'];
        \Session::put("comment_last_page", $records['data']['current_page']);
        return response()->json($response);
    }



}