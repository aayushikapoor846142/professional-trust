<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use View;
use App\Models\Cases;
use App\Models\CaseWithProfessionals;
use App\Models\AppointmentBooking;
use App\Models\Chat;
use App\Models\ChatGroup;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use App\Models\SupportByUser;
use App\Models\UserSubscriptionHistory;
use Stripe\Stripe;
use App\Models\GroupMessages;
use App\Models\Reviews;
use App\Models\StaffCases;
use App\Services\DashboardService;
use App\Models\CompanyLocations;
use App\Models\Country;
use App\Models\CdsProfessionalCompany;

class DashboardController extends Controller
{

    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the main dashboard overview.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewData['pageTitle'] = "Dashboard Overview";
        $viewData['activeTab'] = 'overview';
        $professionalId = auth()->user()->getRelatedProfessionalId();
        // Get user data
        $userId = auth()->user()->id;
        $professionalId = auth()->user()->getRelatedProfessionalId();
        
        // Cases data
        $viewData['totalCases'] = countCase('all');
        $viewData['upcomingAppointments'] = collect(); // Will be populated in appointments tab
        
        // Messages data
        $viewData['unreadMessages'] = unreadTotalChatMessages($userId);
        
        // Invoices data
        $viewData['pendingInvoices'] = 0; // Will be populated in invoices tab
        $viewData['earningsThisMonth'] = 0; // Will be populated in invoices tab
        
        // Points data
        $viewData['pointsEarned'] = 0; // Will be populated from points system
        
