@if(isset($user))
<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise">
    <span class="mb-2 d-block">Personal Details :</span>
    <ul class="cdscountTwo">
        @if($user->first_name || $user->last_name)
        <li><i class="fa-solid fa-circle-check black"></i> Name: {{ $user->first_name ?? 'N/A' }} {{ $user->last_name ?? 'N/A' }}</li>
        @endif
        @if($user->email)
        <li><i class="fa-solid fa-circle-check black"></i> Email: {{ $user->email ?? 'N/A' }}</li>
        @endif
        @if($user->country_code || $user->phone_no)
        <li><i class="fa-solid fa-circle-check black"></i> Phone: {{ $user->country_code ?? 'N/A' }} {{ $user->phone_no ?? 'N/A' }}</li>
        @endif
        @if($user->date_of_birth)
        <li><i class="fa-solid fa-circle-check black"></i> Date of Birth:{{ $user->date_of_birth ? date('d M Y', strtotime($user->date_of_birth)) : 'N/A' }}</li>
        @endif
        @if($user->gender)
        <li><i class="fa-solid fa-circle-check black"></i> Gender: {{ ucfirst($user->gender) }}</li>
        @endif
        @if($user->timezone)
        <li><i class="fa-solid fa-circle-check black"></i> TimeZone: {{ $user->timezone ?? 'N/A' }}</li>
        @endif
        @if(isset($user_details) && $user_details->languages_known != '')
            @php $languages = explode(',', $user_details->languages_known); @endphp
            <li><i class="fa-solid fa-circle-check black"></i> <strong>Language:</strong> {{ implode(', ', $languages) }}</li>
        @endif
    </ul>
</div>
@endif

