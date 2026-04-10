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

class MessageSettingsController extends Controller
{
    /**
     * Display the list of settings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $messageCenterSlug = config('privacysettings.MESSAGE-CENTER');
        $privacyIDs = ModulePrivacy::where('slug', $messageCenterSlug)->first();

        $viewData['privacySettings'] = [];
        if ($privacyIDs?->id) {
            $viewData['privacySettings'] = ModulePrivacyOptions::with(['userPrivacy'])
                ->where('module_privacy_id', $privacyIDs->id)
                ->where('applicable_role', 'LIKE', '%professional%')
                ->get();
        }

        $viewData['pageTitle'] = "Message Settings";
        return view('admin-panel.01-message-system.message_settings.edit', $viewData);
    }

    /**
     * Update the specified setting in the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.privacy_option_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $userId = \Auth::id();
        $incoming = collect($request->settings);
        $incomingIds = $incoming->pluck('privacy_option_id')->toArray();

        $privacyIDs = ModulePrivacy::where('slug', config('privacysettings.MESSAGE-CENTER'))->first();
        $IDS = ModulePrivacyOptions::where('module_privacy_id', $privacyIDs?->id)->pluck('id')->toArray();

        $existingSettings = UserPrivacySettings::whereIn('privacy_option_id', $IDS)
            ->where('added_by', $userId)
            ->get()
            ->keyBy('privacy_option_id');

        $emptyValueIds = [];

        DB::beginTransaction();
        try {
            foreach ($incoming as $item) {
                $privacyOptionId = $item['privacy_option_id'];
                $value = $item['value'] ?? null;

                if ($this->isEmptyValue($value)) {
                    if (($item['type'] ?? null) === 'toogle' && isset($existingSettings[$privacyOptionId])) {
                        $existingSettings[$privacyOptionId]->update(['privacy_option_value' => 'hide']);
                    } else {
                        $emptyValueIds[] = $privacyOptionId;
                    }
                    continue;
                }

                $valueString = $this->valueToString($value);

                UserPrivacySettings::updateOrCreate(
                    [
                        'privacy_option_id' => $privacyOptionId,
                        'user_id'           => $userId,
                        'added_by'          => $userId,
                    ],
                    [
                        'privacy_option_value' => $valueString
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

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('message-settings');
        $response['message'] = "Record updated successfully";

        return response()->json($response);
    }

    /**
     * Check if a value is considered empty for privacy settings.
     *
     * @param mixed $value
     * @return bool
     */
    private function isEmptyValue($value): bool
    {
        return !isset($value)
            || (is_array($value) && empty($value))
            || (is_string($value) && trim($value) === '');
    }

    /**
     * Convert a value to string for storage.
     *
     * @param mixed $value
     * @return string
     */
    private function valueToString($value): string
    {
        return is_array($value) ? implode(',', $value) : (string)$value;
    }
}
