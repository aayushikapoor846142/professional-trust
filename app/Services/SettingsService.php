<?php

namespace App\Services;

use App\Models\DomainVerify;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    /**
     * Get security settings data
     *
     * @return array
     */
    public function getSecurityData(): array
    {
        try {
            $viewData = [];
            $viewData['pageTitle'] = "Security Settings";
            
            // Add any additional security-related data here
            // For example, user's security settings, 2FA status, etc.
            
            return $viewData;
        } catch (\Exception $e) {
            Log::error('Error getting security data: ' . $e->getMessage());
            return ['pageTitle' => "Security Settings"];
        }
    }

    /**
     * Verify domain for user
     *
     * @param int $userId
     * @param string $domain
     * @return array
     */
    public function verifyDomain(int $userId, string $domain): array
    {
        try {
            // Validate domain format
            $validator = Validator::make(['domain_name' => $domain], [
                'domain_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return [
                    'status' => false,
                    'message' => $validator->errors()->first()
                ];
            }

            // Clean and normalize domain
            $domain = trim(strtolower($domain));
            
            // Remove protocol if present
            $domain = preg_replace('/^https?:\/\//', '', $domain);
            
            // Remove trailing slash
            $domain = rtrim($domain, '/');

            // Check if domain already exists for another user
            $existingDomain = DomainVerify::where('domain', $domain)
                ->where('user_id', '!=', $userId)
                ->first();

            if ($existingDomain) {
                return [
                    'status' => false,
                    'message' => 'Domain already verified by another user'
                ];
            }

            // Generate DNS TXT record
            $dnsTxtRecord = generateDnsTxt();
            $txtFile = base64_encode($dnsTxtRecord);

            // Create or update domain verification
            $domainVerify = DomainVerify::updateOrCreate(
                ['user_id' => $userId],
                [
                    'domain' => $domain,
                    'dns_txt_record' => $dnsTxtRecord,
                    'txt_file' => $txtFile,
                    'domain_verify' => 'pending',
                    'domain_file_verify' => 'pending'
                ]
            );

            Log::info('Domain verification created/updated', [
                'user_id' => $userId,
                'domain' => $domain,
                'domain_verify_id' => $domainVerify->id
            ]);

            return [
                'status' => true,
                'message' => 'Domain verification initiated',
                'data' => $domainVerify
            ];

        } catch (\Exception $e) {
            Log::error('Domain verification error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'domain' => $domain
            ]);

            return [
                'status' => false,
                'message' => 'An error occurred while processing domain verification'
            ];
        }
    }

    /**
     * Verify domain TXT record
     *
     * @param int $userId
     * @return array
     */
    public function verifyDomainTxt(int $userId): array
    {
        try {
            $domainVerify = DomainVerify::where('user_id', $userId)->first();

            if (!$domainVerify) {
                return [
                    'status' => false,
                    'message' => 'No domain found for verification'
                ];
            }

            // Verify TXT record using helper function
            if (verifyTxtRecord($domainVerify->domain, $domainVerify->dns_txt_record)) {
                $domainVerify->update(['domain_verify' => 'verified']);
                
                Log::info('Domain TXT verification successful', [
                    'user_id' => $userId,
                    'domain' => $domainVerify->domain
                ]);

                return [
                    'status' => true,
                    'message' => 'Domain TXT verification completed successfully'
                ];
            } else {
                return [
                    'status' => false,
                    'message' => 'Domain TXT verification failed. Please check your DNS settings.'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Domain TXT verification error: ' . $e->getMessage(), [
                'user_id' => $userId
            ]);

            return [
                'status' => false,
                'message' => 'An error occurred while verifying domain TXT record'
            ];
        }
    }

    /**
     * Remove domain verification
     *
     * @param int $userId
     * @return array
     */
    public function removeDomain(int $userId): array
    {
        try {
            $domainVerify = DomainVerify::where('user_id', $userId)->first();

            if ($domainVerify) {
                $domain = $domainVerify->domain;
                $domainVerify->delete();
                
                Log::info('Domain removed successfully', [
                    'user_id' => $userId,
                    'domain' => $domain
                ]);
            }

            return [
                'status' => true,
                'message' => 'Domain removed successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Domain removal error: ' . $e->getMessage(), [
                'user_id' => $userId
            ]);

            return [
                'status' => false,
                'message' => 'An error occurred while removing domain'
            ];
        }
    }

    /**
     * Update sidebar status
     *
     * @param string $status
     * @return array
     */
    public function updateSidebarStatus(string $status): array
    {
        try {
            // Validate status
            if (!in_array($status, ['collapsed', 'expanded'])) {
                return [
                    'status' => false,
                    'message' => 'Invalid sidebar status'
                ];
            }

            // Store in session
            session(['sidebar_status' => $status]);

            // Optionally store in database for persistence across sessions
            // You can add this if needed
            
            Log::info('Sidebar status updated', [
                'user_id' => auth()->id(),
                'status' => $status
            ]);

            return [
                'status' => true,
                'message' => 'Sidebar status updated successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Sidebar status update error: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'status' => $status
            ]);

            return [
                'status' => false,
                'message' => 'An error occurred while updating sidebar status'
            ];
        }
    }

    /**
     * Get global notifications
     *
     * @return array
     */
    public function getGlobalNotifications(): array
    {
        try {
            // Use cache to improve performance
            $cacheKey = 'global_notifications_' . auth()->id();
            
            $notifications = Cache::remember($cacheKey, 300, function () {
                // Implement your notification logic here
                // For example, fetch from database
                return [
                    // Add your notification data structure here
                ];
            });

            return [
                'status' => true,
                'data' => $notifications
            ];

        } catch (\Exception $e) {
            Log::error('Global notifications error: ' . $e->getMessage(), [
                'user_id' => auth()->id()
            ]);

            return [
                'status' => false,
                'message' => 'An error occurred while fetching notifications',
                'data' => []
            ];
        }
    }
} 