        $viewData['recentCases'] = Cases::with(['userAdded', 'submitProposal'])
                    ->where('status', 'posted')
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();
        $viewData['recentCaseWithProfessionals'] = CaseWithProfessionals::with(['client', 'professional'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();
        $viewData['recentInvoices'] = Invoice::where('added_by',auth()->user()->id)->orderBy('id','desc')->limit(5)->get();

         $viewData['upcomingAppointments'] = AppointmentBooking::with(['client','service'])
            ->where('professional_id', $professionalId)
            ->whereIn('status', ['approved', 'awaiting'])
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();

        $viewData['cases']  = Cases::with([
            'services',
            'submitProposal',
        ])
        ->whereHas("userAdded")
        ->whereHas("services")
        ->whereHas("subServices")
        ->where("status", "posted")->orderBy('id','desc')->limit(5)->get();

       $viewData['recentChats'] =  Chat::with(['lastMessage', 'addedBy', 'chatWith'])
                                ->where(function ($query) {
                                    $query->where('user2_id', auth()->user()->id)
                                        ->orWhere('user1_id', auth()->user()->id);
                                })
                                ->limit(5)
                                ->get()
                                ->sortByDesc(fn($chat) => $chat->lastMessage->created_at ?? null);
 $user_id=auth()->user()->id;
        $viewData['groupdata'] = ChatGroup::whereHas("groupMembers",function($query) use($user_id){
                    $query->where("user_id",$user_id);
                })
                ->addSelect(['last_message_date' => GroupMessages::query()
                    ->select('created_at')
                    ->whereColumn('group_messages.group_id', 'chat_groups.id')
                    ->latest('created_at')
                    ->limit(1)
                ])
                ->orderBy('last_message_date','desc')->limit(5)
                ->get();

        $viewData['totalChats'] = Chat::where(function($query) use($userId){
            $query->where('user1_id', $userId)->orWhere('user2_id', $userId);
        })->count();

        // Unread Messages (individual)
        $viewData['unreadMessages'] = unreadTotalChatMessages($userId);

        // Total Group Chats
        $viewData['totalGroupChats'] = ChatGroup::whereHas('groupMembers', function($query) use($userId) {
            $query->where('user_id', $userId);
        })->count();

        // Invitations
        $viewData['invitations'] = chatReqstCount($userId);

        // Notifications
        $viewData['notifications'] = chatNotificationsCount($userId);
$user = auth()->user();
        // review
        $viewData['reviews'] = Reviews::with('user:id,first_name,email,unique_id')
               ->where('is_spam', 0)
            ->when($user->role == 'professional', fn($q) => $q->where('professional_id', $user->id))
            ->when($user->role == 'admin', fn($q) => $q)
            ->when($user->role == 'client', fn($q) => $q->where('added_by', $user->id))->limit(5)->get();
        
        

        // end review

        // case with professional
        $caseWithProfessionals = CaseWithProfessionals::with(['services']);
            if(auth()->user()->role == 'professional'){
                $caseWithProfessionals->where('professional_id',auth()->user()->id);
            }else{
              $cases_ids = StaffCases::where('staff_id',auth()->user()->id)->get()->pluck('case_id')->toArray();
              $caseWithProfessionals->whereIn('id',$cases_ids);
            }


            $caseWithProfessionals = $caseWithProfessionals->orderBy('id','desc')->limit(5)
            // ->where('payment_status','paid')
            ->get();
        $viewData['caseWithProfessionals'] = $caseWithProfessionals;

        // transcation overview
         $userId = auth()->user()->id;
        
        // Get transaction statistics
        $statistics = $this->getTransactionStatistics($userId);
        $chartData = $this->getChartData($userId);
        $recentTransactions = $this->getRecentTransactions($userId);
        $viewData['totalRevenue'] = $statistics['totalRevenue'];
        $viewData['revenueGrowth'] = $statistics['revenueGrowth'];
        $viewData['totalTransactions'] = $statistics['totalTransactions'];
        $viewData['transactionGrowth'] = $statistics['transactionGrowth'];
        $viewData[ 'activeSubscriptions'] = $statistics['activeSubscriptions'];
        $viewData['subscriptionGrowth'] = $statistics['subscriptionGrowth'];
        $viewData[ 'pendingTransactions'] = $statistics['pendingTransactions'];
        $viewData['pendingChange'] = $statistics['pendingChange'];
        $viewData[ 'completedTransactions'] = $statistics['completedTransactions'];
        $viewData['failedTransactions'] = $statistics['failedTransactions'];
        $viewData['refundedTransactions'] = $statistics['refundedTransactions'];
        $viewData['chartData'] = $chartData;
        $viewData['recentTransactions'] = $recentTransactions;
     
        return view("admin-panel.dashboard", $viewData);
    }

    /**
     * Display the cases dashboard tab.
     *
     * @return \Illuminate\View\View
     */
    public function securitySetting()
    {
        $id = auth()->user()->unique_id;
        $viewData['pageTitle'] = "Change Password";
        $record = User::where("unique_id", $id)->first();
        $viewData['record'] = $record;
        return view('admin-panel.04-profile.security-settings.security-overview', $viewData);
    }
    public function cases()
    {
        $viewData['pageTitle'] = "Cases Dashboard";
        $viewData['activeTab'] = 'cases';
        
        // Get user data
        $userId = auth()->user()->id;
        
        // Get recent post cases
        $viewData['recentPostCases'] = Cases::with(['userAdded', 'submitProposal'])
                    ->where('status', 'posted')
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();

        // Get recent case with professionals
        $viewData['recentCaseWithProfessionals'] = CaseWithProfessionals::with(['client', 'professional'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(5)
                    ->get();

        // Get unread messages count for header
        $viewData['unreadMessages'] = unreadTotalChatMessages($userId);

        return view("admin-panel.dashboard-tabs.cases", $viewData);
    }

    /**
     * Display the appointments dashboard tab.
     *
     * @return \Illuminate\View\View
     */
    public function appointments()
    {
        $viewData['pageTitle'] = "Appointments Dashboard";
        $viewData['activeTab'] = 'appointments';
        
        // Get user data
        $userId = auth()->user()->id;
        $professionalId = auth()->user()->getRelatedProfessionalId();
        
        // Get appointments count by status
        $statusKeys = ['all','draft', 'approved', 'awaiting', 'cancelled', 'archieved', 'completed', 'non-conducted'];
        $appointmentsCount = [];
        foreach ($statusKeys as $key) {
            if ($key == 'all') {
                $appointmentsCount[$key] = AppointmentBooking::where('professional_id',$professionalId)
                                             ->count();
            } else {
                $appointmentsCount[$key] = AppointmentBooking::where('status', $key)            
                                        ->where('professional_id',$professionalId)
                                        ->count();
            }
        }
        $viewData['appointmentsCount'] = $appointmentsCount;

        // Get upcoming appointments
        $viewData['upcomingAppointments'] = AppointmentBooking::with(['client','service'])
            ->where('professional_id', $professionalId)
            ->whereIn('status', ['approved', 'awaiting'])
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->limit(5)
            ->get();

        // Get unread messages count for header
        $viewData['unreadMessages'] = unreadTotalChatMessages($userId);

        return view("admin-panel.dashboard-tabs.appointments", $viewData);
    }

    /**
     * Display the messages dashboard tab.
     *
     * @return \Illuminate\View\View
     */
    public function messages()
    {
        $viewData['pageTitle'] = "Messages Dashboard";
        $viewData['activeTab'] = 'messages';
        
        // Get user data
        $userId = auth()->user()->id;
        
        // Total Chats (individual)
        $viewData['totalChats'] = Chat::where(function($query) use($userId){
            $query->where('user1_id', $userId)->orWhere('user2_id', $userId);
        })->count();

        // Unread Messages (individual)
        $viewData['unreadMessages'] = unreadTotalChatMessages($userId);

        // Total Group Chats
        $viewData['totalGroupChats'] = ChatGroup::whereHas('groupMembers', function($query) use($userId) {
            $query->where('user_id', $userId);
        })->count();

        // Invitations
        $viewData['invitations'] = chatReqstCount($userId);

        // Notifications
        $viewData['notifications'] = chatNotificationsCount($userId);

        // Recent Group Chats (latest 5 by last message date)
        $viewData['recentGroupChats'] = ChatGroup::with('lastMessage')
            ->whereHas('groupMembers', function($query) use($userId) {
                $query->where('user_id', $userId);
            })
            ->addSelect(['last_message_date' => \App\Models\GroupMessages::query()
                ->select('created_at')
                ->whereColumn('group_messages.group_id', 'chat_groups.id')
                ->latest('created_at')
                ->limit(1)
            ])
            ->orderByDesc('last_message_date')
            ->limit(5)
            ->get();

        // Recent Chats (latest 5 by last message date)
        $viewData['recentChats'] = Chat::with(['lastMessage', 'addedBy', 'chatWith'])
            ->where(function ($query) use ($userId) {
                $query->where('user2_id', $userId)
                    ->orWhere('user1_id', $userId);
            })
            ->orderByDesc(\DB::raw('(SELECT MAX(created_at) FROM chat_messages WHERE chat_id = chats.id)'))
            ->limit(5)
            ->get();

        return view("admin-panel.dashboard-tabs.messages", $viewData);
    }

    /**
     * Display the invoices dashboard tab.
     *
     * @return \Illuminate\View\View
     */
    public function invoices()
    {
        $userId = \Auth::user()->id;
        
        // Get transaction statistics
        $statistics = $this->getTransactionStatistics($userId);
        
        // Get chart data
        $chartData = $this->getChartData($userId);
        
        // Get recent transactions
        $recentTransactions = $this->getRecentTransactions($userId);
        
        $viewData = [
            'pageTitle' => "Transaction Overview",
            'activeTab' => 'invoices',
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
        
        return view("admin-panel.dashboard-tabs.invoices", $viewData);
    }

    /**
     * Display the reports dashboard tab.
     *
     * @return \Illuminate\View\View
     */
    public function reports()
    {
        $viewData['pageTitle'] = "Reports Dashboard";
        $viewData['activeTab'] = 'reports';
        
        // Get user data
        $userId = auth()->user()->id;
        
        // Get unread messages count for header
        $viewData['unreadMessages'] = unreadTotalChatMessages($userId);

        return view("admin-panel.dashboard-tabs.reports", $viewData);
    }

    /**
     * Display the settings dashboard tab.
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        $viewData['pageTitle'] = "Settings Dashboard";
        $viewData['activeTab'] = 'settings';
        
        // Get user data
        $userId = auth()->user()->id;
        
        // Get unread messages count for header
        $viewData['unreadMessages'] = unreadTotalChatMessages($userId);

        return view("admin-panel.dashboard-tabs.settings", $viewData);
    }

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
        
    public function profile($page='')
    {
        $viewData = $this->dashboardService->getProfileData(\Auth::user()->id, $page);
        return view('admin-panel.04-profile.profile.profile-master', $viewData);
    }


    public function imageCropper(Request $request)
    {
        $viewData['pageTitle'] = "User Image Cropper"; // Set the page title
        $view = View::make('components.professional-image-cropper', $viewData);
        $contents = $view->render();

        // Prepare the JSON response
        $response['contents'] = $contents;
        $response['status'] = true;

        return response()->json($response);
    }

    public function saveCroppedImage(Request $request){
       
        if ($file = $request->file('file')){
            $extension       = $file->getClientOriginalExtension() ?: 'png';
            $newName        = mt_rand().".".$extension;
            $source_url = $file->getPathName();
            // $destinationPath = uapProfessionalsDir();
            $destinationPath = public_path('uploads/temp');
            if($file->move($destinationPath, $newName)){
                $sourcePath =  public_path('uploads/temp/'.$newName);
                $media_path = userDir();
                $response = mediaUploadApi("upload-file",$sourcePath,$media_path,$newName);
                if(isset($response['status'])){
                    if($response['status'] == 'success'){
                        \File::delete($sourcePath);
                        User::where("id",auth()->user()->id)->update(['profile_image'=>$newName]);
                        return response()->json(['status'=>true,'message' => "Image cropped successfully.", 'filename' => $newName,'filepath' => userDirUrl($newName)]);
                        
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

    public function bannerCropper(Request $request)
    {
       
        $viewData['pageTitle'] = "Banner Image Cropper"; // Set the page title
      
        $view = View::make('components.professional-banner-cropper', $viewData);
        $contents = $view->render();

        // Prepare the JSON response
        $response['contents'] = $contents;
        $response['status'] = true;

        return response()->json($response);
    }

    public function saveBannerImage(Request $request){
       
        if ($file = $request->file('file')){
            $extension       = $file->getClientOriginalExtension() ?: 'png';
            $newName        = mt_rand().".".$extension;
            $source_url = $file->getPathName();
            // $destinationPath = uapProfessionalsDir();
            $destinationPath = public_path('uploads/temp');
            if($file->move($destinationPath, $newName)){
                $sourcePath =  public_path('uploads/temp/'.$newName);
                $media_path = userBannerDir();
                $response = mediaUploadApi("upload-file",$sourcePath,$media_path,$newName);
                if(isset($response['status'])){
                    if($response['status'] == 'success'){
                        \File::delete($sourcePath);
                        User::where("id",auth()->user()->id)->update(['banner_image'=>$newName]);
                        return response()->json(['status'=>true,'message' => "Image cropped successfully.", 'filename' => $newName,'filepath' => userBannerDirUrl($newName)]);
                        
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

    public function addCompanyAddress($id,Request $request){
        $adddressInfo = array();
        if($id != 0){
            $adddressInfo = CompanyLocations::where(function ($query) {
                $query->where('user_id',\Auth::user()->id);
            })
            ->orderBy('id', "desc")
            ->where('unique_id',$id)
            ->first();
        }
        $viewData['adddressInfo'] = $adddressInfo;
        $viewData['pageTitle'] = "Company Address";
        $viewData['countries'] = Country::all();
        $viewData['id'] = $id;
        $view = view("admin-panel.04-profile.profile.company-address-modal",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;

        return response()->json($response);
    }

    public function addPersonalAddress($id,Request $request){
        $adddressInfo = array();
        if($id != 0){
            $adddressInfo = CompanyLocations::where(function ($query) {
                $query->where('user_id',\Auth::user()->id);
            })
            ->orderBy('id', "desc")
            ->where('unique_id',$id)
            ->first();
        }
        $viewData['adddressInfo'] = $adddressInfo;
        $viewData['pageTitle'] = "Personal Address";
        $viewData['countries'] = Country::all();
        $viewData['id'] = $id;
        $view = view("admin-panel.04-profile.profile.personal-address-modal",$viewData)->render();
        $response['status'] = true;
        $response['contents'] = $view;

        return response()->json($response);
    }

    public function generateAssessmentForm(Request $request){
        $message =  $request->input("message");
        $data['message'] = $request->input("message");
        $res = assistantApiCall("application_form");
    }

    public function notificationRedirect($id)
    {
        $chatNotification = ChatNotification::where('unique_id',$id)->first();

        if(!empty($chatNotification)){

            if($chatNotification->type == "post_case"){
                if($chatNotification->is_read == 0){
                    $chatNotification->is_read = 1;
                    $chatNotification->save();
                }
                $url = baseUrl('cases/view/'.$chatNotification->redirect_link);
                return redirect($url);
            }else if($chatNotification->type == "award_case"){
                if($chatNotification->is_read == 0){
                    $chatNotification->is_read = 1;
                    $chatNotification->save();
                }
                $url = baseUrl('case-with-professionals/view/'.$chatNotification->redirect_link);
                return redirect($url);
            }
            else if($chatNotification->type == "accept_retain_agreement"){
                if($chatNotification->is_read == 0){
                    $chatNotification->is_read = 1;
                    $chatNotification->save();
                }
                $url = baseUrl('case-with-professionals/view/'.$chatNotification->redirect_link);
                return redirect($url);
            }
            else
            {
                return redirect($chatNotification->redirect_link);
            }

        }else{
            return redirect(baseUrl('/'));
        }
    }

    public function getCompanies(Request $request){
        $companies = CdsProfessionalCompany::where("user_id",auth()->user()->id)->get();
        $viewData['records'] = $companies;
        $view = view("admin-panel.04-profile.profile.companies.companies-ajax",$viewData);
        $content = $view->render();

        $response['status'] = true;
        $response['contents'] = $content;
        
        return response()->json($response);
    }

    public function changePassword($id)
    {
        $record = User::where("unique_id", $id)->first();
        $viewData['record'] = $record;
        $viewData['pageTitle'] = "Change Password";
        return view('admin-panel.change-password', $viewData);
    }

        /**
     * Update the password for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword($id,Request $request)
    {
        $object =  User::where('unique_id',$id)->first();

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|different:old_password|password_validation',
            'password_confirmation' => 'required|password_validation',
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

         // check the old password is correct
         if (!Hash::check($request->input("old_password"), $object->password)) {
            return response()->json([
                'status'  => false,
                'message' => "The old password is incorrect.",
            ]);
        }

        // check new password is in history
        if ($object->isPasswordInHistory($request->input("password"))) {
            return response()->json([
                'status'  => false,
                'message' => "You cannot reuse an old password.",
            ]);
        }

        try {
            \DB::beginTransaction();

            if ($request->input("password")) {
                $object->password = bcrypt($request->input("password"));
            }

            $object->save();
            $object->storePasswordHistory();
            \DB::commit();

            $response['status'] = true;
            $response['message'] = "Password updated successfully";
        
        } catch (\Exception $e) {
            \DB::rollBack();

            $response['status'] = false;
            $response['message'] = "Failed to update password. Please try again later.";
        }

        return response()->json($response);
    }
}
