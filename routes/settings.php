<?php

use Illuminate\Support\Facades\Route;

// Settings Routes
Route::group([
    'prefix' => 'settings',
    'as' => 'settings.',
    'middleware' => ['auth', 'check_profile', 'role_check', 'check_access_permission']
], function () {
    
    Route::get('/security', [App\Http\Controllers\Panel\SettingsController::class, 'security'])->name('security');
    Route::post('/sidebar/status', [App\Http\Controllers\Panel\SettingsController::class, 'sidebarStatus'])->name('sidebar.status');
    Route::get('/get-global-notification', [App\Http\Controllers\Panel\SettingsController::class, 'getGlobalNotification'])->name('getGlobalNotification');
    
    // Timezone settings
    Route::post('/save-timezone', [App\Http\Controllers\Panel\AppointmentBookingController::class, 'saveUserTimezone'])->name('saveTimezone');
    
    // Login confirmation routes
    Route::get('/confirm-save-login/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'confirmSaveLogin'])->name('confirmSaveLogin');
    
    // Professional services
    Route::post('/professional-services/save', [App\Http\Controllers\Panel\CompaniesController::class, 'professionalServicesSave'])->name('professionalServicesSave');
    
    // Search and services
    Route::get('/search-services', [App\Http\Controllers\Panel\CompaniesController::class, 'searchServices'])->name('searchServices');
    Route::post('/choose-services', [App\Http\Controllers\Panel\CompaniesController::class, 'linkServiceWithProfesional'])->name('chooseServices');
    
    // Companies AJAX
    Route::post('/companies-ajax', [App\Http\Controllers\Panel\DashboardController::class, 'getCompanies'])->name('companies');
}); 