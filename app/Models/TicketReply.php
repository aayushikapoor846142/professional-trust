<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_id',
        'ticket_id',
        'user_id',
        'message',
        'reply_type',
        'is_internal',
        'is_public',
        'metadata'
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_public' => 'boolean',
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

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'reply_id');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('reply_type', $type);
    }

    public function getReplyTypeTextAttribute()
    {
        return ucfirst($this->reply_type);
    }

    public function getReplyTypeBadgeAttribute()
    {
        $badges = [
            'user' => 'badge bg-primary',
            'admin' => 'badge bg-success',
            'system' => 'badge bg-secondary'
        ];
        
        return $badges[$this->reply_type] ?? 'badge bg-secondary';
    }

    public function getFormattedMessageAttribute()
    {
        // Convert line breaks to HTML
        $message = nl2br(e($this->message));
        
        // Convert URLs to clickable links
        $message = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
            $message
        );
        
        return $message;
    }

    public function getShortMessageAttribute()
    {
        return \Str::limit(strip_tags($this->message), 100);
    }

    public function isFromAdmin()
    {
        return $this->reply_type === 'admin';
    }

    public function isFromUser()
    {
        return $this->reply_type === 'user';
    }

    public function isSystemMessage()
    {
        return $this->reply_type === 'system';
    }
} 