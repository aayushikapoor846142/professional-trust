<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class ReviewReplies extends BaseModel
{
    use HasFactory,SoftDeletes;
    protected $table="review_replies";
    protected $fillable = ['unique_id', 'professional_id','reply','review_id'];

    protected $encodedAttributes = ['unique_id', 'professional_id','reply','review_id'];
}
