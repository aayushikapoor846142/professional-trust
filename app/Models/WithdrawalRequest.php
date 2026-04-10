<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WithdrawalRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'withdrawal_requests';

    protected $fillable = [
        'unique_id',
        'user_id',
        'amount',
        'currency',
        'banking_detail_id',
        'status',
        'request_date',
        'processed_date',
        'file_upload',
        'description',
        'admin_notes',
        'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_date' => 'datetime',
        'processed_date' => 'datetime',
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

    /**
     * Get the user that owns the withdrawal request
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the banking details for this withdrawal request
     */
    public function bankingDetail()
    {
        return $this->belongsTo(UserBankingDetails::class, 'banking_detail_id');
    }

    /**
     * Get the admin who processed the request
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope to get pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to get requests for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            'processing' => '<span class="badge bg-info">Processing</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute()
    {
        if ($this->file_upload) {
            return downloadMediaFile($this->file_upload, 'withdrawal-requests');
        }
        return null;
    }
} 