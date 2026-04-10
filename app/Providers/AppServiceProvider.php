<?php

namespace App\Providers;

use App\Helper\FormHelper;
use App\Rules\InputStringValidation;
use App\Rules\SiteUrlValidation;
use App\Rules\StringLimitRule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\MarketingSearchCampaign;
use Illuminate\Support\Facades\Validator;
use App\Rules\InputSanitizeValidation;
use App\Rules\PasswordValidation;
use App\Rules\ValidEmail;
use App\Models\SiteSettings;
use App\Rules\PhoneNumberValidation;
use Illuminate\Support\Facades\URL;
use Auth;
use Carbon\Carbon;
use App\Models\User;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
               
        Paginator::useBootstrap();
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
        
        $data = configData();
        if ($data) {
            config([
                'database.connections.mysql.host' => decrypt($data['v']),
                'database.connections.mysql.port' => decrypt($data['w']),
                'database.connections.mysql.database' => decrypt($data['x']),
                'database.connections.mysql.username' => decrypt($data['y']),
                'database.connections.mysql.password' => decrypt($data['z']),
            ]);
        }
         
       
        $this->app->singleton('formhelper', function () {
            return new FormHelper();
        });

      
                    
        // if(!\Session::get('user_current_location')){
        //     \Session::put('user_current_location',detectUserLocation());
        // }
        // Capture URL parameters
        $url = request()->fullUrl();
        $utm_source = request()->get('utm_source');
        $utm_medium = request()->get('utm_medium');
        $utm_campaign = request()->get('utm_campaign');
        $utm_terms = request()->get('utm_term');
       

        $uid = randomNumber();
        // Store in marketing_search_campaigns table if utm parameters are present
        if ($utm_source || $utm_medium || $utm_campaign || $utm_terms) {
            MarketingSearchCampaign::create([
                'unique_id' => $uid,
                'url' => $url,
                'utm_source' => $utm_source,
                'utm_medium' => $utm_medium,
                'utm_campaign' => $utm_campaign,
                'utm_terms' => $utm_terms,
                'marketing_search_terms' => json_encode(request()->all())
            ]);
        }
        
        // PUSHER API KEY

        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher.key' => apiKeys("PUSHER_APP_KEY"),
            'broadcasting.connections.pusher.secret' => apiKeys("PUSHER_APP_SECRET"),
            'broadcasting.connections.pusher.app_id' => apiKeys("PUSHER_APP_ID"),
            'broadcasting.connections.pusher.options.cluster' => apiKeys("PUSHER_APP_CLUSTER"),
            
            'broadcasting.apps.0.id' => apiKeys("PUSHER_APP_ID"),
            'websockets.apps.0.key' => apiKeys("PUSHER_APP_KEY"),
            'broadcasting.apps.0.secret' => apiKeys("PUSHER_APP_SECRET"),
        ]);
        if(\Str::contains(request()->getHost(), 'trustvisory.com')){
            config([
                'broadcasting.connections.pusher.options.0.scheme' => 'https',
            ]);
        }else{
            config([
                'broadcasting.connections.pusher.options.0.scheme' => 'http',
            ]);
        }


        config([
            'services.google.client_id' => apiKeys('GOOGLE_CLIENT_ID'),
            'services.google.client_secret' => apiKeys('GOOGLE_CLIENT_SECRET'),
            'services.google.redirect' => apiKeys('GOOGLE_REDIRECT_URL'),
        ]);
        config([
            'services.linkedin.client_id' => apiKeys('LINKEDIN_CLIENT_ID'),
            'services.linkedin.client_secret' => apiKeys('LINKEDIN_CLIENT_SECRET'),
            'services.linkedin.redirect' => apiKeys('LINKEDIN_REDIRECT_URI'),
        ]);

        Validator::extend('input_sanitize', function ($attribute, $value, $parameters, $validator) {
            return (new InputSanitizeValidation())->passes($attribute, $value);
        });
        Validator::extend('password_validation', function ($attribute, $value, $parameters, $validator) {
            return (new PasswordValidation())->passes($attribute, $value);
        });
        Validator::extend('site_url_validation', function ($attribute, $value, $parameters, $validator) {
            return (new SiteUrlValidation())->passes($attribute, $value);
        });
        Validator::extend('input_string', function ($attribute, $value, $parameters, $validator) {
            return (new InputStringValidation())->passes($attribute, $value);
        });
        Validator::extend('valid_email', function ($attribute, $value, $parameters, $validator) {
            return (new ValidEmail())->passes($attribute, $value);
        });
        Validator::extend('string_limit', function ($attribute, $value, $parameters, $validator) {
            return (new StringLimitRule())->passes($attribute, $value);
        });

        Validator::extend('phone_validation', function ($attribute, $value, $parameters, $validator) {
            return (new PhoneNumberValidation())->passes($attribute, $value);
        });
        

        $seoData =  SiteSettings::first();
        \View::share('seoData', $seoData);
                
        // if(getUserTimeZone()){
        //     date_default_timezone_set(getUserTimeZone()); // Change this to your desired timezone
        // }   else{
        //     date_default_timezone_set('Asia/Kolkata');

        // }

    
    }



}
