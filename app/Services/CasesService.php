<?php

namespace App\Services;

use App\Models\Cases;
use App\Models\CaseComment;
use App\Models\ProfessionalFavouriteCase;
use App\Models\ModulePrivacy;
use App\Models\ModulePrivacyOptions;
use App\Models\UserPrivacySettings;
use App\Models\ProfessionalCaseViewed;
use App\Models\ProfessionalServices;
use App\Models\ProfessionalSubServices;
use App\Models\Quotation;
use App\Models\CaseQuotation;
use App\Models\ClientCaseHistory;
use App\Models\CaseQuotationItem;
use App\Models\CaseProposalHistory;
use App\Models\ChatNotification;
use App\Models\ChatGroup;
use App\Models\GroupMembers;
use App\Models\CaseChat;
use View;
use DB;
use App\Models\ImmigrationServices;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CasesService
{
    protected $featureCheckService;

    public function __construct()
    {
        $this->featureCheckService = new FeatureCheckService();
    }

    public function getIndexData($userId, $type, $fullUrl)
    {
        $viewData = [];
        $viewData['totalCase'] = Cases::where("status", "posted")->orderBy("id", "desc")->count();

        $privacyIDs = ModulePrivacy::where('slug', config('privacysettings.CASE-POST-SETTINGS'))->first();

        if ($privacyIDs) {
            $privacySettings = ModulePrivacyOptions::with(['userPrivacy'])
                ->where('module_privacy_id', $privacyIDs->id)
                ->where("applicable_role", "LIKE", "%professional%")
                ->get();
            $viewData['privacySettings'] = $privacySettings;

            $settings = ModulePrivacyOptions::where('module_privacy_id', $privacyIDs->id)
                ->where("applicable_role", "LIKE", "%professional%")
                ->first();

            $userPrivacySettings = UserPrivacySettings::where('user_id', $userId)
                ->where('privacy_option_id', $settings->id)
                ->first();

            if (empty($userPrivacySettings)) {
                $userPrivacySettings = new UserPrivacySettings;
                $userPrivacySettings->user_id = $userId;
                $userPrivacySettings->privacy_option_id = $settings->id;
                $userPrivacySettings->privacy_option_value = 'enable';
                $userPrivacySettings->added_by = $userId;
                $userPrivacySettings->save();
            }
        } else {
            $viewData['privacySettings'] = [];
        }

        $liveChat = 'enable';
        if (function_exists('checkPrivacySettings') && !checkPrivacySettings('feed-visibility-settings', 'live-case-updates', $userId, $fullUrl)) {
            $liveChat = 'disable';
        }
        $viewData['totalProposal'] = CaseComment::where('added_by', $userId)->count();
        $viewData['pageTitle'] = "Post Cases";
        $viewData['type'] = $type;
        $viewData['liveChat'] = $liveChat;
        $viewData['mainServices'] = ImmigrationServices::where('parent_service_id',0)->orderBy('id','desc')->get();
        return $viewData;
    }

    public function getCasesList($userId, $type, $search, $caseId, $isMobile,$service_id,$sub_service_id,$priority,$start_date,$end_date,$hour_range,$trending_case,$sort_by)
    {
        $records = Cases::with([
            'services',
            'submitProposal',
            'professionalFavouriteCase' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }
        ])
        ->whereHas("userAdded")
        ->whereHas("services")
        ->whereHas("subServices")
        ->where("status", "posted");

        if ($type == "unread_case") {
            $records->whereDoesntHave('ProfessionalCaseViewed', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        }

        if ($start_date && $end_date) {
            $records->whereDate('created_at', '>=', $start_date)
                  ->whereDate('created_at', '<=', $end_date);
        } elseif ($start_date && !$end_date) {
            $records->whereDate('created_at', '>=', $start_date);
        } elseif (!$start_date && $end_date) {
            $records->whereDate('created_at', '<=', $end_date);
        }


        if ($trending_case) {
            // Order by count of case views to show trending cases
            $records->withCount('ProfessionalCaseViewed as view_count')
                   ->orderBy('view_count', 'desc');
        }

          // Quick date ranges: today, this_week, this_month
        if ($hour_range) {
            $ranges = is_array($hour_range) ? $hour_range : [$hour_range];
            $records->where(function ($q) use ($ranges) {
                foreach ($ranges as $r) {
                    switch ($r) {
                        case 'today':
                            $q->orWhereBetween('created_at', [Carbon::today(), Carbon::tomorrow()]);
                            break;
                        case 'this_week':
                            $q->orWhereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $q->orWhereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                            break;
                    }
                }
            });
        }
        
        if(!empty($priority)){
            if (in_array('urgent', $priority)) {
                $records->where("is_urgent",1);
            }

            if (in_array('time_constraints', $priority)) {
                $records->where('is_time_constrained',1);
            }
        }
       

        if ($type == "viewed_case") {
            $records->whereHas('ProfessionalCaseViewed', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        }

        if ($type == "proposal_sent") {
            $records->whereHas('submitProposal', function ($query) use ($userId) {
                $query->where('added_by', $userId);
            });
        }

        if ($type == "favourite") {
            $records->whereHas('professionalFavouriteCase', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        }

        if($service_id != 0 && $service_id != 0){
            $service = ImmigrationServices::where('unique_id',$service_id)->first();
            $records->where('parent_service_id',$service->id);
        }

        if($sub_service_id != 0 && $sub_service_id != 0){
            $sub_service = ImmigrationServices::where('unique_id',$sub_service_id)->first();
            $records->where('sub_service_id',$sub_service->id);
        }

        if($search != ''){
            $records->where("title", "LIKE", "%" . $search . "%");
        }
        
        // Apply ordering based on trending case or default ordering
        if (!$trending_case && $sort_by == '') {
            $records = $records->orderBy("id", "desc");
        }

        if($sort_by != ''){
            if($sort_by == "name"){
                $records->orderByRaw('(SELECT CONCAT(first_name, " ", last_name) FROM users WHERE users.id = cases.added_by) ASC');
            }else{
                $records->orderBy('created_at', 'desc');
            }
           
        }
        
        $records = $records->paginate(5);

        $viewData['records'] = $records;
        $viewData['current_page'] = $records->currentPage() ?? 0;
        $viewData['last_page'] = $records->lastPage() ?? 0;
        $viewData['next_page'] = ($records->lastPage() ?? 0) != 0 ? ($records->currentPage() + 1) : 0;
        $viewData['case_id'] = $caseId;

        if ($isMobile) {
            $view = View::make('admin-panel.08-cases.cases.case-ajax-list', $viewData);
        } else {
            $view = View::make('admin-panel.08-cases.cases.case-ajax-list', $viewData);
        }

        $contents = $view->render();
        return [
            'contents' => $contents,
            'last_page' => $records->lastPage(),
            'current_page' => $records->currentPage(),
            'total_records' => $records->total()
        ];
    }

      public function getGridCasesList($userId, $type, $search, $caseId, $isMobile,$service_id,$sub_service_id,$priority,$start_date,$end_date,$hour_range,$trending_case,$sort_by)
    {
        $records = Cases::with([
            'services',
            'submitProposal',
            'professionalFavouriteCase' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }
        ])
        ->whereHas("userAdded")
        ->whereHas("services")
        ->whereHas("subServices")
        ->where("status", "posted");

        if ($type == "unread_case") {
            $records->whereDoesntHave('ProfessionalCaseViewed', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        }

        if ($start_date && $end_date) {
            $records->whereDate('created_at', '>=', $start_date)
                  ->whereDate('created_at', '<=', $end_date);
        } elseif ($start_date && !$end_date) {
            $records->whereDate('created_at', '>=', $start_date);
        } elseif (!$start_date && $end_date) {
            $records->whereDate('created_at', '<=', $end_date);
        }


        if ($trending_case) {
            // Order by count of case views to show trending cases
            $records->withCount('ProfessionalCaseViewed as view_count')
                   ->orderBy('view_count', 'desc');
        }

          // Quick date ranges: today, this_week, this_month
        if ($hour_range) {
            $ranges = is_array($hour_range) ? $hour_range : [$hour_range];
            $records->where(function ($q) use ($ranges) {
                foreach ($ranges as $r) {
                    switch ($r) {
                        case 'today':
                            $q->orWhereBetween('created_at', [Carbon::today(), Carbon::tomorrow()]);
                            break;
                        case 'this_week':
                            $q->orWhereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                            break;
                        case 'this_month':
                            $q->orWhereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
                            break;
                    }
                }
            });
        }
        
        if(!empty($priority)){
            if (in_array('urgent', $priority)) {
                $records->where("is_urgent",1);
            }

            if (in_array('time_constraints', $priority)) {
                $records->where('is_time_constrained',1);
            }
        }
       

        if ($type == "viewed_case") {
            $records->whereHas('ProfessionalCaseViewed', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        }

        if ($type == "proposal_sent") {
            $records->whereHas('submitProposal', function ($query) use ($userId) {
                $query->where('added_by', $userId);
            });
        }

        if ($type == "favourite") {
            $records->whereHas('professionalFavouriteCase', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            });
        }

        if($service_id != 0 && $service_id != 0){
            $service = ImmigrationServices::where('unique_id',$service_id)->first();
            $records->where('parent_service_id',$service->id);
        }

        if($sub_service_id != 0 && $sub_service_id != 0){
            $sub_service = ImmigrationServices::where('unique_id',$sub_service_id)->first();
            $records->where('sub_service_id',$sub_service->id);
        }

        if($search != ''){
            $records->where("title", "LIKE", "%" . $search . "%");
        }
        
        // Apply ordering based on trending case or default ordering
        if (!$trending_case && $sort_by == '') {
            $records = $records->orderBy("id", "desc");
        }

        if($sort_by != ''){
            if($sort_by == "name"){
                $records->orderByRaw('(SELECT CONCAT(first_name, " ", last_name) FROM users WHERE users.id = cases.added_by) ASC');
            }else{
                $records->orderBy('created_at', 'desc');
            }
           
        }
        
        $records = $records->paginate(5);

        $viewData['records'] = $records;
        $viewData['current_page'] = $records->currentPage() ?? 0;
        $viewData['last_page'] = $records->lastPage() ?? 0;
        $viewData['next_page'] = ($records->lastPage() ?? 0) != 0 ? ($records->currentPage() + 1) : 0;
        $viewData['case_id'] = $caseId;

        if ($isMobile) {
            $view = View::make('admin-panel.08-cases.cases.case-grid-ajax-list', $viewData);
        } else {
            $view = View::make('admin-panel.08-cases.cases.case-grid-ajax-list', $viewData);
        }

        $contents = $view->render();
        return [
            'contents' => $contents,
            'last_page' => $records->lastPage(),
            'current_page' => $records->currentPage(),
            'total_records' => $records->total()
        ];
    }

    public function toggleFavourite($userId, $caseUniqueId)
    {
        $case = Cases::where('unique_id', $caseUniqueId)->first();

        if ($case) {
            $exist = ProfessionalFavouriteCase::where('case_id', $case->id)->where('user_id', $userId)->first();

            if ($exist) {
                ProfessionalFavouriteCase::where('case_id', $case->id)->where('user_id', $userId)->delete();
                return ['status' => 'success', 'message' => 'Case removed from favourite successfully'];
            } else {
                $object = new ProfessionalFavouriteCase;
                $object->case_id = $case->id;
                $object->user_id = $userId;
                $object->save();
                return ['status' => 'success', 'message' => 'Case marked as favourite successfully'];
            }
        } else {
            return ['status' => 'false', 'message' => 'Case not found'];
        }
    }

    public function updatePrivacySettings($userId, $settings)
    {
        $incoming = collect($settings);
        $privacyIDs = ModulePrivacy::where('slug', config('privacysettings.CASE-POST-SETTINGS'))->first();
        $IDS = ModulePrivacyOptions::where('module_privacy_id', $privacyIDs->id)->first();
        $existingSettings = UserPrivacySettings::where('privacy_option_id', $IDS->id)
            ->where('user_id', $userId)
            ->first();

        if (isset($incoming[0]['value'])) {
            $existingSettings->privacy_option_value = "enable";
        } else {
            $existingSettings->privacy_option_value = "disable";
        }
        $existingSettings->save();
        return ['status' => 'success', 'message' => 'Status updated successfully!'];
    }

    public function setReadUnread($ids, $type)
    {
        $ids = explode(",", $ids);
        $isRead = $type == "read" ? 1 : 0;
        ChatNotification::whereIn('id', $ids)->update(['is_read' => $isRead]);
        $message = $type == "read" ? "Posting notification read" : "Posting notification unread";
        return ['status' => true, 'message' => $message];
    }

    public function getCaseDetails($userId, $caseUniqueId)
    {
        $record = Cases::where("unique_id", $caseUniqueId)->with(['services', 'subServices', 'comments', 'clientLocation'])->first();

        $professionalCaseView = ProfessionalCaseViewed::where('user_id', $userId)->where('case_id', $record->id)->first();

        if ($professionalCaseView) {
            $professionalCaseView->view_count = $professionalCaseView->view_count + 1;
            $professionalCaseView->last_view_date = now();
        } else {
            $professionalCaseView = new ProfessionalCaseViewed;
            $professionalCaseView->user_id = $userId;
            $professionalCaseView->case_id = $record->id;
            $professionalCaseView->view_count = 1;
            $professionalCaseView->last_view_date = now();
        }
        $professionalCaseView->save();

        $comment = CaseComment::where('case_id', $record->id)
            ->where('added_by', $userId)
            ->orderBy('id', 'desc')
            ->where('status', 'pending')
            ->first();

        $professionalService = ProfessionalServices::where('user_id', $userId)
            ->where('parent_service_id', $record->parent_service_id)
            ->where('service_id', $record->sub_service_id)
            ->first();

        $sub_services = [];
        if ($professionalService) {
            $sub_services = ProfessionalSubServices::with(['subServiceTypes'])
                ->where('professional_service_id', $professionalService->id)
                ->get();
        }

        $quotations = Quotation::where("service_id", $record->sub_service_id)->get();
        $case_quotations = CaseQuotation::where("case_id", $record->id)->where("added_by", $userId)->first();
        $case_history = ClientCaseHistory::where('client_case_id', $record->id)->where('professional_id', $userId)->first();
        $case_quotation_history = CaseQuotation::where("case_id", $record->id)->where("added_by", $userId)->get();

        $viewData['professionalProposal'] = CaseComment::where('case_id', $record->id)->where('added_by', '!=', $userId)->groupBy('added_by')->get()->count();
        $viewData['allProposal'] = CaseComment::where('case_id', $record->id)->where('status', 'sent')->where('added_by', $userId)->get()->count();
        $viewData['firstProposal'] = CaseComment::where('case_id', $record->id)->orderBy('id', 'asc')->first();
        $viewData['lastProposal'] = CaseComment::where('case_id', $record->id)->orderBy('id', 'desc')->first();
        $viewData['pageType'] = 'view';
        $viewData['record'] = $record;
        $viewData['case_history'] = $case_history;
        $viewData['quotations'] = $quotations;
        $viewData['case_quotations'] = $case_quotations;
        $viewData['pageTitle'] = "View Case Details";
        $viewData['comment'] = $comment;
        $viewData['sub_services'] = $sub_services;
        $viewData['case_quotation_history'] = $case_quotation_history;

        $proposalFeatureStatus = $this->featureCheckService->canAddProposal();
        $viewData['proposalFeatureStatus'] = $proposalFeatureStatus;
        $viewData['canAddProposal'] = $proposalFeatureStatus['allowed'];
        return $viewData;
    }

    public function getQuotationHtml($quotationId)
    {
        $quotation = Quotation::where("id", $quotationId)->first();
        $html = '';
        if ($quotation) {
            foreach ($quotation->particulars as $particular) {
                $html .= '<tr>';
                $html .= '<input type="hidden" name="items[' . $particular->id . '][id]" value="' . $particular->id . '" />';
                $html .= '<td><input type="text" class="item-name form-control" name="items[' . $particular->id . '][name]" placeholder="Enter item name" value="' . $particular->particular . '"></td>';
                $html .= '<td><input type="number" name="items[' . $particular->id . '][amount]" class="item-amount form-control" min="0" value="' . $particular->amount . '"></td>';
                $html .= '<td class="CDSPostCaseDetail-total-cell">' . $particular->amount . '<input type="hidden" class="row-sub-total form-control" name="items[' . $particular->id . '][row_sub_total]"  value="' . $particular->amount . '">';
                $html .= '</td>';
                $html .= '<td><button type="button" class="CDSPostCaseDetail-remove-btn invoice-remove-btn" onclick="removeQuotationItem(this)">X</button></td>';
                $html .= '</tr>';
            }
        }
        return ['status' => true, 'contents' => $html];
    }

    public function saveProposal($userId, $data)
    {
        try {
            
            // You should move validation to a FormRequest in the controller for best practice
            DB::beginTransaction();
            $case = Cases::where('id', $data['case_id'])->first();

            if (!$case) {
                return ['error' => 'Case not found.'];
            }

            $existingComment = CaseComment::where('case_id', $data['case_id'])
                ->where('status', 'sent')
                ->where('added_by', $userId)
                ->first();

            if ($existingComment) {
                $existingComment->status = 'withdraw';
                $existingComment->save();
            }

            $newComment = new CaseComment();
            $newComment->case_id = $data['case_id'];
            $newComment->comments = htmlentities($data['description']);
            $newComment->sub_service_type_id = $data['sub_service_type_id'];
            $newComment->added_by = $userId;
            $newComment->status = 'sent';
            $newComment->save();

            $case_comment_id = $newComment->id;

            $case_quotation = new CaseQuotation();
            if (isset($data['quotation_id']) && $data['quotation_id']) {
                $quotation = Quotation::where("id", $data['quotation_id'])->first();
                $case_quotation->currency = $quotation->currency;
            } else {
                $case_quotation->currency = 'CAD';
            }
            $case_quotation->case_id = $data['case_id'];
            $case_quotation->total_amount = $data['total_amount'];
            $case_quotation->client_id = $case->added_by;
            $case_quotation->added_by = $userId;
            $case_quotation->quotation_id = $data['quotation_id'] ?? null;
            $case_quotation->save();

            foreach ($data['items'] as $item) {
                CaseQuotationItem::create([
                    'particular' => $item['name'],
                    'amount' => $item['amount'],
                    'quotation_id' => $case_quotation->id
                ]);
            }

            // Notification logic (implement sendChatNotification and globalNotification as needed)
            if (function_exists('sendChatNotification')) {
                sendChatNotification([
                    'user_id' => $case->added_by,
                    'case_id' => $case->unique_id
                ]);
            }

            $caseProposalHistory = new CaseProposalHistory();
            $caseProposalHistory->case_id = $case->id;
            $caseProposalHistory->case_comment_id = $case_comment_id;
            $caseProposalHistory->case_quotation_id = $case_quotation->id;
            $caseProposalHistory->added_by = $userId;
            $caseProposalHistory->save();

            // Save case_submit_proposal feature usage to UserPlanFeatureHistory
            $featureResult = $this->featureCheckService->saveCaseSubmitProposal($userId, $case->id, [
                'case_comment_id' => $case_comment_id,
                'case_quotation_id' => $case_quotation->id,
                'case_unique_id' => $case->unique_id,
                'case_title' => $case->title,
                'total_amount' => $data['total_amount'],
                'currency' => $case_quotation->currency
            ]);

            // Log the feature tracking result
            if (!$featureResult['success']) {
                Log::warning('Failed to save case submit proposal feature usage', [
                    'user_id' => $userId,
                    'case_id' => $case->id,
                    'error' => $featureResult['message']
                ]);
            } else {
                Log::info('Case submit proposal feature usage saved successfully', [
                    'user_id' => $userId,
                    'case_id' => $case->id,
                    'history_id' => $featureResult['data']['history_id'] ?? null
                ]);
            }

            if (function_exists('globalNotification')) {
                globalNotification([
                    'case_proposals_count' => $case->comments ? $case->comments->count() : 0,
                    'user_id' => $case->added_by,
                    'case_id' => $case->unique_id,
                    'send_by' => $userId,
                    'comment' => 'New Proposal for Case ' . $case->title,
                    'type' => 'new_case_proposal',
                ]);
            }

            DB::commit();
            return [
                'status' => true,
                'redirect_back' => baseUrl('/cases/view/' . $case->unique_id),
                'message' => "Data Added successfully"
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }


      public function updateProposals($userId, $data)
    {
        try {
            // You should move validation to a FormRequest in the controller for best practice
            DB::beginTransaction();
            
            $case = Cases::where('id', $data['case_id'])->first();

            if (!$case) {
                return ['error' => 'Case not found.'];
            }

            // Get the existing comment to update
            $existingComment = CaseComment::where('id', $data['case_comment_id'])
                ->where('added_by', $userId)
                ->where('status', 'sent')
                ->first();

            if (!$existingComment) {
                return ['error' => 'Proposal not found or not authorized to edit.'];
            }

            // Update the comment
            $existingComment->comments = htmlentities($data['description']);
            $existingComment->sub_service_type_id = $data['sub_service_type_id'];
            $existingComment->save();

            // Get the case proposal history to find the quotation
            $caseProposalHistory = CaseProposalHistory::where('case_id', $data['case_id'])
                ->where('case_comment_id', $data['case_comment_id'])
                ->where('added_by', $userId)
                ->first();

            if ($caseProposalHistory) {
                // Update the case quotation
                $case_quotation = CaseQuotation::where('id', $caseProposalHistory->case_quotation_id)
                    ->where('added_by', $userId)
                    ->first();

                if ($case_quotation) {
                    if (isset($data['quotation_id']) && $data['quotation_id']) {
                        $quotation = Quotation::where("id", $data['quotation_id'])->first();
                        $case_quotation->currency = $quotation->currency;
                    } else {
                        $case_quotation->currency = 'CAD';
                    }
                    
                    $case_quotation->total_amount = $data['total_amount'];
                    $case_quotation->quotation_id = $data['quotation_id'] ?? null;
                    $case_quotation->save();

                    // Delete existing quotation items
                    CaseQuotationItem::where('quotation_id', $case_quotation->id)->delete();

                    // Create new quotation items
                    foreach ($data['items'] as $item) {
                        CaseQuotationItem::create([
                            'particular' => $item['name'],
                            'amount' => $item['amount'],
                            'quotation_id' => $case_quotation->id
                        ]);
                    }
                }
            }

            DB::commit();
            return [
                'status' => true,
                'redirect_back' => baseUrl('/cases/view/' . $case->unique_id),
                'message' => "Proposal updated successfully"
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function getProposalHistory($userId, $caseUniqueId)
    {
        $comment = Cases::where("unique_id", $caseUniqueId)->with(['services', 'subServices', 'comments'])->first();
        $records = CaseComment::with(['caseProposalHistory.caseQuotation.particulars'])
            ->where('case_id', $comment->id)
            ->where('added_by', $userId)
            ->orderBy('id', 'desc')
            ->paginate();
        $case_history = ClientCaseHistory::where('client_case_id', $comment->id)->first();

        $viewData['case_history'] = $case_history;
        $viewData['records'] = $records;
        $viewData['current_page'] = $records->currentPage() ?? 0;
        $viewData['last_page'] = $records->lastPage() ?? 0;
        $viewData['next_page'] = ($records->lastPage() ?? 0) != 0 ? ($records->currentPage() + 1) : 0;

        $view = View::make('admin-panel.08-cases.cases.proposal-history', $viewData);
        $contents = $view->render();

        return [
            'last_page' => $records->lastPage(),
            'current_page' => $records->currentPage(),
            'total_records' => $records->total(),
            'contents' => $contents
        ];
    }

    public function getEditProposalData($userId, $caseUniqueId)
    {
        $record = Cases::where("unique_id", $caseUniqueId)
            ->with(['services', 'subServices', 'comments'])
            ->first();

        $comment = CaseComment::where('case_id', $record->id)
            ->where('added_by', $userId)
            ->orderBy('id', 'desc')
            ->where('status', 'sent')
            ->first();

        $professionalService = ProfessionalServices::where('user_id', $userId)
            ->where('parent_service_id', $record->parent_service_id)
            ->where('service_id', $record->sub_service_id)
            ->first();

        $sub_services = [];
        if ($professionalService) {
            $sub_services = ProfessionalSubServices::with(['subServiceTypes'])
                ->where('professional_service_id', $professionalService->id)
                ->get();
        }

        $quotations = Quotation::where("service_id", $record->sub_service_id)->get();
        $case_praposal_history = CaseProposalHistory::where("case_id", $record->id)
            ->where('case_comment_id', $comment->id)
            ->where("added_by", $userId)
            ->first();

        $case_quotations = CaseQuotation::where("case_id", $record->id)
            ->where('id', $case_praposal_history->case_quotation_id)
            ->where("added_by", $userId)
            ->first();

        $case_history = ClientCaseHistory::where('client_case_id', $record->id)
            ->where('professional_id', $userId)
            ->first();

        $case_quotation_history = CaseQuotation::where("case_id", $record->id)
            ->where("added_by", $userId)
            ->get();

        $viewData['pageType'] = 'edit';
        $viewData['record'] = $record;
        $viewData['case_history'] = $case_history;
        $viewData['quotations'] = $quotations;
        $viewData['case_quotations'] = $case_quotations;
        $viewData['pageTitle'] = "View Case Details";
        $viewData['comment'] = $comment;
        $viewData['sub_services'] = $sub_services;
        $viewData['case_quotation_history'] = $case_quotation_history;

      
        $view = View::make("admin-panel.08-cases.cases.edit-proposal", $viewData);
        $contents = $view->render();

        return ['contents' => $contents, 'status' => true];
    }

    public function createOrGetGroup($userId, $caseUniqueId)
    {
        $case = Cases::where('unique_id', $caseUniqueId)->first();
        $group_members = [$userId, $case->added_by];
        $expectedCount = count($group_members);

        $caseChat = CaseChat::where('case_id', $case->id)
            ->whereHas('groupChat.groupMembers', function ($query) use ($group_members) {
                $query->whereIn('user_id', $group_members);
            }, '=', $expectedCount)
            ->with(['groupChat.groupMembers' => function ($query) use ($group_members) {
                $query->whereIn('user_id', $group_members);
            }])
            ->first();

        if (empty($caseChat)) {
            $group = new ChatGroup();
            $group->name = $case->title;
            $group->type = 'private';
            $group->description = 'Quick Case';
            $group->added_by = $userId;
            $group->save();

            foreach (array_unique($group_members) as $member) {
                $groupuser = new GroupMembers();
                $groupuser->unique_id = randomNumber();
                $groupuser->group_id = $group->id;
                $groupuser->user_id = $member;
                $groupuser->save();
            }

            $caseChat = new CaseChat();
            $caseChat->unique_id = randomNumber();
            $caseChat->case_id = $case->id;
            $caseChat->group_chat_id = $group->id;
            $caseChat->added_by = $userId;
            $caseChat->save();
            $response['group_id'] = $group->id;
            $response['group_unique_id'] = $group->unique_id;
        } else {
            $response['group_id'] = $caseChat->groupChat->id;
            $response['group_unique_id'] = $caseChat->groupChat->unique_id;
        }

        $response['status'] = true;
        $response['message'] = "Record added successfully";
        return $response;
    }

    public function withdrawProposal($userId, $caseUniqueId)
    {
        $case = Cases::where('unique_id', $caseUniqueId)->first();
        if ($case) {
            $case_history = ClientCaseHistory::where('client_case_id', $case->id)->where('professional_id', $userId)->first();
            if (empty($case_history)) {
                CaseComment::where('case_id', $case->id)->where('added_by', $userId)->update(['status' => 'withdraw']);
                return ['status' => 'success', 'message' => 'Proposal withdrawn successfully'];
            } else {
                return ['status' => 'error', 'message' => 'Proposal not withdrawn'];
            }
        }
        return ['status' => 'error', 'message' => 'Case not found'];
    }
} 