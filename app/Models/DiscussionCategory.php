<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscussionCategory extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'discussion_category';

    protected $fillable = [
        'name',
        'image',
        'description',
        'added_by',
        'status'
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
    
    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    static function deleteRecord($id,$file_name)
    {
        $cat_path = categoryDir();
        mediaDeleteApi($cat_path,$file_name);
        DiscussionCategory::where("id", $id)->delete();
    }

    public function discussionThreads()
    {
        return $this->hasMany(DiscussionBoard::class, 'category_id');
    }
}
