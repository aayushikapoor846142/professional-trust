<?php

use App\Models\AppointmentBooking;
use App\Models\AppointmentBookingFlow;
use App\Models\Article;
use App\Models\CaseRetainAgreements;
use App\Models\CaseWithProfessionals;
use App\Models\DocumentsFolder;
use App\Models\Forms;
use App\Models\Invoice;
use App\Models\PredefinedCaseStages;
use App\Models\ProfessionalSubServices;
use App\Models\Reviews;
use App\Models\ReviewsInvitations;
use App\Models\Roles;
use App\Models\Ticket;
use App\Models\User;

Route::get('/g/join/{groupEncodedId}', [App\Http\Controllers\Panel\GroupChatController::class, 'joinGroupWithLink'])->name('joinGroupWithLink')->middleware('auth');

Route::get('unauthorized-access',[App\Http\Controllers\HomeController::class, 'notAccess'])->name('unauthorized-access');
//'check_profile',
Route::group([
    'prefix' => 'panel',
    'as' => 'panel.',
    'middleware' => [
        'auth',
        'check_profile',
        'role_check', 
        'check_access_permission'
        // function ($request, $next) {
        //     // Extract route information (module and action from route name)
        //     $routeName = $request->route()->getName();

        //     $parts = explode('.', $routeName);

        //     $module = $parts[0] ?? 'panel'; // Default module if not found
        //     $action = $parts[1] ?? 'list'; // Default action if not found

        //     // Dynamically call the RoleBasedAccessMiddleware
        //     return app()->make(\App\Http\Middleware\CheckAccessPermission::class)
        //         ->handle($request, $next, $module, $action);
        // }
    ],
], function () {
    
    Route::get('/get-global-notification', [App\Http\Controllers\Panel\DashboardController::class, 'getGlobalNotification'])->name('get-global-notification');
    Route::get('/search-services', [App\Http\Controllers\Panel\CompaniesController::class, 'searchServices']);
    Route::post('/choose-services', [App\Http\Controllers\Panel\CompaniesController::class, 'linkServiceWithProfesional']);
    Route::get('/', [App\Http\Controllers\Panel\DashboardController::class, 'index'])->withoutMiddleware(['checkAccess'])->name('list');
    
    // Dashboard Tab Routes
    Route::get('/dashboard', [App\Http\Controllers\Panel\DashboardController::class, 'index'])->name('dashboard.overview');
    Route::get('/dashboard/cases', [App\Http\Controllers\Panel\DashboardController::class, 'cases'])->name('dashboard.cases');
    Route::get('/dashboard/appointments', [App\Http\Controllers\Panel\DashboardController::class, 'appointments'])->name('dashboard.appointments');
    Route::get('/dashboard/messages', [App\Http\Controllers\Panel\DashboardController::class, 'messages'])->name('dashboard.messages');
    Route::get('/dashboard/invoices', [App\Http\Controllers\Panel\DashboardController::class, 'invoices'])->name('dashboard.invoices');
    Route::get('/dashboard/reports', [App\Http\Controllers\Panel\DashboardController::class, 'reports'])->name('dashboard.reports');
    Route::get('/dashboard/settings', [App\Http\Controllers\Panel\DashboardController::class, 'settings'])->name('dashboard.settings');
    Route::get('/edit-profile', [App\Http\Controllers\Panel\DashboardController::class, 'editProfile'])->name('editProfile');
    Route::get('/more-company-address', [App\Http\Controllers\Panel\DashboardController::class, 'moreCompanyAddress']);
    Route::get('/my-profile', [App\Http\Controllers\Panel\DashboardController::class, 'myProfile'])->name('my-profile');
    Route::get('/profile', [App\Http\Controllers\Panel\DashboardController::class, 'profile'])->name("my-profile");
    Route::get('/profile/{page}', [App\Http\Controllers\Panel\DashboardController::class, 'profile'])->name("profile-tab");
    // Route::get('profile/confirm-login/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'confirmLogin'])->name('deviceList');
    Route::get('confirm-save-login/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'confirmSaveLogin']);
    Route::post('/companies-ajax', [App\Http\Controllers\Panel\DashboardController::class, 'getCompanies'])->name("companies");
    // generate qr code
    Route::get('/generate-qr-code/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'generateQrCode'])->name('generateQrCode');
    Route::get('/remove-professional-domain/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'removeProfessionalDomain'])->name('removeProfessionalDomain');
    Route::post('/verify-professional-domain-dns', [App\Http\Controllers\Panel\DashboardController::class, 'verifyProfessionalDomainTxt'])->name('verifyProfessionalDomainTxt');
    Route::post('/verify-professional-domain-file', [App\Http\Controllers\Panel\DashboardController::class, 'verifyProfessionalDomainFile'])->name('verifyProfessionalDomainFile');
    // Route::get('/download-domain-verify-file', [App\Http\Controllers\Panel\DashboardController::class, 'generateTxt'])->name('generateTxt');

    Route::get('/crop-user-image', [App\Http\Controllers\Panel\DashboardController::class, 'imageCropper']);
    Route::post('/upload-user-cropped-image', [App\Http\Controllers\Panel\DashboardController::class, 'saveCroppedImage']);

    Route::get('/crop-banner-image', [App\Http\Controllers\Panel\DashboardController::class, 'bannerCropper']);
    Route::post('/upload-banner-cropped-image', [App\Http\Controllers\Panel\DashboardController::class, 'saveBannerImage']);

    Route::post('upload-professional-document', [App\Http\Controllers\Panel\DashboardController::class, 'uploadProfessionalDocument']);
    Route::post('/submit-profile', [App\Http\Controllers\Panel\DashboardController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/professional-submit-profile', [App\Http\Controllers\Panel\DashboardController::class, 'updateProfessionalProfile'])->name('updateProfessionalProfile')->middleware('input_sanitization');
    Route::get('/professional/add-company-address/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'addCompanyAddress']);
    Route::get('/professional/add-personal-address/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'addPersonalAddress']);
    Route::post('/save-address-from-signup', [App\Http\Controllers\Panel\CompanyLocationController::class, 'saveAddressFromSignup'])->name('save-address-from-signup')->middleware('input_sanitization');

    Route::get('/change-password/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'changePassword'])->name('changePassword');
    Route::post('/update-password/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'updatePassword'])->name('updatePassword');
    
    // Social Authentication Routes
    Route::get('/auth/{provider}', [App\Http\Controllers\Panel\SocialAuthController::class, 'redirectToProvider'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [App\Http\Controllers\Panel\SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');
    Route::post('/social/unlink', [App\Http\Controllers\Panel\SocialAuthController::class, 'unlinkSocialAccount'])->name('social.unlink');
    Route::get('/social/status', [App\Http\Controllers\Panel\SocialAuthController::class, 'getSocialAccountStatus'])->name('social.status');
    
    Route::post('edit-professionals/{uniqueid}/', [App\Http\Controllers\Panel\DashboardController::class, 'updateProfessional'])->name('updateProfessional');
    Route::get('professional/download-file', [App\Http\Controllers\Panel\DashboardController::class, 'downloadFile'])->name('downloadFile');

    Route::get('/download-domain-verify-file', [App\Http\Controllers\Panel\DashboardController::class, 'generateTxt'])->name('generateTxt');
    Route::post('/verify-domain-file', [App\Http\Controllers\Panel\DashboardController::class, 'verifyDomainFile'])->name('verifyDomainFile');
    Route::post('/verify-domain-dns', [App\Http\Controllers\Panel\DashboardController::class, 'verifyDomainFile'])->name('verifyDomainDns');
    Route::post('/verify-domain-txt', [App\Http\Controllers\Panel\DashboardController::class, 'verifyDomainTxt'])->name('verifyDomainTxt');
    Route::get('/remove-domain', [App\Http\Controllers\Panel\DashboardController::class, 'removeDomain'])->name('removeDomain');
    Route::post('/domain-verify', [App\Http\Controllers\Panel\DashboardController::class, 'domainVerify'])->name('domain-verify');
    
    Route::post('/sidebar/status', [App\Http\Controllers\Panel\DashboardController::class, 'sidebarStatus'])->name('sidebar.status');

    // Banking Details Routes
    Route::get('/banking-details/get/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'getBankingDetails'])->name('banking-details.get');
    Route::post('/banking-details/save', [App\Http\Controllers\Panel\DashboardController::class, 'saveBankingDetails'])->name('banking-details.save');
    Route::post('/banking-details/update/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'updateBankingDetails'])->name('banking-details.update');
    Route::delete('/banking-details/delete/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'deleteBankingDetails'])->name('banking-details.delete');
    Route::post('/banking-details/set-active/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'setActiveBankingDetails'])->name('banking-details.set-active');
    
    // Withdrawal Request Routes
    Route::get('/withdrawal-requests', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'index'])->name('withdrawal-requests.index');
    Route::get('/withdrawal-requests/create', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'create'])->name('withdrawal-requests.create');
    Route::post('/withdrawal-requests', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'store'])->name('withdrawal-requests.store');
    Route::get('/withdrawal-requests/{id}', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'show'])->name('withdrawal-requests.show');
    Route::delete('/withdrawal-requests/{id}/cancel', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'cancel'])->name('withdrawal-requests.cancel');
    Route::get('/withdrawal-requests/{id}/download', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'downloadFile'])->name('withdrawal-requests.download');
    Route::post('/withdrawal-requests/{id}/remind', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'sendReminder'])->name('withdrawal-requests.remind');
    Route::post('/withdrawal-requests-ajax', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'getWithdrawalRequests'])->name('withdrawal-requests.ajax');
    Route::get('/withdrawal-requests/{id}/download-admin-file', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'downloadAdminFile'])->name('admin-withdrawal-requests.download-admin-file');

    // Withdrawal Request History
    Route::get('/request-history', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'history'])->name('request-history.index');
    Route::post('/request-history-ajax', [App\Http\Controllers\Panel\WithdrawalRequestController::class, 'getRequestHistory'])->name('request-history.ajax');

    // manage feeds in my profile
    Route::get('professional/remove-file/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'removeFile'])->name('removeFile');
    Route::post('/save-timezone', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'saveUserTimezone']);
    Route::get('/appointment-check/{unique_id}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'getAppointmentUniqueCheck'])->name('appointment-check');
    Route::get('/delete-professional-location/{location_id}', [App\Http\Controllers\Auth\RegisterController::class, 'deleteProfessionalLocationData']);
    

   
    Route::post('/professional-services/save', [App\Http\Controllers\Panel\CompaniesController::class, 'professionalServicesSave'])->name('professionalServicesSave');
    

    Route::get('/invitations-sent/delete/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'invitationsDelete'])->name('invitationsDelete');

    Route::group(array('prefix' => 'companies', 'as' => 'companies.'), function () {

        Route::get('/', [App\Http\Controllers\Panel\CompaniesController::class, 'index'])->name('list');

        Route::get('/status/{status}', [App\Http\Controllers\Panel\CompaniesController::class, 'index']);
        Route::post('/ajax-list', [App\Http\Controllers\Panel\CompaniesController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/add', [App\Http\Controllers\Panel\CompaniesController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\CompaniesController::class, 'save'])->name('save');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'deleteSingle'])->name('deleteSingle');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\CompaniesController::class, 'deleteMultiple'])->name('deleteMultiple');
        Route::get('/extra-detail/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'extraDetail'])->name('extraDetail');
        Route::post('/extra-detail/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'updateExtraDetail'])->name('updateExtraDetail');
        Route::get('/view/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'view'])->name('view');
        Route::get('/mark-as-incomplete/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'markAsInComplete'])->name('markAsInComplete');
        Route::post('/mark-as-complete', [App\Http\Controllers\Panel\CompaniesController::class, 'markAsComplete'])->name('markAsComplete');

        // fetch professional for data anylast
        Route::get('/fetch', [App\Http\Controllers\Panel\CompaniesController::class, 'fetchAdd'])->name('fetchAdd');


        Route::post('/save-company', [App\Http\Controllers\Panel\CompaniesController::class, 'saveCompany'])->name('save-company');
        Route::get('/edit-company/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'editCompany'])->name('edit-company');
        Route::post('/update-company/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'updateCompany'])->name('update-company');
        Route::get('/delete-company/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'deleteCompany'])->name('delete-company');
        Route::get('/manage-address/{id}', [App\Http\Controllers\Panel\CompanyLocationController::class, 'manageAddress'])->name('manage-address');
        Route::post('/get-company-address', [App\Http\Controllers\Panel\CompanyLocationController::class, 'getCompanyAddress'])->name('company-address');

        Route::get('/add-company-address/{id}/{company_id}', [App\Http\Controllers\Panel\CompanyLocationController::class, 'addCompanyAddress'])->name('add-company-address');
        Route::post('/save-company-address/{id}/{company_id}', [App\Http\Controllers\Panel\CompanyLocationController::class, 'saveCompanyAddress'])->name('save-company-address')->middleware('input_sanitization');

        Route::get('/crop-company-logo/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'companyLogo']);
        Route::post('/upload-company-logo/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'saveCompanyLogo']);

        Route::get('/crop-company-banner-image/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'companyBannerCropper']);
        Route::post('/upload-company-banner-image/{id}', [App\Http\Controllers\Panel\CompaniesController::class, 'saveCompanyBannerImage']);

         Route::get('/mark-as-primary', [App\Http\Controllers\Panel\CompaniesController::class, 'markAsPrimary'])->name('mark-as-primary');
    });

    Route::group(array('prefix' => 'message-settings', 'as' => 'message-settings.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\MessageSettingsController::class, 'index'])->name('list');
        Route::post('/update', [App\Http\Controllers\Panel\MessageSettingsController::class, 'update'])->name('update');
    });

    Route::group(array('prefix' => 'settings', 'as' => 'settings.'), function () {
          Route::group(array('prefix' => 'security', 'as' => 'security.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\DashboardController::class, 'securitySetting'])->name("list");
     });

        Route::group(array('prefix' => 'account', 'as' => 'account.'), function () {
            Route::get('/', [App\Http\Controllers\Panel\AccountSettingsController::class, 'index'])->name('list');
            Route::post('/update', [App\Http\Controllers\Panel\AccountSettingsController::class, 'update'])->name('update');
        });
        Route::group(array('prefix' => 'feeds', 'as' => 'feeds.'), function () {
            Route::get('/', [App\Http\Controllers\Panel\FeedSettingsController::class, 'index'])->name('list');
            Route::post('/update', [App\Http\Controllers\Panel\FeedSettingsController::class, 'update'])->name('update');
        });
    });

    // This is only for chat invitation connection
    Route::group(array('prefix' => 'connections'), function () {
        Route::group(array('prefix' => 'invitations', 'as' => 'chat-invitations.'), function () {
            Route::get('/', [App\Http\Controllers\Panel\ChatInvitationController::class, 'index'])->name('list');
            Route::post('/ajax-list', [App\Http\Controllers\Panel\ChatInvitationController::class, 'getAjaxList'])->name('ajax-list');
            Route::get('/add', [App\Http\Controllers\Panel\ChatInvitationController::class, 'add'])->name('add');
            Route::post('/save', [App\Http\Controllers\Panel\ChatInvitationController::class, 'save'])->name('save');
            Route::get('/delete/{id}', [App\Http\Controllers\Panel\ChatInvitationController::class, 'deleteSingle'])->name('deleteSingle');
            Route::post('/delete-multiple', [App\Http\Controllers\Panel\ChatInvitationController::class, 'deleteMultiple'])->name('deleteMultiple');
        });

        Route::group(array('prefix' => 'connect', 'as' => 'connect.'), function () {
            Route::get('/', [App\Http\Controllers\Panel\ConnectController::class, 'index'])->name("list");
            Route::post('connected-list', [App\Http\Controllers\Panel\ConnectController::class, 'connectionList']);
            Route::post('pending-connected-list', [App\Http\Controllers\Panel\ConnectController::class, 'pendingConnectionList']);
            Route::post('follow-back', [App\Http\Controllers\Panel\ConnectController::class, 'followBack']);
            Route::get('connect-user-list', [App\Http\Controllers\Panel\ConnectController::class, 'connectUserList']);
            Route::get('send-connection/{id}', [App\Http\Controllers\Panel\ConnectController::class, 'sendConnection']);
            Route::get('remove-connection/{id}', [App\Http\Controllers\Panel\ConnectController::class, 'removeConnection']);
            //Route::get('follow/{id}', [App\Http\Controllers\Panel\ConnectController::class, 'follow']);
            // Route::get('unfollow/{id}', [App\Http\Controllers\Panel\ConnectController::class, 'unfollow']);
            Route::get('remove/{id}/{remove_connection}', [App\Http\Controllers\Panel\ConnectController::class, 'remove']);
            Route::get('remove-from-followers/{id}', [App\Http\Controllers\Panel\ConnectController::class, 'removeFromFollowers']);
        });

        Route::group(array('as' => 'connections.'), function () {
            Route::get('/', [App\Http\Controllers\Panel\ConnectionController::class, 'index'])->name('list');
            Route::post('/ajax-list', [App\Http\Controllers\Panel\ConnectionController::class, 'getAjaxList'])->name('update');
        });
    });
    
    Route::group(array('prefix' => 'working-hours', 'as' => 'working-hours.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\WorkingHoursController::class, 'index'])->name('working_hours');
        Route::post('/update', [App\Http\Controllers\Panel\WorkingHoursController::class, 'update'])->name('update_working_hours');

    });

    
    Route::group(array('prefix' => 'appointments'), function () {
        Route::get('/', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'appointmentDashboard'])->name("appointments-overview");
        Route::get('/settings', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'appointmentSettings'])->name('appointment-settings');
     
        Route::group(array('prefix' => 'appointment-booking-flow','as'=>"appointment-booking-flow."), function () {
            Route::get('/', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'index'])->name('list');
            Route::post('/ajax-list', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'getAjaxList']);
            Route::get('/add', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'add'])->name('add');
            Route::get('/add/{id}', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'add'])->middleware('check.ownership:' . AppointmentBookingFlow::class . ',id')->name('edit');
            Route::post('/save', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'save'])->name('save')->middleware('input_sanitization');

            Route::get('/add-time-duration/{id}', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'addTimeDuration'])->name('add-time-duration');
            Route::post('/save-time-duration', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'saveTimeDuration'])->name('save-time-duration');

            Route::get('/add-appointment-type/{id}', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'addAppointmentType'])->name('add-appointment-type');
            Route::post('/save-appointment-type', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'saveAppointmentType'])->name('save-time-duration');

            Route::get('working-hours-modal/{location_uid}', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'showWorkingHoursModal']);

            Route::get('/add-service/{id}', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'addService'])->name('add-service');
            Route::post('/save-services', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'saveServices'])->name('save-time-duration');

            Route::get('/add-location/{id}', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'addLocation'])->name('add-location');
            Route::post('/save-locations', [App\Http\Controllers\Panel\AppointmentBookingFlowController::class, 'saveLocations'])->name('save-locations');
        });

        Route::get('/appointment-booking-success/{appointment_booking_id}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'appointmentBookingSuccess'])->name('appointment-booking-success');
        Route::group(array('prefix' => 'appointment-booking','as' => 'appointment-booking.'), function () {
            Route::get('/', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'index'])->name('list');
            Route::post('/search-appointments', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'searchAppointments']);
            Route::get('/set-reminder/{appointmentId}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'setReminderModal']);

            Route::post('save-reminder', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'saveReminder']);
            Route::get('/cancel-appointment/{booking_id}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'cancelAwaitingAppointment']);

            Route::post('/ajax-list', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'getAjaxList'])->name('ajax-list');
            Route::get('/delete/{id}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'delete'])->name('delete');
            Route::get('/view/{id}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'viewAppointment'])->middleware('check.ownership:' . AppointmentBooking::class . ',id')->name('view');
            Route::get('/save-booking/{unique_id?}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'addAppointment'])->name('add');
            Route::post('/save', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'saveAppointment']);
            Route::get('/{uid}/mark/{status}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'markStatus']) ->name('markStatus');
            Route::get('add-joining-link/{uid}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'addJoiningLink'])->name('add-joining-link');
            Route::post('save-joining-link/{uid}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'saveJoiningLink']);
        
            Route::get('/reschedule-appointment/{id}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'rescheduleAppointment'])->name('reschedule-appointment');
            Route::get('/calendar', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'viewCalendar'])->name('view-calender');
            Route::post('/fetch-appointments', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'fetchAppointments']);
            Route::post('/ajax-list', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'getAjaxList']);
            Route::get('/status/{id}/{status}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'changeStatus'])->name('change-status');
            // Route::get('/view/{id}', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'viewAppointment']);
            Route::get('/fetch-hours', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'fetchHours']);
            Route::post('/fetch-available-slots', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'fetchAvailableSlots']);
            Route::post('/update-appointment', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'updateAppointment']);
        });

        Route::group(array('prefix' => 'block-dates','as' => 'block-dates.'), function () {
            Route::get('/', [App\Http\Controllers\Panel\BlockDateController::class, 'index'])->name('list');
            Route::post('/ajax-list', [App\Http\Controllers\Panel\BlockDateController::class, 'getAjaxList'])->name('ajax-list');
            Route::get('/delete/{id}', [App\Http\Controllers\Panel\BlockDateController::class, 'delete'])->name('delete');
            Route::get('/add', [App\Http\Controllers\Panel\BlockDateController::class, 'addLeaves'])->name('add');
            Route::post('/save', [App\Http\Controllers\Panel\BlockDateController::class, 'saveLeaves'])->middleware('input_sanitization');
            Route::get('/edit/{id}', [App\Http\Controllers\Panel\BlockDateController::class, 'edit'])->name('edit');
            Route::post('/update/{id}', [App\Http\Controllers\Panel\BlockDateController::class, 'update'])->name('update')->middleware('input_sanitization');
            Route::post('/fetch-location-leaves', [App\Http\Controllers\Panel\BlockDateController::class, 'fetchLocationLeaves'])->name('fetch-location-leaves');

            Route::post('/delete-multiple', [App\Http\Controllers\Panel\BlockDateController::class, 'deleteMultiple'])->name('deleteMultiple');
        });

    });
    

    
    Route::group(array('prefix' => 'appointment-types', 'as' => 'appointment-types.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\AppointmentTypesController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\AppointmentTypesController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/add', [App\Http\Controllers\Panel\AppointmentTypesController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\AppointmentTypesController::class, 'save'])->name('save')->middleware('input_sanitization');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\AppointmentTypesController::class, 'deleteSingle'])->name('deleteSingle');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\AppointmentTypesController::class, 'deleteMultiple'])->name('deleteMultiple');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\AppointmentTypesController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\AppointmentTypesController::class, 'update'])->name('update')->middleware('input_sanitization');

    });

    Route::group(array('prefix' => 'time-duration', 'as' => 'time-duration.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\TimeDurationController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\TimeDurationController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/add', [App\Http\Controllers\Panel\TimeDurationController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\TimeDurationController::class, 'save'])->name('save')->middleware('input_sanitization');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\TimeDurationController::class, 'deleteSingle'])->name('deleteSingle');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\TimeDurationController::class, 'deleteMultiple'])->name('deleteMultiple');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\TimeDurationController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\TimeDurationController::class, 'update'])->name('update')->middleware('input_sanitization');

    });
    Route::group(array('prefix' => 'group-settings', 'as' => 'group-settings.'), function () {
        Route::get('/{group_id}', [App\Http\Controllers\Panel\GroupSettingsController::class, 'index'])->name('group_settings');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\GroupSettingsController::class, 'update'])->name('update');
       
    });
    //chat
    Route::group(array('prefix' => 'group', 'as' => 'group.'), function () {
        Route::post('/check-group-exists', action: [App\Http\Controllers\Panel\GroupChatController::class, 'checkGroupExists'])->name("check-group-exists");
        Route::post('clear-group-messages/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'clearGroupChatForUser']);
        Route::get('/group-members/search', [App\Http\Controllers\Panel\GroupChatController::class, 'searchGroupMembers']);
        Route::get('update-group-name/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'updateGroupName']);
        Route::get('/make-group-admin/{member_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'makeGroupAdmin']);
        Route::get('/remove-group-admin/{member_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'removeGroupAdmin']);
        Route::get('/delete-group/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'deleteGroup']);
        Route::get('remove-group-member/{member_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'removeGroupMember']);
        Route::get('mark-as-admin/{member_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'markAsAdminModal']);
        Route::get('/mark-group-admin/{member_id}/{current_memeber_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'markAsAdmin']);
        Route::get('/groups-list',  [App\Http\Controllers\Panel\GroupChatController::class, 'groupsList'])->name("get-group-list");
        Route::post('/ajax-list',  [App\Http\Controllers\Panel\GroupChatController::class, 'groupsAjaxList'])->name("get-group-ajax-list");
        Route::post('/my-groups-ajax-list',  [App\Http\Controllers\Panel\GroupChatController::class, 'myGroupsAjaxList'])->name("get-my-group-ajax-list");
        Route::get('/my-joined-group-list',  [App\Http\Controllers\Panel\GroupChatController::class, 'groupsList'])->name("get-my-joined-group-list");
        Route::get('/sent-request',  [App\Http\Controllers\Panel\GroupChatController::class, 'groupsList'])->name("get-sent-request");
        Route::get('/received-request',  [App\Http\Controllers\Panel\GroupChatController::class, 'groupsList'])->name("get-received-request");
        Route::post('/group-received-req-ajax-list',  [App\Http\Controllers\Panel\GroupChatController::class, 'groupReceivedReqAjaxList'])->name("get-received-ajax-request");
        Route::get('/my-created-group-list',  [App\Http\Controllers\Panel\GroupChatController::class, 'groupsList'])->name("get-my-created-group-list");

        Route::get('add-new-group', [App\Http\Controllers\Panel\GroupChatController::class, 'addNewGroup']);
        Route::post('create-group', [App\Http\Controllers\Panel\GroupChatController::class, 'createGroup'])->middleware('input_sanitization');
        Route::get('view-group-members/{groupId}', [App\Http\Controllers\Panel\GroupChatController::class, 'viewGroupMembers']);

        Route::get('add-new-members/{groupId}', [App\Http\Controllers\Panel\GroupChatController::class, 'addNewMembers']);
        Route::post('add-new-members/{groupId}', [App\Http\Controllers\Panel\GroupChatController::class, 'saveMemberToGroup']);
        Route::get('my-groups-list', [App\Http\Controllers\Panel\GroupChatController::class, 'myGroupsList']);
        Route::post('/send-msg/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'sendMessage'])->name('group_send_msg');
        Route::post('/add-reaction', [App\Http\Controllers\Panel\GroupChatController::class, 'addReactionToMessage'])->name('group_msg_reaction');
        Route::post('/remove-reaction', [App\Http\Controllers\Panel\GroupChatController::class, 'removeReactionToMessage'])->name('remove_msg_reaction');
        Route::get('/get-conversation/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'getConversation']);
        Route::post('/reacted-message/{group_id}/{message_uid}', [App\Http\Controllers\Panel\GroupChatController::class, 'getReactedMsg']);
        Route::post('/fetch-chats/{group_id}/{last_msg_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'getGroupChat']);
        Route::post('/fetch-older-chats/{group_id}/{first_msg_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'getOlderGroupChat']);
        Route::get('/preview-file', [App\Http\Controllers\Panel\GroupChatController::class, 'previewFile'])->name("preview-file");
        Route::get('/group-attachments/{groupId}', function ($groupId) {
            return groupAttachments($groupId);
        });

        Route::get('refresh-group-list/', [App\Http\Controllers\Panel\GroupChatController::class, 'refreshGroupList']);

        Route::get('edit-new-group/{groupId}', [App\Http\Controllers\Panel\GroupChatController::class, 'editGroup']);
        Route::post('/update-new-group/{groupId}', [App\Http\Controllers\Panel\GroupChatController::class, 'updateGroup'])->middleware('input_sanitization');

        Route::post('/groups-list', [App\Http\Controllers\Panel\GroupChatController::class, 'getGroupsList']);
        Route::get('chat/', [App\Http\Controllers\Panel\GroupChatController::class, 'index'])->name("list");
        Route::get('chat/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'groupChat'])->name("conversation");
        Route::get('chat-ajax/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'groupChatAjax'])->name("conversation");
        Route::get('group-information/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'getGroupInformation']);

        Route::get('/search/{search_param}', [App\Http\Controllers\Panel\GroupChatController::class, 'groupSearch']);
        Route::post('/update-typing', [App\Http\Controllers\Panel\GroupChatController::class, 'updateTypingStatus']);
        Route::get('/fetch-typing/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'fetchTypingStatus']);
        Route::get('/search-messages', [App\Http\Controllers\Panel\GroupChatController::class, 'searchGroupMessages']);
        Route::get('/fetch-first-chat/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'getFirstChat']);
        Route::get('delete-message-centre-msg/{chat_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'deleteChatMessage']);
        Route::get('delete-message-for-all/{msg_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'deleteChatMessageforAll']);
        Route::post('/{groupId}/save-image', [App\Http\Controllers\Panel\GroupChatController::class, 'updateGroupImage'])->name('group.update-image');
        Route::post('/update-message/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'updateGroupMessage']);
        Route::post('/save-message-to-draft', [App\Http\Controllers\Panel\GroupChatController::class, 'saveDraftMessage']);
        Route::get('/chat-notfications', [App\Http\Controllers\Panel\GroupChatController::class, 'chatNotifications']);
        Route::post('/notifications/mark-as-read', [App\Http\Controllers\Panel\GroupChatController::class, 'markAsRead']);

        Route::get('/group-chats', [App\Http\Controllers\Panel\GroupChatController::class, 'fetchGroupChats']);


        Route::post('/other-groups-list', [App\Http\Controllers\Panel\GroupChatController::class, 'getOtherGroupsList']);
        Route::get('/pending-group-join-request', [App\Http\Controllers\Panel\GroupChatController::class, 'pendinggGroupJoinRequest']);
        ;
        Route::get('/other-chat', [App\Http\Controllers\Panel\GroupChatController::class, 'otherGroupIndex']);
        Route::post('/join-request', [App\Http\Controllers\Panel\GroupChatController::class, 'sendJoinRequest']);
        Route::post('/withdraw-request', [App\Http\Controllers\Panel\GroupChatController::class, 'withdrawJoinRequest']);

        Route::get('add-group-member/{member_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'acceptGroupMemberRequest']);

        Route::get('reject-group-member/{member_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'rejectGroupMemberRequest']);

        Route::get('delete-selected-attachments/{msg_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'deleteSelectedAttachments']);

        Route::get('/fetch-chat-bot/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'fetchChatBot']);

        Route::get('/get-group-info/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'getGroupInfo'])->name('get-group-info');
        Route::get('/get-group-join-request/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'getGroupJoinRequest'])->name('get-group-join-request');
        Route::get('/get-shared-file/{group_id}', [App\Http\Controllers\Panel\GroupChatController::class, 'getSharedFile'])->name('get-shared-file');
    });
    Route::post('members-list/ajax-list', [App\Http\Controllers\Panel\GroupChatController::class, 'getMembersAjaxList'])->name('list');
    Route::post('group-members-list/ajax-list', [App\Http\Controllers\Panel\GroupChatController::class, 'getGroupMembersAjaxList'])->name('getGroupMembersAjaxList');


    //chat request
    Route::get('/send-chat-request/{id}', [App\Http\Controllers\Panel\ChatRequestController::class, 'sendChatRequest']);
    Route::get('/chat-request/', [App\Http\Controllers\Panel\ChatRequestController::class, 'index'])->name('chatRequest');
    Route::post('/chat-request/ajax-list/', [App\Http\Controllers\Panel\ChatRequestController::class, 'getAjaxList']);
    Route::get('/accept-chat-request/{id}', [App\Http\Controllers\Panel\ChatRequestController::class, 'acceptChatRequest']);
    Route::get('/decline-chat-request/{id}', [App\Http\Controllers\Panel\ChatRequestController::class, 'declineChatRequest']);

   
    Route::post('/convert-time', [App\Http\Controllers\Panel\TimeController::class, 'convertTime']);

    //membership plan routes
    Route::group(array('prefix' => 'membership-plans', 'as' => 'membership-plans.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\MembershipPlanController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\MembershipPlanController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('plans/{id}', [App\Http\Controllers\Panel\MembershipPlanController::class, 'show'])->name("add");
        Route::post('/subscription', [App\Http\Controllers\Panel\MembershipPlanController::class, 'subscription'])->name("subscription.create")->middleware('input_sanitization');
        Route::post('pay-for-onetime', [App\Http\Controllers\StripeController::class, 'processOneTimePayment'])->name('process.onetime-payment')->middleware('input_sanitization');
    });
    //user membership plan routes
    Route::group(array('prefix' => 'my-membership-plans', 'as' => 'my-membership-plans.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\MembershipPlanController::class, 'userMembershipIndex'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\MembershipPlanController::class, 'userMembershipAjaxList'])->name('ajax-list');
        Route::get('/cancel/{id}', [App\Http\Controllers\Panel\MembershipPlanController::class, 'cancelSubscription'])->name('subscription.cancel');
        Route::get('/add-card/{id}', [App\Http\Controllers\Panel\MembershipPlanController::class, 'addCardDetails'])->name('add-card-details');
        Route::post('/save-card', [App\Http\Controllers\Panel\MembershipPlanController::class, 'saveCardDetails'])->name('save-card-details');
        Route::get('/cards', [App\Http\Controllers\Panel\MembershipPlanController::class, 'userCardList'])->name('save-card-details');
        Route::post('/cards-ajax-list', [App\Http\Controllers\Panel\MembershipPlanController::class, 'useCardAjaxList'])->name('ajax-list');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\MembershipPlanController::class, 'removeCardDetails'])->name('deleteSingle');
        Route::get('/default/{id}', [App\Http\Controllers\Panel\MembershipPlanController::class, 'makeDefaultCard'])->name('deleteSingle');
          Route::post('/upcoming-subscription-history', [App\Http\Controllers\Panel\MembershipPlanController::class, 'subscriptionUpcomingInvoiceAjax'])->name('professionals.upcoming-invoice');

    });



    Route::group(array('prefix' => 'tracking', 'as' => 'tracking.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\TrackingController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\TrackingController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/view/{id}', [App\Http\Controllers\Panel\TrackingController::class, 'view']);
    });

    Route::post('/save-uap-comment', [App\Http\Controllers\Panel\TrackingController::class, 'saveUapComment'])->name('save.uap.comment');
    Route::post('/get-uap-comment', [App\Http\Controllers\Panel\TrackingController::class, 'getUapComment'])->name('get.uap.comment');



    Route::group(array('prefix' => 'forms', 'as' => 'forms.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\FormsController::class, 'forms'])->name('list');
        
        Route::post('/ajax-list', [App\Http\Controllers\Panel\FormsController::class, 'getAjaxList']);
        Route::get('/add', [App\Http\Controllers\Panel\FormsController::class, 'createForm'])->name('add');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\FormsController::class, 'editForm'])->middleware('check.ownership:' . Forms::class . ',id')->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\FormsController::class, 'updateForm'])->name('update');
        Route::get('/render-form/{id}', [App\Http\Controllers\Panel\FormsController::class, 'renderForm'])->middleware('check.ownership:' . Forms::class . ',id')->name('render-form');
        Route::get('/delete-form/{id}', [App\Http\Controllers\Panel\FormsController::class, 'deleteForm'])->middleware('check.ownership:' . Forms::class . ',id')->name('delete');
        Route::post('/save', [App\Http\Controllers\Panel\FormsController::class, 'saveForm'])->name('save-form');
        Route::get('/send-mail/{id}', [App\Http\Controllers\Panel\FormsController::class, 'sendMail'])->name('send-form');
        Route::post('/send-mail/{id}', [App\Http\Controllers\Panel\FormsController::class, 'sendForm']);
         Route::post('/delete-multiple', [App\Http\Controllers\Panel\FormsController::class, 'deleteMultiple'])->name('deleteMultiple');
         Route::get('/view-reply/{id}', [App\Http\Controllers\Panel\FormsController::class, 'viewReply'])->middleware('check.ownership:' . Forms::class . ',id')->name('view-reply');
        // send-form
        Route::get('/generate-via-ai', [App\Http\Controllers\Panel\FormsController::class, 'generateViaAi'])->name('add');
        Route::post('/generated-ai-save', [App\Http\Controllers\Panel\FormsController::class, 'saveAiForm'])->name('save-ai-form');

        Route::get('/fetch-sub-service', [App\Http\Controllers\Panel\FormsController::class, 'fetchSubService'])->name('fetch-sub-service');
        Route::get('sub-services/{parentServiceId}',  [App\Http\Controllers\Panel\FormsController::class, 'getSubServices']);

        Route::get('predefined-templates',  [App\Http\Controllers\Panel\FormsController::class, 'predefinedTemplates']);
        Route::post('predefined-ajaxtemplates',  [App\Http\Controllers\Panel\FormsController::class, 'predefinedAjaxTemplates']);
        Route::get('/predefined-render-form/{id}', [App\Http\Controllers\Panel\FormsController::class, 'predefinedRenderForm'])->name('predefined-render-form');
        Route::get('/save-predefined-template/{id}', [App\Http\Controllers\Panel\FormsController::class, 'savePredefinedTemplate'])->name(' save-predefined-template');
       
    });

    Route::group(array('prefix' => 'articles', 'as' => 'articles.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\ArticleController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\ArticleController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/add', [App\Http\Controllers\Panel\ArticleController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\ArticleController::class, 'save'])->name('save');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\ArticleController::class, 'deleteSingle'])->name('deleteSingle');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\ArticleController::class, 'deleteMultiple'])->name('deleteMultiple');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\ArticleController::class, 'edit'])->middleware('check.ownership:' . Article::class . ',id')->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\ArticleController::class, 'update'])->name('update');
        Route::post('/upload-files', [App\Http\Controllers\Panel\ArticleController::class, 'uploadFiles'])->name('upload-files');
        Route::get('/image-cropper', [App\Http\Controllers\Panel\ArticleController::class, 'imageCropper']);
        Route::post('/upload-cropped-image', [App\Http\Controllers\Panel\ArticleController::class, 'saveCroppedImage']);

    });
    // new chat
    Route::group(array('prefix' => 'individual-chats', 'as' => 'individual-chats.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\IndividualChatController::class, 'index'])->name('list');
        Route::get('/chat/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'individualChat'])->name('conversation');
        Route::post('/switch-chat/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'individualChatAjax'])->name('conversation-ajax');
        Route::post('/load-messages/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'loadChatMessages'])->name('load-messages');
        Route::get('/load-bot-messages/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'loadChatMessages'])->name('load-messages');
        Route::get('/compose-message', [App\Http\Controllers\Panel\IndividualChatController::class, 'composeMessage'])->name("compose-message");
Route::post('/send-msg/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'sendMessage'])->name('send_msg');
        Route::post('/send-message/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'sendMessage'])->name('send_message');
        Route::post('/fetch-sidebar', [App\Http\Controllers\Panel\IndividualChatController::class, 'fetchChatSidebar'])->name('fetch-sidebar');
        Route::post('/delete-message-for-me/{chat_id}   ', [App\Http\Controllers\Panel\IndividualChatController::class, 'deleteChatMessageForMe'])->name('delete-message-for-me');
        Route::post('/delete-message-for-all/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'deleteChatMessageForEveryone'])->name('delete-message-for-everyone');
        Route::post('/update-message/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'updateMessage']);
        Route::get('/chat-files/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'getChatFiles'])->name('chat-files');
        Route::post('/add-reaction', [App\Http\Controllers\Panel\IndividualChatController::class, 'addReactionToMessage'])->name('msg_centre_reaction');
        Route::post('/remove-reaction', [App\Http\Controllers\Panel\IndividualChatController::class, 'removeReactionToMessage'])->name('remove_msg_centre_reaction');
        Route::get('/preview-file', [App\Http\Controllers\Panel\IndividualChatController::class, 'previewFile'])->name("preview-file");

        // profile page 
        Route::get('/get-user-profile/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'getUserProfile'])->name('get-user-profile');

        Route::get('/get-shared-file/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'getSharedFile'])->name('get-shared-file');

        Route::get('block-message-centre/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'blockChat']);
        Route::post('unblock-message-centre/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'unblockChat']);
        Route::post('clear-messages/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'clearChatForUser']);
        Route::get('delete-chat/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'deleteChatForUser']);
        Route::get('/fetch-chat-bot/{chat_id}', [App\Http\Controllers\Panel\IndividualChatController::class, 'fetchChatBot']);
    });
    // end new chat
    // new group
       Route::group(array('prefix' => 'group-message', 'as' => 'group-message.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\GroupMessageController::class, 'index'])->name('list');
        Route::get('/chat/{group_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'groupChat'])->name('group-chat');
        Route::post('/send-message', [App\Http\Controllers\Panel\GroupMessageController::class, 'sendMessage'])->name('send-message');
        Route::post('/update-message/{group_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'updateGroupMessage']);
        
        Route::get('add-new-group', [App\Http\Controllers\Panel\GroupMessageController::class, 'addNewGroup']);
        Route::post('create-group', [App\Http\Controllers\Panel\GroupMessageController::class, 'createGroup'])->middleware('input_sanitization');
        Route::get('view-group-members/{groupId}', [App\Http\Controllers\Panel\GroupMessageController::class, 'viewGroupMembers']);

        Route::get('edit-group/{groupId}', [App\Http\Controllers\Panel\GroupMessageController::class, 'editGroup']);
        Route::post('/update-group/{groupId}', [App\Http\Controllers\Panel\GroupMessageController::class, 'updateGroup'])->middleware('input_sanitization');

        Route::get('add-new-members/{groupId}', [App\Http\Controllers\Panel\GroupMessageController::class, 'addNewMembers']);
        Route::post('add-new-members/{groupId}', [App\Http\Controllers\Panel\GroupMessageController::class, 'saveMemberToGroup']);
        Route::get('/preview-file', [App\Http\Controllers\Panel\GroupMessageController::class, 'previewFile'])->name("preview-file");

        // Group Info Sidebar
        Route::get('/get-group-info/{group_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'getGroupInfo'])->name('get-group-info');
        Route::get('/get-group-join-request/{group_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'getGroupJoinRequest'])->name('get-group-join-request');
        Route::get('/get-shared-file/{group_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'getSharedFile'])->name('get-shared-file');
        Route::get('/remove-group-admin/{member_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'removeGroupAdmin']);
        Route::get('/delete-group/{group_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'deleteGroup']);
        Route::get('remove-group-member/{member_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'removeGroupMember']);

        // Route::post('/fetch-messages/{group_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'fetchMessages'])->name('fetch-messages');
        Route::post('/switch-group/{group_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'switchGroup'])->name('switch-group');
        Route::post('/load-messages/{group_id}', [App\Http\Controllers\Panel\GroupMessageController::class, 'loadGroupMessages'])->name('load-messages');
        Route::post('/add-reaction', [App\Http\Controllers\Panel\GroupMessageController::class, 'addReactionToMessage'])->name('group_msg_reaction');
        Route::post('/remove-reaction', [App\Http\Controllers\Panel\GroupMessageController::class, 'removeReactionToMessage'])->name('remove_msg_reaction');
        Route::post('/reacted-message/{group_id}/{message_uid}', [App\Http\Controllers\Panel\GroupMessageController::class, 'getReactedMessage']);
        Route::post('delete-message-for-me/{message_uid}', [App\Http\Controllers\Panel\GroupMessageController::class, 'deleteMessageForMe']);
        Route::post('delete-message-for-all/{message_uid}', [App\Http\Controllers\Panel\GroupMessageController::class, 'deleteMessageForAll']);
        Route::post('/fetch-group-sidebar', [App\Http\Controllers\Panel\GroupMessageController::class, 'fetchGroupSidebar'])->name('fetch-group-sidebar');
        Route::post('/fetch-older-messages', [App\Http\Controllers\Panel\GroupMessageController::class, 'fetchOlderMessages'])->name('fetch-older-messages');

    });

    // end group
    //chat
     Route::get('/messages', [App\Http\Controllers\Panel\MessageCentreController::class, 'overview'])->name('message-overview');
    Route::group(array('prefix' => 'message-centre', 'as' => 'message-centre.'), function () {
        Route::post('/check-chat-exists', [App\Http\Controllers\Panel\MessageCentreController::class, 'checkChatExists'])->name("check-chat-exists");
        Route::get('/search-users', [App\Http\Controllers\Panel\MessageCentreController::class, 'searchUsers'])->name("search-useers");
        Route::get('/chat-requests', [App\Http\Controllers\Panel\MessageCentreController::class, 'chatRequests'])->name("chat-request");
        Route::get('/', [App\Http\Controllers\Panel\MessageCentreController::class, 'index'])->name("list");
        Route::get('/chat/{id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'individualChat'])->name("conversation");
        Route::post('/chat-ajax/{id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'individualChatAjax'])->name("conversation-ajax");
        Route::get('refresh-chat-list/', [App\Http\Controllers\Panel\MessageCentreController::class, 'refreshChatList']);
        Route::get('/compose-message', [App\Http\Controllers\Panel\MessageCentreController::class, 'composeMessage'])->name("compose-message");

        Route::get('/preview-file', [App\Http\Controllers\Panel\MessageCentreController::class, 'previewFile'])->name("preview-file");
        Route::post('/track-chats', [App\Http\Controllers\Panel\MessageCentreController::class, 'trackMessages']);
        Route::get('/initialize-chat/{receiver_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'chatInitialize'])->name('chat_initialize');
        
        Route::get('/fetch-first-chat/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'getFirstChat']);
        Route::get('/attachments/{chatId}', function ($chatId) {
            return chatAttachments($chatId);
        });
        Route::post('/add-reaction', [App\Http\Controllers\Panel\MessageCentreController::class, 'addReactionToMessage'])->name('msg_centre_reaction');
        Route::post('/remove-reaction', [App\Http\Controllers\Panel\MessageCentreController::class, 'removeReactionToMessage'])->name('remove_msg_centre_reaction');

        Route::get('delete-message-centre-msg/{msg_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'deleteChatMessage']);
        Route::get('delete-message-for-all/{msg_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'deleteChatMessageforBoth']);

        Route::get('delete-selected-attachments/{msg_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'deleteSelectedAttachments']);

        Route::get('/fetch-older-chats/{chat_id}/{first_msg_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'fetchOlderChats']);
        Route::get('/fetch-chats/{chat_id}/{last_msg_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'fetchChats']);
        Route::post('clear-message-centre/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'clearChatForUser']);
        Route::get('delete-message-centre/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'deleteChatForUser']);
        Route::get('block-message-centre/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'blockChat']);
        Route::get('unblock-message-centre/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'unblockChat']);
        Route::post('get-conversation/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'getConversation']);
        Route::get('/search', [App\Http\Controllers\Panel\MessageCentreController::class, 'chatSearch']);
        Route::post('/update-typing', [App\Http\Controllers\Panel\MessageCentreController::class, 'updateTypingStatus']);
        Route::get('/fetch-typing/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'fetchTypingStatus']);
        Route::get('/search-messages', [App\Http\Controllers\Panel\MessageCentreController::class, 'searchChatMessages']);
        Route::get('conversation-list', [App\Http\Controllers\Panel\MessageCentreController::class, 'conversationList']);
        Route::post('/update-message/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'updateMessage']);
        Route::post('/save-message-to-draft', [App\Http\Controllers\Panel\MessageCentreController::class, 'saveDraftMessage']);

        Route::get('/fetch-chat-bot/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'fetchChatBot']);
        Route::post('/reacted-message/{group_id}/{message_uid}', [App\Http\Controllers\Panel\MessageCentreController::class, 'getReactedMsg']);



        Route::get('/get-user-profile/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'getUserProfile'])->name('get-user-profile');
        Route::get('/get-shared-file/{chat_id}', [App\Http\Controllers\Panel\MessageCentreController::class, 'getSharedFile'])->name('get-shared-file');

    });


    Route::group(array('prefix' => 'quotations', 'as' => 'quotations.'), function () {

        Route::get('/', [App\Http\Controllers\Panel\QuotationController::class, 'index'])->name('list');
        Route::get('/add', [App\Http\Controllers\Panel\QuotationController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\QuotationController::class, 'save'])->name('save');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\QuotationController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\QuotationController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\QuotationController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\QuotationController::class, 'deleteSingle'])->name('delete');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\QuotationController::class, 'deleteMultiple'])->name('deleteMultiple');

    });
    Route::group(array('prefix' => 'case-with-professionals', 'as' => 'case-with-professionals.'), function () {
        Route::get('/overview', [App\Http\Controllers\Panel\MyCasesController::class, 'overview'])->name('overview');
        Route::get('/', [App\Http\Controllers\Panel\MyCasesController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\MyCasesController::class, 'getAjaxList'])->name('ajax-list');
        Route::post('/compact-ajax-list', [App\Http\Controllers\Panel\MyCasesController::class, 'getCompactAjaxList'])->name('compact-ajax-list');
        Route::get('/view/{case_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'viewDetails'])->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('view');
        Route::get('/send-request/{case_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'sendReqeust'])->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('send-request');
        Route::post('/request-ajax-list/{case_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'requestAjaxList'])->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('request-ajax-list');
        Route::get('/add-request/{case_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'addReqeust'])->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('add-request');
        Route::get('/edit-request/{request_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'editRequest'])->name('edit-request');
        
        Route::get('/delete-request/{case_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'deleteRequest'])->name('delete-request');
        Route::post('/update-request/{case_request_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'updateRequest'])->name('update-request');
        Route::post('/upload-request-attachment', [App\Http\Controllers\Panel\MyCasesController::class, 'uploadRequestAttachment'])->name('upload-request-attachment');
        Route::post('/save-request', [App\Http\Controllers\Panel\MyCasesController::class, 'saveRequest'])->name('save-request');
        Route::post('/view-request-comments/{case_request_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'fetchCaseRequestComments'])->name('case-request-comment');

        Route::get('/view-request/{request_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'viewReqeust'])->name('view-request');
        Route::get('/mark-as-complete-request/{request_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'markAsCompleteRequest'])->name('mark-as-complete-request');
        
        Route::get('/view-request-form/{case_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'viewReqeustForm'])->name('view-request-form');

        Route::get('/download-request-attachment', [App\Http\Controllers\Panel\MyCasesController::class, 'downloadRequestAttachment']);
        Route::post('/upload-request-note-attachment', [App\Http\Controllers\Panel\MyCasesController::class, 'uploadRequestNoteAttachment'])->name('upload-request-note-attachment');

        Route::get('/download-note-attachment', [App\Http\Controllers\Panel\MyCasesController::class, 'downloadFile'])->name('downloadFile');

        Route::post('/save-note/{id}', [App\Http\Controllers\Panel\MyCasesController::class, 'saveNote'])->name('save-note');
        Route::get('/view-assesment-form/{id}', [App\Http\Controllers\Panel\MyCasesController::class, 'viewAssesmentForm'])->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('view-assesment-form');

        Route::get('/assign-to-staff/{id}', [App\Http\Controllers\Panel\MyCasesController::class, 'assignToStaff'])->name('assign-to-staff');
        Route::post('/save-assign-staff', [App\Http\Controllers\Panel\MyCasesController::class, 'saveAssignStaff'])->name('save-assign-staff');

        // aggrement routr
        Route::get('/retainers', [App\Http\Controllers\Panel\MyCasesController::class, 'retainers'])->name('retainers');
        Route::post('/retainers-ajax-list', [App\Http\Controllers\Panel\MyCasesController::class, 'getRetainerAgreementAjax'])->name('retainers-ajax-list');
        Route::get('/retainers/send-reminder/{id}', [App\Http\Controllers\Panel\MyCasesController::class, 'sendRetainerAgreementReminder'])->name('retainers-reminder');

        Route::group(array('prefix' => 'retain-agreements', 'as' => 'retain-agreements.'), function () {
            Route::get('/{case_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'retainAgreements'])->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('retain-agreements');
            Route::post('/save-retain-agreements', [App\Http\Controllers\Panel\MyCasesController::class, 'saveRetainAgreements'])->name('save-retain-agreements');
            Route::get('/check-retain-agreements/{id}', [App\Http\Controllers\Panel\MyCasesController::class, 'checkRetainAgreements'])->name('save-retain-agreements');
            Route::get('/generate-retain-agreements/{id}', [App\Http\Controllers\Panel\MyCasesController::class, 'aiRetainerAgreementForm'])->name('ai-retain-agreements');
            Route::post('/generate-retain-agreements/{id}', [App\Http\Controllers\Panel\MyCasesController::class, 'generateRetainAgreements'])->name('generate-retain-agreements');
            Route::post('/save-ai-retain-agreements/{id}', [App\Http\Controllers\Panel\MyCasesController::class, 'saveAiRetainAgreements'])->name('save-ai-retain-agreements');

            Route::get('/ai-bot/{case_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'retainAgreementAiBot'])->name('ai-bot');
            Route::post('/ai-bot-chat/{case_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'submitRetainAgreementBot'])->name('ai-bot');
            Route::get('/fetch-ai-bot-chat/{case_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'fetchRetainAgreementBot'])->name('fetch-ai-bot');
            Route::get('/save-popup/{agreement_id}', [App\Http\Controllers\Panel\MyCasesController::class, 'showPopupForSaveAgreement'])->name('save-popup');
            // Route::post('/save-ai-retain-agreements', [App\Http\Controllers\Panel\MyCasesController::class, 'saveAiRetainAgreements'])->name('save-ai-retain-agreements');
        });
        // end
        // documents route
        Route::get('/documents/{case_id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'documents'])->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('documents');
        Route::get('/documents/add-folder/{case_id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'documentsAddFolder'])->name('documents.add.folder');
        Route::post('/documents/save-folder/{case_id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'documentsSaveFolder'])->name('documents.save.folder')->middleware('input_sanitization');
        Route::get('/documents/{type}/{case_id}/{folder_id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'showDocuments'])->name('show.documents');
        Route::post('/upload-document', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'uploadDocument'])->name('upload.documents');
        Route::get('/delete-document/{id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'deleteDocument'])->name('delete.documents');
        Route::post('/save-document', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'saveDocument'])->name('save.documents');
        Route::get('/download-documents', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'downloadDocument']);
        Route::get('/view-document/{case_id}/{folder_id}/{document_id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'showFilePreview']);
        Route::post('/view-case-document/{case_id}/{folder_id}/{document_id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'showCaseDocumentPreview']);
        Route::post('/save-document-comment', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'saveDocumentComment']);
        Route::post('/fetch-document-comments', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'fetchDocumentComments']);
        Route::post('/case-document/comment-delete/{comment_id}/{document_id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'documentCommentDelete']);
       
        Route::post('/delete-multiple-documents', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'deleteMultipleDocuments']);
        Route::get('/rename-document/{id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'renameDocument']);
        Route::post('/rename-document/{id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'saveRenameDocument']);
       

        Route::get('/delete-document-folder/{id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'deleteDocumentFolder']);

        Route::get('/documents/edit-folder/{id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'documentsEditFolder']);
        Route::post('/documents/update-folder/{id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'documentsUpdateFolder'])->middleware('input_sanitization');

        Route::post('/documents-folders/reorder', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'reorderFolders']);

        Route::post('download-multiple-documents', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'downloadMultipleDocument']);

        
        Route::post('/documents/link-to-groups', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'linkToGroups']);
        Route::post('/documents/encrypt-documents', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'encryptDocuments']);

        Route::get('/encrypted-documents/{case_id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'encryptedDocuments'])
->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('encryptedDocuments');
        
        Route::get('/show-decryption-document-form', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'decryptDocumentPopup'])->name('encryptedDocuments');

        Route::post('/documents/decrypt-documents', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'decryptDocuments']);

        Route::get('/download-zip', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'downloadZip']);

        Route::get('/encryption/forgot-key/{id}', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'sendEncryptionOtp']);


        Route::post('/verify-encryption-login-otp', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'encryptionVerifyOtp'])->name('document.verify.otp.success');

        Route::post('/send-login-otp', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'loginSendOtp'])->name('login.send.otp');


        Route::get('/show-encryption-folder-model', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'showEncryptionFolderModal'])->name('login.send.otp');
        Route::post('/documents/reorder', [App\Http\Controllers\Panel\CaseDocumentsController::class, 'reorderDocuments']);

        Route::group(array('prefix' => 'stages', 'as' => 'stages.'), function () {
            Route::get('/{case_id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'stagesList'])->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('list');
            Route::get('/add/{case_id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'addStages'])->name('add');
            Route::post('/save/{case_id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'saveStages'])->name('save')->middleware('input_sanitization');
            Route::post('/ajax-list', [App\Http\Controllers\Panel\CaseStagesController::class, 'getStagesAjaxList'])->name('ajax-list');
            Route::get('/edit/{id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'editStages'])->name('edit');
            Route::post('/update/{id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'updateStages'])->name('update')->middleware('input_sanitization');
            Route::get('/workflow/{id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'workflow'])->name('workflow');
            Route::post('/generate-workflow', [App\Http\Controllers\Panel\CaseStagesController::class, 'generateWorkflow'])->name('generateWorkflow');
            Route::get('/delete/{id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'deleteStages'])->name('delete');
            Route::get('/add-workflow/{id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'addWorkflow'])->name('addWorkflow');
            Route::post('/save-workflow', [App\Http\Controllers\Panel\CaseStagesController::class, 'saveWorkflow'])->name('saveWorkflow');
            Route::post('/update-sorting', [App\Http\Controllers\Panel\CaseStagesController::class, 'updateSorting']);
            Route::get('/generate-stages-via-ai/{case_id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'generateStagesViaAi'])->name('generate-stages-via-ai');
            Route::get('/view/{sub_stage_id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'viewSubStage'])->name('view');

            Route::post('/mark-as-complete', [App\Http\Controllers\Panel\CaseStagesController::class, 'markAsComplete'])->name('mark-as-complete');

            Route::get('fill-sub-stage/{stage_id}',[App\Http\Controllers\Panel\CaseStagesController::class, 'fillSubStage']);
            Route::get('view-form/{sub_stage_id}',[App\Http\Controllers\Panel\CaseStagesController::class, 'viewForm']);
            Route::group(array('prefix' => 'sub-stages', 'as' => 'sub-stages.'), function () {
                // Route::get('/{case_id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'stagesList'])->name('list');
                Route::get('/add/{stage_id}', [App\Http\Controllers\Panel\CaseSubStagesController::class, 'addSubStages'])->name('add');
                Route::post('/save/{stage_id}', [App\Http\Controllers\Panel\CaseSubStagesController::class, 'saveSubStages'])->name('save');
                // Route::post('/ajax-list', [App\Http\Controllers\Panel\CaseStagesController::class, 'getStagesAjaxList'])->name('ajax-list');
                Route::get('/edit/{id}', [App\Http\Controllers\Panel\CaseSubStagesController::class, 'editSubStages'])->name('edit');
                Route::post('/update/{id}', [App\Http\Controllers\Panel\CaseSubStagesController::class, 'updateSubStages'])->name('update');
                Route::get('/delete/{id}', [App\Http\Controllers\Panel\CaseSubStagesController::class, 'deleteSubStages'])->name('delete');
                Route::post('/mark-as-complete', [App\Http\Controllers\Panel\CaseSubStagesController::class, 'markAsComplete'])->name('mark-as-complete');
                 Route::post('/update-sorting', [App\Http\Controllers\Panel\CaseSubStagesController::class, 'updateSorting']);
            });
        });

        Route::group(array('prefix' => 'messages', 'as' => 'messages.'), function () {
            Route::get('/{case_id}', [App\Http\Controllers\Panel\CaseMessagesController::class, 'messageList'])->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('list');
            Route::get('/add/{case_id}', [App\Http\Controllers\Panel\CaseMessagesController::class, 'createGroup'])->name('add');
            // Route::post('/save/{case_id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'saveStages'])->name('save');
            // Route::post('/ajax-list', [App\Http\Controllers\Panel\CaseStagesController::class, 'getStagesAjaxList'])->name('ajax-list');
            // Route::get('/edit/{id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'editStages'])->name('edit');
            // Route::post('/update/{id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'updateStages'])->name('update');
            // Route::get('/delete/{id}', [App\Http\Controllers\Panel\CaseStagesController::class, 'deleteStages'])->name('delete');
        });
        
        Route::group(array('prefix' => 'invoices', 'as' => 'invoices.'), function () {
            Route::get('/{case_id}', [App\Http\Controllers\Panel\CaseInvoicesController::class, 'index'])->middleware('check.ownership:' . CaseWithProfessionals::class . ',case_id')->name('list');
            Route::post('/ajax-list', [App\Http\Controllers\Panel\CaseInvoicesController::class, 'getAjaxList'])->name('ajax-list');

            Route::get('/add/{case_id}', [App\Http\Controllers\Panel\CaseInvoicesController::class, 'add'])->name('add');
            Route::post('/save/{case_id}', [App\Http\Controllers\Panel\CaseInvoicesController::class, 'save'])->name('save')->middleware('input_sanitization');
            Route::get('/edit/{id}', [App\Http\Controllers\Panel\CaseInvoicesController::class, 'edit'])->middleware('check.ownership:' . Invoice::class . ',id,added_by')->name('edit');
            Route::post('/update/{id}', [App\Http\Controllers\Panel\CaseInvoicesController::class, 'update'])->name('update');
           
            Route::get('/download-invoice-pdf/{id}', [App\Http\Controllers\Panel\CaseInvoicesController::class, 'downloadInvoicePDF'])->name('download.invoice.pdf');

            Route::get('/delete/{id}', [App\Http\Controllers\Panel\CaseInvoicesController::class, 'deleteSingle'])->name('deleteSingle');
            Route::get('/copy-link/{id}', [App\Http\Controllers\Panel\CaseInvoicesController::class, 'copyLink'])->name('copy.link');

        });
        
    });


    Route::group(array('prefix' => 'roles', 'as' => 'roles.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\RolesController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\RolesController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/add', [App\Http\Controllers\Panel\RolesController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\RolesController::class, 'save'])->name('save')->middleware('input_sanitization');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\RolesController::class, 'deleteSingle'])->middleware('check.ownership:' . Roles::class . ',id')->name('deleteSingle');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\RolesController::class, 'deleteMultiple'])->name('deleteMultiple');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\RolesController::class, 'edit'])->middleware('check.ownership:' . Roles::class . ',id')->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\RolesController::class, 'update'])->name('update')->middleware('input_sanitization');
    });
    
    Route::group(array('prefix' => 'staff', 'as' => 'staff.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\StaffController::class, 'index'])->name('list');
        Route::get('/active-staffs-list', [App\Http\Controllers\Panel\StaffController::class, 'activeStaffList'])->name('active-staff-list');
        Route::get('/trash-staffs-list', [App\Http\Controllers\Panel\StaffController::class, 'trashStaffList'])->name('trash-staff-list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\StaffController::class, 'getAjaxList'])->name('ajax-list');
          Route::get('/add', [App\Http\Controllers\Panel\StaffController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\StaffController::class, 'save'])->name('save')->middleware('input_sanitization');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\StaffController::class, 'edit'])->middleware('check.ownership:' . User::class . ',id')->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\StaffController::class, 'update'])->name('update')->middleware('input_sanitization');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\StaffController::class, 'deleteSingle'])->name('deleteSignle');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\StaffController::class, 'deleteMultiple'])->name('deleteMultiple');
        Route::get('/change-password/{id}', [App\Http\Controllers\Panel\StaffController::class, 'changePassword'])->name('changePassword');
        Route::post('/update-password/{id}', [App\Http\Controllers\Panel\StaffController::class, 'updatePassword'])->name('updatePassword');
        Route::get('/privileges/{id}', [App\Http\Controllers\Panel\StaffController::class, 'setPrivileges'])->name('setPrivileges');
        Route::post('/privileges/{id}', [App\Http\Controllers\Panel\StaffController::class, 'savePrivileges'])->name('savePrivileges');

        Route::get('/trash-staff-list', [App\Http\Controllers\Panel\StaffController::class, 'trashStaffs'])->name('trash-staffs');
        Route::post('/trash-staff-ajax-list', [App\Http\Controllers\Panel\StaffController::class, 'getTrashStaffsAjaxList']);
        Route::get('/restore/{id}', [App\Http\Controllers\Panel\StaffController::class, 'restoreSingle']);
        Route::post('/restore-multiple', [App\Http\Controllers\Panel\StaffController::class, 'restoreMultiple']);
    });




    Route::group(array('prefix' => 'document-folders', 'as' => 'document-folders.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\DocumentsFolderController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\DocumentsFolderController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/add', [App\Http\Controllers\Panel\DocumentsFolderController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\DocumentsFolderController::class, 'save'])->name('save')->middleware('input_sanitization');;
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\DocumentsFolderController::class, 'deleteSingle'])->middleware('check.ownership:' . DocumentsFolder::class . ',id')->name('deleteSingle');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\DocumentsFolderController::class, 'deleteMultiple'])->name('deleteMultiple');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\DocumentsFolderController::class, 'edit'])->middleware('check.ownership:' . DocumentsFolder::class . ',id')->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\DocumentsFolderController::class, 'update'])->name('update')->middleware('input_sanitization');;
    });


    Route::get('/support/thankyou', [App\Http\Controllers\HomeController::class, 'supportThankyou']);
    Route::get('/go-to-support', [App\Http\Controllers\HomeController::class, 'goToSupport']);
    Route::get('/notification/redirect/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'notificationRedirect']);

    Route::group(array('prefix' => 'notifications', 'as' => 'notifications.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\NotificationsController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\NotificationsController::class, 'getAjaxList'])->name('ajax-list');
    });


    Route::group(array('prefix' => 'cases', 'as' => 'cases.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\CasesController::class, 'index'])->name('list');
        Route::get('/fetch-sub-service', [App\Http\Controllers\Panel\CasesController::class, 'fetchSubService'])->name('fetch-sub-service');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\CasesController::class, 'getAjaxList'])->name('ajax-list');
        Route::post('/grid-ajax-list', [App\Http\Controllers\Panel\CasesController::class, 'getGridAjaxList'])->name('grid-ajax-list');
        Route::post('/read-unread', [App\Http\Controllers\Panel\CasesController::class, 'readUnread'])->name('read-unread');
        Route::post('/case-ajax-list', [App\Http\Controllers\Panel\CasesController::class, 'caseGetAjaxList'])->name('case-ajax-list');
        Route::get('/mark-as-favourite/{case_id}', [App\Http\Controllers\Panel\CasesController::class, 'markAsFavourite']);
        Route::post('/settings', [App\Http\Controllers\Panel\CasesController::class, 'updateSettings']);
        Route::get('view/{id}', [App\Http\Controllers\Panel\CasesController::class, 'viewDetails']);
        Route::post('/fetch-quotation', [App\Http\Controllers\Panel\CasesController::class, 'fetchQuotation'])->name('fetchQuotation');
        Route::post('/save-proposal', [App\Http\Controllers\Panel\CasesController::class, 'saveProposals'])->name('save-proposal');
        Route::post('/update-proposal', [App\Http\Controllers\Panel\CasesController::class, 'updateProposals'])->name('update-proposal');

        Route::post('/proposal-history', [App\Http\Controllers\Panel\CasesController::class, 'proposalHistory'])->name('proposal-history');
        Route::get('/edit-proposal/{case_id}', [App\Http\Controllers\Panel\CasesController::class, 'editProposal'])->name('edit-proposal');
        Route::get('/create-group/{case_id}', [App\Http\Controllers\Panel\CasesController::class, 'createGroup'])->name('create-group');
         Route::post('/generate-case-proposal', [App\Http\Controllers\Panel\CasesController::class, 'generateCaseProposal']);
        Route::get('/withdraw-proposal/{case_id}', [App\Http\Controllers\Panel\CasesController::class, 'withdrawProposal'])->name('withdraw-proposal');
    });


    Route::group(array('prefix' => 'transactions/receipts', 'as' => 'transactions.receipts.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\TransactionReceiptController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\TransactionReceiptController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/add', [App\Http\Controllers\Panel\TransactionReceiptController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\TransactionReceiptController::class, 'save'])->name('save');
        Route::get('/view/{id}', [App\Http\Controllers\Panel\TransactionReceiptController::class, 'view'])->middleware('check.ownership:' . Invoice::class . ',id,user_id')->name('view');

        Route::get('/delete/{id}', [App\Http\Controllers\Panel\TransactionReceiptController::class, 'deleteSingle'])->name('deleteSingle');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\TransactionReceiptController::class, 'deleteMultiple'])->name('deleteMultiple');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\TransactionReceiptController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\TransactionReceiptController::class, 'update'])->name('update');
    });

    Route::group(array('prefix' => 'earnings'), function () {
        Route::get('/', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'overview'])->name('earning-overview');
         Route::group(array('prefix' => 'points-earn-history', 'as' => 'points-earn-history.'), function () {
                Route::get('/', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'index'])->name('list');
                Route::post('/ajax-list', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'getAjaxList'])->name('ajax-list');
                Route::get('/add', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'add'])->name('add');
                Route::post('/save', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'save'])->name('save');
                Route::get('/view/{id}', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'view'])->name('view');
                Route::get('/download-badge/{id}', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'downloadBadge'])->name('downloadBadge');

                Route::get('/delete/{id}', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'deleteSingle'])->name('deleteSingle');
                Route::post('/delete-multiple', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'deleteMultiple'])->name('deleteMultiple');
                Route::get('/edit/{id}', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'edit'])->name('edit');
                Route::post('/update/{id}', [App\Http\Controllers\Panel\PointEarnHistoryController::class, 'update'])->name('update');
            });
    });
   
    Route::get('/transactions', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'overview'])->name('transaction-overview');
    Route::group(array('prefix' => 'transactions/history', 'as' => 'transactions.history.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'getAjaxList'])->name('ajax-list');
        Route::post('/monthly-ajax-list', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'getMonthlyAjaxList'])->name('monthly-ajax-list');
        Route::get('/view/{id}', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'view'])->name('view');
        Route::get('/view-details/{id}', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'viewOneTime'])->name('view');
        Route::get('/cancel/{id}', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'cancelSubscription'])->name('subscription.cancel');
        Route::get('/onetime-quick-view/{id}', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'quickViewOneTime'])->name('quick-view-onetime');
        Route::get('/monthly-quick-view/{id}', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'quickViewMonthly'])->name('quick-view-monthly');
    });

    Route::group(array('prefix' => 'payment-methods', 'as' => 'payment-methods.'), function () {
        Route::get('/add-card/{id}', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'addCardDetails'])->name('add-card-details');
        Route::post('/save-card', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'saveCardDetails'])->name('save-card-details');
        Route::get('/cards', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'userCardList'])->name('save-card-details');
        Route::post('/cards-ajax-list', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'useCardAjaxList'])->name('ajax-list');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'removeCardDetails'])->name('deleteSingle');
        Route::get('/default/{id}', [App\Http\Controllers\Panel\TransactionHistoryController::class, 'makeDefaultCard'])->name('deleteSingle');
    });
    Route::group(array('prefix' => 'role-privileges', 'as' => 'role-privileges.'), function () {
    
        Route::get('/', [App\Http\Controllers\Panel\ModuleController::class, 'rolePrivileges'])->name('list');
        Route::post('/', [App\Http\Controllers\Panel\ModuleController::class, 'saveRolePrivileges']);
    });
    

    
    Route::get('/payment-methods/payment', [App\Http\Controllers\Panel\PaymentMethodsPaymentController::class, 'professionalSupportInitiative'])->name('professional-support')->middleware('input_sanitization');

    Route::post('pay-for-support', [App\Http\Controllers\Panel\PaymentMethodsPaymentController::class, 'processSupportPayment'])->name('process.support-payment');

    Route::get('/processing-payment', [App\Http\Controllers\Panel\PaymentMethodsPaymentController::class, 'processingPayment']);

    Route::post('payment-methods/stripe/complete-payment', [App\Http\Controllers\Panel\PaymentMethodsPaymentController::class, 'completePaymentAction']);


    Route::group(array('prefix' => 'invoices', 'as' => 'invoices.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\InvoicesController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\InvoicesController::class, 'getAjaxList'])->name('ajax-list');
           Route::get('/add', [App\Http\Controllers\Panel\InvoicesController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\InvoicesController::class, 'save'])->name('save')->middleware('input_sanitization');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\InvoicesController::class, 'edit'])->middleware('check.ownership:' . Invoice::class . ',id,added_by')->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\InvoicesController::class, 'update'])->name('update');
        Route::get('/generate-link-option/{id}', [App\Http\Controllers\Panel\InvoicesController::class, 'generateLink'])->name('update')->middleware('input_sanitization');;
        
        Route::post('/generate-payment-link', [App\Http\Controllers\Panel\InvoicesController::class, 'createPaymentLink'])->name('payment.link');
        Route::get('/download-invoice-pdf/{id}', [App\Http\Controllers\Panel\InvoicesController::class, 'downloadInvoicePDF'])->name('download.invoice.pdf');
        Route::get('/copy-link/{id}', [App\Http\Controllers\Panel\InvoicesController::class, 'copyLink'])->name('copy.link');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\InvoicesController::class, 'deleteSingle'])->name('deleteSingle');



        Route::group(array('prefix' => 'pay', 'as' => 'pay.'), function () {
            Route::get('/{id}', [App\Http\Controllers\Panel\CommonInvoiceController::class, 'index'])->name('invoice.pay');
            Route::post('pay-for-global', [App\Http\Controllers\Panel\CommonInvoiceController::class, 'processGlobalPayment'])->name('process.global-payment');
          
        });
        Route::get('/processing-payment', [App\Http\Controllers\Panel\CommonInvoiceController::class, 'processingPayment']);
        Route::post('/stripe/complete-payment', [App\Http\Controllers\Panel\CommonInvoiceController::class, 'completePaymentAction']);
        // payment
        // Route::get('/payment/{id}', [App\Http\Controllers\Panel\InvoicesController::class, 'professionalGlobalInitiative'])->name('global-support');



        // Route::post('pay-for-support', [App\Http\Controllers\Panel\InvoicesController::class, 'processSupportPayment'])->name('process.support-payment');

        // Route::get('/processing-payment', [App\Http\Controllers\Panel\InvoicesController::class, 'processingPayment']);
    
        // Route::post('/stripe/complete-payment', [App\Http\Controllers\Panel\InvoicesController::class, 'completePaymentAction']);


       

    });
    Route::post('pay-for-appointment-booking', [App\Http\Controllers\StripeController::class, 'processAppointmentBooking'])->name('process.appointment-booking');
             


    Route::group(array('prefix' => 'predefined-case-stages', 'as' => 'predefined-case-stages.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/add', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'save'])->name('save')->middleware('input_sanitization');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'edit'])->middleware('check.ownership:' . PredefinedCaseStages::class . ',id')->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'update'])->name('update')->middleware('input_sanitization');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'deleteSingle'])->name('deleteSingle');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'deleteMultiple'])->name('deleteMultiple');
        Route::get('/view/{id}', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'view'])->name('view');
        Route::get('/views/{id}', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'views'])->name('views');

        Route::post('/mark-as-complete', [App\Http\Controllers\Panel\PredefinedCaseStagesController::class, 'markAsComplete'])->name('mark-as-complete');
    });


    Route::group(array('prefix' => 'predefined-case-sub-stages', 'as' => 'predefined-case-sub-stages.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\PredefinedCaseSubStagesController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\PredefinedCaseSubStagesController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/add', [App\Http\Controllers\Panel\PredefinedCaseSubStagesController::class, 'add'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\PredefinedCaseSubStagesController::class, 'save'])->name('save');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\PredefinedCaseSubStagesController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\PredefinedCaseSubStagesController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\PredefinedCaseSubStagesController::class, 'deleteSingle'])->name('deleteSingle');
        Route::get('/view/{id}', [App\Http\Controllers\Panel\PredefinedCaseSubStagesController::class, 'view'])->name('view');
        Route::post('/update-sorting', [App\Http\Controllers\Panel\PredefinedCaseSubStagesController::class, 'updateSorting']);

        Route::post('/mark-as-complete', [App\Http\Controllers\Panel\PredefinedCaseSubStagesController::class, 'markAsComplete'])->name('mark-as-complete');
    });

 Route::group(array('prefix' => 'earning-report', 'as' => 'earning-report.'), function () {
    Route::get('/', [App\Http\Controllers\Panel\EarningReportController::class, 'earningReport'])->name('list');
    Route::post('/earning-report-ajax-list', [App\Http\Controllers\Panel\EarningReportController::class, 'earningReportGetAjaxList'])->name('earning-report-ajax-list');
    });
    Route::get('/earning-appointment-report', [App\Http\Controllers\Panel\EarningReportController::class, 'earningAppointmentReport'])->name('earningAppointmentReport');
    Route::post('/earning-appointment-report-ajax-list', [App\Http\Controllers\Panel\EarningReportController::class, 'earningAppointmentReportGetAjaxList'])->name('earning-appointment-report-ajax-list');

    Route::get('/earning-global-invoice-report', [App\Http\Controllers\Panel\EarningReportController::class, 'earningGlobalInvoice'])->name('earningGlobalInvoiceReport');
    Route::post('/earning-global-invoice-report-ajax-list', [App\Http\Controllers\Panel\EarningReportController::class, 'earningGlobalInvoiceReportGetAjaxList'])->name('earning-global-invoice-report-ajax-list');

    Route::group(array('prefix' => 'my-feeds', 'as' => 'my-feeds.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\ManageFeedsController::class, 'index'])->name('list');
        Route::get('/status/{status}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'index'])->name('index');
         Route::get('/add-new-feed', [App\Http\Controllers\Panel\ManageFeedsController::class, 'addNewFeed'])->name('add');
        Route::post('/save', [App\Http\Controllers\Panel\ManageFeedsController::class, 'save'])->name('save');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\ManageFeedsController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/detail/{id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'feedDetail'])->name('feed-detail');
        Route::get('/edit/{id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'deleteSingle'])->name('feed-delete');
        Route::post('/save-comment/{id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'saveComments'])->name('save-comment')->middleware('input_sanitization');
        Route::post('/fetch-comments', [App\Http\Controllers\Panel\ManageFeedsController::class, 'fetchComments'])->name('fetch-comments');
        Route::post('/fetch-reply-comments', [App\Http\Controllers\Panel\ManageFeedsController::class, 'fetchReplyComments'])->name('fetch-reply-comments');
        Route::post('/load-more-replies', [App\Http\Controllers\Panel\ManageFeedsController::class, 'loadMoreReply'])->name('load-more-reply');
        
        Route::post('/{feed_id}/like', [App\Http\Controllers\Panel\ManageFeedsController::class, 'likeFeed'])->name('feed.like');
        Route::post('/{feed_id}/pin-post', [App\Http\Controllers\Panel\ManageFeedsController::class, 'pinPost']);
        Route::post('/{feed_id}/unpin-post', [App\Http\Controllers\Panel\ManageFeedsController::class, 'unpinPost']);
        Route::post('/reply-comment-form/{comment_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'replyCommentForm']);
        Route::post('/comment-like/{comment_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'likeComment']);
        Route::post('/comment-unlike/{comment_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'unlikeComment']);

        Route::get('/flag-comment/{comment_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'flagComment']);
        Route::post('/save-flag-comment', [App\Http\Controllers\Panel\ManageFeedsController::class, 'saveFlagComment']);
        Route::get('/remove-flag/{comment_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'removeFlagComment']);
        
        Route::get('/{user}/follow', [App\Http\Controllers\Panel\ManageFeedsController::class, 'follow'])->name('feeds.follow');
        Route::get('/{user}/unfollow', [App\Http\Controllers\Panel\ManageFeedsController::class, 'unfollow'])->name('feeds.unfollow');
        Route::get('/mutual-follows', [App\Http\Controllers\Panel\ManageFeedsController::class, 'mutualFollows'])->name('feeds.mutualFollows');
        Route::get('/feed-setting/{feed_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'addSetting'])->name('feed-settings');
        Route::post('/copy-feed/{feed_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'copyFeed'])->name('copy-feed');
        Route::post('/repost-feed/{feed_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'repostFeed'])->name("repost");

        Route::get('/edit-comment/{comment_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'editComment']);
        Route::post('/update-comment/{comment_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'updateComment']);
        Route::get('/delete-comment/{comment_id}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'deleteComment']);
        Route::post('/fetch-updated-comments', [App\Http\Controllers\Panel\ManageFeedsController::class, 'fetchUpdatedComments']);

        Route::post('/view-media/{feed_id}/{file_name}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'viewMedia']);
        Route::post('/view-comment-media/{comment_id}/{file_name}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'viewCommentMedia']);
        
           Route::post('/favourites/{id}/{type}', [App\Http\Controllers\Panel\ManageFeedsController::class, 'FavouritesFeed'])->name('favourites');


    });

    Route::group(array('prefix' => 'manage-discussion-threads', 'as' => 'manage-discussion-threads.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'index'])->name('index');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'getAjaxList'])->name('ajax-list');
        Route::post('/save', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'save'])->name('save')->middleware('input_sanitization');
        Route::get('/{status}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'index'])->name('index');
        Route::get('/category/{category_id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'categoryList'])->name('category-list');
        Route::get('/{id}/detail', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'discussionDetail'])->name('detail');
        Route::post('/update-comment/{id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'updateComment']);
        Route::post('/save-comment/{discussion_id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'saveComments'])->name('feed.comment');
        // Route::post('/fetch-comment', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'fetchComment'])->name('fetch.comments');
        Route::post('/fetch-content/{discussion_id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'getDiscussionContent']);


        Route::post('/comment/{comment_id}/like', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'commentLike']);
        Route::post('/comment/{comment_id}/unlike', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'commentUnlike']);
          Route::get('/{comment_id}/delete', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'deleteComment'])->name('feed.delete.comment');
        Route::post('/comment/{comment_id}/mark-as-answer', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'markCommentAsAnswer']);
        Route::post('/comment/{comment_id}/remove-as-answer', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'removeCommentAsAnswer']);
        Route::post('/comment/{comment_id}/potential-answer', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'markAsPotentialAnswer']);
        Route::post('/comment/{comment_id}/remove-as-potential-answer', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'removeAsPotentialAnswer']);

        Route::get('/flag-comment/{comment_id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'flagComment']);
        Route::post('/save-flag-comment', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'saveFlagComment']);
        Route::get('/remove-flag/{comment_id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'removeFlagComment'])->name('feeds.remove-flag');

        Route::get('/edit/{id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'editDiscussionThread'])->name('edit');
        Route::get('/edit/{id}/modal', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'editDiscussionThreadModal'])->name('edit-modal');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'updateDiscussionThread'])->name('update');


        Route::get('add/thread', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'addeDiscussionThread']);
        Route::get('add/thread/modal', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'addDiscussionThreadModal']);
        Route::post('/save/thread', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'saveDiscussionThread']);

        Route::get('/delete/{id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'deleteDiscussion'])->name('deleteSingle');
        Route::post('/reply-comment-form/{comment_id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'replyCommentForm']);

        Route::get('/delete-member/{id}/{discussion_id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'deleteMemberFromDiscussion']);
        Route::get('/accept-member/{id}/{discussion_id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'acceptMemberForDiscussion']);

        Route::post('/view-media/{discussion_id}/{file_name}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'viewMedia']);
        Route::post('/view-comment-media/{comment_id}/{file_name}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'viewCommentMedia']);
        Route::post('/upload-file', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'uploadDiscussionFiles']);
     Route::get('/favourite/{id}', [App\Http\Controllers\Panel\ManageDiscussionThreadsController::class, 'addFavourite'])->name('favourite');
   
    });

    Route::group(array('prefix' => 'send-invitations','as'=>'send-invitations.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\SendInvitationController::class, 'index'])->name('lists');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\SendInvitationController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/add', [App\Http\Controllers\Panel\SendInvitationController::class, 'add'])->name('lists');
        Route::post('/save', [App\Http\Controllers\Panel\SendInvitationController::class, 'save'])->name('save');
        Route::post('/show-email-preview', [App\Http\Controllers\Panel\SendInvitationController::class, 'showMailPreview'])->name('show-mail-preview');
        Route::post('/bulk-upload-csv', [App\Http\Controllers\Panel\SendInvitationController::class, 'bulkUploadCsv'])->name('bulk-upload-csv');
   
    Route::post('/delete-multiple', [App\Http\Controllers\Panel\SendInvitationController::class, 'deleteMultiple'])->name('deleteMultiple');
     Route::get('/delete/{id}', [App\Http\Controllers\Panel\SendInvitationController::class, 'deleteSingle'])->middleware('check.ownership:' . ReviewsInvitations::class . ',case_id')->name('deleteSingle');
    });




        // Ticket routes for client panel
        Route::group(['prefix' => 'tickets', 'as' => 'tickets.'], function () {
            Route::get('/', [App\Http\Controllers\Panel\TicketController::class, 'index'])->name('index');
            Route::post('/ajax-list', [App\Http\Controllers\Panel\TicketController::class, 'getAjaxList'])->name('ajax-list');
            Route::get('/create', [App\Http\Controllers\Panel\TicketController::class, 'create'])->name('create');
            Route::get('/create-modal', [App\Http\Controllers\Panel\TicketController::class, 'createModal'])->name('create-modal');
            Route::post('/store', [App\Http\Controllers\Panel\TicketController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Panel\TicketController::class, 'view'])->middleware('check.ownership:' . Ticket::class . ',id')->name('show');
            Route::post('/reply/{id}', [App\Http\Controllers\Panel\TicketController::class, 'addReply'])->name('reply');
        });
          Route::group(array('prefix' => 'reviews'), function () {
         Route::get('/', [App\Http\Controllers\Panel\ReviewsController::class, 'reviewsOverview'])->name('review-overview');
        Route::get('/send-invitation-email/add', [App\Http\Controllers\Panel\ReviewsController::class, 'sendInviteNew'])->name('add');
        Route::post('/send-invitation-email/save', [App\Http\Controllers\Panel\ReviewsController::class, 'sendInviteCSV'])->name('sendInviteCSV');
        Route::get('/send-invitations', [App\Http\Controllers\Panel\ReviewsController::class, 'sendInvitations'])->name('send-invitations.list');
        Route::post('/send-invitation', [App\Http\Controllers\Panel\ReviewsController::class, 'sendInvitation'])->name('sendInvitation');
        Route::post('/invitations-sent/ajax-list', [App\Http\Controllers\Panel\ReviewsController::class, 'invitationsAjaxList'])->name('invitationsAjaxList');

        Route::get('/review-invitations', [App\Http\Controllers\Panel\ReviewsController::class, 'reviewsRequests'])->name('review-invitations.list');
        Route::post('/review-invitations/ajax-list', [App\Http\Controllers\Panel\ReviewsController::class, 'reviewsRequestAjaxList'])->name('reviewsRequestAjaxList');

              Route::group(array('prefix' => 'review-received','as'=>'review-received.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\ReviewsController::class, 'getReviews'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\ReviewsController::class, 'getReviewsAjax'])->name('getReviewsAjax');
        Route::get('/edit-review/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'editReview'])->name('editReview');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'deleteReviews'])->name('deleteReviews');
        Route::get('/approve/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'approveReviews'])->name('approveReviews');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'updateReview'])->middleware('check.ownership:' . Reviews::class . ',id')->name('updateReview');
        Route::post('/send-review-reply/{unique_id}', [App\Http\Controllers\Panel\ReviewsController::class, 'saveReply'])->name('saveReply')->middleware('input_sanitization');
        Route::get('/delete-review-reply/{unique_id}', [App\Http\Controllers\Panel\ReviewsController::class, 'replyDelete'])->name('replyDelete');
        Route::post('/update-review-reply/{unique_id}', [App\Http\Controllers\Panel\ReviewsController::class, 'updateReply'])->name('updateReply')->middleware('input_sanitization');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\ReviewsController::class, 'deleteMultipleReview'])->name('deleteMultipleReview');
        
        Route::get('/report-spam-review/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'checkReview']);
        Route::post('/update-spam-review/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'spamSubmitReview']);

         });


           Route::group(array('prefix' => 'spam-reviews','as'=>'spam-reviews.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\ReviewsController::class, 'getSpamReviews'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\ReviewsController::class, 'getSpamReviewsAjax'])->name('getReviewsAjax');
        Route::get('/edit-review/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'editReview'])->name('editReview');
        Route::get('/delete/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'deleteReviews'])->name('deleteReviews');
        Route::get('/approve/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'approveReviews'])->name('approveReviews');
        Route::post('/update/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'updateReview'])->middleware('check.ownership:' . Reviews::class . ',id')->name('updateReview');
        Route::post('/send-review-reply/{unique_id}', [App\Http\Controllers\Panel\ReviewsController::class, 'saveReply'])->name('saveReply')->middleware('input_sanitization');
        Route::get('/delete-review-reply/{unique_id}', [App\Http\Controllers\Panel\ReviewsController::class, 'replyDelete'])->name('replyDelete');
        Route::post('/update-review-reply/{unique_id}', [App\Http\Controllers\Panel\ReviewsController::class, 'updateReply'])->name('updateReply')->middleware('input_sanitization');
        Route::post('/delete-multiple', [App\Http\Controllers\Panel\ReviewsController::class, 'deleteMultipleReview'])->name('deleteMultipleReview');
        
        Route::get('/report-spam-review/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'checkReview']);
        Route::post('/update-spam-review/{id}', [App\Http\Controllers\Panel\ReviewsController::class, 'spamSubmitReview']);

         });

    });

       Route::group(array('prefix' => 'manage-services', 'as' => 'manage-services.'), function () {
        
        Route::get('/get-all-services', [App\Http\Controllers\Panel\ManageServicesController::class, 'getAllServices'])->name('list');
        Route::post('/save-my-service', [App\Http\Controllers\Panel\ManageServicesController::class, 'saveMyService'])->name('saveMyService');

        Route::get('/remove-sub-service-type/{id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'removeSubServiceType']);
        Route::get('/remove-sub-service/{id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'removeSubService']);

        Route::get('/', [App\Http\Controllers\Panel\ManageServicesController::class, 'getSelectedServices'])->name('list');
        Route::get('/add-service/{main_service_id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'getAllSubServices']);
        Route::post('/save-services', [App\Http\Controllers\Panel\ManageServicesController::class, 'saveService']);
        Route::get('/get-subservice-type', [App\Http\Controllers\Panel\ManageServicesController::class, 'getSubServiceType'])->name('get-subservice-type');
        Route::post('/add-service-types', [App\Http\Controllers\Panel\ManageServicesController::class, 'addServiceType'])->name('add-service-types');

        Route::get('/add-servicetype-detail/{id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'addServiceTypeDetail'])->name('add-servicetype-detail');

        Route::post('/update-sub-service-types/{id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'updateSubServiceType'])->name('update-sub-service-types');

        Route::get('/generate-assessments/{id}', [App\Http\Controllers\Panel\ServiceAssesmentFormController::class, 'generateAssessment'])->name('generateAssessment');
        
        Route::post('/generate-assessment/{id}', [App\Http\Controllers\Panel\ServiceAssesmentFormController::class, 'submitGenerateAssessment'])->name('submitGenerateAssessment')->middleware('input_sanitization');

        Route::post('/generate-assessment-form-save/{id}', [App\Http\Controllers\Panel\ServiceAssesmentFormController::class, 'saveGenerateAssessment'])->name('saveGenerateAssessment')->middleware('input_sanitization');

         Route::get('/list-assessment/{id}', [App\Http\Controllers\Panel\ServiceAssesmentFormController::class, 'listAssessment'])->name('listAssessment');
        Route::post('/assesment-ajax-list', [App\Http\Controllers\Panel\ServiceAssesmentFormController::class, 'assessmentAjaxList'])->name('assessmentAjaxList');
        Route::get('/view-assesment/{id}', [App\Http\Controllers\Panel\ServiceAssesmentFormController::class, 'viewAssessment'])->middleware('check.ownership:' . Forms::class . ',id')->name('viewAssesment');


        Route::post('/pin-my-service', [App\Http\Controllers\Panel\ManageServicesController::class, 'pinMyService'])->name('pinMyService');

        Route::post('/pinned-services-ajax', [App\Http\Controllers\Panel\ManageServicesController::class, 'pinnedServicesAjax'])->name('pinned-services-ajax');

        // add new flow
         Route::get('add-pathway', [App\Http\Controllers\Panel\ManageServicesController::class, 'addPathway'])->name('add-pathway');
        Route::get('add-pathway/{id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'addPathway'])->name('add-pathway');
        Route::get('fetch-pathways', [App\Http\Controllers\Panel\ManageServicesController::class, 'fetchPathways'])->name('fetch-pathways');
        Route::get('fetch-sub-pathways/{id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'fetchSubPathways'])->name('fetch-sub-pathways');
        Route::post('save-pathways', [App\Http\Controllers\Panel\ManageServicesController::class, 'savePathways'])->name('save-pathways');
        Route::get('add-subtype-pathways/{id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'addSubTypePathways'])->name('add-subtype-pathways');

        Route::post('save-subtype-pathways', [App\Http\Controllers\Panel\ManageServicesController::class, 'saveSubTypePathways'])->name('save-subtype-pathways');
        Route::post('remove-configuration/{id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'removeConfiguration'])->middleware('check.ownership:' . ProfessionalSubServices::class . ',id')->name('remove-configuration');
        
        Route::get('display-sub-pathway', [App\Http\Controllers\Panel\ManageServicesController::class, 'displaySubPathway'])->name('display-sub-pathway');

        // New routes for editing individual configurations
        Route::get('edit-configuration/{id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'editConfiguration'])->name('edit-configuration');
        Route::post('update-configuration/{id}', [App\Http\Controllers\Panel\ManageServicesController::class, 'updateConfiguration'])->middleware('check.ownership:' . ProfessionalSubServices::class . ',id')->name('update-configuration');
    });


     Route::group(array('prefix' => 'user-plan-feature','as'=>'user-plan-feature.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\UserPlanFeatureController::class, 'index'])->name('lists');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\UserPlanFeatureController::class, 'getAjaxList'])->name('ajax-list');
    });

      
  Route::group(array('prefix' => 'associates', 'as' => 'associates.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\AssociatesController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\AssociatesController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/view-join-request/{id}', [App\Http\Controllers\Panel\AssociatesController::class, 'viewJoinRequest'])->name('view-join-request');
        Route::get('/accept-proposal/{id}', [App\Http\Controllers\Panel\AssociatesController::class, 'acceptProposal'])->name('accept-proposal');
        Route::get('/reject-proposal/{id}', [App\Http\Controllers\Panel\AssociatesController::class, 'rejectProposal'])->name('reject-proposal');
    });

   // Agreement routes
Route::group(array('prefix' => 'agreement', 'as' => 'agreement.'), function () {
Route::get('/{associate_id}', [App\Http\Controllers\Panel\CaseAgreementController::class, 'create'])->name('create');
Route::post('/{associate_id}', [App\Http\Controllers\Panel\CaseAgreementController::class, 'store'])->name('store');
Route::get('/view/{agreement_id}', [App\Http\Controllers\Panel\CaseAgreementController::class, 'view'])->name('view');
  Route::get('/download-pdf/{id}', [App\Http\Controllers\Panel\CaseAgreementController::class, 'downloadPdf'])->name('download-pdf');
});

// Agreement Comment routes
    Route::group(array('prefix' => 'agreement-comments', 'as' => 'agreement-comments.'), function () {
        Route::post('/store', [App\Http\Controllers\Panel\AgreementCommentController::class, 'store'])->name('store');
        Route::put('/update/{id}', [App\Http\Controllers\Panel\AgreementCommentController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [App\Http\Controllers\Panel\AgreementCommentController::class, 'destroy'])->name('delete');
        Route::get('/{agreement_id}', [App\Http\Controllers\Panel\AgreementCommentController::class, 'getComments'])->name('get');
        Route::get('/comment/{id}', [App\Http\Controllers\Panel\AgreementCommentController::class, 'getComment'])->name('getComment');
        Route::get('/view/{agreement_id}', [App\Http\Controllers\Panel\AgreementCommentController::class, 'getCommentsView'])->name('getView');
    });

    Route::group(array('prefix' => 'case-join-requests', 'as' => 'case-join-requests.'), function () {
        Route::get('/', [App\Http\Controllers\Panel\CaseJoinRequestController::class, 'index'])->name('list');
        Route::post('/ajax-list', [App\Http\Controllers\Panel\CaseJoinRequestController::class, 'getAjaxList'])->name('ajax-list');
        Route::get('/view/{id}', [App\Http\Controllers\Panel\CaseJoinRequestController::class, 'view'])->name('view');

        Route::post('/accept/{id}', [App\Http\Controllers\Panel\CaseJoinRequestController::class, 'accept'])->name('accept');
        Route::get('/reject/{id}', [App\Http\Controllers\Panel\CaseJoinRequestController::class, 'reject'])->name('reject');
        Route::get('/accept-modal/{id}', [App\Http\Controllers\Panel\CaseJoinRequestController::class, 'acceptModal'])->name('accept-modal');
    });
});