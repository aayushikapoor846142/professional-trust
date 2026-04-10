<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unique_id',
        'name',
        'description',
        'color',
        'is_active',
        'sort_order',
        'added_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
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

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
    }

    public function getTicketCountAttribute()
    {
        return $this->tickets()->count();
    }

    public function getOpenTicketCountAttribute()
    {
        return $this->tickets()->whereIn('status', ['open', 'in_progress'])->count();
    }
} 