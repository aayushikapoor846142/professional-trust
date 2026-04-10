<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalAgreementComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agreement_id',
        'user_id',
        'parent_id',
        'comment',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the agreement that owns the comment.
     */
    public function agreement()
    {
        return $this->belongsTo(ProfessionalAssociateAgreement::class, 'agreement_id');
    }

    /**
     * Get the user who made the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment (for replies).
     */
    public function parent()
    {
        return $this->belongsTo(ProfessionalAgreementComment::class, 'parent_id');
    }

    /**
     * Get the replies to this comment.
     */
    public function replies()
    {
        return $this->hasMany(ProfessionalAgreementComment::class, 'parent_id')->where('status', 'active');
    }

    /**
     * Get all replies recursively (nested).
     */
    public function allReplies()
    {
        return $this->replies()->with('allReplies', 'user');
    }

    /**
     * Check if comment has replies.
     */
    public function hasReplies()
    {
        return $this->replies()->count() > 0;
    }

    /**
     * Get the comment level (0 for main comment, 1 for reply, etc.).
     */
    public function getLevelAttribute()
    {
        $level = 0;
        $parent = $this->parent;
        
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        
        return $level;
    }

    /**
     * Scope to get only active comments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only main comments (not replies).
     */
    public function scopeMainComments($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get only replies.
     */
    public function scopeReplies($query)
    {
        return $query->whereNotNull('parent_id');
    }
}
