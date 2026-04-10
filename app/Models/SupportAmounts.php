<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportAmounts extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'support_amounts';
    protected $fillable = ['unique_id', 'amount','status','added_by'];

    static function deleteRecord($id)
    {
       // dd($id);
        SupportAmounts::where("unique_id", $id)->delete();
    }
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'added_by');
    }
}
