<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AppointmentStatus extends Model
{
    protected $table = 'appointment_statuses';
    use HasFactory;
    protected $fillable=['status','unique_id','appointment_id','status_date'];
 

    public function appointment()
    {
        return $this->belongsTo('App\Models\AppointmentBooking','appointment_id','id');
    }
  
    public function professional()
    {
        return $this->belongsTo('App\Models\User','professional_id','id');
    }
    public function client()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
        


}
