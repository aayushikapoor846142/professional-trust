<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CdsProfessionalCompany;
use App\Models\CompanyLocations;
use Illuminate\Support\Facades\Validator;
use App\Models\Professional;
use App\Models\User;
use View;
use DB;
use App\Models\ReviewsInvitations;
use App\Models\ReviewReplies;
use Illuminate\Support\Str;
use App\Models\Reviews;
use App\Services\FeatureCheckService;

class ReviewsController extends Controller
{
    protected $featureCheckService;

    public function __construct(FeatureCheckService $featureCheckService)
    {
        $this->featureCheckService = $featureCheckService;
    }

    /**
     * Show the review overview page.
     */
    public function reviewsOverview()
    {
        $user = auth()->user();
        // Get total, pending, and given reviews counts
        $totalReviews = \App\Models\ReviewsInvitations::visibleToUser($user->id)->count();
        $pendingReviews = \App\Models\ReviewsInvitations::visibleToUser($user->id)->where('status', 'pending')->count();
        $reviewsGiven = \App\Models\ReviewsInvitations::visibleToUser($user->id)->where('status', 'review_given')->count();
        // Get recent 5 reviews (with professional relation)
        $recentReviews = \App\Models\ReviewsInvitations::with(['professional'])
            ->visibleToUser($user->id)
            ->orderByDesc('id')
            ->limit(5)
            ->get();
        $viewData = [
            'pageTitle' => 'Review Overview',
            'totalReviews' => $totalReviews,
            'pendingReviews' => $pendingReviews,
            'reviewsGiven' => $reviewsGiven,
            'recentReviews' => $recentReviews,
        ];
        return view('admin-panel.07-invitations.review-overview', $viewData);
    }

    /**
     * Show the reviews list page.
     */
    public function getReviews()
    {
        $user = auth()->user();
        $reviewFeatureStatus = $this->featureCheckService->canAddReviewNew($user->id);
        
        $viewData['pageTitle'] = "Reviews";
        $viewData['reviewFeatureStatus'] = $reviewFeatureStatus;
        $viewData['canAddReview'] = $reviewFeatureStatus['allowed'];
        $viewData['reviewStatus'] = Reviews::distinct()->pluck('status');
        return view('admin-panel.07-invitations.reviews.lists', $viewData);
    }

