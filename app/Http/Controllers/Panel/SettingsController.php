<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DomainVerify;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Display security settings page
     *
     * @return \Illuminate\View\View
     */
    public function security(): View
    {
        try {
            $viewData = $this->settingsService->getSecurityData();
            return view("admin-panel.security-settings", $viewData);
        } catch (\Exception $e) {
            Log::error('Error loading security settings: ' . $e->getMessage());
            return view("admin-panel.security-settings", ['pageTitle' => "Security Settings"]);
        }
    }

    /**
     * Verify domain for user
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function domainVerify(Request $request): JsonResponse
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'domain_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Sanitize input
            $domainName = trim($request->input('domain_name'));
            
            // Log the action
            Log::info('Domain verification requested', [
                'user_id' => Auth::id(),
                'domain' => $domainName
            ]);

            $result = $this->settingsService->verifyDomain(Auth::id(), $domainName);
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Domain verification error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'domain' => $request->input('domain_name')
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while verifying domain'
            ], 500);
        }
    }

    /**
     * Verify domain TXT record
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyDomainTxt(): JsonResponse
    {
        try {
            // Log the action
            Log::info('Domain TXT verification requested', [
                'user_id' => Auth::id()
            ]);

            $result = $this->settingsService->verifyDomainTxt(Auth::id());
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Domain TXT verification error: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while verifying domain TXT record'
            ], 500);
        }
    }

    /**
     * Remove domain verification
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeDomain(): JsonResponse
    {
        try {
            // Log the action
            Log::info('Domain removal requested', [
                'user_id' => Auth::id()
            ]);

            $result = $this->settingsService->removeDomain(Auth::id());
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Domain removal error: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while removing domain'
            ], 500);
        }
    }

    /**
     * Update sidebar status
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sidebarStatus(Request $request): JsonResponse
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'status' => 'required|string|in:collapsed,expanded',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Sanitize input
            $status = trim($request->input('status', 'collapsed'));
            
            // Log the action
            Log::info('Sidebar status update requested', [
                'user_id' => Auth::id(),
                'status' => $status
            ]);

            $result = $this->settingsService->updateSidebarStatus($status);
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Sidebar status update error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'status' => $request->input('status')
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while updating sidebar status'
            ], 500);
        }
    }

    /**
     * Get global notifications
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGlobalNotification(Request $request): JsonResponse
    {
        try {
            // Log the action
            Log::info('Global notifications requested', [
                'user_id' => Auth::id()
            ]);

            $result = $this->settingsService->getGlobalNotifications();
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Global notifications error: ' . $e->getMessage(), [
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'An error occurred while fetching notifications'
            ], 500);
        }
    }
} 