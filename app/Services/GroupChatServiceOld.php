<?php

namespace App\Services;

use App\Models\User;
use App\Models\ChatGroup;
use App\Models\GroupMembers;
use App\Models\GroupMessages;
use App\Models\GroupMessagesRead;
use App\Models\GroupMessageReaction;
use App\Models\GroupJoinRequest;
use App\Models\GroupSettings;
use App\Models\GroupMessagePermission;
use App\Models\Chat;
use App\Models\ChatNotification;
use App\Models\DraftChatMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GroupChatServiceOld
{
    public function getAddNewGroupData($userId)
    {
        $viewData = [];
        $viewData['pageTitle'] = "Add New Group";

        $userIDS = Chat::where('user1_id', $userId)
            ->orWhere('user2_id', $userId)
            ->get()
            ->flatMap(function ($chat) {
                return [$chat->user1_id, $chat->user2_id];
            })
            ->unique()
            ->filter(fn($id) => $id != $userId)
            ->values()
            ->toArray();

        $users = User::whereIn('id', $userIDS)->with('userPrivacySettingForGroup')->get();

        $records = $users->filter(function ($user) {
            $privacySetting = $user->userPrivacySettingForGroup;

            if (!$privacySetting) {
                return true;
            }

            return checkUserPrivacy($user->id, $privacySetting->privacy_option_value);
        })->values();

        $memberIDS = collect($records)->pluck('id');

        $viewData['members'] = Chat::where(function ($query) use ($userId) {
            $query->where('user1_id', $userId)
                ->orWhere('user2_id', $userId);
        })
            ->with(['addedBy', 'chatWith'])
            ->get()
            ->map(function ($chat) use ($userId) {
                return $chat->user1_id == $userId ? $chat->chatWith : $chat->addedBy;
            })
            ->whereIn('id', $memberIDS)
            ->unique();

        return $viewData;
    }

    public function getAddNewMembersData($userId, $groupId)
    {
        $viewData = [];
        $viewData['pageTitle'] = "Add New Member";
        $viewData['group_id'] = $groupId;

        $getGroup = ChatGroup::where('unique_id', $groupId)->first();
        $group_members = GroupMembers::with('member')->where('group_id', '=', $getGroup->id)->get()->pluck('user_id')->toArray();

        $viewData['members'] = Chat::where(function ($query) use ($userId) {
            $query->where('user1_id', $userId)
                ->orWhere('user2_id', $userId);
        })
            ->where(function ($query) {
                $query->where('blocked_chat', 0)
                    ->orWhereNull('blocked_chat');
            })
            ->with(['addedBy', 'chatWith'])
            ->get()
            ->map(function ($chat) use ($userId) {
                return $chat->user1_id == $userId ? $chat->chatWith : $chat->addedBy;
            })
            ->unique();

        $viewData['group_members'] = $group_members;
        return $viewData;
    }

    public function getViewGroupMembersData($userId, $groupId)
    {
        $viewData = [];
        $viewData['pageTitle'] = "Group Members";
        $viewData['group_id'] = $groupId;

        $group = ChatGroup::where('unique_id', $groupId)->first();
        $group_members = GroupMembers::with('member')->where('group_id', '=', $group->id)->get();

        $member = GroupMembers::where(['group_id' => $group->id, 'user_id' => $userId])->first();
        $viewData['member'] = $member;
        $viewData['currentGroupMember'] = null;

        if ($group) {
            $currentGroupMember = $group->groupMembers->firstWhere('user_id', $userId);
            $viewData['currentGroupMember'] = $currentGroupMember;
        }

        $viewData['group_members'] = $group_members;
        return $viewData;
    }

    public function searchGroupMembers($groupId, $search)
    {
        if ($search) {
            $search = str_replace("@", "", $search);
        } else {
            $search = '';
        }

        $groupData = ChatGroup::where('id', $groupId)->first();
        $group_id = $groupData->id;

        $members = GroupMembers::addSelect(['member_name' => User::select(DB::raw("CONCAT(first_name, ' ', last_name)"))
            ->whereColumn('group_members.user_id', 'users.id')
            ->limit(1)
        ])
            ->where("user_id", "!=", auth()->user()->id)
            ->where('group_id', $group_id)
            ->whereHas('member', function ($query) use ($search) {
                if ($search != '') {
                    $query->where('first_name', 'LIKE', "%{$search}%");
                    $query->orWhere('last_name', 'LIKE', "%{$search}%");
                }
            })
            ->get()
            ->pluck("member_name");

        return [
            'status' => true,
            'members' => $members
        ];
    }

    public function getGroupsListData($userId)
    {
        $viewData = [];
        $viewData['pageTitle'] = "Groups List";
        $lastSegment = collect(request()->segments())->last();
        $viewData['type'] = $lastSegment;
        return $viewData;
    }

    public function getGroupReceivedReqAjaxList($userId, $request)
    {
        $search = $request->search;
        $perPage = 15;
        $page = $request->input('page', 1);
        $lastSegment = collect(request()->segments())->last();

        $query = GroupJoinRequest::with(['group', 'user'])
            ->where('user_id', $userId)
            ->where('status', 'pending');

        if ($search) {
            $query->whereHas('group', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();
        $requests = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'status' => true,
            'data' => $requests,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page
        ];
    }

    public function getMyGroupsAjaxList($userId, $request)
    {
        $search = $request->search;
        $perPage = 15;
        $page = $request->input('page', 1);

        $query = GroupMembers::with(['group', 'member'])
            ->where('user_id', $userId);

        if ($search) {
            $query->whereHas('group', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();
        $groups = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'status' => true,
            'data' => $groups,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page
        ];
    }

    public function getGroupsAjaxList($userId, $request)
    {
        $search = $request->search;
        $perPage = 15;
        $page = $request->input('page', 1);

        $query = ChatGroup::with(['groupMembers', 'addedBy'])
            ->where('added_by', $userId);

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $total = $query->count();
        $groups = $query->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return [
            'status' => true,
            'data' => $groups,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page
        ];
    }

    public function createGroup($userId, $data)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($data, [
            'name' => 'required|string|max:255',
            'group_type' => 'required',
            'description' => 'nullable|string|max:300',
            'member_id' => 'nullable|array',
            'member_id.*' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first()
            ];
        }

        $group = ChatGroup::create([
            'name' => $data['name'],
            'type' => $data['group_type'],
            'description' => $data['description'] ?? '',
            'added_by' => $userId,
            'group_image' => $data['group_image'] ?? null
        ]);

        // Add creator as admin member
        GroupMembers::create([
            'group_id' => $group->id,
            'user_id' => $userId,
            'is_admin' => 1,
            'added_by' => $userId
        ]);

        // Add other members
        if (isset($data['member_id']) && is_array($data['member_id'])) {
            foreach ($data['member_id'] as $memberId) {
                if ($memberId != $userId) {
                    GroupMembers::create([
                        'group_id' => $group->id,
                        'user_id' => $memberId,
                        'is_admin' => 0,
                        'added_by' => $userId
                    ]);
                }
            }
        }

        // Create system message for group creation
        $group_message = new GroupMessages();
        $group_message->group_id = $group->id;
        $group_message->message = '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '* has created this group.';
        $group_message->user_id = 0;
        $group_message->save();

        // Send websocket notification
        $socket_data = [
            "action" => "group_created",
            'last_message_id' => $group_message->id,
            "group_id" => $group->id,
        ];
        initGroupChatSocket($group->id, $socket_data);

        // Send notifications to all members
        $allMembers = GroupMembers::where('group_id', $group->id)->get();
        foreach ($allMembers as $member) {
            if ($member->user_id != $userId) {
                $arr = [
                    'comment' => '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '* has created a new group: ' . $group->name,
                    'type' => 'group_chat',
                    'redirect_link' => 'group/chat/' . $group->unique_id,
                    'is_read' => 0,
                    'user_id' => $member->user_id,
                    'send_by' => $userId,
                ];
                chatNotification($arr);
            }
        }

        $response['status'] = true;
        $response['redirect_back'] = baseUrl('group/chat/');
        $response['message'] = "Record Added Successfully";
        
        return $response;
    }

    public function sendMessage($groupId, $userId, $data)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($data, [
                'send_msg' => ['sometimes', 'input_sanitize'],
            ]);

            if ($validator->fails()) {
                $response['status'] = false;
                $error = $validator->errors()->toArray();
                $errMsg = array();

                foreach ($error as $key => $err) {
                    $errMsg[$key] = $err[0];
                }
                $response['message'] = $errMsg;
                return $response;
            }

            // Validate group exists
            $group_chat = ChatGroup::where("id", $groupId)->first();
            if (!$group_chat) {
                return [
                    'status' => false,
                    'message' => 'Group not found'
                ];
            }

            if (isset($data['send_msg']) && $data['send_msg'] != null) {
                $message = $data['send_msg'];
            } else {
                $message = $data['message'] ?? '';
            }

            $newName = "";
            $attachedFile = [];
            $chat_message = null;

            // Only proceed if there's a message or attachment
            if (isset($data['attachment']) || ($message != null && $message != "")) {
                $chat_message = new GroupMessages();

                // Handle file attachments
                if (isset($data['attachment']) && is_array($data['attachment'])) {
                    foreach ($data['attachment'] as $file) {
                        $allowedTypes = [
                            'jpeg', 'jpg', 'png', 'gif', 'bmp', 'webp', 'svg', // Image
                            'xls', 'xlsx', 'csv', // Excel
                            'pdf',         // PDF
                            'txt',         // Plain text
                            'mp3',         // Audio
                            'mp4', 'mpeg'  // Video
                        ];

                        $fileResponse = validateFileType($file, $allowedTypes);
                        if (!$fileResponse['status']) {
                            return $fileResponse;
                        }

                        $fileName = $file->getClientOriginalName();
                        $newName = mt_rand(1, 99999) . "-" . $fileName;
                        $uploadPath = groupChatDir();
                        $sourcePath = $file->getPathName();
                        
                        $api_response = mediaUploadApi("upload-file", $sourcePath, $uploadPath, $newName);
                    
                        if (($api_response['status'] ?? '') === 'success') {
                            $attachedFile[] = $newName;
                            $socket_data = [
                                "action" => "new_file_uploaded",
                                "file" => $newName,
                            ];
                            initGroupChatSocket($groupId, $socket_data);
                        }
                    }
                }

                $chat_message->message = $message;
                $chat_message->group_id = $groupId;
                if (isset($data['reply_to'])) {
                    $chat_message->reply_to = $data['reply_to'];
                }
                $chat_message->user_id = $userId;

                if (($message != "" && $message != null) || !empty($attachedFile)) {
                    if (!empty($attachedFile)) {
                        $chat_message->attachment = implode(',', $attachedFile);
                    }
                    $chat_message->save();
                }

                // Delete draft message
                DraftChatMessage::where("reference_id", $groupId)
                    ->where("type", "group_chat")
                    ->where("user_id", $userId)
                    ->delete();

                // Mark as read for other group members
                $group_members = GroupMembers::where("group_id", $groupId)
                    ->where("user_id", "!=", $userId)
                    ->get();

                foreach ($group_members as $gm) {
                    $group_message_read = new GroupMessagesRead();
                    $group_message_read->group_id = $groupId;
                    $group_message_read->user_id = $gm->user_id;
                    $group_message_read->group_message_id = $chat_message->id;
                    $group_message_read->save();

                    updateMessagingBox($userId, $gm->user_id);
                }

                // Get chat message for view
                $get_chat_message = GroupMessages::withTrashed()
                    ->where('id', $chat_message->id)
                    ->where(function ($query) use ($userId) {
                        $query->whereNull('clear_for');
                        $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                    })
                    ->with('replyTo')
                    ->first();

                $viewData = [];
                if ($get_chat_message->message != null || $get_chat_message->attachment != null) {
                    $viewData['chat_msg'] = $get_chat_message;
                } else {
                    $viewData['chat_msg'] = null;
                }

                $group_chat_first_msg = GroupMessages::where("id", $data['reply_to'] ?? 0)->first();
                $viewData['openfrom'] = $data['openfrom'] ?? null;

                $view = \View::make('admin-panel.01-message-system.message-centre.groups.chat.msg_sent_block', $viewData);
                $contents = $view->render();

                // Reply notification
                if (isset($data['reply_to']) && $data['reply_to'] != null) {
                    if ($group_chat_first_msg && $group_chat_first_msg->user_id != $userId) {
                        $arr = [
                            'comment' => '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '* has replied to your message ' . $group_chat->name . ' Group.',
                            'type' => 'group_chat',
                            'redirect_link' => 'group/chat/' . $group_chat->unique_id,
                            'is_read' => 0,
                            'user_id' => $group_chat_first_msg->user_id ?? '',
                            'send_by' => $userId ?? '',
                        ];
                        chatNotification($arr);
                    }
                }

                // Mention notification
                if (strpos($data['send_msg'] ?? '', '@') !== false) {
                    $parts = explode('*', $data['send_msg']);
                    if (isset($parts[1])) {
                        $name = trim(str_replace('@', '', $parts[1]));
                        $names = explode(' ', $name, 2);

                        $receiver = User::where('first_name', $names[0] ?? '')
                            ->where('last_name', $names[1] ?? '')
                            ->latest()
                            ->first();

                        if ($receiver) {
                            $arr = [
                                'comment' => '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '* has mentioned you on ' . $group_chat->name . ' Group.',
                                'type' => 'group_chat',
                                'redirect_link' => 'group/chat/' . $group_chat->unique_id,
                                'is_read' => 0,
                                'user_id' => $receiver->id ?? '',
                                'send_by' => $userId ?? '',
                            ];
                            chatNotification($arr);
                        }
                    }
                }

                // Group settings and permissions
                $senderId = $userId;
                $group = ChatGroup::findOrFail($groupId);

                // Get settings
                $grpSettings = GroupSettings::where('group_id', $groupId)->first();
                $group_chat = $group; // for message title use

                if ($grpSettings) {
                    $type = $grpSettings->who_can_see_my_message;
                } else {
                    $type = "none";
                }

                // Determine recipients based on settings
                $receiverIds = [];

                switch ($type) {
                    case 'members':
                        $receiverIds = GroupMessagePermission::where('group_id', $groupId)
                            ->where('member_id', '!=', $senderId)
                            ->pluck('member_id')
                            ->toArray();
                        break;

                    case 'admins':
                        $receiverIds = GroupMembers::where('group_id', $groupId)
                            ->where('is_admin', true)
                            ->where('user_id', '!=', $senderId)
                            ->pluck('user_id')
                            ->toArray();
                        break;

                    case 'everyone':
                    default:
                        $receiverIds = GroupMembers::where('group_id', $groupId)
                            ->where('user_id', '!=', $senderId)
                            ->pluck('user_id')
                            ->toArray();
                        break;
                }

                // Send websocket notifications to each recipient
                foreach ($receiverIds as $memberId) {
                    $socket_data = [
                        "action" => "new_message",
                        "message" => $message,
                        "group_id" => $groupId,
                        "last_message_id" => $chat_message->id,
                        "sender_id" => $senderId,
                        "receiver_id" => $memberId,
                    ];
                    initGroupChatSocket($groupId, $socket_data);

                    // User-specific notification
                    $notification_data = [
                        "action" => "new_group_message",
                        "message" => "Message received in group {$group_chat->name} from " . auth()->user()->first_name . ' ' . auth()->user()->last_name,
                        "group_id" => $groupId,
                        "sender_id" => $senderId,
                        "total_unread_count" => unreadTotalGroupMessages($memberId) + unreadTotalChatMessages($memberId),
                        "unread_count" => unreadTotalGroupMessages($memberId),
                        "receiver_id" => $memberId,
                    ];
                    initUserSocket($memberId, $notification_data);
                }

                $response['status'] = true;
                $response['contents'] = $contents;
                $response['id'] = $chat_message->id;
                return $response;
            } else {
                // Return error if no message and no attachment
                return [
                    'status' => false,
                    'message' => 'Message or attachment is required'
                ];
            }
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function getGroupChat($groupId, $lastMsgId = null)
    {
        $group = ChatGroup::where('unique_id', $groupId)->first();
        if (!$group) {
            return [
                'status' => false,
                'message' => 'Group not found'
            ];
        }

        $query = GroupMessages::with(['sender', 'reactions'])
            ->where('group_id', $group->id)
            ->orderBy('created_at', 'desc');

        if ($lastMsgId) {
            $query->where('id', '<', $lastMsgId);
        }

        $messages = $query->limit(50)->get()->reverse();

        return [
            'status' => true,
            'data' => $messages,
            'group' => $group
        ];
    }

    public function updateGroupName($groupId, $userId, $data)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($data, [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first()
            ];
        }

        $group = ChatGroup::where('unique_id', $groupId)->first();
        if (!$group) {
            return [
                'status' => false,
                'message' => 'Group not found'
            ];
        }

        // Check if user is admin
        $member = GroupMembers::where(['group_id' => $group->id, 'user_id' => $userId, 'is_admin' => 1])->first();
        if (!$member) {
            return [
                'status' => false,
                'message' => 'Only admins can update group name'
            ];
        }

        $group->update(['name' => $data['name']]);

        return [
            'status' => true,
            'message' => 'Group name updated successfully'
        ];
    }

    public function removeGroupMember($memberId, $userId, $data)
    {
        $member = GroupMembers::find($memberId);
        if (!$member) {
            return [
                'status' => false,
                'message' => 'Member not found'
            ];
        }

        // Check if user is admin or removing themselves
        $currentMember = GroupMembers::where(['group_id' => $member->group_id, 'user_id' => $userId])->first();
        if (!$currentMember || (!$currentMember->is_admin && $currentMember->user_id != $member->user_id)) {
            return [
                'status' => false,
                'message' => 'You can only remove yourself or must be an admin'
            ];
        }

        $member->delete();

        // Create system message for member removal
        $group_message = new GroupMessages();
        $group_message->group_id = $member->group_id;
        $group_message->message = '*' . auth()->user()->first_name . " " . auth()->user()->last_name . '* has removed *' . $member->member->first_name . ' ' . $member->member->last_name . '* from this group.';
        $group_message->user_id = 0;
        $group_message->save();

        // Send websocket notification
        $socket_data = [
            "action" => "member_removed",
            'last_message_id' => $group_message->id,
            "group_id" => $member->group_id,
            "removed_user_id" => $member->user_id
        ];
        initGroupChatSocket($member->group_id, $socket_data);

        // Send notification to removed member
        $arr = [
            'comment' => 'You have been removed from ' . $member->group->name . ' group by ' . auth()->user()->first_name . " " . auth()->user()->last_name,
            'type' => 'group_chat',
            'redirect_link' => 'group/chat/',
            'is_read' => 0,
            'user_id' => $member->user_id,
            'send_by' => $userId,
        ];
        chatNotification($arr);

        return [
            'status' => true,
            'message' => 'Member removed successfully'
        ];
    }

    public function addReactionToMessage($userId, $data)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($data, [
            'message_id' => 'required|exists:group_messages,id',
            'reaction' => 'required|string|max:10'
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->first()
            ];
        }

        $reaction = GroupMessageReaction::updateOrCreate(
            [
                'message_id' => $data['message_id'],
                'user_id' => $userId
            ],
            [
                'reaction' => $data['reaction']
            ]
        );

        return [
            'status' => true,
            'message' => 'Reaction added successfully',
            'data' => $reaction
        ];
    }

    public function removeReactionFromMessage($userId, $data)
    {
        $reaction = GroupMessageReaction::where([
            'message_id' => $data['message_id'],
            'user_id' => $userId
        ])->first();

        if ($reaction) {
            $reaction->delete();
        }

        return [
            'status' => true,
            'message' => 'Reaction removed successfully'
        ];
    }
} 