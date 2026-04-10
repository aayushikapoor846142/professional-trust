<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketReply;
use App\Models\TicketAttachment;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class TicketService
{
    /**
     * Get paginated tickets with filters
     */
    public function getPaginatedTickets(Request $request): LengthAwarePaginator
    {
        $query = Ticket::with(['category', 'user', 'assignedTo', 'lastReply']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'LIKE', "%{$search}%")
                  ->orWhere('subject', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('email', 'LIKE', "%{$search}%")
                               ->orWhere('first_name', 'LIKE', "%{$search}%")
                               ->orWhere('last_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }

        // Filter by assigned user
        if ($request->filled('assigned_to')) {
            $query->assignedTo($request->assigned_to);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter urgent tickets
        if ($request->boolean('urgent_only')) {
            $query->urgent();
        }

        // Filter overdue tickets
        if ($request->boolean('overdue_only')) {
            $query->where(function ($q) {
                $q->whereIn('status', ['open', 'in_progress'])
                  ->where(function ($subQ) {
                      $subQ->where('priority', 'urgent')
                           ->where('created_at', '<=', Carbon::now()->subHours(2))
                           ->orWhere('priority', 'high')
                           ->where('created_at', '<=', Carbon::now()->subHours(24))
                           ->orWhere('priority', 'medium')
                           ->where('created_at', '<=', Carbon::now()->subHours(72))
                           ->orWhere('priority', 'low')
                           ->where('created_at', '<=', Carbon::now()->subHours(168));
                  });
            });
        }

        // Sorting
        $sortColumn = $request->get('sort_column', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortColumn, $sortDirection);

        return $query->paginate($request->get('per_page', 15));
    }

    /**
     * Generate a unique ticket number
     */
    private function generateUniqueTicketNumber(): string
    {
        $prefix = 'TKT';
        $year = date('Y');
        $month = date('m');
        
        // Use a loop to handle race conditions
        $maxAttempts = 10;
        $attempt = 0;
        
        do {
            $attempt++;
            
            // Get the last ticket number for this month
            $lastTicket = Ticket::where('ticket_number', 'like', $prefix . $year . $month . '%')
                ->orderBy('ticket_number', 'desc')
                ->first();
            
            if ($lastTicket) {
                $lastNumber = (int) substr($lastTicket->ticket_number, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $ticketNumber = $prefix . $year . $month . rand(100000,99999999);
            
            // Check if this ticket number already exists
            $exists = Ticket::where('ticket_number', $ticketNumber)->exists();
            
            if (!$exists) {
                return $ticketNumber;
            }
            
            // If it exists, try the next number
            $newNumber++;
            
        } while ($attempt < $maxAttempts);
        
        // If we still can't find a unique number, use timestamp
        return $prefix . $year . $month . str_pad(time() % 10000, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new ticket
     */
    public function createTicket(array $data, $userId): Ticket
    {
        DB::beginTransaction();
        try {
            // Validate required data
            if (empty($data['subject']) || empty($data['description']) || empty($data['category_id'])) {
                throw new \Exception('Missing required ticket data');
            }

            // Generate a unique ticket number first
            $ticketNumber = $this->generateUniqueTicketNumber();
            
            // Create the ticket with the pre-generated ticket number
            $ticket = Ticket::create([
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? 'medium',
                'category_id' => $data['category_id'],
                'user_id' => $userId,
                'status' => 'open',
                'ticket_number' => $ticketNumber,
                'custom_fields' => $data['custom_fields'] ?? null,
            ]);

            // Add initial history
            try {
                $user = User::find($userId);
                $userName = $user ? ($user->first_name . ' ' . $user->last_name) : 'Unknown User';
                $ticket->addHistory('created', 'Ticket created by ' . $userName);
            } catch (\Exception $historyError) {
                \Log::warning('Failed to add ticket history: ' . $historyError->getMessage());
                // Don't fail the entire ticket creation for history issues
            }

            // Handle attachments
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    if ($attachment && $attachment->isValid()) {
                        try {
                            $this->uploadAttachment($ticket, $attachment, $userId);
                        } catch (\Exception $attachmentError) {
                            \Log::warning('Failed to upload attachment: ' . $attachmentError->getMessage());
                            // Don't fail the entire ticket creation for attachment issues
                        }
                    }
                }
            }

            DB::commit();
            return $ticket;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Ticket creation failed: ' . $e->getMessage(), [
                'data' => $data,
                'user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Update ticket
     */
    public function updateTicket(Ticket $ticket, array $data): bool
    {
        DB::beginTransaction();
        try {
            $oldData = $ticket->toArray();
            
            $ticket->update($data);

            // Track changes
            foreach ($data as $field => $value) {
                if (in_array($field, ['status', 'priority', 'assigned_to']) && $oldData[$field] != $value) {
                    $this->trackFieldChange($ticket, $field, $oldData[$field], $value);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Add reply to ticket
     */
    public function addReply(Ticket $ticket, array $data, $userId): TicketReply
    {
        DB::beginTransaction();
        try {
            $reply = $ticket->replies()->create([
                'user_id' => $userId,
                'message' => $data['message'],
                'reply_type' => $data['reply_type'] ?? 'user',
                'is_internal' => $data['is_internal'] ?? false,
                'is_public' => $data['is_public'] ?? true,
                'metadata' => $data['metadata'] ?? null,
            ]);

            // Add history
            $user = User::find($userId);
            $userName = $user ? ($user->first_name . ' ' . $user->last_name) : 'Unknown User';
            $ticket->addHistory('replied', 'Reply added by ' . $userName);

            // Handle attachments
            \Log::info('=== addReply attachments check ===');
            \Log::info('Attachments data:', [
                'has_attachments' => isset($data['attachments']),
                'is_array' => isset($data['attachments']) ? is_array($data['attachments']) : 'N/A',
                'count' => isset($data['attachments']) ? count($data['attachments']) : 'N/A'
            ]);
            
            if (isset($data['attachments']) && is_array($data['attachments'])) {
                foreach ($data['attachments'] as $index => $attachment) {
                    \Log::info("Processing attachment {$index}:", [
                        'attachment_exists' => $attachment ? 'yes' : 'no',
                        'is_valid' => $attachment ? $attachment->isValid() : 'N/A',
                        'original_name' => $attachment ? $attachment->getClientOriginalName() : 'N/A'
                    ]);
                    
                    if ($attachment && $attachment->isValid()) {
                        try {
                            $this->uploadAttachment($ticket, $attachment, $userId, $reply->id);
                        } catch (\Exception $attachmentError) {
                            \Log::warning('Failed to upload attachment: ' . $attachmentError->getMessage());
                            // Don't fail the entire ticket creation for attachment issues
                        }
                    }
                }
            }

            // Update ticket status if needed
            if (isset($data['update_status'])) {
                $ticket->updateStatus($data['update_status']);
            }

            // Calculate response time for first admin reply
            if ($reply->reply_type === 'admin' && !$ticket->response_time) {
                $ticket->calculateResponseTime();
            }

            DB::commit();

            // Websocket: Notify ticket owner of new reply (TicketSystemSocket)
            if (!function_exists('initTicketSystemSocket')) {
                require_once app_path('Helper/SocketHelper.php');
            }
            $ticketAssignedTo = $ticket->assigned_to;

            $ticketOwnerId = $ticket->user_id;
            $adminId = User::where('role', 'admin')->first()->id ?? null;
            
            if ($ticketOwnerId && function_exists('initTicketSystemSocket')) {
                try {
                    // Render reply HTML using a Blade view
                    $replyHtml = view('admin-panel.20-support.tickets._reply', ['reply' => $reply])->render();
                    \Log::info('Reply HTML rendered successfully. Length: ' . strlen($replyHtml));
                    
                    $socketData = [
                        'action' => 'ticket_reply',
                        'ticket_id' => $ticket->unique_id,
                        'reply_id' => $reply->id,
                        'reply_html' => $replyHtml,
                    ];
                    
                    initTicketSystemSocket($ticketAssignedTo, $socketData);
                    initTicketSystemSocket($adminId, $socketData);
                    initTicketSystemSocket($ticketOwnerId, $socketData);
                } catch (\Exception $e) {
                    \Log::error('Error in websocket dispatch: ' . $e->getMessage());
                }
            } else {
                \Log::warning('TicketSystemSocket not triggered. Owner ID: ' . $ticketOwnerId . ', Function exists: ' . (function_exists('initTicketSystemSocket') ? 'yes' : 'no'));
            }

            return $reply;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Upload attachment
     */
    public function uploadAttachment(Ticket $ticket, $file, $userId, $replyId = null): TicketAttachment
    {
        try {
            \Log::info('=== uploadAttachment START ===');
            \Log::info('File info:', [
                'original_name' => $file ? $file->getClientOriginalName() : 'null',
                'size' => $file ? $file->getSize() : 'null',
                'mime_type' => $file ? $file->getMimeType() : 'null',
                'is_valid' => $file ? $file->isValid() : 'null'
            ]);
            
            // Validate file first
            if (!$file || !$file->isValid()) {
                throw new \Exception('Invalid file upload');
            }

            // Get file info before moving
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();
            $extension = $file->getClientOriginalExtension() ?: 'file';
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            
            \Log::info('Processing file upload: ' . $originalName . ' (Size: ' . $fileSize . ', Type: ' . $mimeType . ')');
            
            // Get the current temporary path
            $tempPath = $file->getPathname();
            \Log::info('Original temp path: ' . $tempPath);
            
            // Check if temp file exists and is readable
            if (!file_exists($tempPath) || !is_readable($tempPath)) {
                throw new \Exception('Temporary file not accessible: ' . $tempPath);
            }
            
            $mediaPath = ticketDir();
            $response = null;
            $usedTempFile = false;
            
            // Try to upload directly from temp location first
            try {
                \Log::info('Attempting direct upload from temp location: ' . $tempPath);
                $response = mediaUploadApi("upload-file", $tempPath, $mediaPath, $fileName);
                \Log::info('Direct upload response: ' . json_encode($response));
                
                if (isset($response['status']) && $response['status'] == 'success') {
                    $usedTempFile = true;
                }
            } catch (\Exception $e) {
                \Log::warning('Direct upload failed, trying copy method: ' . $e->getMessage());
            }
            
            // If direct upload failed, try copying to temp directory first
            if (!$usedTempFile) {
                // Create temp directory if it doesn't exist
                $destinationPath = public_path('uploads/temp');
                if (!is_dir($destinationPath)) {
                    if (!mkdir($destinationPath, 0755, true)) {
                        throw new \Exception('Failed to create temp directory');
                    }
                }
                
                // Check if temp directory is writable
                if (!is_writable($destinationPath)) {
                    throw new \Exception('Temp directory is not writable: ' . $destinationPath);
                }
                
                // Try to copy the file
                $newPath = $destinationPath . '/' . $fileName;
                
                if (copy($tempPath, $newPath)) {
                    \Log::info('File copied successfully to: ' . $newPath);
                    
                    // Verify the copied file
                    if (!file_exists($newPath) || !is_readable($newPath)) {
                        throw new \Exception('Failed to verify copied file: ' . $newPath);
                    }
                    
                    \Log::info('Uploading to media server. Source: ' . $newPath . ', Media path: ' . $mediaPath);
                    
                    // Upload to media server using your API
                    $response = mediaUploadApi("upload-file", $newPath, $mediaPath, $fileName);
                    
                    \Log::info('Media upload response: ' . json_encode($response));
                    
                    // Clean up temp file
                    if (file_exists($newPath)) {
                        unlink($newPath);
                        \Log::info('Temp file cleaned up: ' . $newPath);
                    }
                } else {
                    $errorMessage = 'Failed to copy uploaded file to temp directory';
                    \Log::error($errorMessage . ' - From: ' . $tempPath . ' To: ' . $newPath);
                    throw new \Exception($errorMessage);
                }
            }
            
            if (isset($response['status']) && $response['status'] == 'success') {
                // Create attachment record
                $attachment = TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'reply_id' => $replyId, // This can be null for initial ticket attachments
                    'user_id' => $userId,
                    'file_name' => $fileName,
                    'original_name' => $originalName,
                    'file_path' => $fileName, // Store just filename, path handled by media server
                    'file_type' => $mimeType,
                    'file_size' => $fileSize,
                    'description' => null,
                ]);

                // Add history
                $ticket->addHistory('attachment_added', 'Attachment added: ' . $originalName);

                \Log::info('Attachment created successfully: ' . $attachment->id);
                return $attachment;
            } else {
                $errorMessage = 'Failed to upload file to media server: ' . ($response['message'] ?? 'Unknown error');
                \Log::error($errorMessage);
                throw new \Exception($errorMessage);
            }
        } catch (\Exception $e) {
            \Log::error('File upload error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' at line ' . $e->getLine());
            throw new \Exception('Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Assign ticket to user
     */
    public function assignTicket(Ticket $ticket, $assignedToId, $assignedById = null): bool
    {
        $assignedById = $assignedById ?? auth()->id();
        
        $ticket->assignTo($assignedToId, $assignedById);
        
        return true;
    }

    /**
     * Update ticket status
     */
    public function updateTicketStatus(Ticket $ticket, $status, $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        
        $ticket->updateStatus($status, $userId);
        
        if ($status === 'resolved') {
            $ticket->calculateResolutionTime();
        }
        
        return true;
    }

    /**
     * Get ticket statistics
     */
    public function getTicketStatistics(): array
    {
        $totalTickets = Ticket::count();
        $openTickets = Ticket::open()->count();
        $closedTickets = Ticket::closed()->count();
        $urgentTickets = Ticket::urgent()->count();
        $overdueTickets = Ticket::where(function ($query) {
            $query->whereIn('status', ['open', 'in_progress'])
                  ->where(function ($q) {
                      $q->where('priority', 'urgent')
                        ->where('created_at', '<=', Carbon::now()->subHours(2))
                        ->orWhere('priority', 'high')
                        ->where('created_at', '<=', Carbon::now()->subHours(24))
                        ->orWhere('priority', 'medium')
                        ->where('created_at', '<=', Carbon::now()->subHours(72))
                        ->orWhere('priority', 'low')
                        ->where('created_at', '<=', Carbon::now()->subHours(168));
                  });
        })->count();

        $avgResponseTime = Ticket::whereNotNull('response_time')->avg('response_time');
        $avgResolutionTime = Ticket::whereNotNull('resolution_time')->avg('resolution_time');

        return [
            'total_tickets' => $totalTickets,
            'open_tickets' => $openTickets,
            'closed_tickets' => $closedTickets,
            'urgent_tickets' => $urgentTickets,
            'overdue_tickets' => $overdueTickets,
            'avg_response_time' => round($avgResponseTime ?? 0, 2),
            'avg_resolution_time' => round($avgResolutionTime ?? 0, 2),
        ];
    }

    /**
     * Get tickets by category statistics
     */
    public function getTicketsByCategory(): array
    {
        return TicketCategory::withCount(['tickets', 'tickets as open_tickets' => function ($query) {
            $query->whereIn('status', ['open', 'in_progress']);
        }])->get()->toArray();
    }

    /**
     * Get tickets by status statistics
     */
    public function getTicketsByStatus(): array
    {
        return Ticket::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();
    }

    /**
     * Track field changes
     */
    private function trackFieldChange(Ticket $ticket, $field, $oldValue, $newValue): void
    {
        $fieldNames = [
            'status' => 'Status',
            'priority' => 'Priority',
            'assigned_to' => 'Assignment',
        ];

        $description = $fieldNames[$field] . ' changed from ' . $oldValue . ' to ' . $newValue;
        
        $ticket->addHistory(
            $field . '_changed',
            $description,
            $field,
            $oldValue,
            $newValue
        );
    }

    /**
     * Find ticket by unique ID
     */
    public function findByUniqueId($uniqueId): ?Ticket
    {
        return Ticket::with(['category', 'user', 'assignedTo', 'replies.user', 'attachments', 'histories.user'])
            ->where('unique_id', $uniqueId)
            ->first();
    }

    /**
     * Delete ticket
     */
    public function deleteTicket(Ticket $ticket): bool
    {
        DB::beginTransaction();
        try {
            // Delete attachments from media server
            foreach ($ticket->attachments as $attachment) {
                // You might want to implement a mediaDeleteApi function
                // For now, we'll just delete the database records
                // The media server might handle cleanup automatically
            }

            $ticket->delete();
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
} 