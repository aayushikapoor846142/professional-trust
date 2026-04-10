<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserEarningsHistory extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'user_earnings_history';

    protected $fillable = [
        'unique_id',
        'user_id',
        'earn_from',
        'total_amount',
        'platform_fees_percent',
        'platform_fees_amount',
        'user_earn_amount',
        'reference_id',
        'payment_transactions_id',
        'description'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($object) {
            $object->unique_id = randomNumber();
        });
        static::updating(function ($object) {
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }
    // User who earned the amount
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointment()
    {
            return $this->belongsTo(AppointmentBooking::class, 'reference_id')->where('earn_from', 'appointment_fees');
    }

    public function case()
    {
            return $this->belongsTo(CaseWithProfessionals::class, 'reference_id')->where('earn_from', 'case_fees');
    }

      public function globalInvoice()
    {
            return $this->belongsTo(Invoice::class, 'reference_id')->where('earn_from', 'general_invoice_fees');
    }
}
