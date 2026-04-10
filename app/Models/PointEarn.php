<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointEarn extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['unique_id','user_id', 'points','bonus_points','total_points'];

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
    static function deleteRecord($id)
    {
        PointEarn::where("unique_id", $id)->delete();
    }
    public function userAdded()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public static function incrementPoints($userId, $pointsToAdd)
    {
        // Check if the user already has an entry
        $pointEarn = self::where('user_id', $userId)->first();

        if ($pointEarn) {
            // Update existing points by adding new points
            $pointEarn->increment('points', $pointsToAdd['points']);
            $pointEarn->increment('bonus_points', $pointsToAdd['bonusPoints']);
            $pointEarn->increment('total_points', $pointsToAdd['totalPoints']);
        } else {
            // Create a new entry if none exists
            $pointEarn = self::create([
                'user_id' => $userId,
                'points'  => $pointsToAdd['points'],
                'bonus_points'  => $pointsToAdd['bonusPoints'],
                'total_points'  => $pointsToAdd['totalPoints'],
            ]);
        }

        return $pointEarn;
    }
}
