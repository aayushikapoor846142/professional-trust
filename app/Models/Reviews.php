<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BaseModel;
class Reviews extends Model
{
    use HasFactory,SoftDeletes;
    protected $table="reviews";
    protected $fillable = ['added_by', 'invitation_id','review','rating','edited','professional_id','unique_id'];

    public function addedby(){
    	
        return $this->belongsTo('App\Models\User', 'added_by');
    }

    public function user(){
    	
        return $this->belongsTo('App\Models\User', 'added_by');
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

    public function replies()
    {
        return $this->hasOne(ReviewReplies::class, 'review_id', 'id');
    }

    
    public function professional(){
    	
        return $this->belongsTo('App\Models\User', 'professional_id');
    }

    static function deleteRecord($id)
    {
        $record = Reviews::where("id", $id)->first();
        if ($record) {

            ReviewsInvitations::where("id", $record->invitation_id)->delete();
            Reviews::where("id", $id)->delete();
        }
     
    }
       public function cdsCompanyDetail()
    {
        return $this->hasOne('App\Models\CdsProfessionalCompany','user_id','professional_id');
        
    }

    public function isEditableBy($userId)
    {
        $professionalId = \App\Models\StaffUser::where('user_id', $userId)->value('added_by');

        if ($professionalId) {
            // Staff: can only edit their own records (not professional's)
            return $this->added_by == $userId;
        } else {
            // Professional: can edit their own and their staff's records
            $staffIds = \App\Models\StaffUser::where('added_by', $userId)->pluck('user_id')->toArray();
            return $this->added_by == $userId || in_array($this->added_by, $staffIds);
        }
    }
}
