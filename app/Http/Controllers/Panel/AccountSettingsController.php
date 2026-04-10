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

class AccountSettingsController extends Controller
{
    /**
     * Display the list of settings.
     *
     * @return \Illuminate\View\View
     */
    public function index(): \Illuminate\View\View
    {
        $privacySlug = config('privacysettings.PROFILE-VISIBILITY-SETTINGS');
        $privacy = ModulePrivacy::where('slug', $privacySlug)->first();

        if (is_null($privacy)) {
            $viewData['privacySettings'] = [];
        } else {
            $viewData['privacySettings'] = ModulePrivacyOptions::with('userPrivacy')
                ->where('module_privacy_id', $privacy->id)
                ->where('applicable_role', 'LIKE', '%professional%')
                ->get();
        }

        $viewData['pageTitle'] = "Account Settings";
        return view('admin-panel.04-profile.account-settings.edit', $viewData);
    }

    /**
     * Update the privacy settings for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.privacy_option_id' => 'required|integer',
            // Add more validation rules as needed
        ]);

        $userId = \Auth::id();
        $incoming = collect($request->settings);
        $incomingIds = $incoming->pluck('privacy_option_id')->toArray();

        $privacySlug = config('privacysettings.PROFILE-VISIBILITY-SETTINGS');
        $privacy = ModulePrivacy::where('slug', $privacySlug)->first();

        if (is_null($privacy)) {
            return response()->json([
                'status' => false,
                'message' => 'Privacy settings not found.',
            ]);
        }

        $optionIds = ModulePrivacyOptions::where('module_privacy_id', $privacy->id)
            ->pluck('id')
            ->toArray();

        $existingSettings = UserPrivacySettings::whereIn('privacy_option_id', $optionIds)
            ->where('added_by', $userId)
            ->get()
            ->keyBy('privacy_option_id');

        $emptyValueIds = [];

        // Use constants for magic strings
        $TOGGLE_TYPE = 'toogle';
        $TOGGLE_HIDE_VALUE = 'hide';

        foreach ($incoming as $item) {
            $valueIsEmpty = !isset($item['value']) ||
                (is_array($item['value']) && empty($item['value'])) ||
                (is_string($item['value']) && trim($item['value']) === '');

            if ($valueIsEmpty) {
                if (($item['type'] ?? null) === $TOGGLE_TYPE) {
                    if (isset($existingSettings[$item['privacy_option_id']])) {
                        $existingSettings[$item['privacy_option_id']]->update(['privacy_option_value' => $TOGGLE_HIDE_VALUE]);
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

        $existingIds = $existingSettings->keys()->toArray();

        $idsToDelete = array_unique(array_merge(
            array_diff($existingIds, $incomingIds),
            $emptyValueIds
        ));

        if (!empty($idsToDelete)) {
            UserPrivacySettings::where('user_id', $userId)
                ->whereIn('privacy_option_id', $idsToDelete)
                ->delete();
        }

        return response()->json([
            'status' => true,
            'redirect_back' => baseUrl('settings/account'),
            'message' => "Record updated successfully"
        ]);
    }
}