
@section("styles")
<link rel="stylesheet" href="{{ url('assets/css/23-CDS-ai-protection.css') }}">
@endsection    
    
       <div class="CDSDashboardActivity-AI-Protection-variation">


    <div class="CDSDashboardActivity-AI-Protection-v2-container">
        <div class="CDSDashboardActivity-AI-Protection-card">
            <div class="d-flex justify-content-between align-items-center" style="margin-bottom: 20px;">
        <div>
            <h2 style="margin-bottom: 10px;">Domain Verification</h2>
            <p style="color: #6c757d; margin-bottom: 0;">Verify ownership of your digital assets</p>
        </div>
        <div>
            <strong>Status:</strong> 
            <span class="CDSDashboardActivity-AI-Protection-status-badge 
                @if(!empty($domain_data) && ($domain_data->domain_verify == 'verified')) 
                    CDSDashboardActivity-AI-Protection-status-active
                @else
                    CDSDashboardActivity-AI-Protection-status-pending
                @endif">
                @if(!empty($domain_data) && ($domain_data->domain_verify == 'verified'))
                    Verified
                @elseif(!empty($domain_data))
                    Pending Verification
                @else
                    Not Configured
                @endif
            </span>
        </div>
    </div>
            
            @if(empty($domain_data))
            <form id="domain-form" class="js-validate" action="{{ baseUrl('/domain-verify') }}" method="post">
                @csrf
                <input type="hidden" name="type" value="domain_verify">
                <input type="hidden" name="user_id" value="{{auth()->user()->id}}">
                
                <div class="CDSDashboardActivity-AI-Protection-input-group">
                    <label class="CDSDashboardActivity-AI-Protection-input-label">Domain to Verify</label>
                    {!! FormHelper::formInputText([
                        'name'=>"domain",
                        'id'=>"domain",
                        "value"=> $domain_data->domain??'',
                        "required"=>true,
                         'label'=>"Enter Domain",
                        // "class"=>"CDSDashboardActivity-AI-Protection-input-field",
                        "placeholder"=>"example.com"
                    ])!!}
                </div>

                <div style="text-align: right; margin-top: 20px;">
                    <button type="submit" class="CDSDashboardActivity-AI-Protection-btn CDSDashboardActivity-AI-Protection-btn-primary">Save</button>
                </div>
            </form>
            @else
            <div class="CDSDashboardActivity-AI-Protection-input-group">
                <label class="CDSDashboardActivity-AI-Protection-input-label">Verified Domain</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" class="CDSDashboardActivity-AI-Protection-input-field" 
                           value="{{$domain_data->domain??''}}" readonly style="flex: 1;">
                    <button class="CDSDashboardActivity-AI-Protection-btn CDSDashboardActivity-AI-Protection-btn-danger" 
                            onclick="confirmAction(this)" 
                            data-href="{{ baseUrl('remove-professional-domain/'.$domain_data->unique_id) }}">
                        Remove
                    </button>
                </div>
            </div>

            <div class="CDSDashboardActivity-AI-Protection-v3-tabs">
                <button class="CDSDashboardActivity-AI-Protection-tab CDSDashboardActivity-AI-Protection-active" 
                        onclick="switchTab(this, 'dns')">DNS CNAME Verify</button>
                <button class="CDSDashboardActivity-AI-Protection-tab" 
                        onclick="switchTab(this, 'file')">Text File Verify</button>
            </div>

            <div id="dns-method" class="CDSDashboardActivity-AI-Protection-verification-method">
                <h4 style="margin-bottom: 16px;">Add CNAME Record</h4>
                <p style="color: #6c757d; margin-bottom: 16px;">Add the following CNAME record to your domain DNS configuration:</p>
                
                <div class="CDSDashboardActivity-AI-Protection-code-block">
                    CNAME: {{$domain_data->dns_txt_record??''}}
                </div>

                @if($domain_data->domain_file_verify != 'verified')
                <form action="{{ baseUrl('verify-professional-domain-dns') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{$domain_data->unique_id}}" />
                    <button class="CDSDashboardActivity-AI-Protection-btn CDSDashboardActivity-AI-Protection-btn-primary">
                        Verify Now
                    </button>
                </form>
                @else
                <div style="background: #d4edda; padding: 16px; border-radius: 8px; margin-top: 20px;">
                    <strong>✓ Success!</strong> Your domain has been verified via DNS.
                </div>
                @endif
            </div>

            <div id="file-method" class="CDSDashboardActivity-AI-Protection-verification-method" style="display: none;">
                <h4 style="margin-bottom: 16px;">Upload Verification File</h4>
                <p style="color: #6c757d; margin-bottom: 16px;">Download the verification file and upload it to your website root folder:</p>
                
                <p style="margin-bottom: 16px;">
                    <strong>File Location:</strong> {{$domain_data->domain}}/tv-verify.txt
                </p>
                
                <div style="display: flex; gap: 10px;">
                    <a class="CDSDashboardActivity-AI-Protection-btn CDSDashboardActivity-AI-Protection-btn-secondary"
                       href="{{baseUrl('download-domain-verify-file')}}">
                        Download File
                    </a>
                    @if($domain_data->domain_file_verify != 'verified')
                    <form action="{{ baseUrl('verify-professional-domain-file') }}" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{$domain_data->unique_id}}" />
                        <button class="CDSDashboardActivity-AI-Protection-btn CDSDashboardActivity-AI-Protection-btn-primary">
                            Verify Now
                        </button>
                    </form>
                    @else
                    <div style="background: #d4edda; padding: 16px; border-radius: 8px; flex: 1;">
                        <strong>✓ Success!</strong> Your domain has been verified via file.
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push("scripts")
<script>
function switchTab(element, method) {
    $('.CDSDashboardActivity-AI-Protection-tab').removeClass('CDSDashboardActivity-AI-Protection-active');
    $(element).addClass('CDSDashboardActivity-AI-Protection-active');
    
    $('.CDSDashboardActivity-AI-Protection-verification-method').hide();
    $('#'+method+'-method').show();
}

$("#domain-form").submit(function(e) {
    e.preventDefault();
    var formData = new FormData($(this)[0]);
    var url = $("#domain-form").attr('action');
    var is_valid = formValidation("domain-form");
    if (!is_valid) {
        return false;
    }
    $.ajax({
        url: url,
        type: "post",
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            hideLoader();
            if (response.status == true) {
                successMessage(response.message);
                location.reload();
            } else {
                validation(response.message);
            }
        },
        error: function() {
            internalError();
        }
    });
});
</script>
@endpush

