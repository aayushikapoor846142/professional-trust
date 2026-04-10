<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\UserBankingDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class WithdrawalRequestController extends Controller
{
    /**
     * Display a listing of withdrawal requests for the authenticated user
     */
    public function index()
    {
        $viewData = [];
        $viewData['pageTitle'] = "Withdrawal Requests";
        
        $user = auth()->user();
        $viewData['user'] = $user;
        
        // Get user's withdrawal requests
        $withdrawalRequests = WithdrawalRequest::with(['bankingDetail'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $viewData['withdrawalRequests'] = $withdrawalRequests;
        
        // Get user's active banking details
        $viewData['activeBankingDetail'] = $user->activeBankingDetail;
        $viewData['bankingDetails'] = $user->bankingDetails()->orderBy('is_active', 'desc')->get();
        
        // Calculate earnings and withdrawal amounts
        $totalEarnings = totalProfessionalEarning('all', $user->id);
        $completedWithdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');
        $pendingWithdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');
        $pendingEarnings = $totalEarnings - $completedWithdrawals;
        
        $viewData['totalEarnings'] = $totalEarnings;
        $viewData['completedWithdrawals'] = $completedWithdrawals;
        $viewData['pendingWithdrawals'] = $pendingWithdrawals;
        $viewData['pendingEarnings'] = $pendingEarnings;
        
        return view('admin-panel.withdrawal-requests.index', $viewData);
    }

    /**
     * Show the form for creating a new withdrawal request
     */
    public function create()
    {
        $viewData = [];
        $viewData['pageTitle'] = "Create Withdrawal Request";
        
        $user = auth()->user();
        
        // Check if user has at least one banking detail
        $activeBankingDetail = $user->activeBankingDetail;
        if (!$activeBankingDetail) {
            return redirect()->route('panel.withdrawal-requests.index')
                ->with('error', 'You must have at least one active banking detail to create a withdrawal request.');
        }
        
        $viewData['user'] = $user;
        $viewData['activeBankingDetail'] = $activeBankingDetail;
        $viewData['bankingDetails'] = $user->bankingDetails()->orderBy('is_active', 'desc')->get();
        
        // Calculate earnings and withdrawal amounts
        $totalEarnings = totalProfessionalEarning('all', $user->id);
        $completedWithdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');
        $pendingWithdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');
        $pendingEarnings = $totalEarnings - $completedWithdrawals;
        
        $viewData['totalEarnings'] = $totalEarnings;
        $viewData['completedWithdrawals'] = $completedWithdrawals;
        $viewData['pendingWithdrawals'] = $pendingWithdrawals;
        $viewData['pendingEarnings'] = $pendingEarnings;
        
        return view('admin-panel.withdrawal-requests.create', $viewData);
    }

    /**
     * Store a newly created withdrawal request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'banking_detail_id' => 'required|exists:user_banking_details,unique_id',
            'description' => 'nullable|string|max:1000',
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048'
        ]);

        // Get user's total earnings and calculate pending earnings
        $user = auth()->user();
        $totalEarnings = totalProfessionalEarning('all', $user->id);
        $completedWithdrawals = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');
        $pendingEarnings = $totalEarnings - $completedWithdrawals;
        
        // Add custom validation for amount vs pending earnings
        if ($request->amount > $pendingEarnings) {
            return response()->json([
                'status' => false,
                'message' => [
                    'amount' => ['Withdrawal amount cannot exceed your available earnings of $' . number_format($pendingEarnings, 2)]
                ]
            ]);
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->toArray()
            ]);
        }

        try {
            $user = auth()->user();
            
            // Debug logging
            \Log::info('Withdrawal request creation started', [
                'user_id' => $user->id,
                'request_data' => $request->all()
            ]);
            
            // Verify the banking detail belongs to the user
            $bankingDetail = UserBankingDetails::where('unique_id', $request->banking_detail_id)
                ->where('user_id', $user->id)
                ->first();
                
            if (!$bankingDetail) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid banking detail selected.'
                ]);
            }

            $data = [
                'user_id' => $user->id,
                'amount' => $request->amount,
                'banking_detail_id' => $bankingDetail->id, // Use the actual ID, not unique_id
                'description' => $request->description,
                'status' => 'pending',
                'request_date' => now()
            ];

            // Handle file upload using mediaUploadApi
            if ($request->hasFile('file_upload')) {
                $file = $request->file('file_upload');
                $extension = $file->getClientOriginalExtension() ?: 'pdf';
                $fileName = time() . '_' . $file->getClientOriginalName();
                
                // Move file to temp directory first
                $destinationPath = public_path('uploads/temp');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                if ($file->move($destinationPath, $fileName)) {
                    $sourcePath = public_path('uploads/temp/' . $fileName);
                    $mediaPath = 'withdrawal-requests';
                    
                    $response = mediaUploadApi("upload-file", $sourcePath, $mediaPath, $fileName);
                    
                    if (isset($response['status']) && $response['status'] == 'success') {
                        // Delete temp file
                        \File::delete($sourcePath);
                        $data['file_upload'] = $fileName;
                    } else {
                        // Delete temp file and return error
                        \File::delete($sourcePath);
                        return response()->json([
                            'status' => false,
                            'message' => 'Error uploading file: ' . ($response['message'] ?? 'Unknown error')
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Error moving uploaded file to temp directory'
                    ]);
                }
            }

            $withdrawalRequest = WithdrawalRequest::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Withdrawal request submitted successfully',
                'data' => $withdrawalRequest
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error creating withdrawal request: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified withdrawal request
     */
    public function show($id)
    {
        $withdrawalRequest = WithdrawalRequest::with(['bankingDetail', 'processedBy'])
            ->where('unique_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$withdrawalRequest) {
            return redirect()->route('panel.withdrawal-requests.index')
                ->with('error', 'Withdrawal request not found.');
        }

        $viewData = [];
        $viewData['pageTitle'] = "Withdrawal Request Details";
        $viewData['withdrawalRequest'] = $withdrawalRequest;
        $viewData['user'] = auth()->user();

        return view('admin-panel.withdrawal-requests.show', $viewData);
    }

    /**
     * Cancel a withdrawal request (only if status is pending)
     */
    public function cancel($id)
    {
        try {
            $withdrawalRequest = WithdrawalRequest::where('unique_id', $id)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->first();

            if (!$withdrawalRequest) {
                return response()->json([
                    'status' => false,
                    'message' => 'Withdrawal request not found or cannot be cancelled.'
                ]);
            }

            $withdrawalRequest->delete();

            return response()->json([
                'status' => true,
                'message' => 'Withdrawal request cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error cancelling withdrawal request: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download the uploaded file
     */
    public function downloadFile($id)
    {
        $withdrawalRequest = WithdrawalRequest::where('unique_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$withdrawalRequest || !$withdrawalRequest->file_upload) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Use media API to get download URL
        $downloadUrl = downloadMediaFile($withdrawalRequest->file_upload, 'withdrawal-requests');
        
        return redirect($downloadUrl);
    }

    /**
     * Get withdrawal requests for AJAX
     */
    public function getWithdrawalRequests(Request $request)
    {
        try {
            $user = auth()->user();
            $status = $request->get('status', '');
            $search = $request->get('search', '');

            $query = WithdrawalRequest::with(['bankingDetail'])
                ->where('user_id', $user->id);

            if ($status) {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('unique_id', 'LIKE', "%{$search}%")
                      ->orWhere('amount', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $withdrawalRequests = $query->orderBy('created_at', 'desc')->paginate(10);

            // Return the AJAX list view
            $contents = view('admin-panel.withdrawal-requests.ajax-list', compact('withdrawalRequests', 'user'))->render();

            return response()->json([
                'status' => true,
                'contents' => $contents,
                'total_records' => $withdrawalRequests->total(),
                'current_page' => $withdrawalRequests->currentPage(),
                'last_page' => $withdrawalRequests->lastPage(),
                'per_page' => $withdrawalRequests->perPage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error loading withdrawal requests: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show request history page
     */
    public function history()
    {
        $user = auth()->user();

        // Summary counts
        $total = WithdrawalRequest::where('user_id', $user->id)->count();
        $pending = WithdrawalRequest::where('user_id', $user->id)->where('status', 'pending')->count();
        $completed = WithdrawalRequest::where('user_id', $user->id)->where('status', 'completed')->count();

        // Initial page data so the table is not empty before AJAX
        $initialRequests = WithdrawalRequest::with(['bankingDetail'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin-panel.request-history.index', compact('user', 'total', 'pending', 'completed', 'initialRequests'));
    }

    /**
     * Ajax list for request history
     */
    public function getRequestHistory(Request $request)
    {
        try {
            $user = auth()->user();
            $status = $request->get('status', '');
            $search = $request->get('search', '');

            $query = WithdrawalRequest::with(['bankingDetail'])
                ->where('user_id', $user->id);

            if ($status !== '') {
                $query->where('status', $status);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('unique_id', 'LIKE', "%{$search}%")
                        ->orWhere('amount', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            $requests = $query->orderBy('created_at', 'desc')->paginate(10);

            $contents = view('admin-panel.request-history.ajax-list', compact('requests'))->render();

            return response()->json([
                'status' => true,
                'contents' => $contents,
                'total_records' => $requests->total(),
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error loading request history: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function downloadAdminFile($id)
    {
        $withdrawalRequest = WithdrawalRequest::where('unique_id', $id)->first();

        if (!$withdrawalRequest || !$withdrawalRequest->admin_file_upload) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Get the file URL from media server
        $fileUrl = otherFileDir() . '/' . $withdrawalRequest->admin_file_upload;
        
        // Redirect to the media server URL for download
        return redirect($fileUrl);
    }

    /**
     * Send reminder to admin for a pending withdrawal request
     */
    public function sendReminder($id, Request $request)
    {
        $user = auth()->user();

        $withdrawalRequest = WithdrawalRequest::where('unique_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$withdrawalRequest) {
            return response()->json(['status' => false, 'message' => 'Request not found.'], 404);
        }

        if ($withdrawalRequest->status !== 'pending') {
            return response()->json(['status' => false, 'message' => 'Only pending requests can be reminded.']);
        }

        // Find an admin to notify (first admin)
        $admin = \App\Models\User::where('role', 'admin')->first();
        if (!$admin) {
            return response()->json(['status' => false, 'message' => 'No admin found to notify.']);
        }
        //

        // Prepare notification via existing helper (stores ChatNotification and pushes socket)
        chatNotification([
            'user_id' => $admin->id,
            'send_by' => $user->id,
            'type' => 'withdrawal_request_reminder',
            'comment' => $user->first_name . ' ' . $user->last_name . ' sent a reminder for withdrawal request #' . $withdrawalRequest->unique_id . ' of $' . number_format($withdrawalRequest->amount, 2),
            'redirect_link' => baseUrl('/withdrawal-requests/' . $withdrawalRequest->unique_id),
            'is_read' => 0,
        ]);
        // Optional: email
        if (function_exists('sendMail')) {
            try {
                $mailData['admin_name'] = $admin->first_name . " " . $admin->last_name;
                $mailData['professional_name'] = $user->first_name . " " . $user->last_name;
                $mailData['request_id'] = $withdrawalRequest->unique_id;
                $mailData['amount'] = number_format($withdrawalRequest->amount, 2);
                $mailData['request_date'] = $withdrawalRequest->created_at->format('M d, Y');
                
                $mail_message = \View::make('emails.withdrawal_request_reminder', $mailData);

                $mailData['mail_message'] = $mail_message;
                $parameter['to'] = $admin->email;
                $parameter['to_name'] = $admin->first_name . " " . $admin->last_name;
                $parameter['message'] = $mail_message;
                $parameter['subject'] = "Reminder: Pending Withdrawal Request #" . $withdrawalRequest->unique_id;
                $parameter['view'] = "emails.withdrawal_request_reminder";
                $parameter['data'] = $mailData;
                $mailRes = sendMail($parameter);
            } catch (\Throwable $e) {
                // ignore email errors
            }
        }

        return response()->json(['status' => true, 'message' => 'Reminder sent to admin.']);
    }
} 