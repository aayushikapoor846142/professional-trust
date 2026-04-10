<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseChat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['unique_id','case_id', 'group_chat_id','added_by'];

    public function groupChat() {
        return $this->belongsTo(ChatGroup::class, 'group_chat_id');
    }
}
