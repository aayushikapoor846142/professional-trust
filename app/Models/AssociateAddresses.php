<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssociateAddresses extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'associate_addresses';
    protected $fillable = ['unique_id', 'address_1','address_2','city','state','country','user_id','pincode'];
}
