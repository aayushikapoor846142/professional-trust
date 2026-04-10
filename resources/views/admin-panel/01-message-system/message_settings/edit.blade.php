@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('settings') !!}
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
----------
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 @if(!empty($privacySettings))
                    <form id="form" class="js-validate" action="{{ baseUrl('/message-settings/update') }}" method="post">
                        @csrf
                        <input type="hidden" name="module_settings" value="{{$record->unique_id ?? ''}}">
                        <div class="row">
                            
                                @if(!empty($privacySettings))
                                    @foreach($privacySettings as $key => $settings)
                                        <div class="col-md-12 mb-2">
                                            <input type="hidden" name="settings[{{ $key }}][privacy_option_id]" value="{{ $settings->id }}">
                                            @if($settings->filed_type == "radio")
                                                {!! FormHelper::formRadio([
                                                    'name' => "settings[$key][value]",
                                                    'label' => $settings->action_label,
                                                    'radio_class' => '',                        
                                                    'options' => json_decode($settings->options,true),
                                                    'value_column' => 'value',
                                                    'label_column' => 'label',
                                                    'id' => 'radio-{{$settings->unique_id}}',
                                                    'value' => 'label',
                                                    'selected' =>  !empty($settings->userPrivacy) && $settings->userPrivacy->privacy_option_value != ""
                                                            ? $settings->userPrivacy->privacy_option_value
                                                            : '',
                                                    ]) 
                                                !!}
                                            @endif
                                            @if($settings->filed_type == "select")
                                            <div class="cds-selectbox">
                                                @php
                                                    if($settings->allow_to_select == "multiple"){
                                                    $selected = !empty($settings->userPrivacy) && $settings->userPrivacy->privacy_option_value != ""
                                                            ? explode(',', $settings->userPrivacy->privacy_option_value)
                                                            : [];

                                                    }

                                                    if($settings->allow_to_select == "single"){
                                                        $selected = !empty($settings->userPrivacy) && $settings->userPrivacy->privacy_option_value != ""
                                                            ? $settings->userPrivacy->privacy_option_value
                                                            : '';
                                                    }
                                                

                                                @endphp
                                                {!! FormHelper::formSelect([
                                                    'name' => $settings->allow_to_select == "multiple" ? "settings[$key][value][]" : "settings[$key][value]",
                                                    'select_class' => 'select2-input cds-multiselect add-multi',
                                                    'label' => $settings->action_label,                        
                                                    'options' => json_decode($settings->options,true),
                                                    'value_column' => 'value',
                                                    'label_column' => 'label',
                                                    'id' => 'select-' . $settings->unique_id,
                                                    'value' => 'label',
                                                    'is_multiple' => $settings->allow_to_select == "multiple" ? true : false,
                                                    'selected' => $selected
                                                    ]) 
                                                !!}
                                                </div>
                                            @endif
                                            @if($settings->filed_type == "checkbox")

                                                {!! FormHelper::formMultipleCheckbox([
                                                    'id' => 'check-' . $settings->unique_id,
                                                    'name'=> "settings[$key][value][]",
                                                    'label' => $settings->action_label,
                                                    'options' => json_decode($settings->options,true),
                                                    'value_column' => 'value',
                                                    'label_column' => 'label',
                                                    'selected' => !empty($settings->userPrivacy) && $settings->userPrivacy->privacy_option_value != ""
                                                            ? explode(',', $settings->userPrivacy->privacy_option_value)
                                                            : [],
                                                ]) !!}
                                            @endif
                                        </div>
                                        @if($settings->description != '')
                                            <div class="col-md-12">
                                                <label>Description:</label></br>
                                                {{$settings->description}}
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            
                        </div>
                        <div class="text-end mt-3">
                        <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                    </div>
                    </form>
                    @else
                        <h6> Settings not available. Please contact the admin.</h6>
                    @endif
			</div>
	
	</div>
  </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-12">
            <div class="cds-ty-dashboard-box">
                <div class="cds-ty-dashboard-box-header">
                </div>
                <div class="cds-ty-dashboard-box-body cds-form-container">
                   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<!-- End Content -->
@section('javascript')
<script>
    $(document).ready(function() {
      
        $("#form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("form");
            if (!is_valid) {
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $("#form").attr('action');
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
                        redirect(response.redirect_back);
                    } else {
                        validation(response.message);
                    }
                },
                error: function() {
                    internalError();
                }
            });

        });
    });
</script>
@endsection