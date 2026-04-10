<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMembers extends Model
{
	protected $table = "group_members";
    use HasFactory,SoftDeletes;
	protected $fillable = ['unique_id', 'group_id', 'user_id', 'is_admin', 'added_by'];

	protected $dates = ['deleted_at'];

    public function group()
	{
	    return $this->belongsTo(ChatGroup::class,'group_id');
	}

	public function member()
	{
	    return $this->belongsTo(User::class,'user_id');
	}

	protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            $object->unique_id = randomNumber();
        });

        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

}
