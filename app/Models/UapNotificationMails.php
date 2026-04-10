<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class UapNotificationMails extends BaseModel
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','uap_id','mail_sent_on','mail_content','mail_status','mail_response','mail_tracking','next_mail_date','next_mail_sequence'];

    protected $encodedAttributes = ['unique_id','uap_id','mail_sent_on','mail_content','mail_status','mail_response','mail_tracking','next_mail_date','next_mail_sequence'];
}

