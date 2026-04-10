@extends('emails.mail-master')
@section('content')
<style>
	/* Reset styles */
	body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
	table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
	img { -ms-interpolation-mode: bicubic; }
	
	/* Remove default styling */
	img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
	table { border-collapse: collapse !important; }
	body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; }
	.userProfileImage img{
        border-radius: 50%;
    }
	/* Mobile styles */
	@media screen and (max-width: 600px) {
		.mobile-hide { display: none !important; }
		.mobile-center { text-align: center !important; }
		.container { padding: 0 !important; width: 100% !important; }
		.mobile-padding { padding: 20px !important; }
	}
</style>
<div style="margin: 0; padding: 0; background-color: #f8f9fa; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
    
    <!-- Email Container -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table class="container" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding: 40px 20px; background: linear-gradient(135deg, #0066ff 0%, #0052cc 100%); border-radius: 8px 8px 0 0;">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <div style="width: 80px; height: 80px; border-radius: 50%; display: inline-block; text-align: center; line-height: 80px; font-size: 32px; font-weight: 600; color: #0066ff; margin-bottom: 20px;">
                                            @if(auth()->user()->profile_image != '')
                                                <img id="showProfileImage" style="width: 80px; height: 80px; border-radius: 50%;" src="{{ userDirUrl(auth()->user()->profile_image) }}" class="img-fluid cdsProfileimg" alt="Profile Image">
                                            @else
                                                {{ userInitial(auth()->user()) }}
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="color: #ffffff;">
                                        <h1 style="margin: 0; font-size: 28px; font-weight: 600; line-height: 1.2;">Share Your Experience</h1>
                                        <p style="margin: 10px 0 0 0; font-size: 16px; opacity: 0.9;">Your feedback matters to us</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Body Content -->
                    <tr>
                        <td class="mobile-padding" style="padding: 40px 40px 30px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <!-- Greeting -->
                                <tr>
                                    <td style="font-size: 18px; color: #212529; padding-bottom: 20px;">
                                        Hi {{ $receiver_name??'Receiver Name' }},
                                    </td>
                                </tr>
                                
                                <!-- Main Message -->
                                <tr>
                                    <td style="font-size: 16px; color: #495057; line-height: 1.6; padding-bottom: 20px;">
                                        Thank you for engaging with {{ $professional_name }} in your search for trusted immigration services. We’re always working to improve our platform and ensure that it serves users like you better.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size: 16px; color: #495057; line-height: 1.6; padding-bottom: 20px;">
                                        Your professional have some message to give you.
                                    </td>
                                </tr>
                                <!-- Personal Message (if included) -->
                                <tr>
                                    <td style="background-color: #f8f9fa; border-left: 4px solid #0066ff; padding: 15px 20px; margin-bottom: 20px;">
                                        <p style="margin: 0; font-size: 16px; color: #495057; font-style: italic; line-height: 1.6;">
                                            {{ $professional_message??'' }}
                                        </p>
                                    </td>
                                </tr>
                                
                                <!-- Request Message -->
                                <tr>
                                    <td style="font-size: 16px; color: #495057; line-height: 1.6; padding-top: 20px; padding-bottom: 30px;">
                                        If you found our platform helpful or have any feedback about your experience, we’d be grateful if you could take a moment to leave us a review. Your input helps others make informed decisions and helps us build a more reliable community.
                                    </td>
                                </tr>
                                
                                <!-- CTA Button -->
                                <tr>
                                    <td align="center" style="padding-bottom: 30px;">
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td align="center" style="border-radius: 8px; background-color: #0066ff;">
                                                    <a href="{{ $url??'#' }}" target="_blank" style="display: inline-block; padding: 16px 40px; font-size: 16px; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600;">
                                                        Write a Review
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                
                                <!-- How it works -->
                                <tr>
                                    <td style="border-top: 1px solid #e9ecef; padding-top: 30px;">
                                        <h3 style="margin: 0 0 20px 0; font-size: 18px; color: #212529;">How it works:</h3>
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="40" valign="top">
                                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #e7f3ff; color: #0066ff; text-align: center; line-height: 32px; font-weight: 600;">
                                                        1
                                                    </div>
                                                </td>
                                                <td style="padding-left: 15px; padding-bottom: 15px;">
                                                    <p style="margin: 0; font-size: 16px; color: #495057; line-height: 1.5;">
                                                        Click the button above to access our secure review form
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="40" valign="top">
                                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #e7f3ff; color: #0066ff; text-align: center; line-height: 32px; font-weight: 600;">
                                                        2
                                                    </div>
                                                </td>
                                                <td style="padding-left: 15px; padding-bottom: 15px;">
                                                    <p style="margin: 0; font-size: 16px; color: #495057; line-height: 1.5;">
                                                        Rate your experience and write your review
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="40" valign="top">
                                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: #e7f3ff; color: #0066ff; text-align: center; line-height: 32px; font-weight: 600;">
                                                        3
                                                    </div>
                                                </td>
                                                <td style="padding-left: 15px;">
                                                    <p style="margin: 0; font-size: 16px; color: #495057; line-height: 1.5;">
                                                        Submit your review to help others
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px 40px; border-radius: 0 0 8px 8px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding-bottom: 20px;">
                                        <img src="{{ mediaDirUrl('54218-logo-c.png','m') }}" alt="Trustvisory" width="120" style="display: block;">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="font-size: 14px; color: #6c757d; line-height: 1.5;">
                                        <p style="margin: 0 0 10px 0;">
                                            Questions? Contact us at <a href="mailto:support@trustvisory.com" style="color: #0066ff; text-decoration: none;">support@trustvisory.com</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
    
</div>
@endsection