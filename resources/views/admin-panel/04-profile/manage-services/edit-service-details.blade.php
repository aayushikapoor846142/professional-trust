<!-- <div class="CDSDashboardProfessionalServices-list-sidebar CDSDashboardProfessionalServices-list-active" id="sidebar"> -->
    <div class="CDSDashboardProfessionalServices-list-sidebar-header">
        <h3 class="CDSDashboardProfessionalServices-list-sidebar-title">Additional Settings </h3>
        <button class="CDSDashboardProfessionalServices-list-close-button" onclick="closeSidebar()">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M14 6L6 14M6 6l8 8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>
    
    <div class="CDSDashboardProfessionalServices-list-tabs">
        <button class="CDSDashboardProfessionalServices-list-tab CDSDashboardProfessionalServices-list-active" onclick="switchTab(this, 'fees')">Fees Detail</button>
        <button class="CDSDashboardProfessionalServices-list-tab" onclick="switchTab(this, 'additional')">Additional Detail</button>
    </div>

    <div class="CDSDashboardProfessionalServices-list-sidebar-body">
        <div id="fees-content">
            <form class="edit-sub-service-form" action="{{ baseUrl('manage-services/update-sub-service-types/'.$record->unique_id) }}" method="post">
                @csrf
                <input type="hidden" name="type" value="form">
                <!-- <div class="CDSDashboardProfessionalServices-list-form-group">
                    <label class="CDSDashboardProfessionalServices-list-form-label">Description</label>
                    <textarea class="CDSDashboardProfessionalServices-list-form-input CDSDashboardProfessionalServices-list-form-textarea" placeholder="Enter service description..."></textarea>
                    <p class="CDSDashboardProfessionalServices-list-form-hint">Provide a clear description of this service for clients</p>
                </div>

                <div class="CDSDashboardProfessionalServices-list-form-group">
                    <label class="CDSDashboardProfessionalServices-list-form-label">Assessment Form</label>
                    <div style="padding: 1rem; background: var(--bg); border: 1px solid var(--border); border-radius: 6px;">
                        <p class="CDSDashboardProfessionalServices-list-form-hint" style="margin: 0;">
                            💡 Setting fees to $0 will mark this as a free consultation
                        </p>
                        <p class="CDSDashboardProfessionalServices-list-form-hint" style="margin: 0.5rem 0 0 0;">
                            No forms available. 
                            <a href="#" style="color: var(--primary); font-weight: 500; text-decoration: none;">
                                Generate new form →
                            </a>
                        </p>
                    </div>
                </div>

                <div class="CDSDashboardProfessionalServices-list-setting-row">
                    <div class="CDSDashboardProfessionalServices-list-setting-info">
                        <div class="CDSDashboardProfessionalServices-list-setting-label">Professional Fees</div>
                        <div class="CDSDashboardProfessionalServices-list-setting-value">$500</div>
                    </div>
                    <div class="CDSDashboardProfessionalServices-list-toggle-container">
                        <span style="font-size: 0.8125rem; color: var(--text-muted);">To be decided</span>
                        <label class="CDSDashboardProfessionalServices-list-toggle">
                            <input type="checkbox">
                            <div class="CDSDashboardProfessionalServices-list-toggle-track">
                                <div class="CDSDashboardProfessionalServices-list-toggle-thumb"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="CDSDashboardProfessionalServices-list-setting-row">
                    <div class="CDSDashboardProfessionalServices-list-setting-info">
                        <div class="CDSDashboardProfessionalServices-list-setting-label">Consultancy Fees</div>
                        <div class="CDSDashboardProfessionalServices-list-setting-value">$500</div>
                    </div>
                </div> -->
                <div class="row">
                            
                            <div class="col-xl-6">
                                <label>Professional Fees</label>
                            </div>
                            <div class="col-xl-6">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    
                                    <div class="cds-fees">
                                        {!! FormHelper::formInputText([
                                        'name'=>"professional_fees",
                                        'id'=>"professional_fees",
                                        "label"=> "Professional Fees",
                                        "input_class" => "professional_fees",
                                        "required"=>true,
                                        "value" => $record->professional_fees ?? '',
                                        'events'=>['oninput=validateNumber(this)']
                                        ])!!}
                                    </div>
                                    <div class="cds-tbd">
                                        <label>To be decided later</label><br>
                                        <label class="CDSMainsite-switch">
                                        <input type="checkbox" name="tbd" value="1" class="cds-tbd-checkbox" {{ $record->tbd == 1 ? 'checked' : '' }}>
                                            <span class="CDSMainsite-switch-button-slider CDSMainsite-round"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="cds-price-range" style="@if($record->tbd == 0) display:none @endif">
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        <div class="cds-fees">
                                            {!! FormHelper::formInputText([
                                            'name'=>"minimum_fees",
                                            'id'=>"min_fees",
                                            'input_class' => "min_fees",
                                            "label"=> "Min Fees",
                                            "disabled" => 'disabled',
                                            "value" => $record->minimum_fees ?? '',
                                            "min" => $record->minimum_fees,
                                            'events'=>['oninput=validateNumber(this)']
                                            ])!!}
                                        </div>
                                        <div class="cds-fees">
                                            {!! FormHelper::formInputText([
                                            'name'=>"maximum_fees",
                                            'id'=>"max_fees",
                                            'input_class' => "max_fees",
                                            "label"=> "Max Fees",
                                            "value" => $record->maximum_fees ?? '',
                                            'events'=>['oninput=validateNumber(this)']
                                            ])!!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-6">
                                <label>Consultancy Fees</label>
                                <div>If you add fees as 0 the consultation will be free</div>
                            </div>

                            <div class="col-xl-6">
                                {!! FormHelper::formInputText([
                                'name'=>"consultancy_fees",
                                'id'=>"consultancy_fees",
                                "label"=> "Consultancy Fees",
                                "required"=>true,
                                "value" => $record->consultancy_fees ?? '',
                                'events'=>['oninput=validatePhoneNumber(this)']
                                ])!!}
                            </div>
                        </div>
                <button class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-primary" style="width: 100%; margin-top: 1.5rem;">
                    Save Changes
                </button>
            </form>
        </div>

        <div id="additional-content" style="display: none;">
            <!-- <div class="CDSDashboardProfessionalServices-list-form-group">
                <label class="CDSDashboardProfessionalServices-list-form-label">Additional Information</label>
                <textarea class="CDSDashboardProfessionalServices-list-form-input CDSDashboardProfessionalServices-list-form-textarea" placeholder="Enter additional details..." style="min-height: 150px;"></textarea>
                <p class="CDSDashboardProfessionalServices-list-form-hint">Add any additional information relevant to this service</p>
            </div> -->
            <form class="form-control edit-sub-service-form" action="{{ baseUrl('manage-services/update-sub-service-types/'.$record->unique_id) }}" method="post">
            @csrf
                <input type="hidden" name="type" value="additional-detail">
                <div class="row">
                    <div class="col-xl-12">
                            {!! FormHelper::formTextarea([
                                'name'=>"description",
                                'id'=>"description",
                                "label"=> "Description",
                                "input_class" => "description",
                                "value" => $record->description ?? '',
                                "required"=>false,
                            ])!!}
                        </div>
                    <div class="col-xl-12">
                            <label>Assesment Form</label>
                            <div>If you add fees as 0 the consultation will be free</div>
                        </div>
                        <div class="col-xl-12">
                            <div class="cds-assessment-list">
                                @if($forms->isEmpty())
                                    <span class="text-danger">You don't have any form to add generate</span><a href="javascript:;">Click here</a>
                                @else
                                    @foreach($forms as $form)
                                        <div class="cds-assessment-row">
                                            <div class="cds-assessment-col">
                                                <div class="cds-form-container mb-2">
                                                    <div class="radio-group ">
                                                        <div class="form-check">
                                                            <input type="radio" name="form_id" id="form-id-{{ $form->id }}-{{$record->unique_id}}" value="{{ $form->id }}" class="radio-input required" {{ $record->form_id == $form->id ? 'checked' : '' }}>
                                                            <label for="form-id-{{ $form->id }}-{{$record->unique_id}}"> {{ $form->name }} </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="cds-assessment-col">
                                                <a class="btn btn-sm btn-primary" target="_blank" href="{{ baseUrl('my-services/view-assesment/'.$form->unique_id) }}">
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            
                        </div>
                        <div class="col-xl-12">
                            <label>Select Documents</label>
                        </div>
                        <div class="col-xl-12">
                            @if($documents->isEmpty())
                                <span class="text-danger">You don't have any documents to add please</span><a href="{{baseUrl('document-folders/add')}}">Click here</a>

                            @else
                                <div class="multi-selectbox">
                                    {!! FormHelper::formSelect([
                                        'name' => 'document[]',
                                        'label' => 'Select Document Folder',
                                        'select_class' => 'select2-input cds-multiselect add-multi',
                                        'id' => 'documents-folders',
                                        'options' => $documents,
                                        'value_column' => 'id',
                                        'label_column' => 'name',
                                        'selected' => explode(',',$record->document_folders) ?? [],
                                        'is_multiple' => true,
                                        'required' => false,
                                    ]) !!}
                                </div>
                            @endif
                        </div>
                </div>
                <button class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-primary" style="width: 100%;">
                    Save Information
                </button>
            </form>
        </div>
    </div>
<!-- </div> -->


<script>
     function switchTab(tabElement, tabName) {
        document.querySelectorAll('.CDSDashboardProfessionalServices-list-tab').forEach(tab => {
            tab.classList.remove('CDSDashboardProfessionalServices-list-active');
        });
        
        tabElement.classList.add('CDSDashboardProfessionalServices-list-active');
        
        document.getElementById('fees-content').style.display = 'none';
        document.getElementById('additional-content').style.display = 'none';
        
        if (tabName === 'fees') {
            document.getElementById('fees-content').style.display = 'block';
        } else {
            document.getElementById('additional-content').style.display = 'block';
        }
    }

    $(document).on("submit", ".edit-sub-service-form", function(e) {
        e.preventDefault();

        var $form = $(this); // Get the current form
        var formData = $form.serialize(); // Serialize current form data
        var actionUrl = $form.attr("action"); // Get action URL from the form
        
        $.ajax({
            url: actionUrl,
            type: "post",
            data: formData,
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
                hideLoader();
                internalError();
            }
        });
    });
    $('.cds-tbd-checkbox').on('change', function () {
        if ($(this).is(':checked')) {
            $('.cds-price-range').show();
        } else {
            $('.cds-price-range').hide();
        }
    });
</script>