    /**
     * AJAX: Get paginated reviews with search and role-based filtering.
     */
    public function getReviewsAjax(Request $request)
    {
       
        $user = auth()->user();
        $search = $request->search;
        $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');
        $status = $request->input('status');
        $reviewGiven = $request->input('reviewGiven');
        // Get review feature status first
        $reviewFeatureStatus = $this->featureCheckService->canAddReviewNew($user->id);
        
        $records = Reviews::with('user:id,first_name,email')
               ->where('is_spam', 0)
            ->when($search, function ($query) use ($search) {
                $query->where("review", "LIKE", "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")
                          ->orWhere('email', 'LIKE', "%$search%");
                    });
            })
            ->when($user->role == 'professional', fn($q) => $q->where('professional_id', $user->id))
            ->when($user->role == 'admin', fn($q) => $q)
            ->when($user->role == 'client', fn($q) => $q->where('added_by', $user->id))
            ->when($sortColumn === 'professional_id', function ($q) use ($sortDirection) {
                $q->orderBy(
                    User::select('email')
                        ->whereColumn('users.id', 'reviews.professional_id'),
                    $sortDirection
                );
            }, function ($q) use ($sortColumn, $sortDirection) {
                $q->orderBy($sortColumn, $sortDirection);
            });
            
        // Apply feature limit filtering based on reviewFeatureStatus configuration
        if (!$reviewFeatureStatus['allowed']) {
            // If not allowed, show no reviews or limited reviews based on plan
            if ($reviewFeatureStatus['limit'] == 0) {
                $records = $records->where('id', 0); // Show no reviews
            } else {
                // Show only up to the limit
                $records = $records->limit($reviewFeatureStatus['limit']);
            }
        } else if ($reviewFeatureStatus['limit'] != 'unlimited') {
            // If limited but allowed, show only up to the limit
            $records = $records->limit($reviewFeatureStatus['limit']);
        }
        // If limit is 'unlimited', no additional filtering needed
        
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $records->whereIn('status', $statuses);
        }


        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if ($startDate && $endDate) {
            $records->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        } elseif ($startDate && !$endDate) {
            $records->whereDate('created_at', '>=', $startDate);
        } elseif (!$startDate && $endDate) {
            $records->whereDate('created_at', '<=', $endDate);
        }
        
        $rating = $request->input('rating');

        if($rating != ''){
            $records->where('rating', $rating);
        }

        // Use the feature limit for pagination instead of hardcoded 10
        $paginationLimit = $reviewFeatureStatus['limit'] != 'unlimited' ? $reviewFeatureStatus['limit'] : 10;
        \Log::info($paginationLimit);
        $records = $records->paginate($paginationLimit);
        
        // Apply feature limit to counts
        $countsQuery = ReviewsInvitations::where('added_by', $user->id);
        $reviewsGivenQuery = Reviews::where('professional_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])->where('is_spam', 0);;
          $spamCountQuery = Reviews::where('professional_id', $user->id)
    ->where('is_spam', 1)->count();  
        if (!$reviewFeatureStatus['allowed']) {
            if ($reviewFeatureStatus['limit'] == 0) {
                $counts = (object)['total' => 0, 'pending' => 0];
                $reviewsGiven = 0;
            } else {
                $counts = $countsQuery->limit($reviewFeatureStatus['limit'])
                    ->selectRaw('count(*) as total, sum(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending')
                    ->first();
                $reviewsGiven = $reviewsGivenQuery->limit($reviewFeatureStatus['limit'])->count();
            }
        } else if ($reviewFeatureStatus['limit'] != 'unlimited') {
            $counts = $countsQuery->limit($reviewFeatureStatus['limit'])
                ->selectRaw('count(*) as total, sum(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending')
                ->first();
            $reviewsGiven = $reviewsGivenQuery->limit($reviewFeatureStatus['limit'])->count();
        } else {
            $counts = $countsQuery
                ->selectRaw('count(*) as total, sum(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending')
                ->first();
            $reviewsGiven = $reviewsGivenQuery->count();
        }
       
        $viewData['records'] = $records;
        $viewData['reviewFeatureStatus'] = $reviewFeatureStatus;
        $viewData['canAddReview'] = $reviewFeatureStatus['allowed'];
        $viewData['reviewGiven'] = $reviewGiven;
        $view = View::make('admin-panel.07-invitations.reviews.ajax-list', $viewData);
        $contents = $view->render();

        // Set pagination parameters based on limit
        if ($reviewFeatureStatus['limit'] != 'unlimited') {
            $response = [
                'contents' => $contents,
                'last_page' => 1, // Only one page when limited
                'current_page' => 1,
                'total_records' => $reviewFeatureStatus['limit'], // Show limit as total
                'total' => $counts->total ?? 0,
                'pending' => $counts->pending ?? 0,
                'reviewsGiven' => $reviewsGiven,
                'reviewFeatureStatus' => $reviewFeatureStatus,
                'spamCount' => $spamCountQuery
            ];
        } else {
            $response = [
                'contents' => $contents,
                'last_page' => $records->lastPage(),
                'current_page' => $records->currentPage(),
                'total_records' => $records->total(),
                'total' => $counts->total ?? 0,
                'pending' => $counts->pending ?? 0,
                'reviewsGiven' => $reviewsGiven,
                'reviewFeatureStatus' => $reviewFeatureStatus,
                'spamCount' => $spamCountQuery
            ];
        }
      

        return response()->json($response);
    }

    /**
     * Show the edit review page.
     */
    public function editReview($uid)
    {
        $viewData['record'] = Reviews::where("unique_id", $uid)->firstOrFail();
        $viewData['pageTitle'] = "Edit Review";
        return view('admin-panel.07-invitations.reviews.edit', $viewData);
    }

    /**
     * Delete a review reply by unique_id.
     */
    public function replyDelete($unique_id)
    {
        ReviewReplies::where('unique_id', $unique_id)->delete();
        return redirect()->back()->with("success", "Record deleted successfully");
    }

    /**
     * Delete a review by unique_id.
     */
    public function deleteReviews($id)
    {
        $action = Reviews::where('unique_id', $id)->firstOrFail();
        Reviews::deleteRecord($action->id);
        return redirect()->back()->with("success", "Record deleted Successfully");
    }

    /**
     * Approve a review by unique_id.
     */
    public function approveReviews($id)
    {
        $record = Reviews::where('unique_id', $id)->firstOrFail();
        $record->status = 'approved';
        $record->save();
        return redirect()->back()->with("success", "status updated to Approved.");
    }

    /**
     * Update a review by unique_id (AJAX).
     */
    public function updateReview($uid, Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'rating' => 'required',
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

            $object = Reviews::where('unique_id', $uid)->firstOrFail();
            
            // Add authorization check
           
            
            $object->rating = $request->input("rating");
            $object->review = $request->input("review");
            $object->edited = 1;
            $object->save();

            DB::commit();

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('review-received');
            $response['message'] = "Record updated successfully";

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => 'Failed to update the detail']);
        }
    }

    /**
     * Save a reply to a review.
     */
    public function saveReply($review_unique_id, Request $request)
    {
        DB::beginTransaction();
        try {
            $get_review_id = Reviews::where('unique_id', $review_unique_id)->firstOrFail();
            $saveReply = new ReviewReplies;
            $saveReply->professional_id = auth()->user()->id;
            $saveReply->reply = $request->reply;
            $saveReply->unique_id = randomNumber();
            $saveReply->review_id = $get_review_id->id;
            $saveReply->save();
            DB::commit();
            return back()->with('success', 'Reply Sent Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update a reply to a review.
     */
    public function updateReply($review_uniq_id, Request $request)
    {
        DB::beginTransaction();
        try {
            $saveReply = ReviewReplies::where('unique_id', $review_uniq_id)->firstOrFail();
            $saveReply->reply = $request->reply;
            $saveReply->edited = 1;
            $saveReply->save();
            DB::commit();
            return back()->with('success', 'Reply Updated Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the send invite page.
     */
    public function sendInviteNew()
    {
        $viewData['pageTitle'] = "Send Invites";
        return view('admin-panel.07-invitations.send-invitations.add', $viewData);
    }

    /**
     * Send review invitations via single email or CSV upload (AJAX).
     */
    public function sendInviteCSV(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'single_email' => 'nullable|email',
            'csv_file' => $request->has('single_email') ? 'nullable' : 'required|mimes:csv,txt|max:2048',
            'template_subject' => 'required',
            'template_content' => 'required'
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

        $invitationsSent = 0;
        $totalInvitesSent = ReviewsInvitations::where('added_by', $user->id)->count();
        $invitationsLeft = 50 - $totalInvitesSent;

        if ($totalInvitesSent >= 50) {
            return response()->json([
                'status' => false,
                'message' => 'You have reached the maximum limit of 50 invitations.',
                'redirect_back' => baseUrl('reviews/send-invitation-email/add')
            ]);
        }

        // Single email processing
        if ($request->has('single_email') && $request->single_email != '') {
            $email = $request->input('single_email');
            $checkinvite = ReviewsInvitations::where('email', $email)
                ->where('added_by', $user->id)->first();

            if (!$checkinvite && $invitationsLeft > 0) {
                $userModel = User::where('email', $email)->first();
                if (!$userModel || ($userModel && $userModel->role == 'client')) {
                    $this->sendInviteMail($email, $request->input("template_content"), $request->input("template_subject"));
                    $invitationsSent++;
                    $invitationsLeft--;

                    return response()->json([
                        'status' => true,
                        'message' => "Invitation sent successfully to $email.",
                        'redirect_back' => baseUrl('reviews/send-invitation-email/add')
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'This email is already registered with a non-client role.',
                        'redirect_back' => baseUrl('reviews/send-invitation-email/add')
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'This email has already been invited or you have reached your limit.',
                    'redirect_back' => baseUrl('reviews/send-invitation-email/add')
                ]);
            }
        }

        // CSV file processing
        if ($file = $request->file('csv_file')) {
            $emails = [];
            if (($handle = fopen($file, 'r')) !== false) {
                $row = fgetcsv($handle); // Skip header
                while (($row = fgetcsv($handle)) !== false) {
                    foreach ($row as $value) {
                        if ($invitationsLeft < 1) break;
                        $email = $value ?? null;
                        $validator = Validator::make(['email' => $email], ['email' => 'required|email']);
                        if ($validator->fails()) continue;
                        $emails[] = $email;
                    }
                }
                fclose($handle);
            }
            // Batch check for existing invites and users
            $existingInvites = ReviewsInvitations::where('added_by', $user->id)
                ->whereIn('email', $emails)
                ->pluck('email')->toArray();
            $existingUsers = User::whereIn('email', $emails)->pluck('role', 'email')->toArray();
            foreach ($emails as $email) {
                if ($invitationsLeft < 1) break;
                if (in_array($email, $existingInvites)) continue;
                if (isset($existingUsers[$email]) && $existingUsers[$email] != 'client') continue;
                $this->sendInviteMail($email, $request->input("template_content"), $request->input("template_subject"));
                $invitationsSent++;
                $invitationsLeft--;
            }
            return response()->json([
                'status' => true,
                'message' => "$invitationsSent invitations were successfully sent.",
                'redirect_back' => baseUrl('reviews/send-invitation-email/add')
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => "No file or email was provided.",
            'redirect_back' => baseUrl('reviews/send-invitation-email/add')
        ]);
    }

    /**
     * Send review invitations to multiple emails.
     */
    public function sendInvitation(Request $request)
    {
        $user = auth()->user();
        if (count($request->emails) > 0) {
            $request->validate([
                'emails' => 'required|array',
                'emails.*' => 'email',
            ], [
                'emails.*.email' => 'Each email must be a valid email address.',
            ]);
            $emails = $request->emails;
            $existingInvites = ReviewsInvitations::where('added_by', $user->id)
                ->whereIn('email', $emails)
                ->pluck('email')->toArray();
            $existingUsers = User::whereIn('email', $emails)->pluck('role', 'email')->toArray();
            foreach ($emails as $email) {
                if ($email == NULL) continue;
                if (in_array($email, $existingInvites)) continue;
                if (isset($existingUsers[$email]) && $existingUsers[$email] != 'client') continue;
                $token = Str::random(64);
                $getemail = User::where('email', $email)->first();
                $review = new ReviewsInvitations;
                $review->user_id = $getemail ? $getemail->id : 0;
                $review->added_by = $user->id;
                $review->token = $token;
                $review->email = $email;
                $review->status = "pending";
                $review->save();
                $professional_name = $user->first_name . " " . $user->last_name;
                $mailData = ['token' => $token, 'professional_name' => $professional_name];
                $view = \View::make('emails.review_invitations', $mailData);
                $message = $view->render();
                $parameter = [
                    'to' => $email,
                    'to_name' => $email,
                    'message' => $message,
                    'subject' => 'Invitation for Review',
                    'view' => 'emails.review_invitations',
                    'data' => $mailData,
                ];
                sendMail($parameter);
            }
            return redirect(baseUrl('reviews/send-invitations'))->with('success', 'Invitation Sent Successfully');
        }
        $viewData['pageTitle'] = "Send Invitations";
        return view('admin-panel.07-invitations.send-invitations.lists', $viewData);
    }

    /**
     * Show the send invitations list page.
     */
    public function sendInvitations()
    {
        $user = auth()->user();
        $viewData['status'] = "Pending";
        $viewData['pageTitle'] = "Send Invitations";
        $reviewFeatureStatus = $this->featureCheckService->canAddReviewNew($user->id);
        
       
        $viewData['reviewFeatureStatus'] = $reviewFeatureStatus;
        $viewData['canAddReview'] = $reviewFeatureStatus['allowed'];
        $viewData['sendInvitationStatus'] = ReviewsInvitations::distinct()->pluck('status');
        return view('admin-panel.07-invitations.send-invitations.lists', $viewData);
    }

    /**
     * AJAX: Get paginated invitations with search and counts.
     */
    public function invitationsAjaxList(Request $request)
    {
        $user = auth()->user();
        $search = $request->search;
        $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');

        $status = $request->input('status');
        $records = ReviewsInvitations::with('review')
            ->when(!empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%")
                        ->orWhere('receiver_name', 'like', "%{$search}%");
                });
            })
             ->orderBy($sortColumn, $sortDirection)
            ->visibleToUser($user->id);

        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $records->whereIn('status', $statuses);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if ($startDate && $endDate) {
            $records->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        } elseif ($startDate && !$endDate) {
            $records->whereDate('created_at', '>=', $startDate);
        } elseif (!$startDate && $endDate) {
            $records->whereDate('created_at', '<=', $endDate);
        }
        
        $rating = $request->input('rating');

        if($rating != ''){
            $records->whereHas('review', function($query) use ($rating) {
                $query->where('rating', $rating);
            });
        }

        $records = $records->paginate(10);
         
        $viewData['records'] = $records;
        $total = ReviewsInvitations::visibleToUser($user->id)->count();
        $pending = ReviewsInvitations::visibleToUser($user->id)->where('status', 'pending')->count();
        $reviewsGiven = ReviewsInvitations::visibleToUser($user->id)->where('status', 'review_given')->count();
        $reviewsAccepted = ReviewsInvitations::visibleToUser($user->id)->where('status', 'accepted')->count();

        $view = View::make('admin-panel.07-invitations.send-invitations.ajax-list', $viewData);
        $contents = $view->render();
        $response = [
            'contents' => $contents,
            'last_page' => $records->lastPage(),
            'current_page' => $records->currentPage(),
            'total_records' => $records->total(),
            'total' => $total,
            'pending' => $pending,
            'reviewsGiven' => $reviewsGiven,
            'reviewsAccepted' => $reviewsAccepted

        ];
        return response()->json($response);
    }

    /**
     * Show the review invitations list page for the user.
     */
    public function reviewsRequests()
    {
        $viewData['pageTitle'] = "Review Invitations";
        return view('user.review_invitations.lists', $viewData);
    }

    /**
     * AJAX: Get paginated review invitations for the user.
     */
    public function reviewsRequestAjaxList(Request $request)
    {
        $user = auth()->user();
        $search = $request->input("search");
        $records = ReviewsInvitations::with('professional:id,first_name,last_name')
            ->when($search, function ($query) use ($search) {
                $query->where("email", "LIKE", "%" . $search . "%");
            })
            ->where('email', $user->email)
            ->orderBy('id', "desc")
            ->paginate();
        $viewData['records'] = $records;
        $view = View::make('user.review_invitations.ajax-list', $viewData);
        $contents = $view->render();
        $response = [
            'contents' => $contents,
            'last_page' => $records->lastPage(),
            'current_page' => $records->currentPage(),
            'total_records' => $records->total(),
        ];
        return response()->json($response);
    }

    /**
     * AJAX: Delete multiple reviews by unique_id.
     */
    public function deleteMultipleReview(Request $request)
    {
        $ids = explode(",", $request->input("ids"));
        $reviewIds = Reviews::whereIn('unique_id', $ids)->pluck('id');
        Reviews::deleteRecord($reviewIds->toArray());
        $response['status'] = true;
        \Session::flash('success', 'Records deleted successfully');
        return response()->json($response);
    }

    public function checkReview($id)
    {
         try {
        $viewData['pageTitle'] = "Report Spam Review";
         $viewData['record'] = Reviews::where('unique_id', $id)->firstOrFail();
        $view = View::make('admin-panel.07-invitations.reviews.modals.add',$viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
        } catch (\Exception $e) {

            return response()->json(['status' => false, 'message' => 'Error loading form']);
        }

    }

    /**
     * Update a review by unique_id (AJAX).
     */
    public function spamSubmitReview($uid, Request $request)
    {
        DB::beginTransaction();
    try {
        $validator = Validator::make($request->all(), [
            'spam_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $review = Reviews::where('unique_id', $uid)->firstOrFail();
        $review->is_spam = '1';
        $review->spam_reason = $request->input('spam_reason');
        $review->spam_status = 'spam';
        $review->save();

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'Review Reported Successfully ',
            'redirect_back' => baseUrl('review-received')
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong. Please try again.'
        ]);
    }
    }

     /**
     * Show the reviews list page.
     */
    public function getSpamReviews()
    {
        $user = auth()->user();
        $reviewFeatureStatus = $this->featureCheckService->canAddReviewNew($user->id);
        
        $viewData['pageTitle'] = "Spam Reviews";
        $viewData['reviewFeatureStatus'] = $reviewFeatureStatus;
        $viewData['canAddReview'] = $reviewFeatureStatus['allowed'];
        return view('admin-panel.07-invitations.reviews.spam-lists', $viewData);
    }

    /**
     * AJAX: Get paginated reviews with search and role-based filtering.
     */
    public function getSpamReviewsAjax(Request $request)
    {
       
        $user = auth()->user();
        $search = $request->search;
        $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');

        // Get review feature status first
        $reviewFeatureStatus = $this->featureCheckService->canAddReviewNew($user->id);
        
        $records = Reviews::with('user:id,first_name,email')
        ->where('is_spam', 1)
            ->when($search, function ($query) use ($search) {
                $query->where("review", "LIKE", "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%$search%")
                          ->orWhere('email', 'LIKE', "%$search%");
                    });
            })
            ->when($user->role == 'professional', fn($q) => $q->where('professional_id', $user->id))
            ->when($user->role == 'admin', fn($q) => $q)
            ->when($user->role == 'client', fn($q) => $q->where('added_by', $user->id))
            ->when($sortColumn === 'professional_id', function ($q) use ($sortDirection) {
                $q->orderBy(
                    User::select('email')
                        ->whereColumn('users.id', 'reviews.professional_id'),
                    $sortDirection
                );
            }, function ($q) use ($sortColumn, $sortDirection) {
                $q->orderBy($sortColumn, $sortDirection);
            });
            
        // Apply feature limit filtering based on reviewFeatureStatus configuration
        if (!$reviewFeatureStatus['allowed']) {
            // If not allowed, show no reviews or limited reviews based on plan
            if ($reviewFeatureStatus['limit'] == 0) {
                $records = $records->where('id', 0); // Show no reviews
            } else {
                // Show only up to the limit
                $records = $records->limit($reviewFeatureStatus['limit']);
            }
        } else if ($reviewFeatureStatus['limit'] != 'unlimited') {
            // If limited but allowed, show only up to the limit
            $records = $records->limit($reviewFeatureStatus['limit']);
        }
        // If limit is 'unlimited', no additional filtering needed
        
        // Use the feature limit for pagination instead of hardcoded 10
        $paginationLimit = $reviewFeatureStatus['limit'] != 'unlimited' ? $reviewFeatureStatus['limit'] : 10;
        \Log::info($paginationLimit);
        $records = $records->paginate($paginationLimit);
        
        // Apply feature limit to counts
        $countsQuery = ReviewsInvitations::where('added_by', $user->id);
         $reviewsGivenQuery = Reviews::where('professional_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])->where('is_spam', 0);
          $spamCountQuery = Reviews::where('professional_id', $user->id)
    ->where('is_spam', 1)->count();  
            
        if (!$reviewFeatureStatus['allowed']) {
            if ($reviewFeatureStatus['limit'] == 0) {
                $counts = (object)['total' => 0, 'pending' => 0];
                $reviewsGiven = 0;
            } else {
                $counts = $countsQuery->limit($reviewFeatureStatus['limit'])
                    ->selectRaw('count(*) as total, sum(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending')
                    ->first();
                $reviewsGiven = $reviewsGivenQuery->limit($reviewFeatureStatus['limit'])->count();
            }
        } else if ($reviewFeatureStatus['limit'] != 'unlimited') {
            $counts = $countsQuery->limit($reviewFeatureStatus['limit'])
                ->selectRaw('count(*) as total, sum(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending')
                ->first();
            $reviewsGiven = $reviewsGivenQuery->limit($reviewFeatureStatus['limit'])->count();
        } else {
            $counts = $countsQuery
                ->selectRaw('count(*) as total, sum(CASE WHEN status = \'pending\' THEN 1 ELSE 0 END) as pending')
                ->first();
            $reviewsGiven = $reviewsGivenQuery->count();
        }
       
        $viewData['records'] = $records;
        $viewData['reviewFeatureStatus'] = $reviewFeatureStatus;
        $viewData['canAddReview'] = $reviewFeatureStatus['allowed'];
        $view = View::make('admin-panel.07-invitations.reviews.spam-ajax-list', $viewData);
        $contents = $view->render();

        // Set pagination parameters based on limit
        if ($reviewFeatureStatus['limit'] != 'unlimited') {
            $response = [
                'contents' => $contents,
                'last_page' => 1, // Only one page when limited
                'current_page' => 1,
                'total_records' => $reviewFeatureStatus['limit'], // Show limit as total
                'total' => $counts->total ?? 0,
                'pending' => $counts->pending ?? 0,
                'reviewsGiven' => $reviewsGiven,
                'reviewFeatureStatus' => $reviewFeatureStatus,
                'spamCount' => $spamCountQuery,
            ];
        } else {
            $response = [
                'contents' => $contents,
                'last_page' => $records->lastPage(),
                'current_page' => $records->currentPage(),
                'total_records' => $records->total(),
                'total' => $counts->total ?? 0,
                'pending' => $counts->pending ?? 0,
                'reviewsGiven' => $reviewsGiven,
                'reviewFeatureStatus' => $reviewFeatureStatus,
                'spamCount' => $spamCountQuery,
            ];
        }
      

        return response()->json($response);
    }

    

}
