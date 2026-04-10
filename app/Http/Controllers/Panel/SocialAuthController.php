<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider for linking or login
     */
    public function redirectToProvider($provider)
    {
        // Check if user is authenticated (for linking) or not (for login)
        if (Auth::check()) {
            $user = Auth::user();
            // Check if user already has a social account linked
            if ($user->social_connect == 1 && !empty($user->provider)) {
                return response()->json([
                    'status' => false,
                    'message' => 'You already have a social account linked. Please unlink it first.'
                ]);
            }

            // Store the linking intent in session
            session(['social_linking' => true]);
            session(['linking_provider' => $provider]);
        } else {
            // Store login intent in session
            session(['social_login' => true]);
            session(['login_provider' => $provider]);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle social provider callback for linking or login
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Check if this is a linking request
            if (session('social_linking')) {
                return $this->handleLinking($provider, $socialUser);
            }
            
            // Check if this is a login request
            if (session('social_login')) {
                return $this->handleLogin($provider, $socialUser);
            }
            
            // Default fallback
            return $this->handleLogin($provider, $socialUser);
            
        } catch (\Exception $e) {
            if (Auth::check()) {
                return redirect()->route('profile.myProfile')->with('error', 'Failed to link social account: ' . $e->getMessage());
            } else {
                return redirect()->route('login')->with('error', 'Failed to login with social account: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle social account linking
     */
    protected function handleLinking($provider, $socialUser)
    {
        $user = Auth::user();
        
        // Check if another user already has this social account linked
        $existingUser = User::where('provider_id', $socialUser->getId())
                           ->where('provider', $provider)
                           ->where('id', '!=', $user->id)
                           ->first();
        
        if ($existingUser) {
            return redirect()->route('profile.myProfile')->with('error', 'This social account is already linked to another user.');
        }

        // Update user with social account information
        $user->update([
            'social_connect' => 1,
            'provider_id' => $socialUser->getId(),
            'provider' => $provider,
        ]);

        // Clear session
        session()->forget(['social_linking', 'linking_provider']);

        return redirect()->route('profile.myProfile')->with('success', ucfirst($provider) . ' account linked successfully!');
    }

    /**
     * Handle social login
     */
    protected function handleLogin($provider, $socialUser)
    {
        // Find user by social provider ID
        $user = User::where('provider_id', $socialUser->getId())
                   ->where('provider', $provider)
                   ->first();

        if ($user) {
            // User exists, log them in
            Auth::login($user);
            
            // Clear session
            session()->forget(['social_login', 'login_provider']);
            
            // Redirect to dashboard
            return redirect()->route('panel.list');
        } else {
            // User doesn't exist, redirect to registration or show error
            session()->forget(['social_login', 'login_provider']);
            return redirect()->route('login')->with('error', 'No account found with this ' . ucfirst($provider) . ' account. Please register first.');
        }
    }

    /**
     * Unlink social account
     */
    public function unlinkSocialAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|in:google,facebook,linkedin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid provider specified.'
            ]);
        }

        $user = Auth::user();
        $provider = $request->provider;

        // Check if user has this provider linked
        if ($user->social_connect != 1 || $user->provider !== $provider) {
            return response()->json([
                'status' => false,
                'message' => 'No ' . ucfirst($provider) . ' account is currently linked.'
            ]);
        }

        // Unlink the social account
        $user->update([
            'social_connect' => 0,
            'provider_id' => null,
            'provider' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => ucfirst($provider) . ' account unlinked successfully!'
        ]);
    }

    /**
     * Get social account status
     */
    public function getSocialAccountStatus()
    {
        $user = Auth::user();
        
        return response()->json([
            'status' => true,
            'data' => [
                'is_linked' => $user->social_connect == 1,
                'provider' => $user->provider,
                'provider_id' => $user->provider_id
            ]
        ]);
    }
} 