<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProfessionalAssociateAgreement extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'unique_id','associate_id','professional_id','agreement','pdf','platform_fees','sharing_fees','is_support_accept','is_associate_accept','added_by','original_agreement'
    ];

    /**
     * Get the comments for this agreement.
     */
    public function comments()
    {
        return $this->hasMany(ProfessionalAgreementComment::class, 'agreement_id')->mainComments()->with('user', 'allReplies.user');
    }

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
}