<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise mt-3 mt-md-0">
    <div class="cds-register-address-list">
        <span class="mb-2 d-block">Personal Address :</span>
        @if(isset($user->personalLocation) && $user->personalLocation != '')
        <div class="address-item">
            <div class="address-header d-block d-md-flex">
                <div class="map-thumbnail  d-inline-block d-md-block">
                    <i class="fa-sharp fa-solid fa-location-dot" style="color:#000000;"></i>
                </div>
                <div class="address-details render-address">
                    <div class="address-text">
                        @if(!empty($user->personalLocation->address_1))
                            <div> {{ $user->personalLocation->address_1 }}</div>
                        @endif
                        @if(!empty($user->personalLocation->address_2))
                            <div> {{ $user->personalLocation->address_2 }}</div>
                        @endif
                        @if(!empty($user->personalLocation->city))
                            <div> {{ $user->personalLocation->city }}</div>
                        @endif
                        @if(!empty($user->personalLocation->state))
                            <div> {{ $user->personalLocation->state }}</div>
                        @endif
                        @if(!empty($user->personalLocation->country))
                            <div> {{ $user->personalLocation->country }}</div>
                        @endif
                        @if(!empty($user->personalLocation->pincode))
                            <div> {{ $user->personalLocation->pincode }}</div>
                        @endif
                    </div>
                    <div class="address-action-btns mt-3">
                        <a href="javascript:;" onclick="showPopup('<?php echo baseUrl('professional/add-personal-address/'.$user->personalLocation->unique_id) ?>')">
                            Edit Address
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@if(!empty($user->cdsCompanyDetails))
<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise mt-3 mt-md-0">
    <div class="cds-register-address-list">
        <span class="mb-2 d-block">Companies :</span>
        @foreach($user->cdsCompanyDetails as $key => $record)
        <div class="address-item" id="personal-address-div-{{$record->id}}">
            <div class="address-header">
                <div class="">
                    <img id="showCompanyLogo" class="img-fluid" src="{{ companyLogoDirUrl($record->company_logo) }}" alt="Profile Image" style="height:40px; width:40px;">
                </div>
                <div class="address-details render-address">
                    <div class="address-name">
                        <div class="company-name" data-value="{{$record->company_name ?? ''}}">{{$record->company_name ?? ''}} {{ $record->id }} {{ $record->user_id }}</div>
                    </div>
                    <div class="address-action-btns">
                        <a href="{{ baseUrl('companies/edit-company/'.$record->unique_id) }}" class="btn btn-warning btn-sm me-2" >
                            Edit Company
                        </a>
                        <a href="javascript:;" onclick="confirmAction(this)" data-id="{{$record->id}}" data-href="{{ baseUrl('companies/delete-company/'.$record->unique_id) }}" class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm">
                            Delete Company
                        </a>
                        <a href="{{ baseUrl('companies/manage-address/'.$record->unique_id) }}" class="btn btn-warning btn-sm me-2" >
                            Manage Company Address
                        </a>
                        <div class="radio">
                            <input type="radio" name="address" onchange="markCompanyAsPrimary('{{ $record->unique_id}}','{{$record->user_id}}')" {{ ($record->is_primary == 1 && (!empty($record->is_primary) && $record->is_primary == 1)) ? 'checked' : '' }} />Mark As Primary
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@if(isset($license_detail))
<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise mt-3 mt-md-0">
    <div class="cds-register-address-list">
        <span  class="mb-2 d-block">License Details:</span>
        <div class="address-item">
            <div class="address-header d-block d-md-flex">
                <div class="map-thumbnail  d-inline-block d-md-block">
                    <i class="fa-sharp fa-solid fa-id-card" style="color:#000000;"></i>
                </div>
                <div class="address-details render-license">
                    <ul class="cdscountTwo">
                        <li><b>Title:</b> {{ $license_detail->title ?? 'N/A' }}</li>
                        <li><b>Regulatory Country:</b> {{ $license_detail->regulatoryCountry->name ?? 'N/A' }}</li>
                        <li><b>Regulatory Body:</b> {{ $license_detail->regulatoryBody->name ?? 'N/A' }}</li>
                        <li><b>Class:</b> {{ $license_detail->class_level ?? 'N/A' }}</li>
                        <li><b>License Number:</b> {{ $license_detail->license_number ?? 'N/A' }}</li>
                        <li><b>License Start Date:</b> {{ $license_detail->license_start_date ?? 'N/A' }}</li>
                        <li><b>Country of Practice:</b> {{ $license_detail->country->name ?? 'N/A' }}</li>
                        <li><b>License Status:</b> {{ $license_detail->license_status ?? 'N/A' }}</li>
                        <li><b>Entitled to Practice:</b> {{ $license_detail->entitled_to_practice ?? 'N/A' }}</li>
                        <li><b>More than one license:</b> {{ $license_detail->do_you_more_license ? 'Yes' : 'No' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($my_services->isNotEmpty())
    @php
        $myServices = collect($my_services)->where('is_pin', 1);
        $first3Entries = $myServices->take(3);
        $remainingEntries = $myServices->skip(3);
    @endphp
    @if($myServices->isNotEmpty())
        <div class="cds-t25n-content-professional-profile-container-main-body-information-expertise">
            <span>Core Areas of Practice</span>
            <ul class="cds-top-skilled-list">
                @foreach($first3Entries as $value)
                <li class="load-core"><i class="fa-solid fa-circle-check black"></i>
                    {{$value->ImmigrationServices->name}}
                </li>
                @endforeach
                @foreach($remainingEntries as $value)
                <li class="d-none pending-core"><i class="fa-solid fa-circle-check black"></i>
                    {{$value->ImmigrationServices->name}}
                </li>
                @endforeach
                @if($myServices->count() >3)
                    <li><a href="javascript:;" class="core-more">+{{count($remainingEntries)}} More</a></li>
                @endif
            </ul>
        </div>
    @endif
@endif
@if($my_services->isNotEmpty())
    @php
        $myServices = collect($my_services)->where('is_pin', 0);
        $first3Entries = $myServices->take(3);
        $remainingEntries = $myServices->skip(3);
    @endphp
    @if($myServices->isNotEmpty())
        <div class="cds-t25n-content-professional-profile-container-main-body-information-expertise">
            <span class="mb-2 d-block">Areas of Practice</span>
            <ul class="cds-top-skilled-list">
                @foreach($first3Entries as $value)
                <li class="load-practice"><i class="fa-solid fa-circle-check black"></i>
                    {{$value->ImmigrationServices->name ?? 'N/A'}}
                </li>
                @endforeach
                @foreach($remainingEntries as $value)
                <li class="d-none pending-practice"><i class="fa-solid fa-circle-check black"></i>
                    {{$value->ImmigrationServices->name ?? 'N/A'}}
                </li>
                @endforeach
                @if($myServices->count() >3)
                    <li><a href="javascript:;" class="practise-more">+{{count($remainingEntries)}} More</a></li>
                @endif
            </ul>
        </div>
    @endif
@endif

@if(isset($document) && count($document) > 0)
<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise mt-3 mt-md-0">
    <div class="cds-register-address-list">
    @foreach($document as $document_value)
        @if($document_value->document_type == 'proof_of_identity')
            <span  class="mb-2  d-block font-weight-bold">Proof of Identify:</span>
            <div class="address-item mb-3">
                <div class="address-header d-flex align-items-center">
                    <div class="map-thumbnail mr-2">
                        <i class="fa-solid fa-file" style="color:#000000;"></i>
                    </div>
                    <div class="address-details render-company w-100">
                        <div class="address-text d-flex justify-content-between align-items-center">
                            @foreach(explode(',', $document_value->file_name) as $value)
                                <span class="mb-3 font-weight-bold text-break">{{  $value }}</span>
                                <a href="{{ baseUrl('professional/download-file?file=' . trim($value)) }}" class="cdsTYDashboard-button-light cdsTYDashboard-button-small" download>
                                    <i class="fa-regular fa-cloud-arrow-down"></i> Download
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($document_value->document_type == 'incorporation_certificate')
            <span  class="mb-2  d-block font-weight-bold">Incorporation certificate:</span>
            <div class="address-item mb-3">
                <div class="address-header d-flex align-items-center">
                    <div class="map-thumbnail mr-2">
                        <i class="fa-solid fa-file" style="color:#000000;"></i>
                    </div>
                    <div class="address-details render-company w-100">
                        <div class="address-text d-flex justify-content-between align-items-center">
                            @foreach(explode(',', $document_value->file_name) as $value)
                                <span class="mb-3 font-weight-bold text-break">{{  $value }}</span>
                                <a href="{{ baseUrl('professional/download-file?file=' . trim($value)) }}" class="cdsTYDashboard-button-light cdsTYDashboard-button-small" download>
                                    <i class="fa-regular fa-cloud-arrow-down"></i> Download
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @if($document_value->document_type == 'license')
            <span  class="mb-2 d-block font-weight-bold">License:</span>
            <div class="address-item mb-3">
                <div class="address-header d-flex align-items-center">
                    <div class="map-thumbnail mr-2">
                        <i class="fa-solid fa-file" style="color:#000000;"></i>
                    </div>
                    <div class="address-details render-company w-100">
                        <div class="address-text d-flex justify-content-between align-items-center">
                            @foreach(explode(',', $document_value->file_name) as $value)
                                <span class="mb-3 font-weight-bold text-break">{{  $value }}</span>
                                <a href="{{ baseUrl('professional/download-file?file=' . trim($value)) }}" class="cdsTYDashboard-button-light cdsTYDashboard-button-small" download>
                                    <i class="fa-regular fa-cloud-arrow-down"></i> Download
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
    </div>
</div>
@endif

@if(!empty($license_detail))
<div class="cds-t25n-content-professional-profile-container-main-body-information-expertise">
    <span>Regions</span>
    <ul class="cds-language-list">
        <li>
            <i class="fa-solid fa-circle-check black"></i>
            {{ getRegion($license_detail->country_of_practice) ?? 'N/A' }}
        </li>
    </ul>
</div>
@endif

@section('javascript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('cdstoggle-button');
        const shortText = document.getElementById('cdsshort-text');
        const fullText = document.getElementById('cdsfull-text');
        const contentText = document.getElementById('cdscontent-text');

        function checkOverflow() {
            if (contentText.scrollHeight > contentText.clientHeight) {
                toggleButton.style.display = 'inline';
                contentText.classList.add('cdsHeight');
            } else {
                toggleButton.style.display = 'none';
                contentText.classList.remove('cdsHeight');
            }
        }

        if (toggleButton) {
            toggleButton.addEventListener('click', function() {
                if (fullText.style.display === 'none') {
                    fullText.style.display = 'inline';
                    toggleButton.textContent = 'Show Less';
                } else {
                    fullText.style.display = 'none';
                    toggleButton.textContent = 'Show More';
                }
            });
        }

        window.addEventListener('resize', checkOverflow);
    });

    function markCompanyAsPrimary(company_id,user_id){
        $.ajax({
            type: "GET",
            url: "{{baseUrl('/companies/mark-as-primary')}}",
            data:{
                _token:csrf_token,
                company_id:company_id,
                user_id:user_id
            },
            dataType:'json',
            success: function (response) {
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
@endsection