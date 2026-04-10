<?php

namespace App\Services;

use App\Models\CdsProfessionalCompany;
use App\Models\CompanyLocations;
use App\Models\AppointmentBooking;
use App\Models\User;
use App\Models\ReviewsInvitations;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    /**
     * Validation rules for company creation/update.
     */
    public function companyValidationRules()
    {
        return [
            'company_name' => 'required',
            'owner_type' => 'required',
            'company_type' => 'required',
            'about_company' => 'required',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * Format validation errors for JSON responses.
     */
    public function formatValidationErrors($validator)
    {
        $error = $validator->errors()->toArray();
        $errMsg = [];
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return $errMsg;
    }

    /**
     * Create a new company record.
     * @param array $data
     * @param int $userId
     * @return CdsProfessionalCompany
     */
    public function createCompany(array $data, $userId)
    {
        return CdsProfessionalCompany::create([
            'user_id' => $userId,
            'company_name' => $data['company_name'],
            'owner_type' => $data['owner_type'],
            'company_type' => $data['company_type'],
            'about_company' => $data['about_company'],
            'is_primary' => 0
        ]);
    }

    /**
     * Update an existing company record.
     * @param string $uid
     * @param array $data
     * @param int $userId
     * @return int (number of affected rows)
     */
    public function updateCompany($uid, array $data, $userId)
    {
        return CdsProfessionalCompany::where('unique_id', $uid)->update([
            'user_id' => $userId,
            'company_name' => $data['company_name'],
            'owner_type' => $data['owner_type'],
            'company_type' => $data['company_type'],
            'about_company' => $data['about_company'],
        ]);
    }

    /**
     * Delete a company and its locations after checking for restricted appointments.
     * @param string $uid
     * @return array [success(bool), message(string)]
     */
    public function deleteCompanyWithLocations($uid)
    {
        $company = CdsProfessionalCompany::where('unique_id', $uid)->first();
        if (!$company) {
            return [false, 'Company not found.'];
        }
        $locations = CompanyLocations::where('company_id', $company->id)->get();
        $blockedStatuses = ['approved', 'awaiting'];
        foreach ($locations as $location) {
            $hasBlockedAppointments = AppointmentBooking::where('location_id', $location->id)
                ->where('appointment_date', '>=', now())
                ->whereIn('status', $blockedStatuses)
                ->exists();
            if ($hasBlockedAppointments) {
                return [false, 'Cannot delete company: One or more locations have upcoming appointments in restricted statuses.'];
            }
        }
        foreach ($locations as $location) {
            CompanyLocations::deleteRecord($location->id);
        }
        CdsProfessionalCompany::deleteRecord($uid);
        return [true, 'Company and all associated locations deleted successfully.'];
    }

    /**
     * Bulk create invitations and send emails.
     * @param array $emails
     * @param string $templateContent
     * @param string $templateSubject
     * @param int $addedById
     * @param string $professionalName
     * @return int Number of invitations sent
     */
    public function bulkInvite(array $emails, $templateContent, $templateSubject, $addedById, $professionalName)
    {
        $bulkInvites = [];
        $emailsToSend = [];
        $now = now();
        foreach ($emails as $email) {
            $token = Str::random(64);
            $userId = User::where('email', $email)->value('id') ?? 0;
            $bulkInvites[] = [
                'email' => $email,
                'token' => $token,
                'added_by' => $addedById,
                'status' => 'pending',
                'user_id' => $userId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $emailsToSend[] = [
                'email' => $email,
                'token' => $token,
            ];
        }
        if (!empty($bulkInvites)) {
            ReviewsInvitations::insert($bulkInvites);
            foreach ($emailsToSend as $invite) {
                $mailData = [
                    'token' => $invite['token'],
                    'template_content' => $templateContent,
                    'professional_name' => $professionalName,
                ];
                $view = \View::make('emails.review_invitations', $mailData);
                $message = $view->render();
                $parameter = [
                    'to' => $invite['email'],
                    'to_name' => '',
                    'message' => $message,
                    'subject' => $templateSubject,
                    'view' => 'emails.review_invitations',
                    'data' => $mailData,
                ];
                sendMail($parameter);
            }
        }
        return count($bulkInvites);
    }

    /**
     * Create and send a single invitation.
     * @param string $email
     * @param string $templateContent
     * @param string $templateSubject
     * @param int $addedById
     * @param string $professionalName
     */
    public function createAndSendInvitation($email, $templateContent, $templateSubject, $addedById, $professionalName)
    {
        $token = Str::random(64);
        $userId = User::where('email', $email)->value('id') ?? 0;
        ReviewsInvitations::create([
            'email' => $email,
            'token' => $token,
            'added_by' => $addedById,
            'status' => 'pending',
            'user_id' => $userId
        ]);
        $mailData = [
            'token' => $token,
            'template_content' => $templateContent,
            'professional_name' => $professionalName,
        ];
        $view = \View::make('emails.review_invitations', $mailData);
        $message = $view->render();
        sendMail([
            'to' => $email,
            'to_name' => '',
            'message' => $message,
            'subject' => $templateSubject,
            'view' => 'emails.review_invitations',
            'data' => $mailData
        ]);
    }
} 