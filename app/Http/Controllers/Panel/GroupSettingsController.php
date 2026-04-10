<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\GroupSettings;
use App\Models\ChatGroup;
use App\Models\GroupMessagePermission;
use App\Models\GroupMembers;

class GroupSettingsController extends Controller
{
    //
       public function index($groupId)
    {
        $getGroup = ChatGroup::where('unique_id', $groupId)->first();
        if (!$getGroup) {
            // Error handling: Group not found
            abort(404, 'Group not found');
        }
        $viewData['groupId'] = $getGroup->id;
        $viewData['getGroup'] = $getGroup;
        $viewData['record'] = $data = GroupSettings::where("group_id", $getGroup->id)->first();
        $viewData['members'] = GroupMembers::with('member')->where('group_id', '=', $getGroup->id)->get()->pluck('member');
        $viewData['selectedMembers'] = $selectedMembers = GroupMessagePermission::where('group_id', $getGroup->id)
            ->pluck('member_id')
            ->toArray();
        $viewData['pageTitle'] = "Group Settings";
        return view('admin-panel.01-message-system.group-chat.settings.index', $viewData);
    }

    /**
     * Update the specified setting in the database.
     *
     * @param string $id The unique id of the setting to edit.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($unique_id, Request $request)
    {
        $request->validate([
            'only_admins_can_post' => 'nullable|boolean',
            'members_can_add_members' => 'nullable|boolean',
            'who_can_see_my_message' => 'required|in:everyone,admins,members',
        ]);
        $groupId = $request->group_id;
        $group = ChatGroup::find($groupId);
        if (!$group) {
            // Error handling: Group not found
            return redirect()->back()->withErrors(['Group not found.']);
        }
        $settings = GroupSettings::where(['group_id' => $groupId])->first();
        if (!$settings) {
            $settings = new GroupSettings;
        }
        $settings->fill([
            'unique_id' => $settings->unique_id ?? randomNumber(),
            'group_id' => $groupId,
            'only_admins_can_post' => $request->boolean('only_admins_can_post'),
            'members_can_add_members' => $request->boolean('members_can_add_members'),
            'who_can_see_my_message' => $request->input('who_can_see_my_message'),
        ]);
        $settings->save();
        // Get unique member IDs from request
        $memberIds = array_unique($request->input('visible_members', []));
        $now = now();
        // Get all existing permissions (with trashed) for the group
        $existingPermissions = GroupMessagePermission::withTrashed()
            ->where('group_id', $groupId)
            ->get()
            ->keyBy('member_id');
        // Step 1: Soft delete members no longer selected
        $existingMemberIds = $existingPermissions->keys()->toArray();
        $toDelete = array_diff($existingMemberIds, $memberIds);
        if (!empty($toDelete)) {
            GroupMessagePermission::where('group_id', $groupId)
                ->whereIn('member_id', $toDelete)
                ->delete(); // Soft delete
        }
        // Step 2: Add or restore selected members
        foreach ($memberIds as $memberId) {
            if (isset($existingPermissions[$memberId])) {
                $existing = $existingPermissions[$memberId];
                if ($existing->trashed()) {
                    $existing->restore(); // Restore soft-deleted record
                }
                // Else, already exists — do nothing
            } else {
                // Insert new record
                GroupMessagePermission::create([
                    'group_id' => $groupId,
                    'member_id' => $memberId,
                    'unique_id' => randomNumber(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
    
}
