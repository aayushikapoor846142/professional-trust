<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use DB;
use App\Models\User;
use App\Models\UserDetails;
use App\Rules\PasswordValidation;
use App\Models\CaseWithProfessionals;
use App\Models\Invoice;
use Illuminate\Support\Facades\Hash;
use App\Models\AppointmentBooking;
use App\Models\UserEarningsHistory;
use Carbon\Carbon;


class EarningReportController extends Controller
{
    public function __construct()
    {
        // Constructor method
    }



    public function earningReport($status = 'case')
    {
        $viewData['pageTitle'] = "Professionals Earn Reports";
        $viewData['status'] = $status;
        // return view('admin-panel.09-utilities.earning-reports.case-lists', $viewData);
        return view('admin-panel.09-utilities.earning-reports.lists', $viewData);
    }

    private function getRecords($user, $status, $hasPriceRanges = false, $price_range = null, $minRange = null, $maxRange = null, $hasSlider = false, $startDate = null, $endDate = null, $hour_range = null, $search = null)
    {
        if ($status === 'case') {
            
            $recordIds = CaseWithProfessionals::where('professional_id', $user->id)
                ->where('payment_status', 'paid')
                ->pluck('id')
                ->toArray();
            
           

            $invoices = Invoice::with(['caseInvoice.client'])
                ->whereIn('reference_id', $recordIds)
                ->where('invoice_type', 'professional-case');

            // Apply search filters
            if ($search) {
                $invoices->where(function ($q) use ($search) {
                    // Search by invoice ID
                    $q->orWhere('id', 'LIKE', "%{$search}%")
                      ->orWhere('invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('unique_id', 'LIKE', "%{$search}%");
                    
                    // Search by case title and unique ID
                    $q->orWhereHas('caseInvoice', function ($caseQuery) use ($search) {
                        $caseQuery->where('case_title', 'LIKE', "%{$search}%")
                                 ->orWhere('unique_id', 'LIKE', "%{$search}%");
                    });
                    
                    // Search by client name and email
                    $q->orWhereHas('caseInvoice.client', function ($clientQuery) use ($search) {
                        $clientQuery->where('first_name', 'LIKE', "%{$search}%")
                                   ->orWhere('last_name', 'LIKE', "%{$search}%")
                                   ->orWhere('email', 'LIKE', "%{$search}%")
                                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                });
            }

            // Apply price range filters to invoices
            if ($hasPriceRanges || $hasSlider) {
              
                $invoices->where(function ($q) use ($hasPriceRanges, $price_range, $minRange, $maxRange) {
                    if ($hasPriceRanges && $price_range) {
                        $ranges = is_array($price_range) ? $price_range : [$price_range];
                       
                        
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
                    }
                    
                    // Custom min/max slider range for invoices
                    if ($minRange !== null || $maxRange !== null) {
                       
                        
                        $minIsNumeric = is_numeric($minRange);
                        $maxIsNumeric = is_numeric($maxRange);
                        
                        if ($minIsNumeric && $maxIsNumeric) {
                            $q->orWhereBetween('total_amount', [(float)$minRange, (float)$maxRange]);
                        } elseif ($minIsNumeric) {
                            $q->orWhere('total_amount', '>=', (float)$minRange);
                        } elseif ($maxIsNumeric) {
                            $q->orWhere('total_amount', '<=', (float)$maxRange);
                        }
                    }
                });
            }

            // Apply date range filters to invoices
            if ($startDate || $endDate) {
                
                if ($startDate && $endDate) {
                    $invoices->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate);
                } elseif ($startDate && !$endDate) {
                    $invoices->whereDate('created_at', '>=', $startDate);
                } elseif (!$startDate && $endDate) {
                    $invoices->whereDate('created_at', '<=', $endDate);
                }
            }
            
            // Apply hour range filters to invoices
            if ($hour_range) {
                $ranges = is_array($hour_range) ? $hour_range : [$hour_range];
                $invoices->where(function ($q) use ($ranges) {
                    foreach ($ranges as $r) {
                        switch ($r) {
                            case 'today':
                                $q->orWhereDate('created_at', Carbon::today()->toDateString());
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

            $invoices = $invoices->paginate(10);
            
            
            return $invoices;
        } elseif ($status === 'appointment') {
          
            
            $recordIds = AppointmentBooking::where('professional_id', $user->id)
                ->where('payment_status', 'paid')
                ->pluck('id')
                ->toArray();
            

            $invoices = Invoice::with(['appointmentInvoice.client'])
                ->whereIn('reference_id', $recordIds)
                ->where('invoice_type', 'appointment-booking');

            // Apply search filters
            if ($search) {
                $invoices->where(function ($q) use ($search) {
                    // Search by invoice ID
                    $q->orWhere('id', 'LIKE', "%{$search}%")
                      ->orWhere('invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('unique_id', 'LIKE', "%{$search}%");
                    
                    // Search by appointment unique ID
                    $q->orWhereHas('appointmentInvoice', function ($appointmentQuery) use ($search) {
                        $appointmentQuery->where('unique_id', 'LIKE', "%{$search}%");
                    });
                    
                    // Search by client name and email
                    $q->orWhereHas('appointmentInvoice.client', function ($clientQuery) use ($search) {
                        $clientQuery->where('first_name', 'LIKE', "%{$search}%")
                                   ->orWhere('last_name', 'LIKE', "%{$search}%")
                                   ->orWhere('email', 'LIKE', "%{$search}%")
                                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                });
            }

            // Apply price range filters to invoices
            if ($hasPriceRanges || $hasSlider) {
                $invoices->where(function ($q) use ($hasPriceRanges, $price_range, $minRange, $maxRange) {
                    if ($hasPriceRanges && $price_range) {
                        $ranges = is_array($price_range) ? $price_range : [$price_range];
                        
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
                    }
                    
                    // Custom min/max slider range for invoices
                    if ($minRange !== null || $maxRange !== null) {
                        $minIsNumeric = is_numeric($minRange);
                        $maxIsNumeric = is_numeric($maxRange);
                        
                        if ($minIsNumeric && $maxIsNumeric) {
                            $q->orWhereBetween('total_amount', [(float)$minRange, (float)$maxRange]);
                        } elseif ($minIsNumeric) {
                            $q->orWhere('total_amount', '>=', (float)$minRange);
                        } elseif ($maxIsNumeric) {
                            $q->orWhere('total_amount', '<=', (float)$maxRange);
                        }
                    }
                });
            }

            // Apply date range filters to invoices
            if ($startDate || $endDate) {
               
                if ($startDate && $endDate) {
                    $invoices->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate);
                } elseif ($startDate && !$endDate) {
                    $invoices->whereDate('created_at', '>=', $startDate);
                } elseif (!$startDate && $endDate) {
                    $invoices->whereDate('created_at', '<=', $endDate);
                }
            }
            
            // Apply hour range filters to invoices
            if ($hour_range) {
               
                $ranges = is_array($hour_range) ? $hour_range : [$hour_range];
                $invoices->where(function ($q) use ($ranges) {
                    foreach ($ranges as $r) {
                        switch ($r) {
                            case 'today':
                                $q->orWhereDate('created_at', Carbon::today()->toDateString());
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

            $invoices = $invoices->paginate(10);
            
            return $invoices;
        } elseif ($status === 'global' || $status === 'other') {

            $invoices = Invoice::with(['appointmentInvoice.client'])
                ->where('added_by', $user->id)
                ->where('invoice_type', 'global');

            // Apply search filters
            if ($search) {
                $invoices->where(function ($q) use ($search) {
                    // Search by invoice ID
                    $q->orWhere('id', 'LIKE', "%{$search}%")
                      ->orWhere('invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('unique_id', 'LIKE', "%{$search}%");
                    
                    // Search by client name and email
                    $q->orWhereHas('appointmentInvoice.client', function ($clientQuery) use ($search) {
                        $clientQuery->where('first_name', 'LIKE', "%{$search}%")
                                   ->orWhere('last_name', 'LIKE', "%{$search}%")
                                   ->orWhere('email', 'LIKE', "%{$search}%")
                                   ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
                    });
                });
            }

            // Apply price range filters to invoices
            if ($hasPriceRanges || $hasSlider) {
               
                $invoices->where(function ($q) use ($hasPriceRanges, $price_range, $minRange, $maxRange) {
                    if ($hasPriceRanges && $price_range) {
                        $ranges = is_array($price_range) ? $price_range : [$price_range];
                        
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
                    }
                    
                    // Custom min/max slider range for invoices
                    if ($minRange !== null || $maxRange !== null) {
                        $minIsNumeric = is_numeric($minRange);
                        $maxIsNumeric = is_numeric($maxRange);
                        
                        if ($minIsNumeric && $maxIsNumeric) {
                            $q->orWhereBetween('total_amount', [(float)$minRange, (float)$maxRange]);
                        } elseif ($minIsNumeric) {
                            $q->orWhere('total_amount', '>=', (float)$minRange);
                        } elseif ($maxIsNumeric) {
                            $q->orWhere('total_amount', '<=', (float)$maxRange);
                        }
                    }
                });
            }

            // Apply date range filters to invoices
            if ($startDate || $endDate) {
                
                if ($startDate && $endDate) {
                    $invoices->whereDate('created_at', '>=', $startDate)
                        ->whereDate('created_at', '<=', $endDate);
                } elseif ($startDate && !$endDate) {
                    $invoices->whereDate('created_at', '>=', $startDate);
                } elseif (!$startDate && $endDate) {
                    $invoices->whereDate('created_at', '<=', $endDate);
                }
            }
            
            // Apply hour range filters to invoices
            if ($hour_range) {
               
                $ranges = is_array($hour_range) ? $hour_range : [$hour_range];
                $invoices->where(function ($q) use ($ranges) {
                    foreach ($ranges as $r) {
                        switch ($r) {
                            case 'today':
                                $q->orWhereDate('created_at', Carbon::today()->toDateString());
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

            $invoices = $invoices->paginate(10);
            
            return $invoices;
        }
        return collect([]);
    }

    private function attachEarnings(&$paginator, $earnFrom, $referenceKey,$sortColumn ='created_at', $sortDirection = 'desc',$hour_range,$search,$hasPriceRanges,$minRange,$maxRange,$hasSlider,$startDate,$endDate,$price_range)
    {
        $records = $paginator->getCollection();
        $referenceIds = $records->pluck($referenceKey)->filter()->unique()->toArray();
        
        
        // First, let's check what's actually in the UserEarningsHistory table
        $allEarnings = UserEarningsHistory::whereIn('reference_id', $referenceIds)->get();
        
        $earningsQuery = UserEarningsHistory::where('earn_from', $earnFrom)
            ->whereIn('reference_id', $referenceIds);

       
        $earnings = $earningsQuery->get()->keyBy('reference_id');
        
        // Let's also check the actual structure of the first record
        if ($records->first()) {
            $firstRecord = $records->first();
            $caseInvoiceId = $firstRecord->caseInvoice ? $firstRecord->caseInvoice->id : 'N/A';
        }
        
        foreach ($records as $record) {
            $refId = data_get($record, $referenceKey);
           
            $earning = $earnings->get($refId);
            
            $record->user_earn_amount = $earning->user_earn_amount ?? 0;
            $record->platform_fees_amount = $earning->platform_fees_amount ?? 0;
        }

        // Skip sorting if no column provided
    if (!$sortColumn) {
        return;
    }

    // Check if the column exists on at least one record
    $hasColumn = $records->first() && property_exists($records->first(), $sortColumn) || isset($records->first()->{$sortColumn});

    if ($hasColumn) {
        // Sort the collection dynamically by any property
        $records = $sortDirection === 'asc'
            ? $records->sortBy($sortColumn)
            : $records->sortByDesc($sortColumn);

        // Reassign the re-indexed collection back
        $records = $records->values();
    }
    }

    public function earningReportGetAjaxList(Request $request)
    {   

        $status = $request->input("status");
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'error' => 'User not authenticated'
            ], 401);
        }

       

        try {
            $hour_range = $request->input('hour_range');
            $search = $request->input('search');
            $hasPriceRanges = $request->filled('price_range');
            $minRange = $request->filled('min_range') && $request->input('min_range') !== '' ? $request->input('min_range') : null;
            $maxRange = $request->filled('max_range') && $request->input('max_range') !== '' ? $request->input('max_range') : null;
            $hasSlider = $minRange !== null || $maxRange !== null;
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $price_range = $request->price_range;
            
            $records = $this->getRecords($user, $status ?? 'case', $hasPriceRanges, $price_range, $minRange, $maxRange, $hasSlider, $startDate, $endDate, $hour_range, $search);
            $earnFrom = ($status === 'case') ? 'case_fees' : 'appointment_fees';
            $referenceKey = ($status === 'case') ? 'caseInvoice.id' : 'appointmentInvoice.id';
            $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
            $sortDirection = $request->input('sort_direction', 'asc');
            
            $this->attachEarnings($records, $earnFrom, $referenceKey,$sortColumn,$sortDirection,$hour_range,$search,$hasPriceRanges,$minRange,$maxRange,$hasSlider,$startDate,$endDate,$price_range);

            $viewData['records'] = $records;
            $view = View::make('admin-panel.09-utilities.earning-reports.case-ajax-list', $viewData);
            $contents = $view->render();

            return response()->json([
                'contents' => $contents,
                'last_page' => $records->lastPage(),
                'current_page' => $records->currentPage(),
                'total_records' => $records->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


    public function earningAppointmentReport($status = 'appointment')
    {
        $viewData['pageTitle'] = "Professionals Earn Reports";
        $viewData['status'] = $status;
        return view('admin-panel.09-utilities.earning-reports.appointment-lists', $viewData);
    }

    public function earningAppointmentReportGetAjaxList(Request $request)
    {
        $status = $request->input("status");
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'error' => 'User not authenticated'
            ], 401);
        }

        try {
            $hour_range = $request->input('hour_range');
            $search = $request->input('search');
            $hasPriceRanges = $request->filled('price_range');
            $minRange = $request->filled('min_range') && $request->input('min_range') !== '' ? $request->input('min_range') : null;
            $maxRange = $request->filled('max_range') && $request->input('max_range') !== '' ? $request->input('max_range') : null;
            $hasSlider = $minRange !== null || $maxRange !== null;
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $price_range = $request->price_range;
            
            $records = $this->getRecords($user,'appointment', $hasPriceRanges, $price_range, $minRange, $maxRange, $hasSlider, $startDate, $endDate, $hour_range, $search);
            $earnFrom = 'appointment_fees';
            $referenceKey = 'appointmentInvoice.id';
            $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
            $sortDirection = $request->input('sort_direction', 'asc');
            
            $this->attachEarnings($records, $earnFrom, $referenceKey,$sortColumn,$sortDirection,$hour_range,$search,$hasPriceRanges,$minRange,$maxRange,$hasSlider,$startDate,$endDate,$price_range);

            $viewData['records'] = $records;
            $view = View::make('admin-panel.09-utilities.earning-reports.appointment-ajax-list', $viewData);
            $contents = $view->render();

            return response()->json([
                'contents' => $contents,
                'last_page' => $records->lastPage(),
                'current_page' => $records->currentPage(),
                'total_records' => $records->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }



    public function earningGlobalInvoice($status = 'other')
    {
        $viewData['pageTitle'] = "Global Invoice Earn Reports";
        $viewData['status'] = $status;
        return view('admin-panel.09-utilities.earning-reports.global-invoice-lists', $viewData);
    }

    public function earningGlobalInvoiceReportGetAjaxList(Request $request)
    {
        $status = $request->input("status");
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'error' => 'User not authenticated'
            ], 401);
        }

        try {
            $hour_range = $request->input('hour_range');
            $search = $request->input('search');
            $hasPriceRanges = $request->filled('price_range');
            $minRange = $request->filled('min_range') && $request->input('min_range') !== '' ? $request->input('min_range') : null;
            $maxRange = $request->filled('max_range') && $request->input('max_range') !== '' ? $request->input('max_range') : null;
            $hasSlider = $minRange !== null || $maxRange !== null;
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $price_range = $request->price_range;
            
            $records = $this->getRecords($user, $status ?? 'other', $hasPriceRanges, $price_range, $minRange, $maxRange, $hasSlider, $startDate, $endDate, $hour_range, $search);
            $earnFrom = 'general_invoice_fees';
            $referenceKey = 'id';
            $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
            $sortDirection = $request->input('sort_direction', 'asc');
            
            $this->attachEarnings($records, $earnFrom, $referenceKey,$sortColumn,$sortDirection,$hour_range,$search,$hasPriceRanges,$minRange,$maxRange,$hasSlider,$startDate,$endDate,$price_range);

            $viewData['records'] = $records;
            $view = View::make('admin-panel.09-utilities.earning-reports.global-invoice-ajax-list', $viewData);
            $contents = $view->render();

            return response()->json([
                'contents' => $contents,
                'last_page' => $records->lastPage(),
                'current_page' => $records->currentPage(),
                'total_records' => $records->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
