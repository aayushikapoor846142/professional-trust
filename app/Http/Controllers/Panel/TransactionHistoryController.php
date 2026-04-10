<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use View;
use App\Models\Invoice;
use App\Models\SupportByUser;
use Stripe\Stripe;

use Illuminate\Support\Facades\Log;
use App\Models\UserSubscriptionHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionHistoryController extends Controller
{
    private const PER_PAGE = 10;
    private const DEFAULT_SORT_ORDER = 'desc';
    
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    /**
     * Display the list of Action.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userId = Auth::user()->id;
        
        $subscriptionStatuses = $this->getSubscriptionStatuses($userId);
        $oneTimetatuses = $this->getOneTimeStatuses($userId);

        $viewData['subscriptionStatuses'] = $subscriptionStatuses;
        $viewData['oneTimetatuses'] = $oneTimetatuses;
        $viewData['pageTitle'] = "Amount Contributed";
         $viewData['paymentStatus'] = $statuses = Invoice::distinct()->pluck('payment_status');
        return view('admin-panel.09-utilities.transactions.history.lists', $viewData);
    }

    /**
     * Get the list of Support Payment with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function overview()
    {
        $userId = Auth::user()->id;
        
        // Get transaction statistics
        $statistics = $this->getTransactionStatistics($userId);
        
        // Get chart data
        $chartData = $this->getChartData($userId);
        
        // Get recent transactions
        $recentTransactions = $this->getRecentTransactions($userId);
        
        $viewData = [
            'pageTitle' => "Transaction Overview",
            'totalRevenue' => $statistics['totalRevenue'],
            'revenueGrowth' => $statistics['revenueGrowth'],
            'totalTransactions' => $statistics['totalTransactions'],
            'transactionGrowth' => $statistics['transactionGrowth'],
            'activeSubscriptions' => $statistics['activeSubscriptions'],
            'subscriptionGrowth' => $statistics['subscriptionGrowth'],
            'pendingTransactions' => $statistics['pendingTransactions'],
            'pendingChange' => $statistics['pendingChange'],
            'completedTransactions' => $statistics['completedTransactions'],
            'failedTransactions' => $statistics['failedTransactions'],
            'refundedTransactions' => $statistics['refundedTransactions'],
            'chartData' => $chartData,
            'recentTransactions' => $recentTransactions
        ];
        
        return view("admin-panel.09-utilities.transactions.transaction-overview", $viewData);
    }

    public function getAjaxList(Request $request)
    {
        $search = $request->input("search");
        $status = $request->input('status', 'all');
      
        $onetime_sort_by_column = $request->onetime_sort_by_column;
        $onetime_sort_order = $request->onetime_sort_order;

            $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');

        $hasPriceRanges = $request->filled('price_range');
        // \Log::info($request);
        $minRange = $request->filled('min_range') && $request->input('min_range') !== '' ? $request->input('min_range') : null;
        $maxRange = $request->filled('max_range') && $request->input('max_range') !== '' ? $request->input('max_range') : null;
        $hasSlider = $minRange !== null || $maxRange !== null;

        $priceRanges = $hasPriceRanges ? (is_array($request->price_range) ? $request->price_range : [$request->price_range]) : [];
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $hourRanges = $request->filled('hour_range') ? (is_array($request->hour_range) ? $request->hour_range : [$request->hour_range]) : [];

        $query = $this->buildOneTimeQuery(
            $search,
            $status,
            $hasPriceRanges,
            $minRange,
            $maxRange,
            $hasSlider,
            $priceRanges,
            $startDate,
            $endDate,
            $hourRanges
        );

    $query = $this->applySorting($query, $sortColumn, $sortDirection, 'one_time');

    $records = $query->paginate(self::PER_PAGE);

        return $this->buildAjaxResponse($records, 'admin-panel.09-utilities.transactions.history.ajax-list');
    }

    public function getMonthlyAjaxList(Request $request)
    {
        $search = $request->input("search");
        $status = $request->input('status', 'all');
        $sort_by_column = $request->sort_by_column;
        $sort_order = $request->sort_order;
        $filter_from_date = $request->pass_from_dates;
        $filter_to_date = $request->pass_to_dates;

        $records = $this->buildMonthlyQuery($search, $status, $filter_from_date, $filter_to_date)
            ->when($sort_by_column, function ($query) use ($sort_by_column, $sort_order) {
                return $this->applySorting($query, $sort_by_column, $sort_order, 'monthly');
            })
            ->paginate(self::PER_PAGE);

        return $this->buildAjaxResponse($records, 'admin-panel.09-utilities.transactions.history.monthly-ajax-list');
    }

    public function view($uid)
    {
        $user = auth()->user();
        $records = SupportByUser::where('unique_id', $uid)->first();
        
        if (!$records) {
            return redirect()->back()->with('error', 'Record not found.');
        }
        
        $userSubscriptionHistory = $this->getUserSubscriptionHistory($user->id);
        $stripeData = $this->getStripeData($user, $records);
        
        $viewData['nextInvoiceData'] = $stripeData['nextInvoiceData'];
        $viewData['subscriptionHistory'] = $stripeData['subscriptionHistory'];
        $viewData['record'] = $records;
        $viewData['pageTitle'] = "View Details";
       
        return view('admin-panel.09-utilities.transactions.history.subscription-details', $viewData);
    }
    
    public function viewOneTime($uid)
    {
        $record = SupportByUser::where("unique_id", $uid)->first();
        
        if (!$record) {
            return redirect()->back()->with('error', 'Record not found.');
        }

        $viewData['record'] = $record;
        $viewData['pageTitle'] = "View Details";
       
        return view('admin-panel.09-utilities.transactions.history.view', $viewData);
    }
    
    public function quickViewOneTime($uid)
    {
        $record = SupportByUser::where("unique_id", $uid)->first();
        
        if (!$record) {
            return response()->json(['status' => false, 'message' => 'Record not found.']);
        }
        
        $viewData['pageTitle'] = "View Details";
        $viewData['record'] = $record;
        
        $view = View::make('admin-panel.09-utilities.transactions.history.quick-onetime-view', $viewData);
        $contents = $view->render();
        
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function quickViewMonthly($uid)
    {
        $user = auth()->user();
        $records = SupportByUser::where('unique_id', $uid)->first();
        
        if (!$records) {
            return response()->json(['status' => false, 'message' => 'Record not found.']);
        }
        
        $userSubscriptionHistory = $this->getUserSubscriptionHistory($user->id);
        $stripeData = $this->getStripeData($user, $records);
        
        $viewData['nextInvoiceData'] = $stripeData['nextInvoiceData'];
        $viewData['subscriptionHistory'] = $stripeData['subscriptionHistory'];
        $viewData['record'] = $records;
        $viewData['pageTitle'] = "View Details";
        
        $view = View::make('admin-panel.09-utilities.transactions.history.quick-monthly-view', $viewData);
        $contents = $view->render();
        $response['status'] = true;
        $response['contents'] = $contents;
        return response()->json($response);
    }

    public function cancelSubscription(Request $request, $id)
    {
        $stripe = $this->getStripeClient();
        
        try {
            $subscription = $stripe->subscriptions->retrieve($id);
            $supportRecord = SupportByUser::where("subscription_id", $id)->first();
            
            if ($subscription->status === 'canceled') {
                if ($supportRecord && $supportRecord->userSubscriptionHistory) {
                    $supportRecord->userSubscriptionHistory->update(['subscription_status' => 'cancelled']);
                }
                return redirect()->back()->with('error', 'Subscription canceled successfully.');
            }
    
            $cancelSubscription = $stripe->subscriptions->cancel($id, []);
            if ($cancelSubscription->status === 'canceled' && $supportRecord && $supportRecord->userSubscriptionHistory) {
                $supportRecord->userSubscriptionHistory->update(['subscription_status' => 'cancelled']);
                return redirect()->back()->with('success', 'Subscription canceled successfully.');
            } else {
                return redirect()->back()->with('error', 'Failed to cancel subscription.');
            }
    
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Stripe Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Invalid subscription ID or already canceled.');
        }
    }

    public function addCardDetails(Request $request, $id)
    {
        $user = auth()->user();
        Stripe::setApiKey(apiKeys('STRIPE_SECRET'));

        $viewData['intent'] = \Stripe\SetupIntent::create([
            'customer' => $user->stripe_id,
        ]);

        $viewData['record'] = SupportByUser::where('unique_id', $id)->first();
        $viewData['user'] = $user;
        $viewData['countries'] = Country::get();
        $viewData['pageTitle'] = "Add Card Details";
        return view('admin-panel.09-utilities.transactions.history.cards.add', $viewData);
    }

    public function saveCardDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cardholder' => 'required|max:255',
        ], [
            'cardholder.required' => 'The name on the card is required.',
            'cardholder.max' => 'The name on the card must not exceed 255 characters.',
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
     
        try {
            $stripe = $this->getStripeClient();
            $paymentMethod = $stripe->paymentMethods->retrieve($request->payment_method, []);
 
            $stripe->customers->update(
                $request->customer_id,
                params: [
                    'invoice_settings' => [
                        'default_payment_method' => $request->payment_method,
                    ],
                ]
            );
            
            $stripe->paymentMethods->update(
                $paymentMethod->id, 
                [
                    'billing_details' => [
                        'address' => [
                            'line1' => $request->address ?? null,
                            'city' => $request->city ?? null,
                            'state' => $request->state ?? null,
                            'postal_code' => $request->pincode ?? null,
                            'country' => $request->country ?? null,
                        ],
                    ]
                ]
            );

            $response['status'] = true;
            $response['redirect_back'] = baseUrl('payment-methods/cards');
            $response['message'] = "Record updated successfully";
 
            return response()->json($response);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(["error" => "Stripe Error: " . $e->getMessage()]);
        }
    }

    public function userCardList()
    {
        $user = auth()->user();
        $viewData['record'] = SupportByUser::where('user_id', $user->id)->first();
        $viewData['pageTitle'] = "Payment Method List";
        $viewData['user'] = $user;
        return view('admin-panel.09-utilities.transactions.history.cards.lists', $viewData);
    }

    public function useCardAjaxList(Request $request)
    {
        $user = auth()->user();
        $stripe = $this->getStripeClient();
        
        try {
            $customer = $stripe->customers->retrieve($user->stripe_id);
            $defaultPaymentMethod = $stripe->paymentMethods->retrieve(
                $customer->invoice_settings->default_payment_method
            );
        } catch (\Exception $e) {
            Log::error('Stripe Error: ' . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to retrieve payment methods.']);
        }
   
        $perPage = self::PER_PAGE; 
        $page = request('page', 1);
        $offset = ($page - 1) * $perPage;

        $allPaymentMethods = $stripe->paymentMethods->all([
            'customer' => $user->stripe_id,
            'type' => 'card'
        ]);
      
        $uniqueMethods = $this->getUniquePaymentMethods($allPaymentMethods->data);
        
        $totalRecords = count($uniqueMethods);
        $lastPage = ceil($totalRecords / $perPage);
        $records = array_slice($uniqueMethods, $offset, $perPage);

        $viewData['records'] = $records;
        $viewData['defaultPaymentMethod'] = $defaultPaymentMethod;
        $view = View::make('admin-panel.09-utilities.transactions.history.cards.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['current_page'] = $page;
        $response['last_page'] = $lastPage;
        $response['total_records'] = $totalRecords;
        return response()->json($response);
    }

    public function removeCardDetails($id)
    {
        try {
            $stripe = $this->getStripeClient();
            $stripe->paymentMethods->detach($id);
            return redirect()->back()->with("success", "Payment Method Removed successfully");
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(["error" => "Stripe Error: " . $e->getMessage()]);
        }
    }

    public function makeDefaultCard($id)
    {
        try {
            $user = auth()->user();
            $stripe = $this->getStripeClient();

            $paymentMethod = $stripe->paymentMethods->retrieve($id, []);
            $paymentMethod->attach(['customer' => $user->stripe_id]);

            $stripe->customers->update(
                $user->stripe_id,
                params: [
                    'invoice_settings' => [
                        'default_payment_method' => $id,
                    ],
                ]
            );
            return redirect()->back()->with("success", "Default Payment Method Set successfully");
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(["error" => "Stripe Error: " . $e->getMessage()]);
        }
    }

    // Private helper methods

    /**
     * Get subscription statuses for a user
     */
    private function getSubscriptionStatuses($userId)
    {
        return SupportByUser::with(['userSubscriptionHistory' => function ($query) {
                $query->select('id', 'stripe_subscription_id', 'subscription_status');
            }])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get()
            ->pluck('userSubscriptionHistory.subscription_status') 
            ->filter()        
            ->unique()       
            ->values();
    }

    /**
     * Get one-time payment statuses for a user
     */
    private function getOneTimeStatuses($userId)
    {
        return SupportByUser::with(['invoice' => function ($query) {
                $query->select('id', 'reference_id', 'payment_status');
            }])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get()
            ->pluck('invoice.payment_status') 
            ->filter()        
            ->unique()       
            ->values();
    }

    /**
     * Build query for one-time payments
     */
    private function buildOneTimeQuery(
        $search,
        $status,
        $hasPriceRanges,
        $minRange,
        $maxRange,
        $hasSlider,
        $priceRanges = [],
        $startDate = null,
        $endDate = null,
        $hourRanges = []
    )
    {
        $query = SupportByUser::where('user_id', Auth::user()->id)
            ->where('payment_type', 'One Time')
            ->where(function ($query) use ($search) {
                if ($search != '') {
                    $query->where('amount', 'LIKE', "%{$search}%")
                          ->orWhere('tax', 'LIKE', "%{$search}%")
                          ->orWhere('total_amount', 'LIKE', "%{$search}%");
                }
            })
            ->when($status !== 'all', function ($query) use ($status) {
                $query->whereHas('invoice', function ($q) use ($status) {
                    $q->where('payment_status', $status);
                });
            });

        if ($hasPriceRanges || $hasSlider) {
            $ranges = $hasPriceRanges ? (is_array($priceRanges) ? $priceRanges : [$priceRanges]) : [];
            $query->where(function ($q) use ($ranges, $minRange, $maxRange) {
                foreach ($ranges as $range) {
                    switch ($range) {
                        case 'under-100':
                            $q->orWhere('total_amount', '<', 100);
                            break;
                        case '100-500':
                            $q->orWhereBetween('total_amount', [100, 500]);
                            break;
                        case '500-1000':
                            $q->orWhereBetween('total_amount', [500, 1000]);
                            break;
                        case 'over-1000':
                            $q->orWhere('total_amount', '>', 1000);
                            break;
                    }
                }
                // Custom min/max slider range
                $minIsNumeric = is_numeric($minRange);
                $maxIsNumeric = is_numeric($maxRange);
                if ($minIsNumeric && $maxIsNumeric) {
                    $q->orWhereBetween('total_amount', [(float)$minRange, (float)$maxRange]);
                } elseif ($minIsNumeric) {
                    $q->orWhere('total_amount', '>=', (float)$minRange);
                } elseif ($maxIsNumeric) {
                    $q->orWhere('total_amount', '<=', (float)$maxRange);
                }
            });
        }

        // Date range filters
        if (!empty($startDate) || !empty($endDate)) {
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            } elseif (!empty($startDate)) {
                $query->whereDate('created_at', '>=', $startDate);
            } elseif (!empty($endDate)) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        // Quick ranges: today | this_week | this_month
        if (!empty($hourRanges)) {
            $ranges = is_array($hourRanges) ? $hourRanges : [$hourRanges];
            $query->where(function ($qq) use ($ranges) {
                foreach ($ranges as $r) {
                    switch ($r) {
                        case 'today':
                            $qq->orWhereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);
                            break;
                        case 'this_week':
                            $qq->orWhereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $qq->orWhereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                            break;
                    }
                }
            });
        }

        return $query;
    }

    /**
     * Build query for monthly payments
     */
    private function buildMonthlyQuery($search, $status, $fromDate, $toDate)
    {
        return SupportByUser::with('userSubscriptionHistory')
            ->where(function ($query) use ($search) {
                if ($search != '') {
                    $query->where("amount", "LIKE", "%" . $search . "%");
                    $query->orWhere("tax", "LIKE", "%" . $search . "%");
                    $query->orWhere("total_amount", "LIKE", "%" . $search . "%");
                }
            })
            ->where('user_id', Auth::user()->id)
            ->where('payment_type', 'Monthly')
            ->when($status !== 'all', function ($query) use ($status) {
                $query->whereHas('userSubscriptionHistory', function ($q) use ($status) {
                    $q->where('subscription_status', $status);
                });
            })
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween(DB::raw('DATE(created_at)'), [$fromDate, $toDate]);
            })
            ->when($fromDate && !$toDate, function ($query) use ($fromDate) {
                $query->whereDate('created_at', $fromDate);
            })
            ->when(!$fromDate && $toDate, function ($query) use ($toDate) {
                $query->whereDate('created_at', $toDate);
            });
    }

    /**
     * Apply sorting to query
     */
    private function applySorting($query, $sortColumn, $sortOrder, $type)
    {
        if ($sortColumn == "all") {
            return $query->orderBy('id', 'desc');
        } else if (in_array($sortColumn, ["amount", "tax", "total_amount", "created_at"])) {
            return $query->orderBy($sortColumn, $sortOrder);
        } else if ($sortColumn === "status") {
            if ($type === 'one_time') {
                return $query->orderByRaw("(
                    SELECT payment_status
                    FROM invoices
                    WHERE invoices.reference_id = support_by_users.id
                    LIMIT 1
                ) $sortOrder");
            } else {
                return $query->orderByRaw("(
                    SELECT subscription_status
                    FROM user_subscription_history
                    WHERE user_subscription_history.stripe_subscription_id = support_by_users.subscription_id
                    LIMIT 1
                ) $sortOrder");
            }
        }
        
        return $query;
    }

    /**
     * Build AJAX response
     */
    private function buildAjaxResponse($records, $viewPath)
    {
        $viewData['records'] = $records;
        $view = View::make($viewPath, $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    /**
     * Get user subscription history
     */
    private function getUserSubscriptionHistory($userId)
    {
        return UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_type', 'membership')
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Get Stripe data for subscription
     */
    private function getStripeData($user, $records)
    {
        $nextInvoiceData = null;
        $subscriptionHistory = null;

        if ($records && $user->stripe_id) {
            $stripe = $this->getStripeClient();
            
            try {
                $nextInvoiceData = $stripe->invoices->upcoming([
                    'customer' => $user->stripe_id,
                    'subscription' => $records->subscription_id
                ]);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                Log::error('Stripe Error: ' . $e->getMessage());
            }

            try {
                $subscriptionHistory = $stripe->subscriptions->all([
                    'customer' => $user->stripe_id,
                    'status' => 'all'
                ]);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                Log::error('Stripe Error: ' . $e->getMessage());
            }
        }

        $userSubscriptionHistory = $this->getUserSubscriptionHistory($user->id);
        $filteredHistory = collect($subscriptionHistory->data ?? [])->reject(function ($subscription) use ($userSubscriptionHistory) {
            return isset($userSubscriptionHistory->stripe_subscription_id) && 
                   $subscription->id === $userSubscriptionHistory->stripe_subscription_id;
        });

        return [
            'nextInvoiceData' => $nextInvoiceData,
            'subscriptionHistory' => $filteredHistory
        ];
    }

    /**
     * Get Stripe client instance
     */
    private function getStripeClient()
    {
        return new \Stripe\StripeClient(apiKeys('STRIPE_SECRET'));
    }

    /**
     * Get unique payment methods
     */
    private function getUniquePaymentMethods($paymentMethods)
    {
        $uniqueMethods = [];
        $seenLast4 = [];
        
        foreach ($paymentMethods as $method) {
            $last4 = $method->card->last4 ?? null;
        
            if ($last4 && !in_array($last4, $seenLast4)) {
                $seenLast4[] = $last4;
                $uniqueMethods[] = $method;
            }
        }
        
        return $uniqueMethods;
    }

    /**
     * Get transaction statistics
     */
    private function getTransactionStatistics($userId)
    {
        // Get current month and previous month for growth calculation
        $currentMonth = now()->startOfMonth();
        $previousMonth = now()->subMonth()->startOfMonth();
        
        // Total Revenue (from SupportByUser and Invoice)
        $currentMonthRevenue = SupportByUser::where('user_id', $userId)
            ->whereBetween('created_at', [$currentMonth, now()])
            ->sum('total_amount');
            
        $previousMonthRevenue = SupportByUser::where('user_id', $userId)
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->sum('total_amount');
            
        $totalRevenue = SupportByUser::where('user_id', $userId)->sum('total_amount');
        $revenueGrowth = $previousMonthRevenue > 0 ? 
            round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1) : 0;
        
        // Total Transactions
        $currentMonthTransactions = SupportByUser::where('user_id', $userId)
            ->whereBetween('created_at', [$currentMonth, now()])
            ->count();
            
        $previousMonthTransactions = SupportByUser::where('user_id', $userId)
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->count();
            
        $totalTransactions = SupportByUser::where('user_id', $userId)->count();
        $transactionGrowth = $previousMonthTransactions > 0 ? 
            round((($currentMonthTransactions - $previousMonthTransactions) / $previousMonthTransactions) * 100, 1) : 0;
        
        // Active Subscriptions
        $currentMonthSubscriptions = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_status', 'active')
            ->whereBetween('created_at', [$currentMonth, now()])
            ->count();
            
        $previousMonthSubscriptions = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_status', 'active')
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->count();
            
        $activeSubscriptions = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_status', 'active')
            ->count();
        $subscriptionGrowth = $previousMonthSubscriptions > 0 ? 
            round((($currentMonthSubscriptions - $previousMonthSubscriptions) / $previousMonthSubscriptions) * 100, 1) : 0;
        
        // Pending Transactions
        $currentMonthPending = SupportByUser::where('user_id', $userId)
            ->whereHas('invoice', function($q) {
                $q->where('payment_status', 'pending');
            })
            ->whereBetween('created_at', [$currentMonth, now()])
            ->count();
            
        $previousMonthPending = SupportByUser::where('user_id', $userId)
            ->whereHas('invoice', function($q) {
                $q->where('payment_status', 'pending');
            })
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->count();
            
        $pendingTransactions = SupportByUser::where('user_id', $userId)
            ->whereHas('invoice', function($q) {
                $q->where('payment_status', 'pending');
            })
            ->count();
        $pendingChange = $previousMonthPending > 0 ? 
            round((($currentMonthPending - $previousMonthPending) / $previousMonthPending) * 100, 1) : 0;
        
        // Status breakdown
        $completedTransactions = SupportByUser::where('user_id', $userId)
            ->whereHas('invoice', function($q) {
                $q->where('payment_status', 'paid');
            })
            ->count();
            
        $failedTransactions = SupportByUser::where('user_id', $userId)
            ->whereHas('invoice', function($q) {
                $q->where('payment_status', 'failed');
            })
            ->count();
            
        $refundedTransactions = SupportByUser::where('user_id', $userId)
            ->whereHas('invoice', function($q) {
                $q->where('payment_status', 'refunded');
            })
            ->count();
        
        return [
            'totalRevenue' => $totalRevenue,
            'revenueGrowth' => $revenueGrowth,
            'totalTransactions' => $totalTransactions,
            'transactionGrowth' => $transactionGrowth,
            'activeSubscriptions' => $activeSubscriptions,
            'subscriptionGrowth' => $subscriptionGrowth,
            'pendingTransactions' => $pendingTransactions,
            'pendingChange' => $pendingChange,
            'completedTransactions' => $completedTransactions,
            'failedTransactions' => $failedTransactions,
            'refundedTransactions' => $refundedTransactions
        ];
    }

    /**
     * Get chart data for revenue and transaction types
     */
    private function getChartData($userId)
    {
        // Revenue data for last 6 months
        $revenueData = [];
        $labels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->format('M');
            
            $monthRevenue = SupportByUser::where('user_id', $userId)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount');
                
            $revenueData[] = $monthRevenue;
        }
        
        // Transaction types distribution
        $oneTimeCount = SupportByUser::where('user_id', $userId)
            ->where('payment_type', 'One Time')
            ->count();
            
        $monthlyCount = SupportByUser::where('user_id', $userId)
            ->where('payment_type', 'Monthly')
            ->count();
            
        $subscriptionCount = UserSubscriptionHistory::where('user_id', $userId)
            ->where('subscription_status', 'active')
            ->count();
            
        $supportCount = SupportByUser::where('user_id', $userId)
            ->where('payment_type', 'Support')
            ->count();
        
        return [
            'revenue' => [
                'labels' => $labels,
                'data' => $revenueData
            ],
            'transactionTypes' => [
                'labels' => ['One Time', 'Monthly', 'Subscription', 'Support'],
                'data' => [$oneTimeCount, $monthlyCount, $subscriptionCount, $supportCount]
            ]
        ];
    }

    /**
     * Get recent transactions
     */
    private function getRecentTransactions($userId)
    {
        return SupportByUser::with(['invoice', 'user'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }
}
