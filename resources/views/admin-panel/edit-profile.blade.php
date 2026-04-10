@extends('admin-panel.layouts.app')
@section('content')

 <div class="ch-action">
                    <a href="{{ baseUrl('/') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div><div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
<form method="post" id="form" class="js-validate mt-3" action="{{ baseUrl('/submit-profile') }}">
                @csrf
                <div class="p-0 cds-ty-dashboard-box-body"> <img src="assets/images/img2.jpg" alt="" class="img-fluid">
                    <div class="row align-items-center">
                        <div class="col-lg-4 order-1 order-lg-2 mt-n3" style="margin-top: -3.5rem;">
                            <div class="mt-n5">
                                <label for="profileImage">
                                    <div class="d-flex align-items-center justify-content-center mb-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle linear-gradient"
                                            style="width: 110px; height: 110px;cursor: pointer;">
                                            <div class="d-flex align-items-center border border-4 border-white justify-content-center rounded-circle overflow-hidden"
                                                style="width: 100px; height: 100px;">
                                                <input type="file" name="profile_image" class="d-none" id="profileImage"
                                                    accept="image/*" />
                                                <img id="imagePreview"
                                                    src="{{ $user->profile_image ? userDirUrl($user->profile_image, 't') : 'assets/images/default.jpg' }}"
                                                    alt="" class="h-100 w-100">
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- Card -->
                    <div class="card p-4">
                        <!-- Body -->
                        <div class="cds-ty-dashboard-box-body">
                            <!-- Form Group -->
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    {!! FormHelper::formInputText([
                                    'name' => "first_name",
                                    "label" => "First Name",
                                    "required" => true,
                                    "value" => $user->first_name,
                                    'events' => [
                                    'oninput' => 'validateName(this)',
                                    'onblur' => 'validateName(this)'
                                    ]
                                    ]) !!}
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    {!! FormHelper::formInputText([
                                    'name' => "last_name",
                                    "label" => "Last Name",
                                    "required" => true,
                                    "value" => $user->last_name,
                                    'events' => [
                                    'oninput' => 'validateName(this)',
                                    'onblur' => 'validateName(this)'
                                    ]
                                    ]) !!}
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    {!! FormHelper::formInputEmail([
                                    'name' => "email",
                                    "readonly"=>true,
                                    "label" => "Email Address",
                                    "value" => $user->email,
                                    "required" => true,
                                    'events' => ['oninput=this.value.replace(/\s+/g, "")']
                                    ]) !!}
                                </div>

                                <div class="col-md-6 col-sm-12 mb-3">
                                    <!-- custome phone no -->
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
                                     <div class="col-md-6 col-sm-12 mb-3">
                                           {!! FormHelper::formDatepicker([
                                            'label' => 'Date of Birth',
                                            'name' => 'date_of_birth',
                                            'id' => 'date_of_birth',
                                            'class' => 'select2-input ga-country',
                                            'value' => $user->date_of_birth ?? '',
                                            'required' => true,
                                            ]) !!}
                                     </div>
                                     <div class="col-md-6 col-sm-12 mb-3">
                                    {!! FormHelper::formRadio([
                                                    'name' => 'gender',
                                                    'required' => true,
                                                    'options' => FormHelper::selectThreeGender(),
                                                    'value_column' => 'value',
                                                    'label_column' => 'label',
                                                    'selected' => $user->gender ?? ''
                                                    ]) !!}
                                     </div>


                                     <div class="col-md-6 col-sm-12 mb-3">
                                          {!! FormHelper::formSelect([
                                            'name' => 'timezone',
                                            'id' => 'timezone',
                                            'label' => 'Select Timezone',
                                            'class' => 'select2-input ga-country',       
                                            'options' => FormHelper::getTimezone(),
                                            'value_column' => 'label',
                                            'label_column' => 'value',
                                            'selected' =>$user->timezone ?? '',
                                            'is_multiple' => false,
                                            'required' => true,
                                            ]) !!}

                                    <span class="error-text text-danger" id="error-text-timezone"></span>
                                     </div>

                                     <div class="col-md-6 col-sm-12 mb-3">
                                           {!! FormHelper::formSelect([
                                            'name' => 'languages[]',
                                            'label' => 'Select Language',
                                            'class' => 'select2-input cds-multiselect add-multi',
                                            'select_class' => 'ga-country',
                                            'id' => 'languages',
                                            'options' => $languages,
                                            'value_column' => 'name',
                                            'label_column' => 'name',
                                            'is_multiple' => true,
                                            'selected' => explode(',', $user->userDetail->languages_known ?? ''),
                                            'required' => false,
                                        ]) !!}
                                     </div>
                            </div>
                        </div>
                        <div class="text-start">
                            <button type="submit" class="CdsTYButton-btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
                </div>
            </form> <!-- Card -->
            @if(isset($professionalList) && $professionalList->id > 0)
            <div class="card mt-5">
                <div class="cds-ty-dashboard-box-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0"><i class="fa-table fas me-1"></i>Company Details </h5>
                        {{-- <a href="{{ baseUrl('/') }}" class="btn
                        btn-primary">Back</a> --}}
                    </div>
                </div>
                <!-- Body -->
                <div class="cds-ty-dashboard-box-body">
                    <form id="form2" class="js-validate mt-3" action="{{ baseUrl('edit-professionals/' . $professionalList->unique_id) }}" method="post">
                        @csrf
                        <div class="row justify-content-md-between">
                            <div class="col-md-12 mb-3">
                                <!-- Form Group -->
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">View Profile Url</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="view_profile_url"
                                                id="view_profile_url" placeholder="Profile url"
                                                value="{{ $professionalList->view_profile_url }}">
                                        </div>
                                    </div>
                                </div>
                                <!-- End Form Group -->
                            </div>
                            <!-- div end -->

                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">College Id</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="college_id" id="college_id"
                                                value="{{ $professionalList->college_id }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Name</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="name" id="name"
                                                value="{{ $professionalList->name }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Company</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="company" id="company"
                                                value="{{ $professionalList->company }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Company Type</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="company_type"
                                                id="company_type" value="{{ $professionalList->company_type }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Entitled To Practise</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="entitled_to_practise"
                                                id="entitled_to_practise"
                                                value="{{ $professionalList->entitled_to_practise }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Entitled To Practise College
                                        Id</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control"
                                                name="entitled_to_practis_college_id"
                                                id="entitled_to_practis_college_id"
                                                value="{{ $professionalList->entitled_to_practis_college_id }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Type</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="type" id="type"
                                                value="{{ $professionalList->type }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Suspension Revocation
                                        History</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="suspension_revocation_history"
                                                id="suspension_revocation_history"
                                                value="{{ $professionalList->suspension_revocation_history }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Employment Company</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="employment_company"
                                                id="employment_company"
                                                value="{{ $professionalList->employment_company }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Employment Start
                                        Date</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="employment_startdate"
                                                id="employment_startdate"
                                                value="{{ $professionalList->employment_startdate }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Employment Country</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="employment_country"
                                                id="employment_country"
                                                value="{{ $professionalList->employment_country }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Employment State</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="employment_state"
                                                id="employment_state"
                                                value="{{ $professionalList->employment_state }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Employment City</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="employment_city"
                                                id="employment_city" value="{{ $professionalList->employment_city }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Employment Email</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="employment_email"
                                                id="employment_email"
                                                value="{{ $professionalList->employment_email }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Employment Phone</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <input type="text" class="form-control" name="employment_phone"
                                                id="employment_phone"
                                                value="{{ $professionalList->employment_phone }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">Agents Info</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <textarea type="text" class="form-control" name="agentsinfo"
                                                id="agentsinfo">{{ $professionalList->agentsinfo }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">License History
                                        Class</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <textarea type="text" class="form-control" name="license_historyclass"
                                                id="license_historyclass">{{ $professionalList->license_historyclass }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">License History Start
                                        Date</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <textarea type="text" class="form-control" name="license_historystartdate"
                                                id="license_historystartdate">{{ $professionalList->license_historystartdate }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">License History Expiry
                                        Date</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <textarea type="text" class="form-control" name="license_historyexpiry_date"
                                                id="license_historyexpiry_date">{{ $professionalList->license_historyexpiry_date }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="row form-group">
                                    <label class="col-form-label col-sm-5 input-label">License History
                                        Status</label>
                                    <div class="col-sm-7">
                                        <div class="js-form-message">
                                            <textarea type="text" class="form-control" name="license_history_status"
                                                id="license_history_status">{{ $professionalList->license_history_status }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Banking Details Section -->
                        <div class="card mt-4">
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

                        <div class="form-group text-start">
                            <button type="submit" class="CdsTYButton-btn-primary add-btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
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
@section('javascript')
<script>
    document.getElementById('profileImage').addEventListener('change', function (event) {
        const [file] = this.files;
        if (file) {
            const imgPreview = document.getElementById('imagePreview');
            imgPreview.src = URL.createObjectURL(file);
        }
    });
    $(document).ready(function () {

        initSelect();

        $('#logoUploader').on('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    $('#logoImg').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

          dobDatePicker("date_of_birth");

        $("#form").submit(function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#form").attr('action');
            console.log(formData);
            var is_valid = formValidation("form");
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
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        redirect(response.redirect_back);
                    } else {
                        validation(response.message);
                    }
                },
                error: function () {
                    internalError();
                }
            });

        });

        $("#form2").submit(function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var url = $("#form2").attr('action');
            console.log(formData);
            var is_valid = formValidation("form2");
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
                beforeSend: function () {
                    showLoader();
                },
                success: function (response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        redirect(response.redirect_back);
                    } else {
                        validation(response.message);
                    }
                },
                error: function () {
                    internalError();
                }
            });

        });
    });

</script>

<script>
    $(document).ready(function () {
        $(document).on("click", function (event) {
            if (!$(event.target).closest(".cds-phonedropdown").length) {
                $("#dropdownMenu").addClass("hidden");
            }
        });
    });

    // Banking Details JavaScript
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
@endsection
