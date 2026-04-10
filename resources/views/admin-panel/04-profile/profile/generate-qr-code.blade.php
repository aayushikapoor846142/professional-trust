
@section("styles")
<link rel="stylesheet" href="{{ url('assets/css/23-CDS-ai-protection.css') }}">
@endsection    
    
    <!-- Variation 2: QR Code Generator Focus -->
    <!-- QR Code Generator Section -->
<div class="CDSDashboardActivity-AI-Protection-variation">
    <div class="CDSDashboardActivity-AI-Protection-v2-container">
        <div class="CDSDashboardActivity-AI-Protection-card">
            <h2 style="margin-bottom: 30px; text-align: center;">Create Protected QR Code</h2>
            
            @if($user->userDetail->qrcode != '')
            <div class="CDSDashboardActivity-AI-Protection-qr-preview">
                <img id="logoImg" 
                     src="{{ professionalBarcodeDirUrl(auth()->user()->unique_id.'-horizontal.png', 't',auth()->user()->unique_id) }}" 
                     alt="QR Code"
                     class="img-fluid">
                
                <div class="CDSDashboardActivity-AI-Protection-qr-info mt-3" style="text-align: center;">
                    <strong>Status:</strong> 
                    <span class="CDSDashboardActivity-AI-Protection-status-badge CDSDashboardActivity-AI-Protection-status-active">
                        Active
                    </span>
                </div>

                <div class="mt-4" style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-family: monospace; word-break: break-all;">
                    Embed Code:<br>
                    {!! htmlspecialchars('<img id="trustvisory-verify" src="' . professionalBarcodeDirUrl(auth()->user()->unique_id.'-horizontal.png', 't', auth()->user()->unique_id) . '" alt="Trustvisory Verified">') !!}
                </div>
            </div>
            @else
            <div class="CDSDashboardActivity-AI-Protection-qr-preview">
                <div class="CDSDashboardActivity-AI-Protection-qr-placeholder">
                    QR Preview
                </div>
                <p style="color: #6c757d; text-align: center;">Your QR code will appear here after generation</p>
                
                <div class="CDSDashboardActivity-AI-Protection-qr-info mt-3" style="text-align: center;">
                    {{-- <strong>Verification ID:</strong> {{ auth()->user()->unique_id }}<br> --}}
                    <strong>Status:</strong> 
                    <span class="CDSDashboardActivity-AI-Protection-status-badge CDSDashboardActivity-AI-Protection-status-pending">
                        Not Generated
                    </span>
                </div>
            </div>
            @endif
                <div style="text-align: center; margin-bottom: 30px;">
                <button class="CDSDashboardActivity-AI-Protection-btn CDSDashboardActivity-AI-Protection-btn-primary" 
                        onclick="generateQrCode({{auth()->user()->unique_id}})">
                    Generate Secure QR Code
                </button>
            </div>
        </div>
    </div>
</div>    
     

@push("scripts")
<script>
function generateQrCode(user_id)
{
    $.ajax({
        type: "GET",
        url: "{{baseUrl('/generate-qr-code')}}/"+user_id,
        dataType: 'json',
        success: function(response) {
            if (response.status == true) {
                successMessage(response.message);
                location.reload();
            } else {
                errorMessage(response.message);
            }
        },
    });
}
</script>
@endpush