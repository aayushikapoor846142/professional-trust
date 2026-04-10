<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use App\Services\FeatureCheckService;
use Carbon\Carbon;

class TransactionReceiptController extends Controller
{
    /**
     * Display the list of transaction invoices.
     *
     * @return \Illuminate\View\View
     */
    protected $featureCheckService;

    public function __construct(FeatureCheckService $featureCheckService)
    {
        $this->featureCheckService = $featureCheckService;
    }

    public function index()
    {
        
        $user = \Auth::user();
        $transactionFeatureStatus = $this->featureCheckService->canAddTransactions($user->id);
        $viewData['canAddTransactions'] = $transactionFeatureStatus['allowed'];

        $viewData['paymentStatus'] = $statuses = Invoice::distinct()->pluck('payment_status');

        $viewData['pageTitle'] = "Transaction Invoices";
        return view('admin-panel.09-utilities.transactions.receipts.lists', $viewData);
    }

    /**
     * Get the list of paid invoices with pagination and search functionality.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAjaxList(Request $request)
    {
        $hour_range = $request->input('hour_range');
        $search = $request->input("search");
         $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');
        
        $query = Invoice::where('user_id', Auth::user()->id);
        
        // Apply status filter if provided; default to 'paid' when none selected
        if ($request->filled('status')) {
            $statuses = is_array($request->status) ? $request->status : [$request->status];
            $query->whereIn('payment_status', $statuses);
        } 
        
        // Apply search filter if search term is provided
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where("first_name", "LIKE", "%{$search}%")
                      ->orWhere("last_name", "LIKE", "%{$search}%")
                       ->orWhere("total_amount", "LIKE", "%{$search}%")
                        ->orWhere("payment_status", "LIKE", "%{$search}%");
            });
        }
        // Apply amount filters: predefined ranges OR custom min/max
        $hasPriceRanges = $request->filled('price_range');
        $minRange = $request->filled('min_range') && $request->input('min_range') !== '' ? $request->input('min_range') : null;
        $maxRange = $request->filled('max_range') && $request->input('max_range') !== '' ? $request->input('max_range') : null;
        $hasSlider = $minRange !== null || $maxRange !== null;
        
        if ($hasPriceRanges || $hasSlider) {
            $ranges = $hasPriceRanges ? (is_array($request->price_range) ? $request->price_range : [$request->price_range]) : [];
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

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if ($startDate && $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                  ->whereDate('created_at', '<=', $endDate);
        } elseif ($startDate && !$endDate) {
            $query->whereDate('created_at', '>=', $startDate);
        } elseif (!$startDate && $endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        
        // Quick date ranges: today, this_week, this_month
        if ($request->filled('hour_range')) {
            $ranges = is_array($request->hour_range) ? $request->hour_range : [$request->hour_range];
            $query->where(function ($q) use ($ranges) {
                foreach ($ranges as $r) {
                    switch ($r) {
                        case 'today':
                            $q->orWhereBetween('created_at', [Carbon::today(), Carbon::tomorrow()]);
                            break;
                        case 'this_week':
                            $q->orWhereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $q->orWhereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                            break;
                    }
                }
            });
        }
        
        $records = $query->orderBy($sortColumn, $sortDirection)->paginate();

        $viewData['records'] = $records;

        $user = \Auth::user();
        $transactionFeatureStatus = $this->featureCheckService->canAddTransactions($user->id);
        $viewData['canAddTransactions'] = $transactionFeatureStatus['allowed'];

        $view = View::make('admin-panel.09-utilities.transactions.receipts.ajax-list', $viewData);
        $contents = $view->render();
        
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        
        return response()->json($response);
    }

    /**
     * Display the detailed view of a specific invoice.
     *
     * @param string $uid The unique identifier of the invoice
     * @return \Illuminate\View\View
     */
    public function view($uid)
    {
        $record = Invoice::with(['invoiceItems'])
            ->where("unique_id", $uid)
            ->where('user_id', Auth::user()->id) // Security: Ensure user can only view their own invoices
            ->first();
        
      
        
        $viewData['record'] = $record;
        $viewData['pageTitle'] = "View Invoice";
        
        return view('admin-panel.09-utilities.transactions.receipts.view', $viewData);
    }
}
