<?php

namespace App\Services;

use App\Models\CaseStages;
use App\Models\CaseSubStages;
use App\Models\Forms;
use App\Models\CaseFolders;

class CaseStageService
{
    /**
     * Generate workflow stages and sub-stages for a case based on API response.
     *
     * @param  $case
     * @param  array $apiResponse
     * @param  int $userId
     * @return void
     */
    public function generateWorkflowStages($case, $apiResponse, $userId)
    {
        foreach ($apiResponse['workflow'] as $value) {
            $caseStage = $this->createStage($case, $value, $userId);

            if (!empty($value['sub_stages'])) {
                foreach ($value['sub_stages'] as $row) {
                    $this->createSubStage($case, $caseStage, $row, $userId);
                }
            }
        }
    }

    /**
     * Create a case stage.
     *
     * @param  $case
     * @param  array $stageData
     * @param  int $userId
     * @return CaseStages
     */
    public function createStage($case, $stageData, $userId)
    {
        $caseStage = new CaseStages();
        $caseStage->unique_id = $stageData['unique_id'] ?? randomNumber();
        $caseStage->name = $stageData['stage_name'] ?? null;
        $caseStage->short_description = $stageData['description'] ?? null;
        $caseStage->fees = $stageData['stage_fees'] ?? null;
        $caseStage->case_id = $stageData['case_id'] ?? $case->id;
        $caseStage->user_id = $stageData['user_id'] ?? $userId;
        $caseStage->stage_type = $stageData['stage_type'] ?? 'custom';
        $caseStage->added_by = $stageData['added_by'] ?? $userId;
        $caseStage->sort_order = $stageData['sort_order'] ?? null;
        $caseStage->status = $stageData['status'] ?? 'pending';
        if (isset($stageData['predefined_case_stage_id'])) {
            $caseStage->predefined_case_stage_id = $stageData['predefined_case_stage_id'];
        }
        $caseStage->save();
        return $caseStage;
    }

    /**
     * Create a case sub-stage.
     *
     * @param  $case
     * @param  CaseStages $caseStage
     * @param  array $subStageData
     * @param  int $userId
     * @return CaseSubStages
     */
    public function createSubStage($case, $caseStage, $subStageData, $userId)
    {
        $caseSubStage = new CaseSubStages();
        $caseSubStage->case_id = $case->id;
        $caseSubStage->stage_id = $caseStage->id;
        $caseSubStage->user_id = $subStageData['user_id'] ?? $userId;
        $caseSubStage->client_id = $subStageData['client_id'] ?? $case->client_id;
        $caseSubStage->unique_id = $subStageData['unique_id'] ?? randomNumber();
        $caseSubStage->name = $subStageData['name'] ?? null;
        $caseSubStage->stage_type = $subStageData['stage_type'] ?? 'case-document';
        $caseSubStage->status = $subStageData['status'] ?? 'pending';
        $caseSubStage->predefined_case_sub_stage_id = $subStageData['predefined_case_sub_stage_id'] ?? null;
        $caseSubStage->sort_order = $subStageData['sort_order'] ?? null;
        $caseSubStage->case_documents = $subStageData['case_documents'] ?? null;
        $caseSubStage->type_id = $subStageData['type_id'] ?? null;
        $caseSubStage->save();
        return $caseSubStage;
    }
} 