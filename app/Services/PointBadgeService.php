<?php

namespace App\Services;

use App\Models\PointEarn;

class PointBadgeService
{
    /**
     * Get the total points for a user.
     *
     * @param int $userId
     * @return int
     */
    public function getUserPoints($userId): int
    {
        return PointEarn::where('user_id', $userId)->sum('total_points');
    }

    /**
     * Get the badge for a user based on points.
     *
     * @param int $userPoints
     * @return mixed
     */
    public function getUserBadge($userPoints)
    {
        // Assuming supportBadge is a global helper that returns badge data or null
        return supportBadge($userPoints, 'data');
    }
} 