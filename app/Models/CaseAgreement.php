<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseAgreement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_id',
        'template_name',
        'agreement_content',
        'platform_fees',
        'status',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'platform_fees' => 'decimal:2'
    ];

    /**
     * Get the user who created the template
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the template
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for templates created by a specific user
     */
    public function scopeCreatedByUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Generate unique ID
     */
    public static function generateUniqueId()
    {
        do {
            $uniqueId = 'AG' . date('Y') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (static::where('unique_id', $uniqueId)->exists());

        return $uniqueId;
    }

    /**
     * Get available dynamic variables
     */
    public static function getAvailableVariables()
    {
        return [
            '{ASSOCIATE_NAME}' => 'Associate Name',
            '{PROFESSIONAL_NAME}' => 'Professional Name',
            '{SHARING_FEES}' => 'Sharing Fees Amount',
            '{PLATFORM_FEES}' => 'Platform Fees Amount',
            '{TOTAL_FEES}' => 'Total Fees (Sharing + Platform)',
            '{CURRENT_DATE}' => 'Current Date',
            '{AGREEMENT_ID}' => 'Agreement ID'
        ];
    }

    /**
     * Generate agreement with dynamic content
     */
    public function generateAgreement($associateName, $professionalName, $sharingFees)
    {
        $content = $this->agreement_content;
        $totalFees = $sharingFees + $this->platform_fees;
        
        $replacements = [
            '{ASSOCIATE_NAME}' => $associateName,
            '{PROFESSIONAL_NAME}' => $professionalName,
            '{SHARING_FEES}' => '$' . number_format($sharingFees, 2),
            '{PLATFORM_FEES}' => '$' . number_format($this->platform_fees, 2),
            '{TOTAL_FEES}' => '$' . number_format($totalFees, 2),
            '{CURRENT_DATE}' => date('F d, Y'),
            '{AGREEMENT_ID}' => $this->unique_id
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    /**
     * Check if template can be edited
     */
    public function canBeEdited()
    {
        return $this->status === 'active';
    }
}
