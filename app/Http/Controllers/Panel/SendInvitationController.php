<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\FeedsConnection;
use App\Models\User;
use App\Models\ReviewsInvitations;
use Illuminate\Http\Request;
use View;
use App\Services\InvitationService;

class SendInvitationController extends Controller
{
    private $invitationService;

    public function __construct(InvitationService $invitationService)
    {
        $this->invitationService = $invitationService;
    }

    public function index()
    {
        $viewData['status'] = "Pending";
        $viewData['pageTitle'] = "Send Invitations";
      
        return view('admin-panel.07-invitations.send-invitations.lists', $viewData);
    }

    public function getAjaxList(Request $request)
    {
        $records = ReviewsInvitations::orderBy('id', "desc")
            ->visibleToUser(auth()->user()->id)
            ->paginate();

        $viewData['records'] = $records;
        $view = View::make('admin-panel.07-invitations.send-invitations.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();

        return response()->json($response);
    }

    public function add()
    {
        $viewData['pageTitle'] = "Send Invites";
        $clients = FeedsConnection::whereHas("following", function ($query) {
                $query->where("role", InvitationService::ROLE_CLIENT);
                $query->where("status", InvitationService::STATUS_ACTIVE);
            })
            ->where("user_id", auth()->user()->id)
            ->where("status", InvitationService::STATUS_ACTIVE)
            ->get();
        $viewData['clients'] = $clients;
        return view('admin-panel.07-invitations.send-invitations.add', $viewData);
    }

    public function save(Request $request)
    {
        $validator = $this->invitationService->validateInvitationRequest($request->all());
        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = "validation";
            $error = $validator->errors()->toArray();
            $errMsg = [];
            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }
        \DB::beginTransaction();
        try {
            if ($request->selected_clients) {
                $selected_clients = json_decode($request->selected_clients, true);
                foreach ($selected_clients as $client) {
                    // User lookup and role check
                    if ($client['id'] == 'new') {
                        $user = $this->invitationService->getUserByEmail($client['email']);
                        if (!empty($user)) {
                            if ($user->role != InvitationService::ROLE_CLIENT) {
                                \DB::rollBack();
                                return response()->json([
                                    'status' => false,
                                    'error_type' => 'error',
                                    'message' => 'This email is already registered with a non-client role.',
                                ]);
                            } else {
                                $email = $user->email;
                                $receiver_name = $user->first_name . " " . $user->last_name;
                                $user_id = $user->id;
                            }
                        } else {
                            $receiver_name = $client['name'];
                            $email = $client['email'];
                            $user_id = 0;
                        }
                    } else {
                        $receiver_name = $client['name'];
                        $email = $client['email'];
                        $user_id = $client['id'];
                    }
                    // Invitation existence check
                    $existingInvitation = $this->invitationService->invitationExists($email, auth()->user()->id);
                    if ($existingInvitation) {
                        \DB::rollBack();
                        return response()->json([
                            'status' => false,
                            'error_type' => 'error',
                            'message' => "An invitation has already been sent to $email .",
                        ]);
                    }
                    // Create invitation
                    $token = \Str::random(64);
                    $object = new ReviewsInvitations();
                    $object->receiver_name = $receiver_name;
                    $object->email = $email;
                    $object->user_id = $user_id;
                    $object->token = $token;
                    $object->status = InvitationService::STATUS_PENDING;
                    $object->message = $request->personal_message;
                    $object->added_by = auth()->user()->id;
                    $object->save();
                    
                    // Save plan feature usage to history for review invitation
                    try {
                        $result = app(\App\Services\FeatureCheckService::class)->savePlanFeature(
                            'reviews',
                            auth()->user()->id,
                            1, // action type: add
                            1, // count: 1 invitation
                            [
                                'invitation_id' => $object->id,
                                'invitation_email' => $email,
                                'receiver_name' => $receiver_name,
                                'invitation_token' => $token,
                                'invitation_status' => InvitationService::STATUS_PENDING
                            ]
                        );

                        // Log the result for debugging
                        \Log::info('savePlanFeature result for review invitation', [
                            'user_id' => auth()->user()->id,
                            'invitation_id' => $object->id,
                            'result' => $result
                        ]);

                        if (!$result['success']) {
                            \Log::error('Failed to save plan feature usage for review invitation', [
                                'user_id' => auth()->user()->id,
                                'invitation_id' => $object->id,
                                'error' => $result['message']
                            ]);
                        }

                    } catch (\Exception $e) {
                        \Log::error('Exception in savePlanFeature for review invitation', [
                            'user_id' => auth()->user()->id,
                            'invitation_id' => $object->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                    
                    // Prepare mail data
                    $mailData = [
                        'token' => $token,
                        'url' => clientTrustvisoryUrl() . '/panel/receive-invitations/accept-invitation/' . $token,
                        'receiver_name' => $receiver_name,
                        'professional_id' => auth()->user()->unique_id,
                        'professional_name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        'professional_message' => $request->personal_message,
                    ];
                    $this->invitationService->sendInvitationMail($email, $mailData);
                }
                \DB::commit();
                $success_contents = view("admin-panel.07-invitations.send-invitations.success-invitation")->render();
                return response()->json([
                    'status' => true,
                    'message' => 'Invitation sent successfully',
                    'success_contents' => $success_contents,
                    'redirect_back' => baseUrl('send-invitations')
                ]);
            } else {
                \DB::rollBack();
                return response()->json([
                    'status' => false,
                    'error_type' => 'error',
                    'message' => 'Client not selected',
                    'redirect_back' => baseUrl('send-invitations')
                ]);
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['status' => false, 'error' => 'error', 'message' => $e->getMessage() . " Line No: " . $e->getLine() . " File: " . $e->getFile()]);
        }
    }

    public function showMailPreview(Request $request)
    {
        $validator = $this->invitationService->validateInvitationRequest($request->all());
        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = "validation";
            $error = $validator->errors()->toArray();
            $errMsg = [];
            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }
        try {
            if ($request->selected_clients) {
                $mailData = [
                    'professional_id' => auth()->user()->unique_id,
                    'professional_name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                    'professional_message' => $request->personal_message,
                ];
                $success_contents = view("emails.send-review-invitation", $mailData)->render();
                return response()->json([
                    'status' => true,
                    'success_contents' => $success_contents,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'error_type' => 'error',
                    'message' => 'Client not selected',
                    'redirect_back' => baseUrl('send-invitations')
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => 'error', 'message' => $e->getMessage() . " Line No: " . $e->getLine() . " File: " . $e->getFile()]);
        }
    }

    public function bulkUploadCsv(Request $request)
    {
        $validator = $this->invitationService->validateCsvUpload($request->all());
        if ($validator->fails()) {
            $response['status'] = false;
            $response['error_type'] = "validation";
            $error = $validator->errors()->toArray();
            $errMsg = [];
            foreach ($error as $key => $err) {
                $errMsg[$key] = $err[0];
            }
            $response['message'] = $errMsg;
            return response()->json($response);
        }
        $file = $request->file('csv_file');
        // Detect delimiter
        $firstLine = '';
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $firstLine = fgets($handle);
            fclose($handle);
        }
        $delimiters = [','];
        $delimiter = ',';
        $maxCount = 0;
        foreach ($delimiters as $d) {
            $count = count(str_getcsv($firstLine, $d));
            if ($count > $maxCount) {
                $maxCount = $count;
                $delimiter = $d;
            }
        }
        if ($maxCount == 1 && strpos($firstLine, ' ') !== false) {
            $delimiter = ' ';
        }
        $header = null;
        $data = [];
        $rowNumber = 0;
        $skipped_emails = [];
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle, 0, $delimiter);
            if (!empty($header[0])) {
                $header[0] = preg_replace('/\x{FEFF}/u', '', $header[0]);
            }
            $header = array_map('trim', $header);
            if (count($header) == 1 && strpos($header[0], ' ') !== false) {
                $header = preg_split('/\s+/', $header[0]);
            }
            $normalizedHeader = array_map('strtolower', $header);
            $required = ['name', 'email'];
            $missing = array_diff($required, $normalizedHeader);
            if (!empty($missing)) {
                fclose($handle);
                return response()->json([
                    'status' => false,
                    'error_type' => "error",
                    'message' => 'Missing required column(s): ' . implode(', ', $missing)
                ]);
            }
            $nameIndex = array_search('name', $normalizedHeader);
            $emailIndex = array_search('email', $normalizedHeader);
            $messageIndex = array_search('message', $normalizedHeader);
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNumber++;
                if (empty(array_filter($row))) {
                    continue;
                }
                if (count($row) == 1 && strpos($row[0], ' ') !== false) {
                    $row = preg_split('/\s+/', $row[0]);
                }
                if (count($row) < count($header)) {
                    continue;
                }
                $name = isset($row[$nameIndex]) ? trim($row[$nameIndex]) : '';
                $email = isset($row[$emailIndex]) ? trim($row[$emailIndex]) : '';
                $message = ($messageIndex !== false && isset($row[$messageIndex])) ? trim($row[$messageIndex]) : '';
                if (empty($name) || empty($email)) {
                    continue;
                }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                $user = $this->invitationService->getUserByEmail($email);
                if (!empty($user)) {
                    if ($user->role != InvitationService::ROLE_CLIENT) {
                        $skipped_emails[] = $email;
                    } else {
                        $data[] = [
                            'name' => $name,
                            'email' => $email,
                            'message' => $message,
                        ];
                    }
                } else {
                    $data[] = [
                        'name' => $name,
                        'email' => $email,
                        'message' => $message,
                    ];
                }
            }
            fclose($handle);
        }
        if (empty($data)) {
            return response()->json([
                'status' => false,
                'error_type' => "error",
                'message' => "No valid data found in CSV file."
            ]);
        }
        if (!empty($skipped_emails)) {
            $emails = implode(",", $skipped_emails);
            return response()->json([
                'status' => false,
                'error_type' => "error",
                'message' => "Emails " . $emails . " are registered as non client so cannot send invitations. Remove those email and upload file again"
            ]);
        }
        \DB::beginTransaction();
        try {
            $inserted = 0;
            $skipped = 0;
            $errors = [];
            $chunks = array_chunk($data, 100);
            $skipped_emails = [];
            foreach ($chunks as $chunk) {
                foreach ($chunk as $client) {
                    try {
                        $user = $this->invitationService->getUserByEmail($client['email']);
                        if (!empty($user)) {
                            if ($user->role != InvitationService::ROLE_CLIENT) {
                                $errors[] = $client['email'];
                                return response()->json([
                                    'status' => false,
                                    'error_type' => 'error',
                                    'message' => $client['email'] . ' email is already registered with a non-client role',
                                ]);
                            } else {
                                $email = $user->email;
                                $receiver_name = $user->first_name . " " . $user->last_name;
                                $user_id = $user->id;
                            }
                        } else {
                            $receiver_name = $client['name'];
                            $email = $client['email'];
                            $user_id = 0;
                        }
                        $existingInvitation = $this->invitationService->invitationExists($email, auth()->user()->id, InvitationService::STATUS_PENDING);
                        if ($existingInvitation) {
                            $skipped++;
                            $skipped_emails[] = $email;
                            continue;
                        }
                        $token = \Str::random(64);
                        $object = new ReviewsInvitations();
                        $object->receiver_name = $receiver_name;
                        $object->email = $email;
                        $object->user_id = $user_id;
                        $object->token = $token;
                        $object->status = InvitationService::STATUS_PENDING;
                        $object->message = $client['message'];
                        $object->added_by = auth()->user()->id;
                        $object->save();
                        $mailData = [
                            'token' => $token,
                            'url' => clientTrustvisoryUrl() . '/accept-invitation/' . $token,
                            'receiver_name' => $receiver_name,
                            'professional_id' => auth()->user()->unique_id,
                            'professional_name' => auth()->user()->first_name . ' ' . auth()->user()->last_name,
                            'professional_message' => $client['message'],
                        ];
                        $this->invitationService->sendInvitationMail($email, $mailData);
                    } catch (\Exception $e) {
                        $errors[] = "Error with email {$client['email']}: " . $e->getMessage();
                    }
                }
            }
            \DB::commit();
            $skippedInvitations = '';
            if (!empty($skipped_emails)) {
                $skippedInvitations = "Some invitations were skipped because they were already invited: " . implode(', ', $skipped_emails);
            }
            $success_contents = view("admin-panel.07-invitations.send-invitations.success-invitation", [
                'skippedInvitations' => $skippedInvitations
            ])->render();
            return response()->json([
                'status' => true,
                'message' => 'Invitation sent successfully',
                'success_contents' => $success_contents,
                'redirect_back' => baseUrl('send-invitations')
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'error_type' => 'error',
                'message' => $e->getMessage() . " Line No: " . $e->getLine()
            ]);
        }
    }

    public function deleteMultiple(Request $request)
    {
        $ids = explode(",", $request->input("ids"));
        foreach ($ids as $id) {
            $act = ReviewsInvitations::where('unique_id', $id)->first();
            if ($act) {
                ReviewsInvitations::deleteRecord($act->id);
            }
        }
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }

    public function deleteSingle($id)
    {
        $record = ReviewsInvitations::where('unique_id', $id)->first();
        
        ReviewsInvitations::deleteRecord($record->id);
        return redirect()->back()->with("success", "Record deleted successfully");
    }
}
