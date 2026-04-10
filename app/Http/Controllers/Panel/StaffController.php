<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Services\FeatureCheckService;
use View;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Country;
use Illuminate\Http\Request;
use DB;
use App\Rules\PasswordValidation;
use App\Models\StaffUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View as IlluminateView;
use App\Models\Roles;


class StaffController extends Controller
{
    protected $featureCheckService;
    
    public function __construct()
    {
        $this->featureCheckService = new FeatureCheckService();
    }
    /**
     * Validation rules for staff creation
     */
    private const CREATE_VALIDATION_RULES = [
        'email' => 'required|email|min:5|max:50|unique:users,email|valid_email',
        'first_name' => 'required|min:2|max:20',
        'last_name' => 'required|min:2|max:20',
        'country_code' => 'required',
        'phone_no' => 'required|unique:users,phone_no',
        'password' => 'required|confirmed|password_validation',
        'password_confirmation' => 'required|password_validation',
        'role' => 'required'
    ];

    /**
     * Validation rules for staff update
     */
    private const UPDATE_VALIDATION_RULES = [
        'email' => 'required|min:5|max:50|valid_email|unique:users,email,',
        'first_name' => 'required|min:2|max:50',
        'last_name' => 'required|min:2|max:50',
        'country_code' => 'required',
        'phone_no' => 'required|unique:users,phone_no,',
        'role' => 'required'
    ];

    /**
     * Validation rules for password update
     */
    private const PASSWORD_VALIDATION_RULES = [
        'old_password' => 'required',
        'password' => 'required|confirmed|different:old_password|password_validation',
        'password_confirmation' => 'required|password_validation',
    ];

    /**
     * Cache for roles to avoid repeated database queries
     */
    private $cachedRoles = null;
    
    /**
     * Cache for staff IDs to avoid repeated database queries
     */
    private $cachedStaffIds = null;

    // =========================================================================
    // PRIVATE HELPER METHODS
    // =========================================================================

    /**
     * Get cached roles for the current user
     */
    private function getCachedRoles(): array
    {
        if ($this->cachedRoles === null) {
            $this->cachedRoles = getRoles()->pluck('slug')->toArray();
        }
        return $this->cachedRoles;
    }

    /**
     * Get cached staff IDs for the current user
     */
    private function getCachedStaffIds(): array
    {
        if ($this->cachedStaffIds === null) {
            $this->cachedStaffIds = fetchStaffOfSpecificProfessional(Auth::user()->id);
        }
        return $this->cachedStaffIds;
    }

    /**
     * Format validation errors for consistent JSON response
     */
    private function formatValidationErrors($validator): array
    {
        $error = $validator->errors()->toArray();
        $errMsg = [];
        foreach ($error as $key => $err) {
            $errMsg[$key] = $err[0];
        }
        return $errMsg;
    }

