<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\CdsProfessionalCompany;
use App\Models\CompanyLocations;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\ImmigrationServices;
use App\Models\AppointmentBooking;

use App\Models\ProfessionalServices;

use App\Models\OtherProfessionalDetail;
use App\Models\Professional;
use App\Models\User;
use View;
use DB;
use App\Models\ReviewsInvitations;
use App\Models\ReviewReplies;
use Illuminate\Support\Str;
use App\Models\Reviews;
use App\Services\CompanyService;


class CompaniesController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    /**
     * Display a listing of the professionals.
     *
     * @return \Illuminate\View\View
     */


    /**
     * Get the professionals list via AJAX with search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");
        $status = $request->input("status");
        $records = Professional::orderBy('id', "desc")
            ->where(function ($query) use ($search, $status) {
                if ($search != '') {
                    $query->where("name", "LIKE", "%$search%")
                        ->orWhere('company', "LIKE", "%$search%");

                }
                if (\Auth::user()->role == 'data-analyst') {
                    $query->where('assigned_to', \Auth::user()->id);
                }
                $query->where('status', $status);
            })
            ->paginate();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.companies.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified professional.
     *
     * @param string $uid
     * @return \Illuminate\View\View
     */

    public function add()
    {
        $viewData['pageTitle'] = "Add New Company";
        $viewData['showSidebar'] = true;
        $viewData['user'] = User::where("id", auth()->user()->id)->first();
        $viewData['template'] = 'companies.add-new-company';
        return view('admin-panel.04-profile.profile.profile-master', $viewData);
    }



    public function unauthorized()
    {
        $viewData['pageTitle'] = "Unauthorized";
        return view('unauthorized', $viewData);

    }
    public function pageNotFound()
    {
        $viewData['pageTitle'] = "Page not found";
        return view('page_does_not_exist', $viewData);

    }

    public function writeReview($token, Request $request)
    {
        if (!auth()->user()) {
            return redirect(url('login'))->with('error', 'Please Login with the same account to accept Invitation');

        } else {

            $getinvite = ReviewsInvitations::where('token', $token)->first();
            if ($getinvite != NULL) {
                $viewData['token'] = $token;
                //dd(  $viewData['token']);
                $viewData['pageTitle'] = "Write a Review";
                return view('write_a_review', $viewData);
            } else {
                return redirect(url('/unauthorized'))->with('error', 'Sorry you dont have access to this page');


            }

        }
    }


    public function acceptInvitation($token, Request $request)
    {

        if (!auth()->user()) {
            return redirect(url('login'))->with('error', 'Please Login with the same account to accept Invitation');

        } else {
            $update_invitation_sent = ReviewsInvitations::where(['token' => $token])->update(['status' => 'accepted']);
            $verify_invitation_sent = ReviewsInvitations::with('professional')->where(['token' => $token, 'email' => auth()->user()->email])->first();
            if ($verify_invitation_sent) {

                $data['professional_name'] = $verify_invitation_sent->professional->first_name . " " . $verify_invitation_sent->professional->last_name;
                $data['user_name'] = auth()->user()->first_name . " " . auth()->user()->last_name;
                $mail_message = view("emails.accepted_professional_invitation", $data)->render();
                $mailData['mail_message'] = $mail_message;
                $parameter['to'] = $verify_invitation_sent->email;
                $parameter['to_name'] = $verify_invitation_sent->professional->first_name . " " . $verify_invitation_sent->professional->last_name;
                $parameter['message'] = $mail_message;
                $parameter['subject'] = "Invitation Accepted by User";
                $parameter['view'] = "emails.accepted_professional_invitation";
                $parameter['data'] = $mailData;
                $mailRes = sendMail($parameter);

                if ($verify_invitation_sent->user_id == 0 && auth()->user()->email == $verify_invitation_sent->email) {
                    ReviewsInvitations::where(['id' => $verify_invitation_sent->id])->update(['user_id' => auth()->user()->id]);
                }

                return redirect(url('write-a-review/' . $token));
            } else {
                return redirect(url('/unauthorized'))->with('error', 'Sorry you dont have access to this page');

            }
        }
    }


    public function rejectInvitation($token, Request $request)
    {

        if (!auth()->user()) {
            return redirect(url('login'))->with('error', 'Please Login with the same account to accept Invitation');

        } else {
            $verify_invitation_sent = ReviewsInvitations::with('professional')->where(['token' => $token, 'email' => auth()->user()->email])->first();

            if ($verify_invitation_sent && auth()->user()) {
                $update_invitation_sent = ReviewsInvitations::where(['token' => $token])->update(['status' => 'rejected']);
                $data['professional_name'] = $verify_invitation_sent->professional->first_name . " " . $verify_invitation_sent->professional->last_name;
                $data['user_name'] = auth()->user()->first_name . " " . auth()->user()->last_name;
                $mail_message = view("emails.rejected_professional_invitation", $data)->render();
                $mailData['mail_message'] = $mail_message;
                $parameter['to'] = "developerphp84@gmail.com";
                $parameter['to_name'] = $verify_invitation_sent->professional->first_name . " " . $verify_invitation_sent->professional->last_name;
                $parameter['message'] = $mail_message;
                $parameter['subject'] = "Invitation Rejected by User";
                $parameter['view'] = "emails.rejected_professional_invitation";
                $parameter['data'] = $mailData;
                $mailRes = sendMail($parameter);
                if ($verify_invitation_sent->user_id == 0 && auth()->user()->email == $verify_invitation_sent->email) {
                    ReviewsInvitations::where(['id' => $verify_invitation_sent->id])->update(['user_id' => auth()->user()->id]);
                    ReviewsInvitations::where(['token' => $token])->delete();
                }


            }
            return redirect(url('write-a-review/' . $token));
        }

    }


    public function submitReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|min:1',
            'review' => 'required|max:255',
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


        try {
            // Find the invitation
            $invitation = ReviewsInvitations::where('token', $request->token)->firstOrFail();

            // Check if the user has already given a review
            $existingReview = Reviews::where('added_by', auth()->user()->id())
                            ->where('professional_id', $invitation->added_by)
                            ->first();

            if ($existingReview) {
                return response()->json([
                    'status' => false,
                    'message' => ['review' => 'You have already given a review.']
                ]);
            }

            // Save the new review
            $review = new Reviews();
            $review->added_by = auth()->id();
            $review->rating = $request->rating;
            $review->review = $request->review;
            $review->edited = 0;
            $review->unique_id = randomNumber();
            $review->invitation_id = $invitation->id;
            $review->professional_id = $invitation->added_by;
            $review->save();

            // Update the invitation status
            $invitation->update(['status' => 'review_given']);

            return response()->json([
                'status' => true,
                'message' => 'Review given successfully.',
                'redirect_back' => baseUrl('/review-received')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => ['error' => 'Something went wrong. Please try again.']
            ]);
        }


    }

   

    public function getServices()
    {
        if (auth()->user()->role == "professional") {

            $viewData['pageTitle'] = "Services";
            return view('admin-panel.04-profile.my-services.lists', $viewData);

        } else {
            return redirect(baseUrl('/'));
        }
    }



    
   
    public function professionalServicesSave(Request $request)
    {
        $subservices = $request->subservices;
        ProfessionalServices::whereNotIn('service_id', $subservices)->delete();

        foreach ($subservices as $subserv) {

            $parent_service = ImmigrationServices::where('id', $subserv)->first();
            $check_service = ProfessionalServices::where('service_id', $subserv)
                ->where('user_id', auth()->user()->id)
                ->first();
            if ($check_service == NULL) {
                $prof_servc = new ProfessionalServices;
                $prof_servc->parent_service_id = $parent_service->parent_service_id;
                $prof_servc->unique_id = randomNumber();
                $prof_servc->user_id = auth()->user()->id;
                $prof_servc->service_id = $subserv;
                $prof_servc->save();
            }
        }
        return back()->with('success', 'Services Added Successfully');

    }
      
    private function sendInviteMail($email, $templateContent, $templateSubject)
    {
        $token = Str::random(64);
        $professional_name = auth()->user()->first_name . " " . auth()->user()->last_name;

        ReviewsInvitations::create([
            'email' => $email,
            'token' => $token,
            'added_by' => auth()->user()->id,
            'status' => "pending",
            'user_id' => User::where('email', $email)->value('id') ?? 0
        ]);

        $mailData = [
            'token' => $token,
            'template_content' => $templateContent,
            'professional_name' => $professional_name,
        ];

        $view = \View::make('emails.review_invitations', $mailData);
        $message = $view->render();

        sendMail([
            'to' => $email,
            'to_name' => '',
            'message' => $message,
            'subject' => $templateSubject,
            'view' => 'emails.review_invitations',
            'data' => $mailData
        ]);
    }

    // --- Helper Methods for DRY and Business Logic Separation ---
    /**
     * Format validation errors for JSON responses.
     */
    private function formatValidationErrors($validator)
    {
        $error = $validator->errors()->toArray();
        $errMsg = [];
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return $errMsg;
    }

    /**
     * Get validation rules for company creation/update.
     */
    private function companyValidationRules()
    {
        return [
            'company_name' => 'required',
            'owner_type' => 'required',
            'company_type' => 'required',
            'about_company' => 'required',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Send review invitation email and create invitation record.
     */
    private function createAndSendInvitation($email, $templateContent, $templateSubject, $addedById, $professionalName)
    {
        $token = Str::random(64);
        $userId = User::where('email', $email)->value('id') ?? 0;
        ReviewsInvitations::create([
            'email' => $email,
            'token' => $token,
            'added_by' => $addedById,
            'status' => 'pending',
            'user_id' => $userId
        ]);
        $mailData = [
            'token' => $token,
            'template_content' => $templateContent,
            'professional_name' => $professionalName,
        ];
        $view = \View::make('emails.review_invitations', $mailData);
        $message = $view->render();
        sendMail([
            'to' => $email,
            'to_name' => '',
            'message' => $message,
            'subject' => $templateSubject,
            'view' => 'emails.review_invitations',
            'data' => $mailData
        ]);
    }

    public function sendInviteCSVollddd(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);
        if ($file = $request->file('csv_file')) {
            $rowNumber = 0;
            $errors = [];
            $invitationsSent = 0;
            $totalInvitesSent = ReviewsInvitations::where('added_by', auth()->user()->id)->count();
            $invitationsLeft = 50 - $totalInvitesSent;
            if ($totalInvitesSent >= 50) {
                $response['status'] = false;
                $response['message'] = 'You have reached the maximum limit of 50 invitations.';
                $response['redirect_back'] = baseUrl('reviews/send-invitation-email/add');
                return response()->json($response);
            }
            if (($handle = fopen($file, 'r')) !== false) {
                fgetcsv($handle);
                $totalEmailsInCSV = 0;
                $emails = [];
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $totalEmailsInCSV++;
                    $emails[] = $data[0] ?? null;
                }
                fclose($handle);
                if ($totalEmailsInCSV > $invitationsLeft) {
                    $response['status'] = false;
                    $response['message'] = 'The number of emails in the CSV (' . $totalEmailsInCSV . ') which exceeds the remaining invitations (' . $invitationsLeft . ') .';
                    return response()->json($response);
                }
                $professional_name = auth()->user()->first_name . " " . auth()->user()->last_name;
                $sentCount = $this->companyService->bulkInvite($emails, $request->input("template_content"), $request->input("template_subject"), auth()->user()->id, $professional_name);
                $response['status'] = true;
                $response['message'] = "$sentCount invitations were successfully sent.";
                $response['redirect_back'] = baseUrl('reviews/send-invitation-email/add');
                return response()->json($response);
            }
            $response['status'] = false;
            $response['message'] = "Error opening the file";
            $response['redirect_back'] = baseUrl('reviews/send-invitation-email/add');
            return response()->json($response);
        }
        $response['status'] = false;
        $response['message'] = "No file was uploaded.";
        $response['redirect_back'] = baseUrl('reviews/send-invitation-email/add');
        return response()->json($response);
    }


    public function sendInviteCSVold(Request $request)
    {

        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
            'subject' => 'required',
        ]);



        // if ($validator->fails()) {
        //     $response['status'] = false;
        //     $error = $validator->errors()->toArray();
        //     $errMsg = [];

        //     foreach ($error as $key => $err) {
        //         $errMsg[$key] = $err[0];
        //     }
        //     $response['message'] = $errMsg;
        //     return response()->json($response);
        // }



        if ($file = $request->file('csv_file')) {
            $rowNumber = 0;
            $errors = [];

            $file = $request->file('csv_file');

            // Open and read the file
            if (($handle = fopen($file, 'r')) !== false) {

                // Skip the first line if it contains headers
                fgetcsv($handle);
                $data = fgetcsv($handle, 1000, ',');
                // Loop through the file

                while (($data) !== false) {
                    $rowNumber++;
                    // echo ($rowNumber);
                    // Skip the header row if present
                    if ($rowNumber === 1) {
                        continue;
                    }

                    $getemail = User::where('email', $data[0])->first();
                    $checkinvite = ReviewsInvitations::where('email', $data[0])
                        ->where('added_by', auth()->user()->id)->first();
                    $invite_count = ReviewsInvitations::where('email', $data[0])
                        ->where('added_by', auth()->user()->id)->count();

                    $validation = [
                        'email' => $data[0] ?? null,
                    ];


                    $validator = Validator::make($validation, [
                        'email' => 'required|email',
                    ]);
                    if ($invite_count <= 50) {
                        // If validation fails, collect errors
                        if ($validator->fails()) {
                            $errors[$rowNumber] = $validator->errors()->all();
                            if (!empty($errors)) {
                                return back()->with('error', 'Email must be valid');
                            }

                        } else {
                            // If validation passes, process the row (e.g., save to database)
                            if ($checkinvite == NULL) {
                                $template_content = $request->template_content;
                                $token = Str::random(64);
                                $object = new ReviewsInvitations;
                                $object->email = $validation['email'];
                                // $object->subject  = $request->input("subject");
                                $object->token = $token;
                                $object->added_by = \Auth::user()->id;
                                $object->status = "pending";
                                if ($getemail != NULL) {
                                    $object->user_id = $getemail->id;
                                } else {
                                    $object->user_id = 0;
                                }
                                $object->save();
                                $professional_name = auth()->user()->first_name . " " . auth()->user()->last_name;
                                $mailData = [
                                    'token' => $token,
                                    'professional_name' => $professional_name,
                                    'template_content' => $template_content
                                ];
                                $view = \View::make('emails.review_invitations', $mailData);
                                $message = $view->render();

                                $parameter = [
                                    'to' => $validation['email'],
                                    'to_name' => '',
                                    'message' => $message,
                                    'subject' => $request->input("subject"),
                                    'view' => 'emails.review_invitations',
                                    'data' => $mailData,
                                ];
                                // Send the email
                                $mailRes = sendMail($parameter);
                                //dd($mailRes);
                            }
                        }
                    } else {

                        return back()->with('error', 'You Cannnot send more than 50 mails');

                    }
                }

                // Close the file
                fclose($handle);
                // Return success response if everything is valid
                return back()->with('success', 'CSV file imported successfully!');

            }
            return back()->with('error', 'Error opening the file!');

        }

        //$response['status'] = true;
        //$response['redirect_back'] = baseUrl('send-invitations');
        // $response['message'] = "Email sent successfully";

        // return response()->json($response);
    }

   

    /**
     * Update the specified professional in the database.
     *
     * @param string $uid
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    

   

    public function save(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'view_profile_url' => 'required|url|max:2048',
                'college_id' => 'nullable|string|min:1|max:255',
                'name' => 'required|string|min:3|max:255',
                'company' => 'nullable|string|min:3|max:255',
                'company_type' => 'nullable|string|min:3|max:255',
                'entitled_to_practise' => 'nullable|string|min:3|max:255',
                'entitled_to_practis_college_id' => 'nullable|string|min:1|max:255',
                'type' => 'nullable|string|min:3|max:255',
                'suspension_revocation_history' => 'nullable|string|min:3|max:1000',
                'employment_company' => 'nullable|string|min:3|max:255',
                'employment_startdate' => 'nullable|date',
                'employment_country' => 'nullable|string|min:3|max:100',
                'employment_state' => 'nullable|string|min:2|max:100',
                'employment_city' => 'nullable|string|min:2|max:100',
                'employment_email' => 'nullable|email|max:255',
                'employment_phone' => 'nullable|string|min:10|max:15',
                'agentsinfo' => 'nullable|string|min:3|max:1000',
                'license_historyclass' => 'nullable|string|min:3|max:255',
                'license_historystartdate' => 'nullable|string',
                'license_historyexpiry_date' => 'nullable|string',
                'license_history_status' => 'nullable|string|min:3|max:255',

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

            $object = new Professional;
            $object->view_profile_url = $request->input("view_profile_url") ?? '';
            $object->college_id = $request->input("college_id") ?? '';
            $object->name = $request->input("name") ?? '';
            $object->company = $request->input("company") ?? '';
            $object->company_type = $request->input("company_type") ?? '';
            $object->entitled_to_practise = $request->input("entitled_to_practise") ?? '';
            $object->entitled_to_practis_college_id = $request->input("entitled_to_practis_college_id") ?? '';
            $object->type = $request->input("type") ?? '';
            $object->suspension_revocation_history = $request->input("suspension_revocation_history") ?? '';
            $object->employment_company = $request->input("employment_company") ?? '';
            $object->employment_startdate = $request->input("employment_startdate") ?? '';
            $object->employment_country = $request->input("employment_country") ?? '';
            $object->employment_state = $request->input("employment_state") ?? '';
            $object->employment_city = $request->input("employment_city") ?? '';
            $object->employment_email = $request->input("employment_email") ?? '';
            $object->employment_phone = $request->input("employment_phone") ?? '';
            $object->agentsinfo = $request->input("agentsinfo") ?? '';
            $object->license_historyclass = $request->input("license_historyclass") ?? '';
            $object->license_historystartdate = $request->input("license_historystartdate") ?? '';
            $object->license_historyexpiry_date = $request->input("license_historyexpiry_date") ?? '';
            $object->license_history_status = $request->input("license_history_status") ?? '';
            $object->linked_user_id = 0;
            $object->added_by = auth()->user()->id;
            $object->save();

            DB::commit();

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('companies');
            $response['message'] = "Detail updated successfully";

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating detail: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json(['status' => false, 'message' => 'Failed to update the detail']);
        }
    }

    /**
     * Show the form for editing the specified professional.
     *
     * @param string $uid
     * @return \Illuminate\View\View
     */


    public function edit($uid)
    {
        $viewData['record'] = Professional::where("unique_id", $uid)->first();
        $viewData['pageTitle'] = "Edit Review";
        return view('admin-panel.companies.edit', $viewData);
    }

    /**
     * Show the form for editing the specified professional.
     *
     * @param string $uid
     * @return \Illuminate\View\View
     */



    /**
     * Update the specified professional in the database.
     *
     * @param string $uid
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
   
    /**
     * Update the specified professional in the database.
     *
     * @param string $uid
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($uid, Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all(), [
                'view_profile_url' => 'nullable|url',
                'college_id' => 'nullable|string|min:1|max:255',
                'name' => 'nullable|string',
                'company' => 'nullable|string',
                'company_type' => 'nullable|string',
                'entitled_to_practise' => 'nullable|string',
                'entitled_to_practis_college_id' => 'nullable|string|min:1|max:255',
                'type' => 'nullable|string',
                'suspension_revocation_history' => 'nullable|string',
                'employment_company' => 'nullable|string',
                'employment_startdate' => 'nullable|date',
                'employment_country' => 'nullable|string',
                'employment_state' => 'nullable|string',
                'employment_city' => 'nullable|string',
                'employment_email' => 'nullable|email',
                'employment_phone' => 'nullable|string',
                'agentsinfo' => 'nullable|string',
                'license_historyclass' => 'nullable|string',
                'license_historystartdate' => 'nullable|string',
                'license_historyexpiry_date' => 'nullable|string',
                'license_history_status' => 'nullable|string',
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

            $object = Professional::where('unique_id', $uid)->first();
            $object->view_profile_url = $request->input("view_profile_url");
            $object->college_id = $request->input("college_id") ?? '';
            $object->name = $request->input("name") ?? '';
            $object->company = $request->input("company");
            $object->company_type = $request->input("company_type");
            $object->entitled_to_practise = $request->input("entitled_to_practise");
            $object->entitled_to_practis_college_id = $request->input("entitled_to_practis_college_id");
            $object->type = $request->input("type");
            $object->suspension_revocation_history = $request->input("suspension_revocation_history");
            $object->employment_company = $request->input("employment_company");
            $object->employment_startdate = $request->input("employment_startdate");
            $object->employment_country = $request->input("employment_country");
            $object->employment_state = $request->input("employment_state");
            $object->employment_city = $request->input("employment_city");
            $object->employment_email = $request->input("employment_email");
            $object->employment_phone = $request->input("employment_phone");
            $object->agentsinfo = $request->input("agentsinfo");
            $object->license_historyclass = $request->input("license_historyclass");
            $object->license_historystartdate = $request->input("license_historystartdate");
            $object->license_historyexpiry_date = $request->input("license_historyexpiry_date");
            $object->license_history_status = $request->input("license_history_status");
            $object->linked_user_id = 0;
            // $object->added_by = auth()->user()->id;
            $object->save();

            DB::commit();

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('companies');
            $response['message'] = "Detail updated successfully";

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating detail: ' . $e->getMessage() . ' in file ' . $e->getFile() . ' on line ' . $e->getLine());
            return response()->json(['status' => false, 'message' => 'Failed to update the detail']);
        }
    }

    /**
     * Delete a single state record by its ID.
     *
     * @param string $id Base64 encoded ID of the state.
     * @return \Illuminate\Http\RedirectResponse
     */


    public function invitationsDelete($id)
    {
        $reviewInvitation = ReviewsInvitations::where('id', $id)->first();

        if (!$reviewInvitation->isEditableBy(auth()->id())) {
             return handleUnauthorizedAccess('You are not authorized to edit this Page');
        }

        ReviewsInvitations::where('id', $id)->delete();

        return redirect()->back()->with("success", "Record deleted successfully");
    }

 

    /**
     * Delete a single state record by its ID.
     *
     * @param string $id Base64 encoded ID of the state.
     * @return \Illuminate\Http\RedirectResponse
     */

    public function deleteSingle($id)
    {
        Professional::deleteRecord($id);
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    /**
     * Delete multiple user records based on a comma-separated list of unique identifiers.
     *
     * @param  \Illuminate\Http\Request  $request  The request object containing the list of user IDs to be deleted.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMultiple(Request $request)
    {
        $ids = explode(",", $request->input("ids"));

        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            Professional::deleteRecord($id);
        }
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }

    /**
     * Display extra details of a specific professional.
     *
     * @param string $uid
     * @return \Illuminate\View\View
     */
    public function extraDetail($uid)
    {
        $professional = Professional::where('unique_id', $uid)->first();
        $viewData['records'] = OtherProfessionalDetail::where("professional_id", $professional->id)->get();
        $viewData['professional'] = $professional;
        $viewData['pageTitle'] = "Extra Detail";
        return view('admin-panel.companies.extra-detail', $viewData);
    }

    /**
     * Update extra details of a specific professional.
     *
     * @param string $uid
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateExtraDetail($uid, Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'items' => 'required|array', // Ensure 'items' is present and is an array
                'items.*.item_label' => 'required|string|max:255', // Validate each item's label
                'items.*.item_value' => 'required|string|max:1000|input_sanitize', // Validate each item's value
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


            $professional = Professional::where('unique_id', $uid)->first();
            $items = $request->items;
            $ids = array();

            foreach ($items as $item) {
                $attributes = [
                    'professional_id' => $professional->id,
                    'meta_key' => $item['item_label'],
                ];

                $values = [
                    'meta_value' => $item['item_value'],
                    'added_by' => auth()->user()->id,
                ];

                $rec = OtherProfessionalDetail::updateOrCreate($attributes, $values);
                $ids[] = $rec->id;
            }

            OtherProfessionalDetail::whereNotIn('id', $ids)->where("professional_id", $professional->id)->delete();

            DB::commit();

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('companies');
            $response['message'] = "Detail updated successfully";

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Failed to update extra details']);
        }
    }

    /**
     * Display the details of a specific professional.
     *
     * @param string $uid
     * @return \Illuminate\View\View
     */
    public function view($uid)
    {
        $viewData['record'] = Professional::with(['professionalDetail'])->where("unique_id", $uid)->first();
        $viewData['pageTitle'] = "View Detail";
        return view('admin-panel.companies.view', $viewData);
    }

    /**
     * Fetch and assign professionals to the logged-in user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchAdd()
    {

        $pending_count = Professional::where('assigned_to', auth()->user()->id)->where('status', 'pending')->count();

        if ($pending_count == 0) {
            $octo_professional = Professional::where('assigned_to', 0)->limit(50)->get();

            if ($octo_professional->isNotEmpty()) {
                foreach ($octo_professional as $value) {
                    Professional::where('id', $value->id)->update(['assigned_to' => auth()->user()->id]);
                }

                $response['status'] = true;
                $response['message'] = "Professionals assigned sucessfully";
            } else {
                $response['status'] = false;
                $response['message'] = "No professionals for assign";
            }
        } else {
            $response['status'] = false;
            $response['message'] = "You have pending records please complete first";
        }

        return response()->json($response);
    }

    public function markAsInComplete($id)
    {
        Professional::where('unique_id', $id)->update(['status' => 'incomplete']);
        return redirect()->back()->with("success", "Record has been updated!");
    }

    public function markAsComplete(Request $request)
    {

        $ids = explode(",", $request->input("ids"));
        for ($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            Professional::where('unique_id', $id)->update(['status' => 'complete']);
        }
        $response['status'] = true;
        \Session::flash('success', 'Records updated successfully');
        return response()->json($response);
    }

    public function chooseServicesSave(Request $request)
    {
        $subservices = $request->subservices;
        ProfessionalServices::whereNotIn('service_id', $subservices)->delete();

        foreach ($subservices as $subserv) {

            $parent_service = ImmigrationServices::where('id', $subserv)->first();
            $check_service = ProfessionalServices::where('service_id', $subserv)
                ->where('user_id', auth()->user()->id)
                ->first();
            if ($check_service == NULL) {
                $prof_servc = new ProfessionalServices;
                $prof_servc->parent_service_id = $parent_service->parent_service_id;
                $prof_servc->unique_id = randomNumber();
                $prof_servc->user_id = auth()->user()->id;
                $prof_servc->service_id = $subserv;
                $prof_servc->save();
            }
        }
        return back()->with('success', 'Services Added Successfully');

    }

    public function searchServices(Request $request)
    {
        $search = $request->get('query');
        $data = ImmigrationServices::where('name', 'LIKE', "%{$search}%")
            ->orderBy('parent_service_id', 'asc')
            ->get();
        $viewData['current_services'] = ProfessionalServices::where('user_id', auth()->user()->id)
            ->get()->pluck('service_id')->toArray();
        $viewData['services'] = $data;
        $view = View::make('components.choose-service', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        // $result = [];
        // $sub_service_ids = array();
        // foreach ($data as $row) {
        //     if($row->parent_service_id == 0){
        //         $result[] = ['value' => $row->name, 'id' => $row->id,'is_parent'=>1,'parent_service_id'=>0];
        //         foreach($row->subServices as $subServices){
        //             $sub_service_ids[] = $subServices->id;
        //             $result[] = ['value' => $subServices->name, 'id' => $subServices->id,'is_parent'=>0,'parent_service_id'=>$subServices->parent_service_id];
        //         }
        //     }else{
        //         if(!in_array($row->id,$sub_service_ids)){
        //             $result[] = ['value' => $row->name, 'id' => $row->id,'is_parent'=>0,'parent_service_id'=>$row->parent_service_id];
        //         }
        //     }            
        // }
        return response()->json($response);
    }

    public function linkServiceWithProfesional(Request $request)
    {
        $type = $request->input("type");
        $service_id = $request->input("service_id");
        $service = ImmigrationServices::where('id', $service_id)->first();
        if ($type == 'sub_service') {
            $check_service = ProfessionalServices::where('service_id', $service->id)
                ->where('user_id', auth()->user()->id)
                ->first();
            if ($check_service == NULL) {
                $prof_servc = new ProfessionalServices;
                $prof_servc->parent_service_id = $service->parent_service_id;
                $prof_servc->unique_id = randomNumber();
                $prof_servc->user_id = auth()->user()->id;
                $prof_servc->service_id = $service->id;
                $prof_servc->save();
            }
        } else {
            $services = ImmigrationServices::where('parent_service_id', $service_id)->get();
            foreach ($services as $sub_service) {
                $check_service = ProfessionalServices::where('service_id', $sub_service->id)
                    ->where('user_id', auth()->user()->id)
                    ->first();
                if ($check_service == NULL) {
                    $prof_servc = new ProfessionalServices;
                    $prof_servc->parent_service_id = $sub_service->parent_service_id;
                    $prof_servc->unique_id = randomNumber();
                    $prof_servc->user_id = auth()->user()->id;
                    $prof_servc->service_id = $sub_service->id;
                    $prof_servc->save();
                }
            }
        }
        $response['status'] = true;
        return response()->json($response);
    }

   
 

    public function saveCompany(Request $request)
    {
        $validator = Validator::make($request->all(), $this->companyService->companyValidationRules(), [
            'company_logo.max' => 'The media file must not be greater than 2MB.',
        ]);
        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = $this->companyService->formatValidationErrors($validator);
            return response()->json($response);
        }
        $this->companyService->createCompany($request->all(), \Auth::user()->id);
        $response['status'] = true;
        $response['message'] = "Company added successfully";
        $response['redirect_back'] = baseUrl('profile/companies');
        return response()->json($response);
    }

    public function editCompany($uid)
    {
        $viewData['pageTitle'] = "Edit Company";
        $viewData['showSidebar'] = true;
        $record = CdsProfessionalCompany::where("unique_id", $uid)->first();
        $viewData['record'] = $record;
        $viewData['template'] = 'companies.edit-company';
        return view('admin-panel.04-profile.profile.profile-master', $viewData);
    }

    public function updateCompany($uid, Request $request)
    {
        $validator = Validator::make($request->all(), $this->companyService->companyValidationRules(), [
            'company_logo.max' => 'The media file must not be greater than 2MB.',
        ]);
        if ($validator->fails()) {
            $response['status'] = false;
            $response['message'] = $this->companyService->formatValidationErrors($validator);
            return response()->json($response);
        }
        $this->companyService->updateCompany($uid, $request->all(), \Auth::user()->id);
        $response['status'] = true;
        $response['message'] = "Company updated successfully";
        $response['redirect_back'] = baseUrl('profile/companies');
        return response()->json($response);
    }

    public function deleteCompany($id)
    {
        list($success, $message) = $this->companyService->deleteCompanyWithLocations($id);
        if ($success) {
            return redirect()->back()->with("success", $message);
        } else {
            return redirect()->back()->with("error", $message);
        }
    }

    public function markAsPrimary(Request $request)
    {
        CdsProfessionalCompany::where('user_id',$request->user_id)->update(['is_primary' => 0]);
        CdsProfessionalCompany::where('unique_id',$request->company_id)->where('user_id',$request->user_id)->update(['is_primary' => 1]);
        $response['status'] = true;
        $response['message'] = 'Company set as primary';
        return response()->json($response);
    }

    public function companyLogo($id,Request $request)
    {
        $viewData['pageTitle'] = "Company Logo"; // Set the page title
        $viewData['id'] = $id;
        $view = View::make('admin-panel.04-profile.profile.companies.company-logo', $viewData);
        $contents = $view->render();

        // Prepare the JSON response
        $response['contents'] = $contents;
        $response['status'] = true;

        return response()->json($response);
    }

    public function saveCompanyLogo($id,Request $request){
        if ($file = $request->file('file')){
            $extension = $file->getClientOriginalExtension() ?: 'png';
            $newName = mt_rand().".".$extension;
            $source_url = $file->getPathName();
            // $destinationPath = uapProfessionalsDir();
            $destinationPath = public_path('uploads/temp');
            if($file->move($destinationPath, $newName)){
                $sourcePath =  public_path('uploads/temp/'.$newName);
                $media_path = companyLogoDir();
                $response = mediaUploadApi("upload-file",$sourcePath,$media_path,$newName);
                if(isset($response['status'])){
                    if($response['status'] == 'success'){
                        \File::delete($sourcePath);
                        if($id == 0){
                            $record = CdsProfessionalCompany::create(['company_logo'=>$newName]);
                            $id = $record->unique_id;
                        }else{
                            CdsProfessionalCompany::where("unique_id",$id)->update(['company_logo'=>$newName]);
                        }
                        
                        return response()->json(['status'=>true,'message' => "Image cropped successfully.", 'filename' => $newName,'filepath' => companyLogoDirUrl($newName),'redirect_back'=>baseUrl('companies/edit-company/'.$id)]);
                        
                    }else{
                        return response()->json(['status'=>false,'message' => $response['message'] ]);
                    }
                }else{
                    return response()->json(['status'=>false,'message' => "Failed uploading image. Try again."]);
                }
            }else{
                return response()->json(['status'=>false,'message' => "Some issue while upload. Try again"]);
            }
        }
    }

    public function companyBannerCropper($id,Request $request)
    {
        $viewData['pageTitle'] = "Banner Image Cropper"; // Set the page title
        $viewData['id'] = $id;
        $view = View::make('admin-panel.04-profile.profile.companies.company-banner-logo', $viewData);
        $contents = $view->render();

        // Prepare the JSON response
        $response['contents'] = $contents;
        $response['status'] = true;

        return response()->json($response);
    }

    public function saveCompanyBannerImage($id,Request $request){
        if ($file = $request->file('file')){
            $extension       = $file->getClientOriginalExtension() ?: 'png';
            $newName        = mt_rand().".".$extension;
            $source_url = $file->getPathName();
            // $destinationPath = uapProfessionalsDir();
            $destinationPath = public_path('uploads/temp');
            if($file->move($destinationPath, $newName)){
                $sourcePath =  public_path('uploads/temp/'.$newName);
                $media_path = companyBannerDir();
                $response = mediaUploadApi("upload-file",$sourcePath,$media_path,$newName);
                if(isset($response['status'])){
                    if($response['status'] == 'success'){
                        \File::delete($sourcePath);
                        if($id == 0){
                            $record = CdsProfessionalCompany::create(['banner_image'=>$newName]);
                            $id = $record->unique_id;
                        }else{
                            CdsProfessionalCompany::where("unique_id",$id)->update(['banner_image'=>$newName]);
                        }
                        return response()->json(['status'=>true,'message' => "Image cropped successfully.", 'filename' => $newName,'filepath' => userBannerDirUrl($newName),'redirect_back'=>baseUrl('companies/edit-company/'.$id)]);
                        
                    }else{
                        return response()->json(['status'=>false,'message' => $response['message'] ]);
                    }
                }else{
                    return response()->json(['status'=>false,'message' => "Failed uploading image. Try again."]);
                }
            }else{
                return response()->json(['status'=>false,'message' => "Some issue while upload. Try again"]);
            }
        }
    }
    
}
// 