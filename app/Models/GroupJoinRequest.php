<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class GroupJoinRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'group_join_request';
    protected $fillable = [
        'unique_id',
        'group_id',
        'requested_by',
        'accepted_by',
        'status',
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

    public function group()
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    public function accepter()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    static function deleteRecord($id)
    {
        GroupJoinRequest::where("unique_id", $id)->delete();

    }
}
