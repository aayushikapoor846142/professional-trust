<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalServicePrice extends Model
{
    use HasFactory,SoftDeletes;

    // Specify the table name if it's different from the plural of the model name
    protected $table = 'professional_service_prices';

    // Define the fields that can be mass assigned
    protected $fillable = [
        'unique_id',
        'actual_service_id',
        'professional_service_id',
        'type',
        'professional_fees',
        'consultancy_fees',
        'documents',
        'added_by',
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

    /**
     * Define any relationships if needed, for example:
     * Assuming actual_service and professional_service are other models.
     */



    // If you want to set up a relationship with the user who added the record
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function serviceDocument()
    {
        return $this->belongsTo(ServiceDocument::class, 'document');
    }
    
    public function types()
    {
        return $this->belongsTo(Types::class, 'type');
    }
}
