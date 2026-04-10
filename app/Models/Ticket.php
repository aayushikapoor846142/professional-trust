<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_id',
        'ticket_number',
        'subject',
        'description',
        'priority',
        'status',
        'category_id',
        'user_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'resolved_at',
        'closed_at',
        'response_time',
        'resolution_time',
        'is_urgent',
        'is_escalated',
        'custom_fields'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_urgent' => 'boolean',
        'is_escalated' => 'boolean',
        'custom_fields' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($object) {
            $object->unique_id = randomNumber();
            // Only generate ticket_number if it's not already set
            if (empty($object->ticket_number)) {
                $object->ticket_number = self::generateTicketNumber();
            }
        });
        static::updating(function ($object) {
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

    public static function generateTicketNumber()
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
            $lastTicket = self::where('ticket_number', 'like', $prefix . $year . $month . '%')
                ->orderBy('ticket_number', 'desc')
                ->first();
            
            if ($lastTicket) {
                $lastNumber = (int) substr($lastTicket->ticket_number, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            $ticketNumber = $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            
            // Check if this ticket number already exists
            $exists = self::where('ticket_number', $ticketNumber)->exists();
            
            if (!$exists) {
                return $ticketNumber;
            }
            
            // If it exists, try the next number
            $newNumber++;
            
        } while ($attempt < $maxAttempts);
        
        // If we still can't find a unique number, use timestamp
        return $prefix . $year . $month . str_pad(time() % 10000, 4, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    public function publicReplies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')
            ->where('is_public', true)
            ->orderBy('created_at', 'asc');
    }

    public function lastReply()
    {
        return $this->hasOne(TicketReply::class, 'ticket_id')->latest();
    }

    public function histories()
    {
        return $this->hasMany(TicketHistory::class, 'ticket_id')->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    public function scopeEscalated($query)
    {
        return $query->where('is_escalated', true);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'open' => 'badge bg-warning',
            'in_progress' => 'badge bg-info',
            'waiting_for_customer' => 'badge bg-secondary',
            'resolved' => 'badge bg-success',
            'closed' => 'badge bg-dark'
        ];
        
        return $badges[$this->status] ?? 'badge bg-secondary';
    }

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low' => 'badge bg-success',
            'medium' => 'badge bg-warning',
            'high' => 'badge bg-danger',
            'urgent' => 'badge bg-danger'
        ];
        
        return $badges[$this->priority] ?? 'badge bg-secondary';
    }

    public function getPriorityTextAttribute()
    {
        return ucfirst($this->priority);
    }

    public function getStatusTextAttribute()
    {
        return str_replace('_', ' ', ucfirst($this->status));
    }

    public function getIsOverdueAttribute()
    {
        if ($this->status === 'open' || $this->status === 'in_progress') {
            $hours = $this->priority === 'urgent' ? 2 : 
                    ($this->priority === 'high' ? 24 : 
                    ($this->priority === 'medium' ? 72 : 168));
            
            return $this->created_at->addHours($hours)->isPast();
        }
        return false;
    }

    public function getLastReplyAttribute()
    {
        return $this->replies()->latest()->first();
    }

    public function getLastPublicReplyAttribute()
    {
        return $this->publicReplies()->latest()->first();
    }

    // Methods
    public function assignTo($userId, $assignedBy = null)
    {
        $this->update([
            'assigned_to' => $userId,
            'assigned_by' => $assignedBy ?? auth()->id(),
            'assigned_at' => now(),
        ]);

        $user = User::find($userId);
        $userName = $user ? ($user->first_name . ' ' . $user->last_name) : 'Unknown User';
        $this->addHistory('assigned', 'Ticket assigned to ' . $userName);
    }

    public function updateStatus($status, $userId = null)
    {
        $oldStatus = $this->status;
        $this->update(['status' => $status]);

        if ($status === 'resolved') {
            $this->update(['resolved_at' => now()]);
        } elseif ($status === 'closed') {
            $this->update(['closed_at' => now()]);
        }

        $this->addHistory('status_changed', "Status changed from {$oldStatus} to {$status}", 'status', $oldStatus, $status);
    }

    public function updatePriority($priority, $userId = null)
    {
        $oldPriority = $this->priority;
        $this->update(['priority' => $priority]);

        $this->addHistory('priority_changed', "Priority changed from {$oldPriority} to {$priority}", 'priority', $oldPriority, $priority);
    }

    public function addHistory($action, $description, $fieldName = null, $oldValue = null, $newValue = null)
    {
        $this->histories()->create([
            'unique_id' => randomNumber(),
            'user_id' => auth()->id(),
            'action' => $action,
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'description' => $description,
        ]);
    }

    public function calculateResponseTime()
    {
        $firstReply = $this->replies()->where('reply_type', 'admin')->first();
        if ($firstReply) {
            $responseTime = $this->created_at->diffInMinutes($firstReply->created_at);
            $this->update(['response_time' => $responseTime]);
        }
    }

    public function calculateResolutionTime()
    {
        if ($this->resolved_at) {
            $resolutionTime = $this->created_at->diffInMinutes($this->resolved_at);
            $this->update(['resolution_time' => $resolutionTime]);
        }
    }

          public function isEditableBy($userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value(column: 'added_by');

        if ($professionalId) {
            // Staff: can only edit their own records (not professional's)
            return $this->user_id == $userId;
        } else {
            // Professional: can edit their own and their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id')->toArray();
            return $this->user_id == $userId || in_array($this->user_id, $staffIds);
        }
    }
} 