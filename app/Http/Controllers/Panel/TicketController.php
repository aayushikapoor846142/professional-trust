<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketReply;
use App\Models\TicketAttachment;
use Auth;
use App\Services\TicketService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function index(Request $request)
    {
        $viewData['pageTitle'] = "My Support Tickets";
        $viewData['categories'] = TicketCategory::active()->ordered()->get();

        $viewData['ticket'] = Ticket::where('user_id',auth()->user()->id)->where('status','open')->orWhere('status','in_progress')->orWhere('status','waiting_for_customer')->orWhere('status','resolved')->get()->count();

        return view('admin-panel.20-support.tickets.lists', $viewData);
    }

    public function getAjaxList(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $has_attachment = $request->has_attachments;
        $query = Ticket::where('user_id', Auth::id());
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->whereIn('status', $request->status);
        }

        if ($request->boolean('unassigned')) {
            $query->where(function ($q) {
                $q->whereNull('assigned_to')
                  ->orWhere('assigned_to', 0);
            });
        }
        if ($request->boolean('has_attachments')) {
            $query->whereHas('attachments');
        }

        if ($request->filled('priority')) {
            $query->whereIn('priority', $request->priority);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Date range filtering on created_at
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
        $sortColumn = $request->get('sort_column', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortColumn, $sortDirection);
        $records = $query->paginate($request->get('per_page', 10));
        $viewData['records'] = $records;
        $view = \View::make('admin-panel.20-support.tickets.ajax-list', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['last_page'] = $records->lastPage();
        $response['current_page'] = $records->currentPage();
        $response['total_records'] = $records->total();
        return response()->json($response);
    }

    public function create()
    {
        $categories = TicketCategory::active()->ordered()->get();
        return view('admin-panel.20-support.tickets.create', compact('categories'));
    }

    public function createModal()
    {
        $ticket = Ticket::where('user_id',auth()->user()->id)->where('status','open')->orWhere('status','in_progress')->orWhere('status','waiting_for_customer')->orWhere('status','resolved')->get()->count();
        if ($ticket >= 5) {
            $response['status'] = false;
            $response['message'] = "You have reached the maximum limit of 5 open tickets. Please close a ticket before creating a new one.";
            return response()->json($response);
        }

        $categories = TicketCategory::active()->ordered()->get();
        $viewData['categories'] = $categories;
        $viewData['pageTitle'] = "Raise a Support Ticket";
        $view = \View::make('admin-panel.20-support.tickets.create-modal', $viewData);
        $contents = $view->render();
        $response['contents'] = $contents;
        $response['status'] = true;
        return response()->json($response);
    }

     public function store(Request $request)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|exists:ticket_categories,id',
                'priority' => 'required|in:low,medium,high,urgent',
            ]);
            
            $data = [
                'subject' => $request->subject,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'priority' => $request->priority,
                // 'attachments' => $request->file('attachments', []), // Uncomment if attachments supported
            ];
            
            $userId = \Auth::id();
            $ticket = $this->ticketService->createTicket($data, $userId);
            
            // Check if it's an AJAX request
            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Ticket created successfully.',
                    'ticket' => $ticket
                ]);
            }
            
            return redirect()->route('panel.tickets.index')->with('success', 'Ticket created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Ticket creation error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'An error occurred while creating the ticket. Please try again.'
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'An error occurred while creating the ticket. Please try again.')
                ->withInput();
        }
    }

    public function view($id)
    {
        $ticket = $this->ticketService->findByUniqueId($id);
        

        if (!$ticket) {
            return redirect()->back()->with('error', 'Ticket not found');
        }
    
        $viewData['ticket'] = $ticket;
        $viewData['pageTitle'] = "Ticket #{$ticket->ticket_number}";
        $viewData['categories'] = TicketCategory::active()->ordered()->get();
        
        return view('admin-panel.20-support.tickets.view', $viewData);
    }

    public function addReply(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'is_internal' => 'boolean',
            'update_status' => 'nullable|in:open,in_progress,waiting_for_customer,resolved,closed',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $ticket = $this->ticketService->findByUniqueId($id);
        
        if (!$ticket) {
            return response()->json([
                'status' => false,
                'message' => 'Ticket not found'
            ]);
        }

        try {
            $replyData = [
                'message' => $request->message,
                'reply_type' => 'user',
                'is_internal' => $request->boolean('is_internal'),
                'is_public' => !$request->boolean('is_internal'),
                'update_status' => $request->update_status,
            ];

            // Handle file uploads with better error handling
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                $validFiles = [];
                
                // Ensure it's always an array and validate each file
                $fileArray = is_array($files) ? $files : [$files];
                
                foreach ($fileArray as $file) {
                    if ($file && $file->isValid()) {
                        $validFiles[] = $file;
                    } else {
                        \Log::warning('Invalid file upload attempt in admin reply: ' . ($file ? $file->getClientOriginalName() : 'null'));
                    }
                }
                
                if (!empty($validFiles)) {
                    $replyData['attachments'] = $validFiles;
                }
            }

            $reply = $this->ticketService->addReply($ticket, $replyData, auth()->id());

            return response()->json([
                'status' => true,
                'message' => 'Reply added successfully',
                'reply' => $reply
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error adding reply: ' . $e->getMessage()
            ]);
        }
    }
} 