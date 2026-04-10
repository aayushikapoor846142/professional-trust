<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ChatNotification extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'chat_notifications';
    protected $fillable = [
        'unique_id',
        'comment',
        'type',
        'redirect_link',
        'is_read',
        'user_id',
        'send_by',
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

    public function sender()
    {
        return $this->belongsTo(User::class, 'send_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function case()
    {
        return $this->belongsTo(Cases::class, 'redirect_link', 'unique_id');
    }
}
