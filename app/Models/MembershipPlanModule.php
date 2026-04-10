<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MembershipPlanModule extends Model
{
    use HasFactory,SoftDeletes;
    // Specify the table name (optional if Laravel follows naming conventions)
    protected $table = 'membership_plan_module';

    // Allow mass assignment for these attributes
    protected $fillable = ['membership_plan_id', 'module_id'];

    /**
     * Define relationship with MembershipPlan model
     */
    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class);
    }

    /**
     * Define relationship with Module model
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
