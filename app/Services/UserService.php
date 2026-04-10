<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserDetails;
use App\Models\Professional;
use App\Models\OtherProfessionalDetail;
use App\Models\CdsProfessionalLicense;
use App\Models\CdsRegulatoryBody;
use App\Models\CdsRegulatoryCountry;
use App\Models\CdsProfessionalDocuments;
use App\Models\DomainVerify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserService
{
    public function updateProfile($userId, $data)
    {
        $validator = Validator::make($data, [
            'first_name' => 'required|min:2|max:255|string_limit',
            'last_name' => 'required|min:2|max:255|string_limit',
            'country_code' => 'required',
            'phone_no' => 'string|max:15',
            'date_of_birth' => 'required',
            'gender' => 'required',
            'timezone' => 'required',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->toArray()
            ];
        }

        $user = User::find($userId);
        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found'
            ];
        }

        $cleanPhoneNumber = cleanPhoneNumber($data['phone_no']);

        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->country_code = $data['country_code'];
        $user->phone_no = $cleanPhoneNumber;
        $user->date_of_birth = $data['date_of_birth'];
        $user->gender = $data['gender'];
        $user->timezone = $data['timezone'];
        $user->save();

        // Update user details
        $userDetails = UserDetails::where('user_id', $userId)->first();
        if (!$userDetails) {
            $userDetails = new UserDetails();
            $userDetails->user_id = $userId;
        }

        $userDetails->gender = $data['gender'];
        $userDetails->date_of_birth = $data['date_of_birth'];
        $userDetails->country_id = $data['country_id'] ?? 0;
        $userDetails->state_id = $data['state_id'] ?? 0;
        $userDetails->city_id = $data['city_id'] ?? 0;
        $userDetails->address = $data['address'] ?? '';
        $userDetails->zip_code = $data['zip_code'] ?? '';
        $userDetails->languages_known = $data['languages_known'] ?? '';
        $userDetails->save();

        return [
            'status' => true,
            'message' => 'Profile updated successfully'
        ];
    }

    public function updatePassword($userId, $data)
    {
        $validator = Validator::make($data, [
            'current_password' => 'required',
            'new_password' => 'required|min:8|password_validation',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return [
                'status' => false,
                'message' => $validator->errors()->toArray()
            ];
        }

        $user = User::where('unique_id', $userId)->first();
        if (!$user) {
            return [
                'status' => false,
                'message' => 'User not found'
            ];
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            return [
                'status' => false,
                'message' => ['current_password' => 'Current password is incorrect']
            ];
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        // Store password history
        $user->storePasswordHistory();

        return [
            'status' => true,
            'message' => 'Password updated successfully'
        ];
    }

    public function getProfessionalData($userId)
    {
        $user = User::find($userId);
        if (!$user || $user->role !== 'professional') {
            return null;
        }

        $professionalList = Professional::with(['professionalWebsiteDetail', 'professionalAboutDetail'])
            ->where(['is_linked' => 1, 'linked_user_id' => $userId])
            ->first();

        if (empty($professionalList)) {
            $professionalList = Professional::create([
                'unique_id' => randomNumber(),
                'name' => $user->first_name . ' ' . $user->last_name,
                'is_linked' => 1,
                'linked_user_id' => $userId
            ]);
        }

        // Initialize extra details if empty
        $extraDetails = OtherProfessionalDetail::where('professional_id', $professionalList->id)->get();
        if ($extraDetails->isEmpty()) {
            foreach (extraDetails() as $value) {
                OtherProfessionalDetail::create([
                    'professional_id' => $professionalList->id,
                    'unique_id' => randomNumber(),
                    'meta_key' => $value,
                    'meta_value' => '',
                    'added_by' => $userId
                ]);
            }
        }

        $license_detail = CdsProfessionalLicense::where('added_by', $userId)->latest()->first();
        
        if (!empty($license_detail)) {
            $regulatory_bodies = CdsRegulatoryBody::where('regulatory_country_id', $license_detail->regulatory_country_id)->get();
        } else {
            $regulatory_bodies = CdsRegulatoryBody::get();
        }

        $user_details = UserDetails::where('user_id', $userId)->first();
        
        $document = collect();
        if ($user->cdsCompanyDetail) {
            $document = CdsProfessionalDocuments::where('company_id', $user->cdsCompanyDetail->id)->get();
        }

        return [
            'professionalList' => $professionalList,
            'license_detail' => $license_detail,
            'regulatory_bodies' => $regulatory_bodies,
            'regulatory_countries' => CdsRegulatoryCountry::get(),
            'user_details' => $user_details,
            'document' => $document
        ];
    }

    public function verifyDomain($userId, $domain)
    {
        // Check if domain already exists
        $existingDomain = DomainVerify::where('domain_name', $domain)->first();
        if ($existingDomain && $existingDomain->user_id != $userId) {
            return [
                'status' => false,
                'message' => 'Domain already verified by another user'
            ];
        }

        // Create or update domain verification
        $domainVerify = DomainVerify::updateOrCreate(
            ['user_id' => $userId],
            [
                'domain_name' => $domain,
                'status' => 'pending',
                'verification_method' => 'dns'
            ]
        );

        return [
            'status' => true,
            'message' => 'Domain verification initiated',
            'data' => $domainVerify
        ];
    }
} 