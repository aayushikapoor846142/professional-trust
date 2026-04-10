<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReviewsInvitations;
use Illuminate\Support\Facades\Validator;

class InvitationService
{
    const ROLE_CLIENT = 'client';
    const STATUS_ACTIVE = 'active';
    const STATUS_PENDING = 'pending';

    public function validateInvitationRequest($data)
    {
        return Validator::make($data, [
            'selected_clients' => 'required|json',
            'personal_message' => 'required',
        ]);
    }

    public function validateCsvUpload($data)
    {
        return Validator::make($data, [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function invitationExists($email, $userId, $status = null)
    {
        $query = ReviewsInvitations::where('email', $email)
            ->where('added_by', $userId);
        if ($status) {
            $query->where('status', $status);
        }
        return $query->first();
    }

    public function sendInvitationMail($email, $mailData)
    {
        $view = \View::make('emails.send-review-invitation', $mailData);
        $message = $view->render();

        sendMail([
            'to' => $email,
            'to_name' => '',
            'message' => $message,
            'subject' => "Share Your Thoughts About " . $mailData['professional_name'],
            'view' => 'emails.send-review-invitation',
            'data' => $mailData
        ]);
    }
} 