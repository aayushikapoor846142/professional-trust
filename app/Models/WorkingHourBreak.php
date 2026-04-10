<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHourBreak extends Model
{
    use HasFactory;
    protected $table = "working_hour_breaks";

    public function workingHour()
    {
        return $this->belongsTo(WorkingHours::class);
    }

}
