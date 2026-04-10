<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfessionalServices;
use View;
use App\Models\GroupMembers;
use Illuminate\Support\Facades\Validator;
use App\Models\ChatGroup;
use App\Models\CaseWithProfessionals;
use App\Models\CaseChat;
use App\Models\StaffCases;


class CaseMessagesController extends Controller
{
  
    public function messageList($case_id)
    {
        $case = $this->findCaseByUniqueId($case_id);

        if (!$case) {
            return abort(404, 'Case not found');
        }

        $viewData['pageTitle'] = "Messages";
        $viewData['case_id'] = $case_id;
        $viewData['cases'] = $case->load('caseChats');
        return view('admin-panel.08-cases.case-with-professionals.messages.lists', $viewData);
    }

    public function createGroup($case_id)
    {
        $case = CaseWithProfessionals::where('unique_id',$case_id)->first();

        $staff_users = StaffCases::where('case_id',$case->id)->get()->pluck('staff_id')->toArray();
        $ids = [$case->professional_id,$case->client_id];
        $group_members = array_merge($ids,$staff_users);

        $group = new ChatGroup();
        $group->name = $case->case_title;
        $group->type = 'private';
        $group->added_by = auth()->user()->id;
        $group->save();

        if(!empty($group_members)){
            foreach(array_unique($group_members) as $member){
                $groupuser = new GroupMembers();
                $groupuser->unique_id=randomNumber();
                $groupuser->group_id = $group->id;
                $groupuser->user_id = $member;
                $groupuser->save();
            }
        }
       

        $caseChat = new CaseChat();
        $caseChat->unique_id = randomNumber();
        $caseChat->case_id = $case->id;
        $caseChat->group_chat_id = $group->id;
        $caseChat->added_by = auth()->user()->id;
        $caseChat->save();

        return redirect(baseUrl('group/chat/'.$group->unique_id));

    }

    // --- Private methods ---
    private function findCaseByUniqueId($case_id)
    {
        return CaseWithProfessionals::where('unique_id', $case_id)->first();
    }

    private function authorizeProfessional($case)
    {
        if (auth()->user()->role == "professional" && $case->professional_id !== auth()->user()->id) {
            return false;
        }
        return true;
    }

    private function getGroupMemberIds($case)
    {
        $staff_users = StaffCases::where('case_id', $case->id)->pluck('staff_id')->toArray();
        $ids = [$case->professional_id, $case->client_id];
        return array_unique(array_merge($ids, $staff_users));
    }
}