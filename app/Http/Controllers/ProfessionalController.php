<?php

namespace App\Http\Controllers;

use App\Models\Cities;
use App\Models\KnowledgeBase;
use App\Models\OtpVerify;
use App\Models\SiteSettings;
use App\Models\QuickTipOffs;
use App\Models\SocialGroupUap;
use App\Models\States;
use App\Models\TempUser;
use App\Models\UapExcel;
use Illuminate\Http\Request;
use App\Models\Professional;
use App\Models\UapEmail;
use App\Models\Country;
use App\Models\UapProfessionals;
use App\Models\User;
use App\Models\ClaimProfile;
use App\Models\UnauthorisedProfessional;
use App\Models\IndividualUaps;
use App\Models\CorporateUap;
use App\Models\SocialMediaUap;
use App\Models\ReportProfile;
use App\Models\ReportSubject;
use App\Models\UapReconsider;
use App\Models\FaqCategory;
use App\Models\Faq;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use GuzzleHttp\Client;
use App\Rules\PasswordValidation;
use View;
use DB;
use DateTime;
use DateInterval;
use Carbon\Carbon;
use App\Models\ReportSocialMediaContent;
use App\Models\ReportSocialMediaGroup;
use App\Models\SocialMediaReport;
use App\Models\SeoDetails;
use Illuminate\Support\Facades\Route;
use App\Models\Reviews;
use App\Models\CategoryLevels;
use App\Models\Level;
use App\Models\FeedbackTag;
use App\Models\UserDetails;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfessionalController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function professionalSearch()
    {
        $viewData['page_title'] = "Professional search";
        return view('professionals.search', $viewData);
    }

    public function searchProfessionalProfile(Request $request)
    {
        $search =  $request->term;
        
        if(strlen($search) > 3){
            
           return $professionals = Professional::where(function($query) use ($search) {
                        if ($search != '') {
                            $query->where('college_id', 'LIKE', "%{$search}%")
                                    ->orWhere("name", "LIKE", "%$search%")
                                    ->orWhere("company", "LIKE", "%$search%");
                        }
                    })
                ->orderBy('id', 'asc')->limit(50)
                ->get()->toArray();
        }
       
    }

    public function searchProfessionalDetail($unique_id)
    {
        $claim_profile = "";
        
        $record = Professional::with(['professionalAboutDetail', 'professionalWebsiteDetail', 'professionalAddressDetail', 'reviews'])->where('unique_id', $unique_id)->first();
       
        if (auth()->user()) {
            $claim_profile = ClaimProfile::where('added_by', \Auth::user()->id)
            ->where('professional_id', $record->id)
            ->first();
        }
      
     
        $viewData['record'] = $record;
        $viewData['claim_profile'] = $claim_profile;
       

        $relatedCompany = Professional::with(['professionalAboutDetail', 'professionalWebsiteDetail', 'professionalAddressDetail'])->where('unique_id', '!=', $unique_id)->where('employment_city', $record->employment_city);

        if ($record->employment_startdate != '') {
            $originalDate = $record->employment_startdate;

            // Create a DateTime object from the original date
            $date = DateTime::createFromFormat('d/m/Y', $originalDate);

            // Convert the date to Y-m-d format
            if ($date) {
                // Convert the date to Y-m-d format if valid
                $formattedDate = $date->format('Y-m-d');
            } else {
                // Set a default value if the date format is invalid
                $formattedDate = ''; // or any other default value you prefer
            }

            $date1 = new DateTime($formattedDate);
            $date3 = new DateTime($formattedDate);
            $date2 = new DateTime();

            $diff = $date1->diff($date2);
            $exp_years = $diff->y;

            $prev_exp = $exp_years - 2;
            $next_exp = $exp_years + 2;


            // Get the new date in Y-m-d format
            $prev_exp_date = $date1->modify('-' . $prev_exp . ' years')->format('d/m/Y');
            $next_exp_date = $date3->modify('+' . $next_exp . ' years')->format('d/m/Y');

            $relatedCompany->whereBetween('employment_startdate', [$prev_exp_date, $next_exp_date]);
        }

        $relatedCompany = $relatedCompany->limit(4)->get();


        $viewData['relatedCompany'] = $relatedCompany;

        return view('professionals.search-details', $viewData);
    }

}
