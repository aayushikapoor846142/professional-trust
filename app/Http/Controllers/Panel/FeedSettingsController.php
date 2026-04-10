<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use View;
use DB;
use App\Models\UserPrivacySettings;
use App\Models\ModulePrivacyOptions;
use App\Models\ModulePrivacy;

class FeedSettingsController extends Controller
{
    public function __construct()
    {
        // Constructor method for initializing middleware or other components if needed
    }

    /**
     * Get the feed privacy module by slug from config.
     *
     * @return ModulePrivacy|null
     */
    private function getFeedPrivacyModule()
    {
        return ModulePrivacy::where('slug', config('privacysettings.FEED-VISIBILITY-SETTINGS'))->first();
    }

    /**
     * Get privacy option IDs for the feed privacy module.
     *
     * @param int $modulePrivacyId
     * @return \Illuminate\Support\Collection
     */
    private function getFeedPrivacyOptionIds($modulePrivacyId)
    {
        return ModulePrivacyOptions::where('module_privacy_id', $modulePrivacyId)
            ->pluck('id');
    }

    /**
     * Get privacy settings with user privacy relationship for the feed module and role.
     *
     * @param int $modulePrivacyId
     * @param string $role
     * @return \Illuminate\Support\Collection
     */
    private function getPrivacySettingsWithUserPrivacy($modulePrivacyId, $role = 'professional')
    {
        return ModulePrivacyOptions::with(['userPrivacy'])
            ->where('module_privacy_id', $modulePrivacyId)
            ->where('applicable_role', 'LIKE', "%{$role}%")
            ->get();
    }

    /**
     * Display the list of settings.
     *
     * @return \Illuminate\View\View
     */
 
    public function index()
    {
        $privacyModule = $this->getFeedPrivacyModule();
        if ($privacyModule) {
            $viewData['privacySettings'] = $this->getPrivacySettingsWithUserPrivacy($privacyModule->id);
        } else {
            $viewData['privacySettings'] = [];
        }
        $viewData['pageTitle'] = "Feed Settings";
        return view('admin-panel.04-profile.feed-settings.edit', $viewData);
    }

    
    public function update(Request $request)
    {
        $userId = \Auth::id();
        $incoming = collect($request->settings);
        $incomingIds = $incoming->pluck('privacy_option_id')->toArray();

        $privacyModule = $this->getFeedPrivacyModule();
        if (!$privacyModule) {
            return response()->json([
                'status' => false,
                'message' => 'Privacy module not found',
            ]);
        }
        $IDS = $this->getFeedPrivacyOptionIds($privacyModule->id)->toArray();

        $existingSettings = UserPrivacySettings::whereIn('privacy_option_id', $IDS)
            ->where('added_by', $userId)
            ->get();

        $emptyValueIds = [];

        foreach ($incoming as $item) {
            if (
                !isset($item['value']) ||
                (is_array($item['value']) && empty($item['value'])) ||
                (is_string($item['value']) && trim($item['value']) === '')
            ) {
                if (isset($item['type']) && $item['type'] == 'toogle') {
                    $existing = $existingSettings->firstWhere('privacy_option_id', $item['privacy_option_id']);
                    if ($existing) {
                        $existing->update(['privacy_option_value' => 'hide']);
                    }
                } else {
                    $emptyValueIds[] = $item['privacy_option_id'];
                }
                continue;
            }

            $value = is_array($item['value']) ? implode(',', $item['value']) : $item['value'];

            UserPrivacySettings::updateOrCreate(
                [
                    'privacy_option_id' => $item['privacy_option_id'],
                    'user_id'           => $userId,
                    'added_by'          => $userId,
                ],
                [
                    'privacy_option_value' => $value
                ]
            );
        }

        // Step 2: Delete entries missing from request or with empty non-toggle values
        $existingIds = $existingSettings->pluck('privacy_option_id')->toArray();

        $idsToDelete = array_unique(array_merge(
            array_diff($existingIds, $incomingIds),
            $emptyValueIds
        ));

        if (!empty($idsToDelete)) {
            UserPrivacySettings::where('user_id', $userId)
                ->whereIn('privacy_option_id', $idsToDelete)
                ->delete();
        }

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('settings/feeds');
        $response['message'] = "Record updated successfully";

        return response()->json($response);

    }
}