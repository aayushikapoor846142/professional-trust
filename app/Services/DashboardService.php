<?php

namespace App\Services;

use App\Models\PointEarn;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Country;
use App\Models\Languages;
use App\Models\Professional;
use App\Models\OtherProfessionalDetail;
use App\Models\CdsProfessionalLicense;
use App\Models\CdsRegulatoryBody;
use App\Models\CdsRegulatoryCountry;
use App\Models\CdsProfessionalDocuments;
use App\Models\DomainVerify;
use App\Models\Types;
use App\Models\UserDetails;
use App\Models\CompanyLocations;
use App\Models\ProfessionalServices;
use App\Models\UserLoginActivity;
use App\Models\UserLocationAccessibility;
use App\Models\CaseWithProfessionals;
use App\Models\ChatMessage;
use App\Models\UserBankingDetails;

class DashboardService
{
    public function getDashboardData($userId)
    {
        $viewData = [];
        $viewData['pageTitle'] = "Points Earn";
        
        // Badge HTML generation
        $badge_html = '';
        if (supportBadge(pointEarns($userId)) != '') {
            $pdfData['user'] = User::find($userId);
            $pdfData['showDownload'] = true;
            $pdfData['badge'] = supportBadge(pointEarns($userId), 'data');
            $view = view("components.badge-pdf", $pdfData);
            $badge_html = $view->render();
        }
        $viewData['badge_html'] = $badge_html;

        // Point earns data
        $point_earns = PointEarn::where('user_id', $userId)
            ->orderBy('id', "desc")
            ->limit(3)
            ->get();
        $viewData['point_earns'] = $point_earns;

        // Invoices data
        $invoices = Invoice::where('user_id', $userId)
            ->where("invoice_type", "support")
            ->orderBy('id', "desc")
            ->limit(3)
            ->get();
        $viewData['invoices'] = $invoices;
        // --- ENHANCED DASHBOARD DATA ---
        // Cases
        $viewData['totalCases'] = CaseWithProfessionals::where('professional_id', $userId)->count();
        $viewData['openCases'] = CaseWithProfessionals::where('professional_id', $userId)->where('status', 'open')->count();
        $viewData['closedCases'] = CaseWithProfessionals::where('professional_id', $userId)->where('status', 'closed')->count();
        $viewData['recentCases'] = CaseWithProfessionals::where('professional_id', $userId)->orderBy('updated_at', 'desc')->limit(5)->get();
        // Appointments
        if (class_exists('App\\Models\\AppointmentBooking')) {
            $viewData['upcomingAppointments'] = \App\Models\AppointmentBooking::where('added_by', $userId)
                ->where('appointment_date', '>=', now())
                ->orderBy('appointment_date', 'asc')
                ->limit(5)
                ->get();
        } else {
            $viewData['upcomingAppointments'] = collect();
        }
        // Messages (Chats)
        if (class_exists('App\\Models\\ChatMessage')) {
            $viewData['unreadMessages'] =ChatMessage::where('sent_by', '!=', $userId)
                ->whereDoesntHave('chatMessageRead', function($q) use ($userId) {
                    $q->where('receiver_id', $userId)->where('status', 'unread');
                })->count();
            $viewData['recentMessages'] = ChatMessage::where('sent_by', $userId)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            $viewData['unreadMessages'] = 0;
            $viewData['recentMessages'] = collect();
        }
        // Invoices
        $viewData['pendingInvoices'] = \App\Models\Invoice::where('user_id', $userId)->count();
        $viewData['paidInvoices'] = \App\Models\Invoice::where('user_id', $userId)->count();
        $viewData['recentInvoices'] = \App\Models\Invoice::where('user_id', $userId)->orderBy('created_at', 'desc')->limit(5)->get();
        // Earnings (this month)
        if (\Schema::hasTable('invoices')) {
            $viewData['earningsThisMonth'] = \App\Models\Invoice::where('user_id', $userId)
                // ->where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount');
        } else {
            $viewData['earningsThisMonth'] = 0;
        }
        // Points (total)
        $viewData['pointsEarned'] = \App\Models\PointEarn::where('user_id', $userId)->sum('points');
        // Staff (active)
        if (class_exists('App\\Models\\User')) {
            $viewData['activeStaff'] = \App\Models\User::where('role', 'staff')->where('status', 'active')->count();
        } else {
            $viewData['activeStaff'] = 0;
        }
        // Membership (status)
        if (\Schema::hasTable('memberships')) {
            $membership = \DB::table('memberships')->where('user_id', $userId)->latest()->first();
            $viewData['membershipStatus'] = $membership->status ?? 'N/A';
        } else {
            $viewData['membershipStatus'] = 'N/A';
        }
        // Reviews (latest, average)
        if (class_exists('App\\Models\\Reviews')) {
            $viewData['latestReviews'] = \App\Models\Reviews::where('professional_id', $userId)->orderBy('created_at', 'desc')->limit(5)->get();
            $viewData['averageRating'] = \App\Models\Reviews::where('professional_id', $userId)->avg('rating');
        } else {
            $viewData['latestReviews'] = collect();
            $viewData['averageRating'] = null;
        }
        // Analytics for charts (earnings, appointments, reviews trends)
        // Earnings trend (last 6 months)
        $viewData['earningsTrend'] = \App\Models\Invoice::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
            ->where('user_id', $userId)
            // ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');
        // Appointments trend (last 6 months)
        if (class_exists('App\\Models\\AppointmentBooking')) {
            $viewData['appointmentsTrend'] = \App\Models\AppointmentBooking::selectRaw('MONTH(appointment_date) as month, COUNT(*) as total')
                ->where('added_by', $userId)
                ->where('appointment_date', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');
        } else {
            $viewData['appointmentsTrend'] = collect();
        }
        // Reviews trend (last 6 months)
        if (class_exists('App\\Models\\Reviews')) {
            $viewData['reviewsTrend'] = \App\Models\Reviews::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->where('professional_id', $userId)
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');
        } else {
            $viewData['reviewsTrend'] = collect();
        }
        return $viewData;
    }

    public function getEditProfileData($userId)
    {
        $viewData = [];
        $viewData['pageTitle'] = "Edit Profile";
        
        $countries = Country::all();
        $viewData['countries'] = $countries;

        $user = User::where("id", $userId)->first();
        $viewData['user'] = $user;

        if ($user->role === 'professional') {
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

            $viewData['professionalList'] = $professionalList;
            $viewData['regulatory_bodies'] = $regulatory_bodies;
            $viewData['regulatory_countries'] = CdsRegulatoryCountry::get();

            if ($user->cdsCompanyDetail) {
                $viewData['document'] = CdsProfessionalDocuments::where('company_id', $user->cdsCompanyDetail->id)->get();
            } else {
                $viewData['document'] = collect();
            }

            $viewData['license_detail'] = $license_detail;
            $viewData['types'] = Types::all();
            $viewData['user_details'] = $user_details;
        }

        $viewData['languages'] = Languages::all();

        // Add banking details
        $viewData['bankingDetails'] = $user->bankingDetails()->orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->get();
        $viewData['activeBankingDetail'] = $user->activeBankingDetail;

        if ($user->role == "professional") {
            $viewData['domain_data'] = DomainVerify::where("user_id", $userId)->first();
        }

        return $viewData;
    }

    public function getProfileData($userId, $page = '')
    {
        $viewData = [];
        $viewData['pageTitle'] = "My Profile";
        
        $countries = Country::all();
        $viewData['countries'] = $countries;
        
        $user = User::where("id", $userId)->first();
        $users = User::with('companyLocation')->get();
        $viewData['user'] = $user;

        if ($user->role === 'professional') {
            $personal_address = CompanyLocations::where(function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('id', "desc")
            ->where('type_label', 'personal')
            ->count();

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

            $license_detail = CdsProfessionalLicense::where('added_by', $user->id)->latest()->first();
            $countryIds = [];

            if (!empty($license_detail->country_of_practice)) {
                $string = trim($license_detail->country_of_practice);
                $countryIds = array_map('intval', explode(',', $string));
            }

            if ($license_detail) {
                $license_detail->country_ids = $countryIds ?? '';
                $countryNames = Country::whereIn('id', $countryIds)->pluck('name')->toArray();
                $license_detail->country_names = implode(', ', $countryNames);
            }

            if (!empty($license_detail)) {
                $regulatory_bodies = CdsRegulatoryBody::where('regulatory_country_id', $license_detail->regulatory_country_id)->get();
            } else {
                $regulatory_bodies = CdsRegulatoryBody::get();
            }

            $user_details = UserDetails::where('user_id', $userId)->first();

            $company_address = CompanyLocations::where(function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('id', "desc")
            ->where('type_label', 'company')
            ->get();

            $viewData['license_detail'] = $license_detail;
            $viewData['professionalList'] = $professionalList;
            $viewData['regulatory_bodies'] = $regulatory_bodies;
            $viewData['regulatory_countries'] = CdsRegulatoryCountry::get();

            if ($user->cdsCompanyDetail) {
                $viewData['document'] = CdsProfessionalDocuments::where('company_id', $user->cdsCompanyDetail->id)->get();
            } else {
                $viewData['document'] = collect();
            }

            $viewData['types'] = Types::all();
            $viewData['languages'] = Languages::all();
            $viewData['user_details'] = $user_details;
            $viewData['company_address'] = $company_address;
            $viewData['my_services'] = ProfessionalServices::with(['ImmigrationServices'])->where('user_id', $userId)->get();
        }

        if ($page != '') {
            $viewData['template'] = $page;
        } else {
            $viewData['template'] = 'profile';
        }

        $last_saved = '';
        if (isset($user_details) && $user_details->additional_detail_form != '') {
            $last_saved = trim($user_details->additional_detail_value);
        }
        $viewData['last_saved'] = $last_saved;
        $viewData['showSidebar'] = true;
        $viewData['personal_address'] = $personal_address ?? '';
        $viewData['domain_data'] = DomainVerify::where("user_id", $userId)->first();

        // Add banking details for banking-details template
        if ($page == 'banking-details') {
            $viewData['bankingDetails'] = $user->bankingDetails()->orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->get();
            $viewData['activeBankingDetail'] = $user->activeBankingDetail;
        }

        if ($page == 'confirm-login') {
            $id = $user->unique_id;
            $user = User::where('unique_id', $id)->first();

            $latestLogin = UserLoginActivity::where('user_id', $user->id)
                ->latest()
                ->first();

            $userLocationAccess = UserLocationAccessibility::where('user_id', $user->id)
                ->latest()
                ->get();

            if (!$latestLogin) {
                return redirect()->back()->with('error', 'No login activity found.');
            }

            $loginActivities = UserLoginActivity::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('ip_address');

            $viewData['userLocationAccess'] = $userLocationAccess;
            $viewData['loginActivities'] = $loginActivities;
            $viewData['user'] = $user;
            $viewData['latestLogin'] = $latestLogin;
            $viewData['template'] = "confirm-login";
        }

        return $viewData;
    }

    public function getMyProfileData($userId)
    {
        $viewData = [];
        $viewData['pageTitle'] = "My Profile";
        
        $countries = Country::all();
        $viewData['countries'] = $countries;
        
        $user = User::where("id", $userId)->first();
        $users = User::with('companyLocation')->get();
        $viewData['user'] = $user;

        if ($user->role === 'professional') {
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

            $license_detail = CdsProfessionalLicense::where('added_by', $user->id)->latest()->first();

            if (!empty($license_detail)) {
                $regulatory_bodies = CdsRegulatoryBody::where('regulatory_country_id', $license_detail->regulatory_country_id)->get();
            } else {
                $regulatory_bodies = CdsRegulatoryBody::get();
            }

            $user_details = UserDetails::where('user_id', $userId)->first();
            
            $viewData['professionalList'] = $professionalList;
            $viewData['regulatory_bodies'] = $regulatory_bodies;
            $viewData['regulatory_countries'] = CdsRegulatoryCountry::get();
            
            if ($user->cdsCompanyDetail) {
                $viewData['document'] = CdsProfessionalDocuments::where('company_id', $user->cdsCompanyDetail->id)->get();
            } else {
                $viewData['document'] = collect();
            }
            
            $viewData['license_detail'] = $license_detail;
            $viewData['types'] = Types::all();
            $viewData['user_details'] = $user_details;
        }

        return $viewData;
    }
} 