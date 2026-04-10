<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBankingDetails extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'user_banking_details';

    protected $fillable = [
        'unique_id',
        'user_id',
        'bank_name',
        'account_holder_name',
        'account_number',
        'routing_number',
        'swift_code',
        'iban',
        'bank_address',
        'city',
        'state',
        'country',
        'zip_code',
        'is_active',
        'account_type',
        'currency',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
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
     * Get the user that owns the banking details
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active banking details
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get banking details for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the active banking details for a user
     */
    public static function getActiveForUser($userId)
    {
        return static::where('user_id', $userId)
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Set this banking detail as active and deactivate others for the same user
     */
    public function setAsActive()
    {
        // Deactivate all other banking details for this user
        static::where('user_id', $this->user_id)
              ->where('id', '!=', $this->id)
              ->update(['is_active' => false]);

        // Activate this one
        $this->update(['is_active' => true]);
    }
} 