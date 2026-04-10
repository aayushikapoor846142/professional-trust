<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use View;
use App\Models\PointEarn;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SupportBadge;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View as ViewResponse;
use App\Services\PointBadgeService;
use Carbon\Carbon;

class PointEarnHistoryController extends Controller
{
    protected $pointBadgeService;

    public function __construct(PointBadgeService $pointBadgeService)
    {
        $this->pointBadgeService = $pointBadgeService;
    }

    /**
     * Show the transaction overview page.
     *
     * @return ViewResponse
     */
    public function overview(): ViewResponse
    {
        $viewData['pageTitle'] = "Transaction Overview";
        return view("admin-panel.09-utilities.transactions.earning-overview", $viewData);
    }

    /**
     * Display the list of Action.
     *
     * @return ViewResponse
     */
    public function index(): ViewResponse
    {
        $viewData['pageTitle'] = "Points Earn";
        $badge_html = '';
        try {
            $user = Auth::user();
            $userPoints = $this->pointBadgeService->getUserPoints($user->id);
            $badge = $this->pointBadgeService->getUserBadge($userPoints);
            if ($badge) {
                $pdfData['user'] = $user;
                $pdfData['showDownload'] = true;
                $pdfData['badge'] = $badge;
                $view = view("components.badge-pdf", $pdfData);
                $badge_html = $view->render();
            }
        } catch (\Exception $e) {
            // Optionally log the error
            $badge_html = '';
        }
        $viewData['badge_html'] = $badge_html;
        return view('admin-panel.09-utilities.transactions.points-earned.lists', $viewData);
    }

    /**
     * Get the list of Points Earn with pagination and search functionality.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAjaxList(Request $request): JsonResponse
    {
        try {
                $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');
          $hour_range = $request->input('hour_range');
            $search = $request->input('search');
            $records = PointEarn::where('user_id', Auth::user()->id);

        if ($search != '') {
            $records->where('total_points', $search)->orWhere('bonus_points',$search)->orWhere('points',$search);
        }
             // Apply amount filters: predefined ranges OR custom min/max
        $hasPriceRanges = $request->filled('price_range');
        $minRange = $request->filled('min_range') && $request->input('min_range') !== '' ? $request->input('min_range') : null;
        $maxRange = $request->filled('max_range') && $request->input('max_range') !== '' ? $request->input('max_range') : null;
        $hasSlider = $minRange !== null || $maxRange !== null;
        
        if ($hasPriceRanges || $hasSlider) {
            $ranges = $hasPriceRanges ? (is_array($request->price_range) ? $request->price_range : [$request->price_range]) : [];
            $records->where(function ($q) use ($ranges, $minRange, $maxRange) {
                foreach ($ranges as $range) {
                    switch ($range) {
                        case 'under-100':
                            $q->orWhere('total_points', '<', 100);
                            break;
                        case '100-500':
                            $q->orWhereBetween('total_points', [100, 500]);
                            break;
                        case '500-1000':
                            $q->orWhereBetween('total_points', [500, 1000]);
                            break;
                        case 'over-1000':
                            $q->orWhere('total_points', '>', 1000);
                            break;
                    }
                }
                // Custom min/max slider range
                $minIsNumeric = is_numeric($minRange);
                $maxIsNumeric = is_numeric($maxRange);
                if ($minIsNumeric && $maxIsNumeric) {
                    $q->orWhereBetween('total_points', [(float)$minRange, (float)$maxRange]);
                } elseif ($minIsNumeric) {
                    $q->orWhere('total_points', '>=', (float)$minRange);
                } elseif ($maxIsNumeric) {
                    $q->orWhere('total_points', '<=', (float)$maxRange);
                }
            });
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
        
        // Quick date ranges: today, this_week, this_month
        if ($request->filled('hour_range')) {
            $ranges = is_array($request->hour_range) ? $request->hour_range : [$request->hour_range];
            $records->where(function ($q) use ($ranges) {
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
       
                $records = $records->orderBy($sortColumn, $sortDirection)
                ->paginate();

            $viewData['records'] = $records;
            $view = View::make('admin-panel.09-utilities.transactions.points-earned.ajax-list', $viewData);
            $contents = $view->render();
            $response['contents'] = $contents;
            $response['last_page'] = $records->lastPage();
            $response['current_page'] = $records->currentPage();
            $response['total_records'] = $records->total();
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'contents' => '',
                'last_page' => 1,
                'current_page' => 1,
                'total_records' => 0,
                'error' => 'Unable to fetch records.'
            ], 500);
        }
    }

    /**
     * Download the badge as a PDF for a given user unique ID.
     *
     * @param string $uid
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
     */
    public function downloadBadge($uid)
    {
        try {
            $user = User::where("unique_id", $uid)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found.'], 404);
            }
            $userPoints = $this->pointBadgeService->getUserPoints($user->id);
            $badge = $this->pointBadgeService->getUserBadge($userPoints);
            if (!$badge) {
                return response()->json(['error' => 'Badge not found.'], 404);
            }
            $pdfData['user'] = $user;
            $pdfData['showDownload'] = true;
            $pdfData['badge'] = $badge;
            $pdf = Pdf::loadView('components.badge-pdf', $pdfData);
            return $pdf->download($badge->badge_name . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to generate badge PDF.'], 500);
        }
    }
}
