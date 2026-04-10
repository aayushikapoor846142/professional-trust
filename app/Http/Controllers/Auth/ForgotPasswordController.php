<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use View;
use App\Rules\PasswordValidation;
use App\Models\User;
class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;


    /**
     * Handle the submission of the forgot password request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitForgotPassword(Request $request)
    {
        // Validate the email input
        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        // Remove any existing password reset tokens for the email
        \DB::table('password_resets')->where('email', $request->email)->delete();

        // Generate and insert a new password reset token
        $token = Str::random(64);
        \DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $user = User::where('email', $request->email)->where('role', 'professional')->first();
          if (!$user) {
        return back()->withInput()->with('error', 'We could not find a professional account with that email.');
    }
        $parameter = [
            'user_id' => $user->unique_id,
            'email' => $request->email,
        ];
        $response = checkUserSecurity($parameter);

        if (($response['status'] ?? 'failed') !== 'success') {
            return back()->withInput()->with('error', 'User data does not match.');
        }
        // Prepare and send the reset password email
        $mailData = ['token' => $token];
        $view = \View::make('emails.forgotpassword', $mailData);
        $message = $view->render();

        $parameter = [
            'to' => $request->email,
            'to_name' => $request->email,
            'message' => $message,
            'subject' => 'Reset Password Link',
            'view' => 'emails.forgotpassword',
            'data' => $mailData,
        ];

        sendMail($parameter);

        // Redirect with a success message
        return redirect()->route('password.request')->with('message', 'We have e-mailed your password reset link!');
    }


    /**
     * Show the password reset form.
     * 
     * This method returns the view for resetting the password. It passes the 
     * provided token and a page title to the view, which is used for rendering 
     * the password reset form.
     *
     * @param string $token The password reset token used for verifying the reset request.
     * @return \Illuminate\View\View The view for resetting the password, with the token and page title.
     */
    public function showResetPassword($token)
    {
        return view('auth.passwords.reset', ['token' => $token, 'pageTitle' => 'Reset Password']);
    }


    /**
     * Handle the password reset submission.
     * 
     * This method validates the incoming request for password reset, checks if the 
     * provided reset token is valid, and updates the user's password if everything 
     * is correct. It then deletes the reset token from the `password_resets` table 
     * and redirects the user to the login page with a success message.
     *
     * @param \Illuminate\Http\Request $request The request instance containing the email, password, and token.
     * @return \Illuminate\Http\RedirectResponse Redirects back to the login page with a success or error message.
     */
    public function submitResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|max:255|email|exists:users',
            'password' => 'required|string|password_validation|confirmed',
            'password_confirmation' => 'required|string|password_validation',
        ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'email' => $request->email,
                'token' => $request->token,
            ])
            ->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Link is not valid!Please Try Again using new link');
        }

        $user = User::where('email', $request->email)->where('role', 'professional')->first();

        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        // Check if the new password is the same as the old one
        if ($user->isPasswordInHistory($request->password)) {
            return back()->with('error', 'New password cannot be the same as the previous passwords.');
        }

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }
        // \App\Models\User::where('email', $request->email)
        //     ->update(['password' => Hash::make($request->password)]);
        $user->storePasswordHistory();
        DB::table('password_resets')->where(['email' => $request->email])->delete();

        return redirect('/login')->with('message', 'Your password has been changed!');
    }
}
