<?php

namespace App\Services;

use App\Models\ChatGroup;
use App\Models\GroupMembers;
use App\Models\GroupMessages;
use App\Models\GroupMessagesRead;
use App\Models\GroupMessageReaction;
use App\Models\GroupJoinRequest;
use App\Models\DraftChatMessage;

use App\Models\Chat;
use App\Models\User;
use App\Models\ChatNotification;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class GroupChatService
{
    /**
     * Get available members for creating a new group
     */
    public function getAvailableMembers($userId)
    {
        try {
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

            $members = Chat::where(function ($query) use ($userId) {
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

            return [
                'success' => true,
                'members' => $members
            ];
        } catch (Exception $e) {
            Log::error('Get available members failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to get available members'
            ];
        }
    }

    /**
     * Get group members for adding new members
     */
    public function getGroupMembersForAdding($groupId, $userId)
    {
       try {
            $getGroup = ChatGroup::where('unique_id', $groupId)->first();
            
            if (!$getGroup) {
                return ['success' => false, 'message' => 'Group not found'];
            }

            $group_members = GroupMembers::with('member')
                ->where('group_id', '=', $getGroup->id)
                ->get()
                ->pluck('user_id')
                ->toArray();

            $members = Chat::where(function ($query) use ($userId) {
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

            return [
                'success' => true,
                'members' => $members,
                'group_members' => $group_members,
                'group' => $getGroup
            ];
        } catch (Exception $e) {
            Log::error('Get group members failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to get group members'
            ];
        }
    }

    /**
     * Get group members for viewing
     */
    public function getGroupMembersForViewing($groupId, $userId)
    {
        try {
            $group = ChatGroup::where('unique_id', $groupId)->first();
            
            if (!$group) {
                return ['success' => false, 'message' => 'Group not found'];
            }

            $group_members = GroupMembers::with('member')
                ->where('group_id', '=', $group->id)
                ->get();

            $member = GroupMembers::where([
                'group_id' => $group->id,
                'user_id' => $userId
            ])->first();

            $currentGroupMember = $group->groupMembers->firstWhere('user_id', $userId);

            return [
                'success' => true,
                'group_members' => $group_members,
                'member' => $member,
                'currentGroupMember' => $currentGroupMember,
                'group' => $group
            ];
        } catch (Exception $e) {
            Log::error('Get group members for viewing failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to get group members'
            ];
        }
    }

    /**
     * Search group members
     */
    public function searchGroupMembers($groupId, $search, $userId)
    {
        try {
            $groupData = ChatGroup::where('id', $groupId)->first();
            
            if (!$groupData) {
                return ['success' => false, 'message' => 'Group not found'];
            }

            $search = str_replace("@", "", $search);
            
            $members = GroupMembers::addSelect(['member_name' => User::select(DB::raw("CONCAT(first_name, ' ', last_name)"))
                ->whereColumn('group_members.user_id', 'users.id')
                ->limit(1)
            ])
            ->where("user_id", "!=", $userId)
            ->where('group_id', $groupData->id)
            ->whereHas('member', function ($query) use ($search) {
                if ($search != '') {
                    $query->where('first_name', 'LIKE', "%{$search}%");
                    $query->orWhere('last_name', 'LIKE', "%{$search}%");
                }
            })
            ->get()
            ->pluck("member_name");

            return [
                'success' => true,
                'members' => $members
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to search group members'
            ];
        }
    }

    /**
     * Get user's groups list
     */
    public function getUserGroupsList($userId)
    {
        try {
            // Get groups with members and last message
            $groupdata = ChatGroup::with(['members', 'lastMessage'])
                ->whereHas('members', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->addSelect(['last_message_date' => GroupMessages::query()
                    ->select('created_at')
                    ->whereColumn('group_messages.group_id', 'chat_groups.id')
                    ->latest('created_at')
                    ->limit(1)
                ])
                ->orderBy('last_message_date','desc')
                ->get();
            
            return [
                'success' => true,
                'groupdata' => $groupdata
            ];
        } catch (Exception $e) {
            Log::error('Get user groups list failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to get groups list '.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile()  
            ];
        }
    }

    /**
     * Create a new group
     */
    public function createGroup($data, $userId)
    {
        try {
            \DB::beginTransaction();
            $group = new ChatGroup();
            $group->name = $data['name'];
            $group->type = $data['group_type'];
            $group->added_by = $userId;
            $group->description = $data['description'];
            $group->group_image = $data['group_image'] ?? null;
            $group->banner_image = $data['banner_image'] ?? null;
            $group->save();

            // Add creator as admin member
            $groupuser = new GroupMembers();
            $groupuser->group_id = $group->id;
            $groupuser->is_admin = 1;
            $groupuser->user_id = $userId;
            $groupuser->save();

            // Add other members
            if (isset($data['member_id'])) {
                foreach ($data['member_id'] as $member) {
                    $fetch_members = GroupMembers::where(['group_id' => $group->id, 'user_id' => $member])->first();
                    if ($fetch_members == NULL) {
                        $groupuser = new GroupMembers();
                        $groupuser->group_id = $group->id;
                        $groupuser->user_id = $member;
                        $groupuser->save();

                        $socket_data = [
                            "action" => "new_group_added",
                            "comment" => "You have been added to a ".$data['name']." by ".auth()->user()->first_name." ".auth()->user()->last_name,
                            "receiver_id" => $member,
                        ];
                        initGroupMessageSocket($member,$socket_data);

                        // $arr = [
                        //     'comment' => "You have been added to a *".$data['name']."* by *".auth()->user()->first_name." ".auth()->user()->last_name."*",
                        //     'type' => 'group_chat',
                        //     'redirect_link' => 'group-message/chat/'.$group->unique_id,
                        //     'is_read' => 0,
                        //     'user_id' => $member,
                        //     'send_by' => auth()->user()->id ?? '',
                        // ];
                        // chatNotification($arr);
                    }
                }
            }

            \DB::commit();

            return [
                'success' => true,
                'group' => $group,
                'message' => 'Record added successfully'
            ];
        } catch (Exception $e) {
            Log::error('Create group failed: ' . $e->getMessage());
            \DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to create group '.$e->getMessage().' on line '.$e->getLine().' in file '.$e->getFile()	
            ];
        }
    }

    /**
     * Send message to group
     */
    public function sendGroupMessage($groupId, $data, $userId)
    {
        try {
            $group = ChatGroup::find($groupId);
            
            if (!$group) {
                return ['success' => false, 'message' => 'Group not found'];
            }

            $message = $data['message'] ?? '';
            $attachedFile = $data['attachments'] ?? [];
            $replyTo = $data['reply_to'] ?? null;

            $chat_message = new GroupMessages();
            $chat_message->message = $message;
            $chat_message->group_id = $groupId;
            if ($replyTo != null) {
                $chat_message->reply_to = $replyTo;
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

            return [
                'success' => true,
                'data' => $chat_message,
                'response' => 'Message sent successfully'
            ];
        } catch (Exception $e) {
            Log::error('Send group message failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send message'
            ];
        }
    }


    /** Group Message Chat */
    public function getGroup($groupId)
    {
        $group = ChatGroup::where('unique_id', $groupId)->first();
        if (!$group) {
            return ['success' => false, 'message' => 'Group not found'];
        }

        $group_members = GroupMembers::where('group_id', $group->id)->get();
        $group_messages = GroupMessages::where('group_id', $group->id)->get();

        return [
            'success' => true,
            'group' => $group,
            'group_members' => $group_members,
            'group_messages' => $group_messages
        ];
    }

    public function getGroupMessages($groupId,$last_msg_id,$userId  )
    {
        try{
            $group_messages = GroupMessages::withTrashed()->with('sentBy')
                            ->where('group_id',$groupId)
                            ->where(function($query) use($last_msg_id){
                                if($last_msg_id != 0){
                                    $query->where("id",">",$last_msg_id);
                                }
                            })
                            ->where(function($query) use($userId){
                                $query->whereNull('clear_for');
                                $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                            })  
                            ->limit(10)
                            ->latest()
                            ->get();
            $group_messages = $group_messages->sortBy('id');
            $last_msg_id = $group_messages->last()->id??0;
            $first_msg_id = $group_messages->first()->id??0;
            $hasPreviousMessages = GroupMessages::withTrashed()
                ->where('group_id', $groupId)
                ->where('id', '<', $first_msg_id)
                ->where(function($query) use($userId) {
                    $query->whereNull('clear_for');
                    $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                })
                ->exists();
            GroupMessagesRead::where('group_id',$groupId)->where("user_id",$userId)->update(['status'=>'read']);

            $return_data['status'] = true;
            $return_data['data']['group_id'] = $groupId;
            $return_data['data']['unread_count'] = unreadTotalGroupMessages($userId);
            $return_data['data']['total_unread'] = unreadTotalChatMessages($userId) + unreadTotalGroupMessages($userId);
            $return_data['data']['group_messages'] = $group_messages;
            $return_data['data']['has_previous_messages'] = (!empty($hasPreviousMessages))?1:0;
            $return_data['data']['last_msg_id'] = $last_msg_id;
            $return_data['data']['first_msg_id'] = $first_msg_id;
            return $return_data;
        } catch (\Exception $e) {
            $return_data['status'] = false;
            $return_data['data']['err_message'] = $e->getMessage();
            $return_data['data']['err_line'] = $e->getLine();
            return $return_data;
        }
    }

    /**
     * Fetch older messages for infinite scroll
     */
    public function fetchOlderMessages($group_id, $first_msg_id, $userId)
    {
        try {
            // Fetch 15 older messages
            $group_messages = GroupMessages::withTrashed()->with('sentBy')
                ->where('group_id', $group_id)
                ->where('id', '<', $first_msg_id)
                ->where(function($query) use($userId) {
                    $query->whereNull('clear_for');
                    $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                })
                ->limit(10)
                ->orderBy('id', 'desc')
                ->get();

            if ($group_messages->count() > 0) {
                // Update first message ID for next fetch
                $new_first_msg_id = $group_messages->first()->id;
            } else {
                $new_first_msg_id = $first_msg_id;
            }

            // Check if there are more messages to fetch
            $hasMoreMessages = GroupMessages::withTrashed()
                ->where('group_id', $group_id)
                ->where('id', '<', $new_first_msg_id)
                ->where(function($query) use($userId) {
                    $query->whereNull('clear_for');
                    $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
                })
                ->exists();

            return [
                'success' => true,
                'group_messages' => $group_messages,
                'first_msg_id' => $new_first_msg_id,
                'has_more_messages' => $hasMoreMessages,
                'message_count' => $group_messages->count()
            ];
        } catch (\Exception $e) {
            Log::error('Fetch older messages failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch older messages: ' . $e->getMessage()
            ];
        }
    }

    public function getGroupChatFiles($groupId)
    {
        $group_chat = ChatGroup::where('unique_id', $groupId)->first();
        $userId = auth()->user()->id;
        $attachments = GroupMessages::where('group_id', $group_chat->id)
            ->whereNotNull('attachment')
            ->where(function($query) use($userId){
                $query->whereNull('clear_for');
                $query->orWhereRaw('NOT FIND_IN_SET(?, clear_for)', [$userId]);
            })
            ->latest()
            ->get();
        return $attachments;
    }
} 