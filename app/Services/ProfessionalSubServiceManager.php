<?php

namespace App\Services;

use App\Models\ProfessionalServices;
use App\Models\ProfessionalSubServices;
use App\Models\ProfessionalServicesFees;
use App\Models\Forms;
use App\Models\ServiceAssesmentForm;
use App\Models\ServiceSendForm;
use App\Models\DocumentsFolder;
use App\Models\ServiceFormReply;
use Illuminate\Support\Facades\DB;

class ProfessionalSubServiceManager
{
    /**
     * Create a new ProfessionalSubService and related fees.
     *
     * @param ProfessionalServices $service
     * @param array $data
     * @return ProfessionalSubServices
     */
    public function createSubService(ProfessionalServices $service, array $data)
    {
        return DB::transaction(function () use ($service, $data) {
            $subService = ProfessionalSubServices::create([
                'unique_id' => randomNumber(),
                'user_id' => $data['user_id'],
                'service_id' => $service->service_id,
                'form_id' => $data['form_id'] ?? null,
                'sub_services_type_id' => $data['sub_services_type_id'],
                'professional_fees' => $data['professional_fees'],
                'consultancy_fees' => $data['consultancy_fees'],
                'description' => $data['description'],
                'professional_service_id' => $service->id,
                'document_folders' => !empty($data['document']) ? implode(',', $data['document']) : '',
                'added_by' => $data['user_id'],
            ]);
            if (!empty($data['schedule'])) {
                foreach ($data['schedule'] as $key => $value) {
                    ProfessionalServicesFees::create([
                        'unique_id' => randomNumber(),
                        'professional_sub_services_id' => $subService->id,
                        'schedule_no' => 'schedule ' . ($key + 1),
                        'price' => $value
                    ]);
                }
            }
            return $subService;
        });
    }

    /**
     * Update an existing ProfessionalSubService.
     *
     * @param ProfessionalSubServices $subService
     * @param array $data
     * @return bool
     */
    public function updateSubService(ProfessionalSubServices $subService, array $data)
    {
        return $subService->update([
            'sub_services_type_id' => $data['sub_services_type_id'],
            'professional_fees' => $data['professional_fees'] ?? 0,
            'consultancy_fees' => $data['consultancy_fees'] ?? 0,
            'tbd' => $data['tbd'] ?? 0,
            'minimum_fees' => $data['minimum_fees'] ?? 0,
            'maximum_fees' => $data['maximum_fees'] ?? 0,
            'form_id' => $data['form_id'] ?? null,
            'description' => $data['description'],
            'document_folders' => !empty($data['document']) ? implode(',', $data['document']) : '',
        ]);
    }

    /**
     * Delete a ProfessionalSubService and its related fees.
     *
     * @param ProfessionalSubServices $subService
     * @return void
     */
    public function deleteSubService(ProfessionalSubServices $subService)
    {
        DB::transaction(function () use ($subService) {
            ProfessionalServicesFees::where('professional_sub_services_id', $subService->id)->delete();
            $subService->delete();
        });
    }

    /**
     * Create a new document folder.
     *
     * @param array $data
     * @return DocumentsFolder
     */
    public function createDocumentFolder(array $data)
    {
        return DocumentsFolder::create([
            'unique_id' => randomNumber(),
            'name' => $data['name'],
            'slug' => str_slug($data['name']),
            'user_id' => $data['user_id'],
            'added_by' => $data['user_id'],
        ]);
    }

    /**
     * Generate and save an assessment form.
     *
     * @param ProfessionalServices $service
     * @param array $data
     * @return Forms
     */
    public function saveAssessmentForm(ProfessionalServices $service, array $data)
    {
        return DB::transaction(function () use ($service, $data) {
            $form = Forms::create([
                'unique_id' => randomNumber(),
                'added_by' => $data['user_id'],
                'name' => $data['formName'],
                'form_type' => $data['form_type'],
                'fg_field_json' => $data['fg_field_json'],
            ]);
            ServiceAssesmentForm::create([
                'unique_id' => randomNumber(),
                'added_by' => $data['user_id'],
                'professional_service_id' => $service->id,
                'service_id' => $service->service_id,
                'form_id' => $form->id
            ]);
            return $form;
        });
    }

    /**
     * Send a form to a user (existing or new).
     *
     * @param Forms $form
     * @param ServiceAssesmentForm $serviceForm
     * @param array $data
     * @return ServiceSendForm
     */
    public function sendForm(Forms $form, ServiceAssesmentForm $serviceForm, array $data)
    {
        $object = new ServiceSendForm();
        if ($data['existing_user'] === "yes" && !empty($data['user'])) {
            $object->user_id = $data['user']->id;
            $object->email = $data['user']->email;
        } else {
            $object->email = $data['email'];
        }
        $object->form_id = $form->id;
        $object->service_form_id = $serviceForm->id;
        $object->form_name = $form->name;
        $object->form_type = $form->form_type;
        $object->form_fields_json = $form->fg_field_json;
        $object->unique_id = randomNumber();
        $object->status = 'draft';
        $object->added_by = $data['user_id'];
        $object->save();
        return $object;
    }

    /**
     * Update a sent form's structure and name.
     *
     * @param ServiceSendForm $sendForm
     * @param array $data
     * @return bool
     */
    public function updateSendForm(ServiceSendForm $sendForm, array $data)
    {
        $sendForm->form_name = $data['form_name'];
        $sendForm->form_type = $data['form_type'];
        $sendForm->form_fields_json = json_encode($data['fg_fields']);
        return $sendForm->save();
    }

    /**
     * Mark a sent form as sent and optionally send an email (email logic not included).
     *
     * @param ServiceSendForm $sendForm
     * @return bool
     */
    public function markFormAsSent(ServiceSendForm $sendForm)
    {
        $sendForm->status = 'send';
        return $sendForm->save();
    }

    /**
     * Analyze an assessment reply and save the summary.
     *
     * @param ServiceSendForm $sendForm
     * @param ServiceFormReply $reply
     * @param string $summary
     * @return bool
     */
    public function saveAssessmentSummary(ServiceSendForm $sendForm, ServiceFormReply $reply, $summary)
    {
        $reply->assessment_summary = $summary;
        return $reply->save();
    }
} 