    /**
     * Build base query for staff listing with common filters
     */
    private function buildStaffQuery(string $search = '',$role, bool $includeTrashed = false, string $sortColumn, string $sortDirection,$status)
    {
        $query = $includeTrashed ? User::onlyTrashed() : User::query();
       
        $query->orderBy($sortColumn, $sortDirection)
            ->where('id', '!=', Auth::user()->id)
            ->whereIn('role', $this->getCachedRoles())
            ->where(function ($query) use ($search) {
                if ($search !== '') {
                    $query->where('first_name', 'LIKE', "%{$search}%")
                        ->orWhere('last_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                }
            })
           
            ->when($role, function ($query, $role) {
                if ($role !== '') {
                    $query->whereIn('role', $role);
                }
            })
            ->where(function ($query) {
                $query->visibleToUser(Auth::user()->id);
                
                $staffIds = $this->getCachedStaffIds();
                if (!empty($staffIds)) {
                    $query->orWhereIn('added_by', $staffIds);
                }
            });

            if ($status) {
                $statuses = is_array($status) ? $status : [$status];
                $query->whereIn('status', $statuses);
            }
        return $query;
    }

    /**
     * Generate JSON response for AJAX views
     */
    private function generateAjaxResponse(string $viewPath, array $viewData, $records = null): JsonResponse
    {
        $view = View::make($viewPath, $viewData);
        $contents = $view->render();
        
        $response = [
            'status' => true,
            'contents' => $contents
        ];

        if ($records) {
            $response['last_page'] = $records->lastPage();
            $response['current_page'] = $records->currentPage();
            $response['total_records'] = $records->total();
        }

        return response()->json($response);
    }

    /**
     * Create a new user with staff relationship
     */
    private function createStaffUser(array $data): User
    {
        $user = new User();
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->email = $data['email'];
        $user->country_code = $data['country_code'];
        $user->phone_no = $data['phone_no'];
        $user->status = $data['status'];
        $user->unique_id = randomNumber();
        $user->role = $data['role'];
        $user->password = bcrypt($data['password']);
        $user->is_verified = 1;
        $user->added_by = Auth::user()->id;
        $user->social_connect = 0;
        $user->save();

        // Create staff user relationship
        $professional = staffProfessionals(Auth::user()->id);
        $addedBy = (Auth::user()->role === "professional") ? Auth::user()->id : $professional->added_by;
        
        StaffUser::create([
            'user_id' => $user->id,
            'added_by' => $addedBy,
        ]);

        // Save plan feature usage to history with error handling
        try {
            $result = $this->featureCheckService->savePlanFeature(
                'staff', 
                Auth::user()->id, 
                1, // action type: add
                1, // count: 1 staff member
                [
                    'staff_user_id' => $user->id,
                    'staff_email' => $user->email,
                    'staff_role' => $user->role,
                    'staff_name' => $user->first_name . ' ' . $user->last_name
                ]
            );

            // Log the result for debugging
            \Log::info('savePlanFeature result for staff creation', [
                'user_id' => Auth::user()->id,
                'staff_user_id' => $user->id,
                'result' => $result
            ]);

            if (!$result['success']) {
                \Log::error('Failed to save plan feature usage', [
                    'user_id' => Auth::user()->id,
                    'staff_user_id' => $user->id,
                    'error' => $result['message']
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Exception in savePlanFeature', [
                'user_id' => Auth::user()->id,
                'staff_user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $user;
    }

    /**
     * Update user profile image if provided
     */
    private function handleProfileImageUpload(User $user, $file): void
    {
        if ($file) {
            $fileName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension() ?: 'png';
            $newName = mt_rand(1, 99999) . "-" . $fileName;
            $destinationPath = UserDir() . "/profile";
            
            if ($file->move($destinationPath, $newName)) {
                $user->profile_image = $newName;
            }
        }
    }

    /**
     * Validate and update user password
     */
    private function updateUserPassword(User $user, string $newPassword): void
    {
        $user->password = bcrypt($newPassword);
        $user->save();
        $user->storePasswordHistory();
    }

    /**
     * Get user by unique ID with authorization check
     */
    private function getAuthorizedUser(string $uniqueId): ?User
    {
        $user = User::where('unique_id', $uniqueId)->first();
        
        if (!$user || $user->added_by !== Auth::user()->id) {
            return null;
        }

        return $user;
    }

    // =========================================================================
    // PUBLIC METHODS - VIEW RENDERING
    // =========================================================================

    /**
     * Display the staff listing page.
     */
    public function index(): IlluminateView
    {
        // return checkPrivilege([
        //     'route_prefix' => 'panel.staff',
        //     'module' => 'professional-staff',
        //     'action' => 'list'
        // ]);
        
        // Get feature status for staff management (now includes all limit details)
        $staffFeatureStatus = $this->featureCheckService->canAddStaff();
        
        $viewData['pageTitle'] = "Staff";
        $viewData['staffFeatureStatus'] = $staffFeatureStatus;
        $viewData['canAddStaff'] = $staffFeatureStatus['allowed'];
      
        return view('admin-panel.06-roles.staff.lists', $viewData);
    }

    /**
     * Show the form for adding a new staff member.
     */
    public function add(): IlluminateView
    {
        // Check if user can add staff
        // $staffCheck = $this->featureCheckService->canAddStaff();
        
        // if (!$staffCheck['allowed']) {
        //     return view('errors.not-authorized', [
        //         'message' => $staffCheck['message']
        //     ]);
        // }

        $viewData['pageTitle'] = "Add Staff";
        $viewData['countries'] = Country::get();
        return view('admin-panel.06-roles.staff.add', $viewData);
    }

    /**
     * Show the form for editing a specific staff member.
     */
    public function edit(string $id, Request $request): IlluminateView
    {

        $record = User::where('unique_id', $id)->first();

        $viewData['pageTitle'] = "Edit Staff";
        $viewData['record'] = $record;
        $viewData['countries'] = Country::get();

        return view('admin-panel.06-roles.staff.edit', $viewData);
    }

    /**
     * Show the form to change a specific staff member's password.
     */
    public function changePassword(string $id): IlluminateView
    {
        $record = User::where("unique_id", $id)->first();
        $viewData['record'] = $record;
        $viewData['pageTitle'] = "Change Password";
        return view('admin-panel.06-roles.staff.change-password', $viewData);
    }

    /**
     * Display trash staff listing page
     */
    public function trashStaffs(Request $request): IlluminateView
    {
        $viewData['pageTitle'] = "Staff";
      
        return view('admin-panel.06-roles.staff.trash-lists', $viewData);
    }

    // =========================================================================
    // PUBLIC METHODS - AJAX RESPONSES
    // =========================================================================

    /**
     * Get active staff list via AJAX
     */
    public function activeStaffList(Request $request): JsonResponse
    {
        $viewData['pageTitle'] = "Staff";
        $viewData['staffStatus']  = User::distinct()->pluck('status');
        $viewData['staffRoles']  = Roles::where('added_by',auth()->user()->id)->distinct()->pluck('name');
       
        return $this->generateAjaxResponse('admin-panel.06-roles.staff.active-staff-list', $viewData);
    }

    /**
     * Get trash staff list via AJAX
     */
    public function trashStaffList(Request $request): JsonResponse
    {
        $viewData['pageTitle'] = "Trash Staff";
        return $this->generateAjaxResponse('admin-panel.06-roles.staff.trash-staff-list', $viewData);
    }

    /**
     * Fetch the staff list via AJAX with optional search and pagination.
     */
    public function getAjaxList(Request $request): JsonResponse
    {
        $search = $request->input("search") ?? '';
        $role = $request->input("role") ?? '';
         $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');
        $status = $request->status;
        $role = $request->role;
        $records = $this->buildStaffQuery($search, $role, false, $sortColumn, $sortDirection,$status,$role)->paginate();
        
        $viewData['records'] = $records;
        return $this->generateAjaxResponse('admin-panel.06-roles.staff.ajax-list', $viewData, $records);
    }

    /**
     * Fetch the trash staff list via AJAX with optional search and pagination.
     */
    public function getTrashStaffsAjaxList(Request $request): JsonResponse
    {
        
        $search = $request->input("search") ?? '';
        $role = $request->input("role") ?? '';
        $sortColumn = $request->filled('sort_column') ? $request->input('sort_column') : 'created_at';
        $sortDirection = $request->input('sort_direction', 'asc');
        $status = $request->input("status") ?? '';
          $records = $this->buildStaffQuery($search, $role, false, $sortColumn, $sortDirection,$status,$role)->paginate();
            // $records = $this->buildStaffQuery($search, $role, true, $sortColumn, $sortDirection)->paginate();
        
        $viewData['records'] = $records;
        return $this->generateAjaxResponse('admin-panel.06-roles.staff.trash-ajax-list', $viewData, $records);
    }

    // =========================================================================
    // PUBLIC METHODS - CRUD OPERATIONS
    // =========================================================================

    /**
     * Save a newly created staff member to the database.
     */
    public function save(Request $request): JsonResponse
    {
        // Check if user can add staff
        $staffCheck = $this->featureCheckService->canAddStaff();
        
        if (!$staffCheck['allowed']) {
            return response()->json([
                'status' => false,
                'message' => $staffCheck['message']
            ]);
        }

        $validator = Validator::make($request->all(), self::CREATE_VALIDATION_RULES);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $this->formatValidationErrors($validator)
            ]);
        }

        DB::beginTransaction();
        try {
            $cleanPhoneNumber = cleanPhoneNumber($request->input("phone_no"));

            $userData = [
                'first_name' => $request->input("first_name"),
                'last_name' => $request->input("last_name"),
                'email' => $request->input("email"),
                'country_code' => $request->input("country_code"),
                'phone_no' => $cleanPhoneNumber,
                'status' => $request->input("status"),
                'role' => $request->input("role"),
                'password' => $request->input("password")
            ];

            $this->createStaffUser($userData);

            DB::commit();

            return response()->json([
                'status' => true,
                'redirect_back' => baseUrl('staff'),
                'message' => "Record added successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => "An error occurred while saving the record: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Update a specific staff member in the database.
     */
    public function update(string $id, Request $request): JsonResponse
    {
        $user = User::where('unique_id', $id)->first();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => "Staff member not found"
            ]);
        }

        $rules = self::UPDATE_VALIDATION_RULES;
        $rules['email'] .= $user->id;
        $rules['phone_no'] .= $user->id;

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $this->formatValidationErrors($validator)
            ]);
        }

        DB::beginTransaction();
        try {
            $cleanPhoneNumber = cleanPhoneNumber($request->input("phone_no"));

            $user->first_name = $request->input("first_name");
            $user->last_name = $request->input("last_name");
            $user->email = $request->input("email");
            $user->country_code = $request->input("country_code");
            $user->phone_no = $cleanPhoneNumber;
            $user->status = $request->input("status");
            $user->unique_id = randomNumber();
            $user->role = $request->input('role');

            // Handle profile image upload
            $this->handleProfileImageUpload($user, $request->file('profile_image'));

            $user->is_verified = 1;
            $user->added_by = Auth::user()->id;
            $user->social_connect = 0;
            $user->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'redirect_back' => baseUrl('staff'),
                'message' => "Update successful"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => "An error occurred while updating the record: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Update a specific staff member's password.
     */
    public function updatePassword(string $id, Request $request): JsonResponse
    {
        $user = User::where('unique_id', $id)->first();
        
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => "Staff member not found"
            ]);
        }

        $validator = Validator::make($request->all(), self::PASSWORD_VALIDATION_RULES);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $this->formatValidationErrors($validator)
            ]);
        }

        // Check the old password is correct
        if (!Hash::check($request->input("old_password"), $user->password)) {
            return response()->json([
                'status' => false,
                'message' => "The old password is incorrect."
            ]);
        }

        // Check new password is not in history
        if ($user->isPasswordInHistory($request->input("password"))) {
            return response()->json([
                'status' => false,
                'message' => "You cannot reuse an old password."
            ]);
        }

        DB::beginTransaction();
        try {
            $this->updateUserPassword($user, $request->input("password"));
            
            DB::commit();

            return response()->json([
                'status' => true,
                'redirect_back' => baseUrl('staff'),
                'message' => "Password updated successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => "An error occurred while updating the password: " . $e->getMessage()
            ]);
        }
    }

    // =========================================================================
    // PUBLIC METHODS - DELETE & RESTORE OPERATIONS
    // =========================================================================

    /**
     * Delete a specific staff member.
     */
    public function deleteSingle(string $id)
    {
        // Validate staff removal before proceeding
        $validationResult = $this->featureCheckService->validateStaffRemoval();
        
        if (!$validationResult['can_remove']) {
            return redirect()->back()->with("error", $validationResult['message']);
        }
        
        // Get staff details before deletion for history tracking
        $staffUser = User::where('unique_id', $id)->first();
        
        // Perform the deletion
        User::deleteRecord($id);
        
        // Delete the specific history entry for this staff member
        if ($staffUser) {
            $this->featureCheckService->deletePlanFeatureHistory(
                'staff',
                Auth::user()->id,
                $staffUser->id
            );
        }
        
        // Handle staff removal and get updated limit information
        $removalResult = $this->featureCheckService->handleStaffRemoval();
        
        $successMessage = "Record has been deleted!";
        if ($removalResult['slots_freed'] > 0) {
            $successMessage .= " Staff slot freed successfully.";
        }
        
        return redirect()->back()->with("success", $successMessage);
    }

    /**
     * Delete multiple staff members based on their IDs.
     */
    public function deleteMultiple(Request $request): JsonResponse
    {
        $ids = explode(",", $request->input("ids"));
        
        // Validate staff removal before proceeding
        $validationResult = $this->featureCheckService->validateStaffRemoval();
        
        if (!$validationResult['can_remove']) {
            return response()->json([
                'status' => false,
                'message' => $validationResult['message']
            ]);
        }
        
        $deletedCount = 0;
        $deletedStaff = [];
        
        foreach ($ids as $id) {
            // Get staff details before deletion for history tracking
            $staffUser = User::where('unique_id', $id)->first();
            if ($staffUser) {
                $deletedStaff[] = $staffUser;
            }
            
            User::deleteRecord($id);
            $deletedCount++;
        }
        
        // Delete the specific history entries for all deleted staff members
        if (!empty($deletedStaff)) {
            foreach ($deletedStaff as $staffUser) {
                $this->featureCheckService->deletePlanFeatureHistory(
                    'staff',
                    Auth::user()->id,
                    $staffUser->id
                );
            }
        }
        
        // Handle staff removal and get updated limit information
        $removalResult = $this->featureCheckService->handleStaffRemoval();
        
        $successMessage = "Records deleted successfully";
        if ($removalResult['slots_freed'] > 0) {
            $successMessage .= ". {$removalResult['slots_freed']} staff slot(s) freed.";
        }
        
        Session::flash('success', $successMessage);
        return response()->json([
            'status' => true,
            'message' => $successMessage,
            'deleted_count' => $deletedCount,
            'remaining_slots' => $removalResult['remaining']
        ]);
    }

    /**
     * Restore a single staff member from trash
     */
    public function restoreSingle(string $id)
    {
        // Check if user can add staff before restoring
        $staffCheck = $this->featureCheckService->canAddStaff();
        
        if (!$staffCheck['allowed']) {
            return redirect()->back()->with("error", "Cannot restore staff member. {$staffCheck['message']}");
        }
        
        User::restoreRecord($id);
        return redirect()->back()->with("success", "Record has been restored!");
    }

    /**
     * Restore multiple staff members from trash
     */
    public function restoreMultiple(Request $request): JsonResponse
    {
        $ids = explode(",", $request->input("ids"));
        
        // Check if user can add staff before restoring
        $staffCheck = $this->featureCheckService->canAddStaff();
        
        if (!$staffCheck['allowed']) {
            return response()->json([
                'status' => false,
                'message' => "Cannot restore staff members. {$staffCheck['message']}"
            ]);
        }
        
        $restoredCount = 0;
        foreach ($ids as $id) {
            User::restoreRecord($id);
            $restoredCount++;
        }
        
        Session::flash('success', 'Records restored successfully');
        return response()->json([
            'status' => true,
            'message' => 'Records restored successfully',
            'restored_count' => $restoredCount
        ]);
    }
}
