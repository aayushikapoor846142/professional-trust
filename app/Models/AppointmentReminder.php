<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AppointmentReminder extends Model
{
    protected $table = 'appointment_reminders';
    use HasFactory;
    protected $fillable=['status','unique_id','appointment_id','reminder_date','reminder_time'];
 
    public function appointment()
    {
        return $this->belongsTo('App\Models\AppointmentBooking','appointment_id','id');
    }
  
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    

}
