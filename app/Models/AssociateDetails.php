<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssociateDetails extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'associate_details';
    protected $fillable = ['unique_id', 'user_id','proof_documents','application_fees_status'];

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

    public function nationalityDetail()
    {
        return $this->belongsTo(Nationality::class, 'nationality');
    }


}
