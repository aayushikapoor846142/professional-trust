<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionCommentLike extends Model
{
    use HasFactory;
    protected $fillable = [
        'comment_id',
        'discussion_board_id',
        'comment_icon',
        'user_id',
    ];
}
