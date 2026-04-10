<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory,SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        // Event handler for the creating event
        static::creating(function ($object) {
            // Assign a unique ID using the randomNumber() function
            $object->unique_id = randomNumber();
            $object->invoice_number = generateInvoiceId();
        });

        // Event handler for the updating event
        static::updating(function ($object) {
            // If the unique_id is 0, assign a new unique ID
            if ($object->unique_id == 0) {
                $object->unique_id = randomNumber();
            }
        });
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    public function subscriptionHistory()
    {
        return $this->belongsTo(UserSubscriptionHistory::class, 'user_id', 'user_id')->where("subscription_status","active");
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function supportByUser()
    {
        return $this->belongsTo(SupportByUser::class, 'reference_id', 'id');
    }

    public function invoicePaymentLink()
    {
        return $this->belongsTo('App\Models\InvoicePaymentLink','id' ,'invoice_id');
    }
        static function deleteRecord($id){
        Invoice::where("id",$id)->delete();
    }

    public function caseInvoice()
    {
        return $this->belongsTo('App\Models\CaseWithProfessionals','reference_id' ,'id');
    }

    public function appointmentInvoice()
    {
        return $this->belongsTo('App\Models\AppointmentBooking','reference_id' ,'id');
    }

    
 public function isEditableBy($userId, $ownershipField = 'user_id')
{
    $ownerId = $this->{$ownershipField};
    $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

    if ($professionalId) {
        return $ownerId == $userId;
    } else {
        $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id')->toArray();
        return $ownerId == $userId || in_array($ownerId, $staffIds);
    }
}
}
