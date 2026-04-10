<?php

use Illuminate\Support\Facades\Route;

// Profile Routes
Route::group([
    'prefix' => 'profile',
    'as' => 'profile.',
    'middleware' => ['auth', 'check_profile', 'role_check', 'check_access_permission']
], function () {
    
    Route::get('/edit', [App\Http\Controllers\Panel\ProfileController::class, 'edit'])->name('edit');
    Route::post('/update', [App\Http\Controllers\Panel\ProfileController::class, 'update'])->name('update');
    Route::get('/change-password/{id}', [App\Http\Controllers\Panel\ProfileController::class, 'changePassword'])->name('changePassword');
    Route::post('/update-password/{id}', [App\Http\Controllers\Panel\ProfileController::class, 'updatePassword'])->name('updatePassword');
    Route::get('/show/{page?}', [App\Http\Controllers\Panel\ProfileController::class, 'show'])->name('show');
    Route::get('/my-profile', [App\Http\Controllers\Panel\ProfileController::class, 'myProfile'])->name('myProfile');
    
    // Image cropping routes
    Route::get('/crop-user-image', [App\Http\Controllers\Panel\DashboardController::class, 'imageCropper']);
    Route::post('/upload-user-cropped-image', [App\Http\Controllers\Panel\DashboardController::class, 'saveCroppedImage']);
    Route::get('/crop-banner-image', [App\Http\Controllers\Panel\DashboardController::class, 'bannerCropper']);
    Route::post('/upload-banner-cropped-image', [App\Http\Controllers\Panel\DashboardController::class, 'saveBannerImage']);
    
    // Professional profile routes
    Route::post('/professional-submit-profile', [App\Http\Controllers\Panel\DashboardController::class, 'updateProfessionalProfile'])->name('updateProfessionalProfile')->middleware('input_sanitization');
    Route::post('/edit-professionals/{uniqueid}/', [App\Http\Controllers\Panel\DashboardController::class, 'updateProfessional'])->name('updateProfessional');
    

    Route::get('/professional/download-file', [App\Http\Controllers\Panel\DashboardController::class, 'downloadFile'])->name('downloadFile');
    Route::get('/professional/remove-file/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'removeFile'])->name('removeFile');
    
    // Domain verification routes
    Route::post('/domain-verify', [App\Http\Controllers\Panel\SettingsController::class, 'domainVerify'])->name('domain-verify');
    Route::post('/verify-domain-txt', [App\Http\Controllers\Panel\SettingsController::class, 'verifyDomainTxt'])->name('verifyDomainTxt');
    Route::get('/remove-domain', [App\Http\Controllers\Panel\SettingsController::class, 'removeDomain'])->name('removeDomain');
    
    // QR Code generation
    Route::get('/generate-qr-code/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'generateQrCode'])->name('generateQrCode');
    Route::get('/remove-professional-domain/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'removeProfessionalDomain'])->name('removeProfessionalDomain');
    Route::post('/verify-professional-domain-dns', [App\Http\Controllers\Panel\DashboardController::class, 'verifyProfessionalDomainTxt'])->name('verifyProfessionalDomainTxt');
    Route::post('/verify-professional-domain-file', [App\Http\Controllers\Panel\DashboardController::class, 'verifyProfessionalDomainFile'])->name('verifyProfessionalDomainFile');
    Route::get('/download-domain-verify-file', [App\Http\Controllers\Panel\DashboardController::class, 'generateTxt'])->name('generateTxt');
    
    // Company address routes
    Route::get('/professional/add-company-address/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'addCompanyAddress']);
    Route::get('/professional/add-personal-address/{id}', [App\Http\Controllers\Panel\DashboardController::class, 'addPersonalAddress']);
    Route::post('/save-address-from-signup', [App\Http\Controllers\Panel\CompanyLocationController::class, 'saveAddressFromSignup'])->name('save-address-from-signup')->middleware('input_sanitization');
    Route::get('/more-company-address', [App\Http\Controllers\Panel\DashboardController::class, 'moreCompanyAddress']);
    
    // Document upload
    Route::post('/upload-professional-document', [App\Http\Controllers\Panel\DashboardController::class, 'uploadProfessionalDocument']);
}); 