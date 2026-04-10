<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_id',
        'ticket_id',
        'user_id',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'description',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($object) {
            $object->unique_id = randomNumber();
        });
        static::updating(function ($object) {
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByField($query, $fieldName)
    {
        return $query->where('field_name', $fieldName);
    }

    public function getActionTextAttribute()
    {
        $actions = [
            'created' => 'Ticket Created',
            'status_changed' => 'Status Changed',
            'priority_changed' => 'Priority Changed',
            'assigned' => 'Ticket Assigned',
            'replied' => 'Reply Added',
            'attachment_added' => 'Attachment Added',
            'escalated' => 'Ticket Escalated',
            'resolved' => 'Ticket Resolved',
            'closed' => 'Ticket Closed',
            'reopened' => 'Ticket Reopened',
            'note_added' => 'Internal Note Added',
        ];
        
        return $actions[$this->action] ?? ucfirst(str_replace('_', ' ', $this->action));
    }

    public function getActionIconAttribute()
    {
        $icons = [
            'created' => 'fa-plus-circle',
            'status_changed' => 'fa-exchange-alt',
            'priority_changed' => 'fa-flag',
            'assigned' => 'fa-user-plus',
            'replied' => 'fa-comment',
            'attachment_added' => 'fa-paperclip',
            'escalated' => 'fa-exclamation-triangle',
            'resolved' => 'fa-check-circle',
            'closed' => 'fa-times-circle',
            'reopened' => 'fa-undo',
            'note_added' => 'fa-sticky-note',
        ];
        
        return $icons[$this->action] ?? 'fa-info-circle';
    }

    public function getActionBadgeAttribute()
    {
        $badges = [
            'created' => 'badge bg-success',
            'status_changed' => 'badge bg-info',
            'priority_changed' => 'badge bg-warning',
            'assigned' => 'badge bg-primary',
            'replied' => 'badge bg-secondary',
            'attachment_added' => 'badge bg-dark',
            'escalated' => 'badge bg-danger',
            'resolved' => 'badge bg-success',
            'closed' => 'badge bg-dark',
            'reopened' => 'badge bg-warning',
            'note_added' => 'badge bg-info',
        ];
        
        return $badges[$this->action] ?? 'badge bg-secondary';
    }

    public function getFormattedOldValueAttribute()
    {
        if ($this->field_name === 'status') {
            return ucfirst(str_replace('_', ' ', $this->old_value));
        }
        
        if ($this->field_name === 'priority') {
            return ucfirst($this->old_value);
        }
        
        return $this->old_value;
    }

    public function getFormattedNewValueAttribute()
    {
        if ($this->field_name === 'status') {
            return ucfirst(str_replace('_', ' ', $this->new_value));
        }
        
        if ($this->field_name === 'priority') {
            return ucfirst($this->new_value);
        }
        
        return $this->new_value;
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getFormattedDateAttribute()
    {
        return $this->created_at->format('M d, Y \a\t g:i A');
    }
} 