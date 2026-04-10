<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class CaseJoinRequest extends Model
{
    use HasFactory, SoftDeletes;

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

    protected $fillable = ['unique_id', 'associate_id','professional_id','added_by','lead_case_id','summary'];

    public function associate()
    {
        return $this->belongsTo(User::class, 'associate_id', 'id');
    }

    public function leadCase()
    {
        return $this->belongsTo(LeadCase::class, 'lead_case_id', 'id');
    }


}
