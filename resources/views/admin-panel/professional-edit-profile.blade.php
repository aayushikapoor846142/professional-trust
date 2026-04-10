    @extends('admin-panel.layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">
@endsection

@push('scripts')
<script src="{{ url('assets/js/custom-file-upload.js') }}"></script>
@endpush

    @section('content')
  <div class="ch-action">
                    <a href="{{ baseUrl('/') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div>
<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
                <!-- Nav tabs -->
                <div class="cds-edit-profile pt-3">
                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-information-tab" data-bs-toggle="pill" data-bs-target="#pills-personal-info" type="button" role="tab" aria-controls="pills-personal-info" aria-selected="true">Personal information</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="about-business-tab" data-bs-toggle="pill" data-bs-target="#pills-business" type="button" role="tab" aria-controls="pills-business" aria-selected="false">Company Details</button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="verify-company-details-tab" data-bs-toggle="pill" data-bs-target="#pills-verify-company" type="button" role="tab" aria-controls="pills-verify-company-details" aria-selected="false">Documents</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="licence-tab" data-bs-toggle="pill" data-bs-target="#licence-domain" type="button" role="tab" aria-controls="pills-licence" aria-selected="false">License Detail</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="banking-details-tab" data-bs-toggle="pill" data-bs-target="#pills-banking-details" type="button" role="tab" aria-controls="pills-banking-details" aria-selected="false">Banking Details</button>
                        </li>
                    </ul>
                    <div class="p-3 tab-content" id="pills-tabContent">
                        <!-- 1 -->
                        <div class="active fade show tab-pane" id="pills-personal-info" role="tabpanel" aria-labelledby="personal-information-tab" tabindex="0">
                            <div class="cds-data">
                                <h4 class="title">Tell your customers how to get in touch.</h4>
                                <form id="contact-form" class="js-validate" action="{{ baseUrl('/professional-submit-profile') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="type" value="contact">
                                    {{-- <input type="hidden" name="prof_id" value="{{$professionalList->id}}"> --}}
                                    <input type="hidden" name="personal_location_unique_id" value="{{$user->personalLocation->unique_id ?? null }}">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                            {!! FormHelper::formInputText([
                                                'name'=>"first_name",
                                                'id'=>"first_name",
                                                "label"=> "First name",
                                                "value"=> $user->first_name,
                                                "required"=>true,
                                                'events'=>['oninput=validateName(this)']])
                                            !!}
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                            {!! FormHelper::formInputText([
                                                'name'=>"last_name",
                                                'id'=>"last_name",
                                                "label"=> "Last name",
                                                "value"=> $user->last_name,
                                                "required"=>true,
                                                'events'=>['oninput=validateName(this)']])
                                            !!}
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                            {!! FormHelper::formInputText([
                                                'name'=>"email",
                                                'id'=>"email",
                                                "label"=> "Email",
                                                "value"=> $user->email,
                                                "required"=>true,
                                                'readonly'=>true,
                                            ])!!}
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6 mb-4">                                            
                                                {!! FormHelper::formPhoneNo([
                                                'name' => "phone_no",
                                                'country_code_name' => "country_code",
                                                "label" => "Phone Number",
                                                "value" => $user->phone_no,
                                                "default_country_code"=>$user->country_code,
                                                "required" => true,
                                                'events'=>['oninput=validatePhoneInput(this)']]
                                                ) !!}

                                        </div> 
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xl-6">
                                            <div class="dob-block">
                                            {!! FormHelper::formDatepicker([
                                                'label' => 'Date of Birth',
                                                'name' => 'date_of_birth',
                                                'id' => 'date_of_birth',
                                                'value' => $user->date_of_birth ?? '',
                                                'required' => true,                                              
                                            ]) !!}
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xl-6 m-1rem">
                                            <div class="form-check form-check-inline p-0 cds-gender-list me-0 mt-3 mt-md-0"> 
                                                <label class="mb-1">Gender <span class="text-danger">*</span></label>
                                                {!! FormHelper::formRadio([
                                                    'name' => 'gender',
                                                    'required' => true,
                                                    'options' => FormHelper::selectThreeGender(),
                                                    'value_column' => 'value',
                                                    'label_column' => 'label',
                                                    'selected' => $user->gender ?? ''
                                                ]) !!}                                   
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6 cds-address">
                                            {!! FormHelper::formInputText([
                                                'name' => 'address1',
                                                'label' => 'Address 1',
                                                "required"=>true,
                                                'input_class'=>"google-address",
                                                'id' => 'address1',
                                                'value' => $user->personalLocation->address_1 ??'',
                                            ]) !!}
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6 cds-address">
                                            {!! FormHelper::formInputText([
                                                'name' => 'address2',
                                                'label' => 'Address 2',
                                                'input_class'=>"google-address",
                                                'id' => 'address2',
                                                'value' => $user->personalLocation->address_2 ??'',
                                            ]) !!}
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                            {!! FormHelper::formInputText([
                                                'name' => 'city',
                                                'label' => 'City',
                                                "required"=>true,
                                                'input_class' => 'ga-city',
                                                'value' => $user->personalLocation->city ??'',
                                                'events'=>['oninput=validateName(this)']
                                            ]) !!}
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                            {!! FormHelper::formInputText([
                                                'name' => 'state',
                                                'label' => 'State',
                                                "required"=>true,
                                                'input_class' => 'ga-state',
                                                'value' => $user->personalLocation->state ??'',
                                                'events'=>['oninput=validateName(this)']
                                            ]) !!}
                                        </div>
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                            {!! FormHelper::formInputText([
                                                'name' => 'country',
                                                'label' => 'Country',
                                                "required"=>true,
                                                'input_class' => 'ga-country',
                                                'value' => $user->personalLocation->country ??'',
                                                'events'=>['oninput=validateName(this)']
                                            ]) !!}
                                            {{--<div class="cds-selectbox">
                                                {!! FormHelper::formSelect([
                                                    'name' => 'country',
                                                    "required"=>true,
                                                    'label' => 'Select Country',
                                                    'class' => 'select2-input',
                                                    'select_class' => 'ga-country',
                                                    'options' => $countries,
                                                    'value_column' => 'name',
                                                    'label_column' => 'name',
                                                    'selected' => $user->personalLocation->country ?? null,
                                                    'is_multiple' => false
                                                ]) !!}
                                            </div>--}}
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                            {!! FormHelper::formInputNumber([
                                                'name' => 'pincode',
                                                'label' => 'Pincode',
                                                "required"=>true,
                                                'input_class' => 'ga-pincode',
                                                'value' => $user->personalLocation->pincode ??'',
                                                'events'=>['oninput=validateDigit(this)', 'onblur=validateDigit(this)']
                                            ]) !!}
                                        </div>
                                        <div class="col-xl-12">
                                            {!! FormHelper::formInputText([
                                                'name'=>"profile_type",
                                                'id'=>"profile_type",
                                                "label"=> "Profile Type",
                                                "value"=> $user->userDetail->profile_type ?? 'new_signup',
                                                "required"=>true,
                                                'readonly'=>true,
                                            ])!!}
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label input-label">Profile image</label>
                                                <div class="js-form-message">
                                                    <input type="file" class="form-control" name="profile_image">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-3">
                                            <img id="imagePreview" src="{{ $user->profile_image ? userDirUrl($user->profile_image, 't') : 'assets/images/default.jpg' }}"
                                                    alt="Image" class="img-fluid" width=100 height="100">
                                        </div>
                                    </div>
                                    <div class="text-end mt-4">
                                        <button type="submit" class="CdsTYButton-btn-primary add-btn">Save & publish</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- 2 -->
                        <div class="fade tab-pane" id="pills-business" role="tabpanel" aria-labelledby="about-business-tab" tabindex="0">
                            <div class="cds-data">
                                <form id="company-form" class="js-validate" action="{{ baseUrl('/professional-submit-profile') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="type" value="business">
                                
                                    <input type="hidden" name="company_unique_id" value="{{$user->cdsCompanyDetail->unique_id ?? null }}">
                                    <!-- <input type="hidden" name="company_location_unique_id" value="{{$user->companyLocation[0]->unique_id ?? null }}"> -->
                                    
                                    <div class="row">
                                        
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                            {!! FormHelper::formInputText([
                                                'name'=>"company_name",
                                                'id'=>"name",
                                                "label"=> "Company name",
                                                "value"=> $user->cdsCompanyDetail->company_name ?? '',
                                                "required"=>true,
                                            ])!!}
                                        </div>
                                      
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                            {!! FormHelper::formSelect([
                                                'name' => 'company_type',
                                                'label' => 'Company Type',
                                                'class' => 'select2-input ga-country',
                                                'id' => 'company_type',
                                                'options' => FormHelper::ownerCompanyType(),
                                                'value_column' => 'value',
                                                'label_column' => 'label',
                                                'selected' => isset($user->cdsCompanyDetail->company_type) ? $user->cdsCompanyDetail->company_type : null,
                                                'is_multiple' => false,
                                                'required' => true,
                                            ]) !!}
                                        </div>  
                                        
                                        <div class="col-md-6">
                                            <div class="form-check form-check-inline p-0 cds-gender-list me-0"> 
                                                <label class="mb-1">Ownership Type <span class="text-danger">*</span></label>
                                                {!! FormHelper::formRadio([
                                                    'name' => 'owner_type',
                                                    'options' => [
                                                        ['value' => 'Self Employed', 'label' => 'Self Employed'],
                                                        ['value' => 'Employed', 'label' => 'Employed']
                                                    ],
                                                    'value_column' => 'value',
                                                    'label_column' => 'label',
                                                    'id' => 'label',
                                                    'value' => 'label',
                                                    'selected' => $user->cdsCompanyDetail->owner_type ?? null
                                                ]) !!}                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="company-address">
                                        @if($user->companyLocation->isNotEmpty())
                                            @foreach($user->companyLocation as $value)
                                            @php    
                                                $index = mt_rand(100,999)
                                            @endphp
                                            <div class="row address-block google-address-area">
                                                <input type="hidden" name="company_add[{{$index}}][company_location_unique_id]" value="{{$value->id ?? '' }}">
                                                <div class="col-md-6">
                                                    {!! FormHelper::formInputText([
                                                        'name' => "company_add[$index][address1]",
                                                        "label"=> "Address1",
                                                        'input_class'=>"google-address",
                                                        'value' => $value->address_1 ??'',
                                                        "required"=>true,
                                                    ])!!}          
                                                </div>  
                                        
                                                <div class="col-md-6">
                                                    {!! FormHelper::formInputText([
                                                        'name' => "company_add[$index][address2]",
                                                        "label"=> "Address2",
                                                        'input_class'=>"google-address",
                                                        'value' => $value->address_2 ??'',
                                                        "required"=>true,
                                                    ])!!}          
                                                </div>  
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                                    {!! FormHelper::formInputText([
                                                        'name' => "company_add[$index][city]",
                                                        "label"=> "City",
                                                        'input_class'=>"ga-city",
                                                        'value' => $value->city ?? '',
                                                        'events'=>['oninput=validateName(this)'],
                                                        "required"=>true,
                                                    ])!!}
                                                </div>

                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                                    {!! FormHelper::formInputText([
                                                        'name' => "company_add[$index][state]",
                                                        "label"=> "State",
                                                        'input_class'=>"ga-state",
                                                        'value' => $value->state ?? '',
                                                        'events'=>['oninput=validateName(this)'],
                                                        "required"=>true,
                                                    ])!!}
                                                </div>
                                        
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                                    {!! FormHelper::formSelect([
                                                        'name' => "company_add[$index][country]",
                                                        'id' => 'country',
                                                        'label' => 'Country',
                                                        'class' => 'select2-input',
                                                        'input_class'=>"ga-country",
                                                        'options' => $countries,
                                                        'value_column' => 'name',
                                                        'label_column' => 'name',
                                                        'selected' => $value->country ?? null,
                                                        'is_multiple' => false,
                                                        'required' => true,
                                                    ]) !!}
                                                </div> 
                                                <div class="col-xl-12">
                                                    {!! FormHelper::formInputText([
                                                            'name' => "company_add[$index][zipcode]",
                                                            "label"=>"Zip Code",
                                                            'id'=>"zip_code", 
                                                            "value" => $value->pincode ?? '',
                                                            "required"=>true,
                                                            'input_class'=>"ga-pincode",
                                                            'events'=>['oninput=validateZipCode(this)', 'onblur=validateZipCode(this)']])
                                                                !!}

                                                </div>
                                            </div>
                                            @endforeach
                                        @endif
                                        
                                    </div>
                                    <div class="form-group text-center mt-3">
                                        <button type="button" onclick="addMoreCompanyAdd()" class="btn btn-outline-primary">Add More Address</button>
                                    </div>
                                    
                                    <div class="text-end mt-4">
                                        <button type="submit" class="CdsTYButton-btn-primary add-btn">Save & publish</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- 4 -->
                        <div class="fade tab-pane" id="pills-verify-company" role="tabpanel" aria-labelledby="pills-verify-company-tab" tabindex="0">
                            <div class="cds-data">
                                <form id="verify-form" class="js-validate" action="{{ baseUrl('/professional-submit-profile') }}" method="post"  enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="type" value="verify">
                                    
                                    <input type="hidden" name="company_id" value="{{$user->cdsCompanyDetail->id ?? null}}">
                                    <!-- another accodian -->
                                    
                                    <h6>Proof of identify</h6>
                                    <div class="cds-formbox">
                                        <div id="pfFileUploader">
                                            <div class="CDSFeed-upload-container" id="pfMediaUpload">
                                                <div class="CDSFeed-upload-area">
                                                    <div class="CDSFeed-upload-icon">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke-width="2"/>
                                                            <polyline points="7,10 12,15 17,10" stroke-width="2"/>
                                                            <line x1="12" y1="15" x2="12" y2="3" stroke-width="2"/>
                                                        </svg>
                                                    </div>
                                                    <div class="CDSFeed-upload-text">
                                                        <h4>Drop files here or click to upload </h4>
                                                        <p>Support for JPG, PNG, PDF, DOC, DOCX, TXT, CSV, XLS, XLSX files</p>
                                                    </div>
                                                    <input type="file" class="CDSFeed-file-input" multiple accept=".jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx">
                                                </div>
                                                <div class="CDSFeed-upload-preview"></div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="pf-files" name="pf_files" value="" />
                                        <input type="hidden" id="existing-pf-files" name="existing_pf_files" value="{{ $document->where('document_type', 'proof_of_identity')->pluck('file_name')->implode(',') }}" />
                                        @foreach($document as $key => $document_value) @if($document_value->document_type == 'proof_of_identity') @foreach(explode(',',$document_value->file_name) as $value)
                                        <a href="{{baseUrl('professional/download-file?file='.$value)}}">
                                            Download
                                        </a>
                                        <br />
                                        @endforeach @endif @endforeach
                                    </div>
                                    <h6>Incorporation certificate</h6>
                                    <div class="cds-formbox">
                                        <div id="icFileUploader">
                                            <div class="CDSFeed-upload-container" id="icMediaUpload">
                                                <div class="CDSFeed-upload-area">
                                                    <div class="CDSFeed-upload-icon">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke-width="2"/>
                                                            <polyline points="7,10 12,15 17,10" stroke-width="2"/>
                                                            <line x1="12" y1="15" x2="12" y2="3" stroke-width="2"/>
                                                        </svg>
                                                    </div>
                                                    <div class="CDSFeed-upload-text">
                                                        <h4>Drop files here or click to upload</h4>
                                                        <p>Support for JPG, PNG, PDF, DOC, DOCX, TXT, CSV, XLS, XLSX files</p>
                                                    </div>
                                                    <input type="file" class="CDSFeed-file-input" multiple accept=".jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx">
                                                </div>
                                                <div class="CDSFeed-upload-preview"></div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="ic-files" name="ic_files" value="" />
                                        <input type="hidden" id="existing-ic-files" name="existing_ic_files" value="{{ $document->where('document_type', 'incorporation_certificate')->pluck('file_name')->implode(',') }}" />
                                        @foreach($document as $key => $document_value) @if($document_value->document_type == 'incorporation_certificate') @foreach(explode(',',$document_value->file_name) as $value)
                                        <a href="{{baseUrl('professional/download-file?file='.$value)}}">
                                            Download
                                        </a>
                                        <br />
                                        @endforeach @endif @endforeach
                                    </div>
                                    <h6>License</h6>
                                    <div class="cds-formbox">
                                        <div id="lcFileUploader">
                                            <div class="CDSFeed-upload-container" id="lcMediaUpload">
                                                <div class="CDSFeed-upload-area">
                                                    <div class="CDSFeed-upload-icon">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke-width="2"/>
                                                            <polyline points="7,10 12,15 17,10" stroke-width="2"/>
                                                            <line x1="12" y1="15" x2="12" y2="3" stroke-width="2"/>
                                                        </svg>
                                                    </div>
                                                    <div class="CDSFeed-upload-text">
                                                        <h4>Drop files here or click to upload</h4>
                                                        <p>Support for JPG, PNG, PDF, DOC, DOCX, TXT, CSV, XLS, XLSX files</p>
                                                    </div>
                                                    <input type="file" class="CDSFeed-file-input" multiple accept=".jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx">
                                                </div>
                                                <div class="CDSFeed-upload-preview"></div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="lc-files" name="lc_files" value="" />
                                        <input type="hidden" id="existing-lc-files" name="existing_lc_files" value="{{ $document->where('document_type', 'license')->pluck('file_name')->implode(',') }}" />

                                        @foreach($document as $key => $document_value) @if($document_value->document_type == 'license') @foreach(explode(',',$document_value->file_name) as $value)
                                        <a href="{{baseUrl('professional/download-file?file='.$value)}}">
                                            Download
                                        </a>
                                        <br />
                                        @endforeach @endif @endforeach
                                    </div>
                                    <!-- end another accodian -->
                                    <div class="text-end mt-4">
                                        <button type="submit" class="CdsTYButton-btn-primary add-btn">Save & publish</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    
                        <div class="fade tab-pane" id="licence-domain" role="tabpanel" aria-labelledby="pills-licence-tab" tabindex="0">
                            <div class="cds-data">
                                
                                    <form id="licence-form" class="js-validate" action="{{ baseUrl('/professional-submit-profile') }}" method="post">
                                        @csrf
                                        
                                        <input type="hidden" name="type" value="licence_details">
                                        <input type="hidden" name="licence_unique_id" value="{{$license_detail->unique_id ?? null}}">
                                        <div class="row">

                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                                {!! FormHelper::formSelect([
                                                    'name' => 'regulatory_country_id',
                                                    'label' => 'Regulatory Country',
                                                    'class' => 'select-flotlabel',
                                                    'id' => 'regulatory-country',
                                                    'options' => $regulatory_countries,
                                                    'value_column' => 'id',
                                                    'label_column' => 'name',
                                                    'selected' => $license_detail->regulatory_country_id ?? null,
                                                    'is_multiple' => false,
                                                    'required' => true
                                                ]) !!} 
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                                {!! FormHelper::formSelect([
                                                    'name' => 'regulatory_body_id',
                                                    'label' => 'Regulatory Body',
                                                    'class' => 'select-flotlabel',
                                                    'id' => 'regulatory-body',
                                                    'options' => $regulatory_bodies,
                                                    'value_column' => 'id',
                                                    'label_column' => 'name',
                                                    'selected' => $license_detail->regulatory_body_id ?? null,
                                                    'is_multiple' => false,
                                                    'required' => true,
                                                    'option_attributes' => function ($option) {
                                                        return ['data-prefix' => $option->license_prefix];
                                                    }
                                                ]) !!} 

                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                                {!! FormHelper::formInputText([
                                                    'name' => 'license_number',
                                                    'label' => 'License No',
                                                    'value' => $license_detail->license_number ?? '',
                                                    'prefix' => $license_detail->license_prefix ?? '',
                                                    'required' => true
                                                ]) !!}

                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6">
                                                <div class="dob-block mb-4 mb-md-0">
                                                    {!! FormHelper::formDatepicker([
                                                        'label' => 'License Start Date',
                                                        'name' => 'license_start_date',
                                                        'id' => 'license_start_date',
                                                        'value' => $license_detail->license_start_date ?? '',
                                                        'required' => true
                                                    ]) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="cds-selectbox">
                                                    {!! FormHelper::formSelect([
                                                        'name' => 'country_of_practice',
                                                        'label' => 'Country of Practice',
                                                        'options' => $countries,
                                                        'value_column' => 'id',
                                                        'label_column' => 'name',
                                                        'selected' => $license_detail->country_of_practise ?? null,
                                                        'is_multiple' => false,
                                                        'required' => true
                                                    ]) !!}
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="cds-selectbox">
                                                    {!! FormHelper::formSelect([
                                                        'name' => 'license_status',
                                                        'label' => 'License Status',
                                                        'options' => [
                                                            ['id' => 'None', 'name' => 'None'],
                                                            ['id' => 'Active', 'name' => 'Active'],
                                                            ['id' => 'In Active', 'name' => 'In Active'],
                                                            ['id' => 'Suspended', 'name' => 'Suspended'],
                                                            ['id' => 'Revoked', 'name' => 'Revoked']
                                                        ],
                                                        'value_column' => 'id',
                                                        'label_column' => 'name',
                                                        'selected' => $license_detail->license_status ?? null,
                                                        'is_multiple' => false,
                                                        'required' => true
                                                    ]) !!}
                                                </div>
                                            </div>

                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6 mb-3 mb-md-0">
                                                <label class="form-label">Do you have more licenses?</label>
                                                <div class="d-flex align-items-center gap-2">
                                                    {!! FormHelper::formCheckbox([
                                                        'name' => 'do_you_more_license', 
                                                        'value' => 1, 
                                                        'id' => 'do-you-more-license', 
                                                        'required' => false, 
                                                        'checked' => ($license_detail->do_you_more_license ?? '') === 1
                                                    ]) !!}
                                                    <label class="cds-t66-radio-labels" for="do-you-more-license">Tick if Yes</label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xl-6 cds-gender-list">
                                                <div class="form-check form-check-inline ps-0">                       
                                                    <label class="form-label">Entitled to Practice <span style="color: red;">*</span></label>
                                                    {!! FormHelper::formRadio([
                                                        'name' => 'entitled_to_practice',
                                                        'options' => [
                                                            ['value' => 'Yes', 'label' => 'Yes'],
                                                            ['value' => 'No', 'label' => 'No']
                                                        ],
                                                        'value_column' => 'value',
                                                        'label_column' => 'label',
                                                        'id' => 'entitled_to_practice',
                                                        'selected' => $license_detail->entitled_to_practice ?? null
                                                    ]) !!}
                                                </div> 
                                            </div>


                                        <div class="text-end mt-3">
                                            <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
                                        </div>
                                    </form>
                                    
                                        
                                    </div>
                                
                            </div>
                        </div>

                        <!-- Banking Details Tab -->
                        <div class="fade tab-pane" id="pills-banking-details" role="tabpanel" aria-labelledby="banking-details-tab" tabindex="0">
                            <div class="cds-data">
                                <h4 class="title">Manage your banking details for payments and withdrawals.</h4>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-university me-2"></i>
                                            Banking Details
                                            <button type="button" class="btn btn-sm btn-primary float-end" onclick="openBankingModal()">
                                                <i class="fas fa-plus"></i> Add New
                                            </button>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="bankingDetailsList">
                                            @if($bankingDetails->count() > 0)
                                                @foreach($bankingDetails as $banking)
                                                    <div class="banking-item border rounded p-3 mb-3 {{ $banking->is_active ? 'border-primary' : 'border-secondary' }}">
                                                        <div class="row">
                                                            <div class="col-md-8">
                                                                <h6 class="mb-1">
                                                                    {{ $banking->bank_name }}
                                                                    @if($banking->is_active)
                                                                        <span class="badge bg-success ms-2">Active</span>
                                                                    @endif
                                                                </h6>
                                                                <p class="mb-1"><strong>Account:</strong> {{ $banking->account_holder_name }}</p>
                                                                <p class="mb-1"><strong>Account Number:</strong> ****{{ substr($banking->account_number, -4) }}</p>
                                                                <p class="mb-1"><strong>Type:</strong> {{ ucfirst($banking->account_type) }}</p>
                                                                @if($banking->description)
                                                                    <p class="mb-1"><strong>Description:</strong> {{ $banking->description }}</p>
                                                                @endif
                                                            </div>
                                                            <div class="col-md-4 text-end">
                                                                @if(!$banking->is_active)
                                                                    <button type="button" class="btn btn-sm btn-success mb-1" onclick="setActiveBanking('{{ $banking->unique_id }}')">
                                                                        <i class="fas fa-check"></i> Set Default
                                                                    </button>
                                                                @endif
                                                                <button type="button" class="btn btn-sm btn-primary mb-1" onclick="editBanking('{{ $banking->unique_id }}')">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-danger mb-1" onclick="deleteBanking('{{ $banking->unique_id }}')">
                                                                    <i class="fas fa-trash"></i> Delete
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="text-center text-muted py-4">
                                                    <i class="fas fa-university fa-3x mb-3"></i>
                                                    <p>No banking details added yet.</p>
                                                    <button type="button" class="CdsTYButton-btn-primary" onclick="openBankingModal()">
                                                        <i class="fas fa-plus"></i> Add Your First Banking Details
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- # Nav tabs -->
            </div>
        </div>
    </div>
</div>

    <!-- Banking Details Modal -->
    <div class="modal fade" id="bankingModal" tabindex="-1" aria-labelledby="bankingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bankingModalLabel">Add Banking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="bankingForm">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" id="banking_id" name="banking_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="bank_name" class="form-label">Bank Name *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="account_holder_name" class="form-label">Account Holder Name *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="account_number" class="form-label">Account Number *</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="account_number" name="account_number" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="routing_number" class="form-label">Routing Number</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="routing_number" name="routing_number">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="swift_code" class="form-label">SWIFT Code/IFSC Code</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="swift_code" name="swift_code">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="iban" class="form-label">IBAN</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="iban" name="iban">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="account_type" class="form-label">Account Type *</label>
                                    <div class="input-group">
                                        <select class="form-control" id="account_type" name="account_type" required>
                                            <option value="">Select Account Type</option>
                                            <option value="savings">Savings</option>
                                            <option value="checking">Checking</option>
                                            <option value="business">Business</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-group js-form-message">
                                <label for="bank_address" class="form-label">Branch Name</label>
                                <div class="input-group">
                                    <textarea class="form-control" id="bank_address" name="bank_address" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="city" class="form-label">City</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="city" name="city">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="state" class="form-label">State</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="state" name="state">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="country" class="form-label">Country</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="country" name="country">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="form-group js-form-message">
                                    <label for="zip_code" class="form-label">Zip Code</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="zip_code" name="zip_code">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-group js-form-message">
                                <label for="description" class="form-label">Description</label>
                                <div class="input-group">
                                    <textarea class="form-control" id="description" name="description" rows="2" placeholder="Optional description for this banking account"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1">
                                <label class="form-check-label" for="is_active">
                                    Set as default banking account
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="CdsTYButton-btn-primary">Save Banking Details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<!-- End Content -->
@push('scripts')
    <!-- JS Implementing Plugins -->

    <script>
        // end phone no 
        var pfUploader, icUploader, lcUploader;
        var pf_files_uploaded = [];
        var ic_files_uploaded = [];
        var lc_files_uploaded = [];
        var timestamp = "{{time()}}";
        var upload_count = 0;
        var isError = 0;

        $(document).ready(function() {

            // Check if FileUploadManager is available
            if (typeof FileUploadManager === 'undefined') {
                console.error('FileUploadManager is not loaded');
                return;
            }

            // Initialize FileUploadManager for Proof of Identity
            pfUploader = new FileUploadManager('#pfMediaUpload', {
                uploadUrl: BASEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" + timestamp + "&document_type=proof_of_identity",
                maxFiles: 60,
                maxFileSize: 6 * 1024 * 1024, // 6MB
                acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
                onUploadStart: function() {
                    showLoader(); // Show loader when file starts processing
                },
                onUploadComplete: function() {
                    hideLoader(); // Hide loader when all uploads complete
                },
                onUploadSuccess: function(file, response) {
                    pf_files_uploaded.push(response.filename);
                    updateHiddenField('pf-files', pf_files_uploaded);
                },
                onUploadError: function(file, error) {
                    errorMessage(error);
                    isError = 1;
                    console.error('Upload error:', error);
                },
                onFileRemoved: function(file) {
                    if (file && file.serverName) {
                        const index = pf_files_uploaded.indexOf(file.serverName);
                        if (index > -1) {
                            pf_files_uploaded.splice(index, 1);
                            updateHiddenField('pf-files', pf_files_uploaded);
                        }
                    }
                }
            });

            // Initialize FileUploadManager for Incorporation Certificate
            icUploader = new FileUploadManager('#icMediaUpload', {
                uploadUrl: BASEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" + timestamp + "&document_type=incorporation_certificate",
                maxFiles: 60,
                maxFileSize: 6 * 1024 * 1024, // 6MB
                acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
                onUploadStart: function() {
                    showLoader(); // Show loader when file starts processing
                },
                onUploadComplete: function() {
                    hideLoader(); // Hide loader when all uploads complete
                },
                onUploadSuccess: function(file, response) {
                    ic_files_uploaded.push(response.filename);
                    updateHiddenField('ic-files', ic_files_uploaded);
                },
                onUploadError: function(file, error) {
                    errorMessage(error);
                    isError = 1;
                    console.error('Upload error:', error);
                },
                onFileRemoved: function(file) {
                    if (file && file.serverName) {
                        const index = ic_files_uploaded.indexOf(file.serverName);
                        if (index > -1) {
                            ic_files_uploaded.splice(index, 1);
                            updateHiddenField('ic-files', ic_files_uploaded);
                        }
                    }
                }
            });

            // Initialize FileUploadManager for License
            lcUploader = new FileUploadManager('#lcMediaUpload', {
                uploadUrl: BASEURL + "/upload-professional-document?_token=" + csrf_token + "&timestamp=" + timestamp + "&document_type=license",
                maxFiles: 60,
                maxFileSize: 6 * 1024 * 1024, // 6MB
                acceptedFiles: '.jpg,.jpeg,.gif,.tiff,.png,.pdf,.docx,.doc,.txt,.csv,.xls,.xlsx',
                onUploadStart: function() {
                    showLoader(); // Show loader when file starts processing
                },
                onUploadComplete: function() {
                    hideLoader(); // Hide loader when all uploads complete
                },
                onUploadSuccess: function(file, response) {
                    lc_files_uploaded.push(response.filename);
                    updateHiddenField('lc-files', lc_files_uploaded);
                },
                onUploadError: function(file, error) {
                    errorMessage(error);
                    isError = 1;
                    console.error('Upload error:', error);
                },
                onFileRemoved: function(file) {
                    if (file && file.serverName) {
                        const index = lc_files_uploaded.indexOf(file.serverName);
                        if (index > -1) {
                            lc_files_uploaded.splice(index, 1);
                            updateHiddenField('lc-files', lc_files_uploaded);
                        }
                    }
                }
            });

            // Initialize all uploaders
            pfUploader.init();
            icUploader.init();
            lcUploader.init();
            
            // Helper function to update hidden fields
            function updateHiddenField(fieldId, filesArray) {
                const fileValue = filesArray.join(",");
                $('#' + fieldId).val(fileValue);
            }

            // for about company
            // intialize description editor
            if ($("#about").val() !== undefined) {
                initEditor("about");
            }

            
            
            initSelect();

            dobDatePicker("date_of_birth");

            initPastDatePicker("license_start_date");

            

            $("#company-form").submit(function(e) {
                
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                var url = $("#company-form").attr('action');
                
                var is_valid = formValidation("company-form");
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
                            window.location.reload();
                        } else {
                            validation(response.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });

            });

            $("#company-description").submit(function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                var url = $("#company-description").attr('action');
                console.log(formData);
                var is_valid = formValidation("company-description");
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
                        } else {
                            validation(response.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });

            });

            $("#contact-form").submit(function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                var url = $("#contact-form").attr('action');
                
                var is_valid = formValidation("contact-form");
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
                            window.location.reload();
                        } else {
                            validation(response.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });

            });

            $("#category-description").submit(function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                var url = $("#category-description").attr('action');
                console.log(formData);
                var is_valid = formValidation("category-description");
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
                        } else {
                            validation(response.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });

            });

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
            
        
            $("#licence-form").submit(function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                var url = $("#licence-form").attr('action');
                var is_valid = formValidation("licence-form");
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
                            window.location.reload();
                        } else {
                            validation(response.message);
                        }
                    },
                    error: function() {
                        internalError();
                    }
                });
            });
            // verify proof form
            $("#verify-form").submit(function(e) {
                e.preventDefault();

                // Check if there are any files still uploading
                const pfUploading = pfUploader.getFileCountByStatus('uploading');
                const icUploading = icUploader.getFileCountByStatus('uploading');
                const lcUploading = lcUploader.getFileCountByStatus('uploading');
                
                if (pfUploading > 0 || icUploading > 0 || lcUploading > 0) {
                    errorMessage("Please wait for all files to finish uploading");
            return;
        }

                // Check if any files are selected or if there are existing documents
                const existingDocuments = $('.cds-formbox a[href*="download-file"]').length;
                
                if (pf_files_uploaded.length === 0 && ic_files_uploaded.length === 0 && lc_files_uploaded.length === 0 && existingDocuments === 0) {
                    errorMessage('Please select at least one document');
                    return;
                }
                
                // All files are uploaded, submit the form
                submitForm();
            });
        });

        function submitForm() {
            
            // If no new files were uploaded, set the existing document values
            if ($('#pf-files').val() === '' && $('#ic-files').val() === '' && $('#lc-files').val() === '') {
                const existingPfFiles = $('#existing-pf-files').val();
                const existingIcFiles = $('#existing-ic-files').val();
                const existingLcFiles = $('#existing-lc-files').val();
                
                if (existingPfFiles) {
                    $('#pf-files').val(existingPfFiles);
                }
                if (existingIcFiles) {
                    $('#ic-files').val(existingIcFiles);
                }
                if (existingLcFiles) {
                    $('#lc-files').val(existingLcFiles);
                }
            } else {
                // Set the uploaded files
                if (pf_files_uploaded.length > 0) {
                    $('#pf-files').val(pf_files_uploaded.join(','));
                }
                if (ic_files_uploaded.length > 0) {
                    $('#ic-files').val(ic_files_uploaded.join(','));
                }
                if (lc_files_uploaded.length > 0) {
                    $('#lc-files').val(lc_files_uploaded.join(','));
                }
            }
            
            var formData = new FormData($("#verify-form")[0]);
            var url = $("#verify-form").attr('action');
            
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
                        window.location.reload();
                    } else {
                        validation(response.message);
                    }
                },
                error: function() {
                    hideLoader();
                    internalError();
                }
            });
        }

        function addMoreCompanyAdd(){
            $.ajax({
                url: "{{ baseUrl('more-company-address') }}",
                type: "get",
                dataType: "json",
                beforeSend: function() {
                },
                success: function(response) {
                    $(".company-address").append(response.contents);
                    initGoogleAddress();
                    initFloatingLabel();
                    initSelect();
                },
                error: function() {
                    internalError();
                }
            });
        }

        // Banking Details JavaScript for Professional Edit Profile
        let currentBankingId = null;

        function openBankingModal(bankingId = null) {
            console.log('Opening modal with bankingId:', bankingId);
            currentBankingId = bankingId;
            
            if (bankingId) {
                // Edit mode - load existing data
                console.log('Edit mode - loading data for ID:', bankingId);
                resetBankingForm();
                loadBankingData(bankingId);
                $('#bankingModalLabel').text('Edit Banking Details');
            } else {
                // Add mode
                console.log('Add mode');
                resetBankingForm();
                currentBankingId = null; // Ensure it's null for add mode
                $('#bankingModalLabel').text('Add Banking Details');
            }
            
            $('#bankingModal').modal('show');
        }

        function resetBankingForm() {
            $('#bankingForm')[0].reset();
            $('#banking_id').val('');
            console.log('Form reset, currentBankingId:', currentBankingId);
        }

        function loadBankingData(bankingId) {
            // For security, we'll need to get full data via AJAX
            $.ajax({
                url: BASEURL + '/banking-details/get/' + bankingId,
                type: 'GET',
                success: function(response) {
                    if (response.status) {
                        const data = response.data;
                        $('#bank_name').val(data.bank_name);
                        $('#account_holder_name').val(data.account_holder_name);
                        $('#account_number').val(data.account_number);
                        $('#routing_number').val(data.routing_number);
                        $('#swift_code').val(data.swift_code);
                        $('#iban').val(data.iban);
                        $('#bank_address').val(data.bank_address);
                        $('#city').val(data.city);
                        $('#state').val(data.state);
                        $('#country').val(data.country);
                        $('#zip_code').val(data.zip_code);
                        $('#account_type').val(data.account_type);

                        $('#description').val(data.description);
                        $('#is_active').prop('checked', data.is_active);
                    }
                }
            });
        }

        $('#bankingForm').submit(function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            let url;
            
                    console.log('Form submission - currentBankingId:', currentBankingId);
        
        if (currentBankingId) {
            // Edit mode
            url = BASEURL + '/banking-details/update/' + currentBankingId;
            console.log('Using update URL:', url);
        } else {
            // Add mode
            url = BASEURL + '/banking-details/save';
            console.log('Using save URL:', url);
        }
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status) {
                        successMessage(response.message);
                        $('#bankingModal').modal('hide');
                        location.reload(); // Refresh to show updated data
                    } else {
                        validation(response.message);
                    }
                },
                error: function() {
                    hideLoader();
                    internalError();
                }
            });
        });

        function setActiveBanking(bankingId) {
            if (confirm('Are you sure you want to set this as your default banking account?')) {
                $.ajax({
                    url: BASEURL + '/banking-details/set-active/' + bankingId,
                    type: 'POST',
                    data: {
                        _token: csrf_token
                    },
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        hideLoader();
                        if (response.status) {
                            successMessage(response.message);
                            location.reload();
                        } else {
                            errorMessage(response.message);
                        }
                    },
                    error: function() {
                        hideLoader();
                        internalError();
                    }
                });
            }
        }

        function deleteBanking(bankingId) {
            if (confirm('Are you sure you want to delete this banking account? This action cannot be undone.')) {
                $.ajax({
                    url: BASEURL + '/banking-details/delete/' + bankingId,
                    type: 'DELETE',
                    data: {
                        _token: csrf_token
                    },
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        hideLoader();
                        if (response.status) {
                            successMessage(response.message);
                            location.reload();
                        } else {
                            errorMessage(response.message);
                        }
                    },
                    error: function() {
                        hideLoader();
                        internalError();
                    }
                });
            }
        }

        function editBanking(bankingId) {
            openBankingModal(bankingId);
        }

    </script>
@endpush
