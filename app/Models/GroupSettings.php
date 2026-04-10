<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupSettings extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "group_settings";
    protected $fillable = [
        'group_id',
        'only_admins_can_post',
        'members_can_add_members',
         'unique_id',
        'who_can_see_my_message',
    ];
    protected $encodedAttributes = [
        'group_id',
        'unique_id',
        'only_admins_can_post',
        'members_can_add_members',
        'who_can_see_my_message',
    ];

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
