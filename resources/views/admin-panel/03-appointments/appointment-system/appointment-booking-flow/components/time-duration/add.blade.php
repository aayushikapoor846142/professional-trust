@extends('components.custom-popup',['modalTitle'=>'Time Duration'])
@section('custom-popup-content')
<div class="cds-form-container mb-0">
    
@php
    $hasDurations = count($durations);
@endphp

    <div class="container">
        <div class="row">
            
            <div class="col-xl-12">
                <div class="card rounded">
                    <div class="card-body">
                        <div class="cds-form-container mb-0">
                            
                            <form id="add-duration-form" class="js-validate mt-3" action="{{ baseUrl('time-duration/save') }}" method="post" style="{{ $hasDurations ? 'display: none;' : '' }}">
                                <div class="cds-form-header">
                                    <h5>Add Time Duration</h5>
                                </div>
                                <input type="hidden" name="duration_booking_flow_id" id="duration_booking_flow_id" value="{{$appointmentBookingFlow->unique_id ?? ''}}">
                                @csrf
                                <div class="row cdsTimeduration">
                                    <div class="col-xl-12">
                                        {!! FormHelper::formInputText(['name'=>"name","id" => "name","label"=>"Enter Name","required"=>true]) !!}
                                    </div>
                                    <div class="col-xl-12">
                                        <div class="cds-selectbox">
                                            <label class="col-form-label non-floting">Appointments Gap (Minutes)(Gap between 2 Appointments)</label>
                                            {!! FormHelper::formInputNumber([
                                                'name' => 'break_time',
                                                'id' => 'break_time',
                                                'label' => 'Appointments Gap (Minutes)(Gap between 2 Appointments)',
                                                'class' => 'select2-input cds-left',
                                                'input_class' => 'ga-pincode',
                                                "required" => true,
                                                'events'=>['oninput=validateDigit(this)', 'onblur=validateDigit(this)']
                                            ]) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        {!! FormHelper::formInputNumber(['name'=>"duration","id" => "duration","label"=>"Enter Duration","required"=>true,
                                            'events'=>['oninput=validateDigit(this)', 'onblur=validateDigit(this)']
                                        ]) !!}
                                    </div>
                                    <div class="col-xl-12">
                                    {!! FormHelper::formSelect([
                                                'name' => 'type',
                                                'label' => 'Select Type',
                                                'class' => 'select2-input ga-country',                                    
                                                'options' => FormHelper::selectTimeDurationType(),
                                                'value_column' => 'value',
                                                'label_column' => 'label',
                                                'selected' => old('type') ?? null,
                                                'is_multiple' => false
                                            ]) !!}
                                    </div>
                                </div>
                                <div class="CdsDashboardCustomPopup-modal-submit-section">
                                    <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>

                                    <button type="button" class="btn btn-dark cancel-duration-btn">Cancel</button>
                                </div>
                            </form>

                            @if(!empty($durations))
                            <form id="select-duration-form" class="js-validate" action="{{baseUrl('appointments/appointment-booking-flow/save-time-duration')}}" method="post" style="{{ $hasDurations ? '' : 'display: none;' }}">
                                @csrf
                                <div class="cds-form-header">
                                    <h5>Choose Time Duration</h5>
                                </div>
                                <input type="hidden" name="duration_booking_flow_id"  value="{{$appointmentBookingFlow->unique_id ?? ''}}">
                                <div class="row">                               
                                    <div class="col-xl-12" id="radioContainer">
                                        <span class="mb-2 d-block">Time Duration :</span>
                                        
                                        @php 
                                        $selected = $appointmentBookingFlow->time_duration_id ?? ''
                                        @endphp
                                        @include('admin-panel.03-appointments.appointment-system.appointment-booking-flow.components.time-duration.select', ['durations' => $durations, 'selected' => $selected])
                                    </div>
                                </div>                           
                                <div class="CdsDashboardCustomPopup-modal-submit-section">
                                    <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
                                    <a href="{{baseUrl('appointments/settings')}}" class="btn add-CdsTYButton-btn-primary">Time Durations</a>

                                    <button type="button" class="btn btn-dark add-duration-btn" style="{{ $hasDurations ? '' : 'display: none;' }}">
                                        Add Duration
                                    </button>
                                </div>
                            </form>
                            @else
                            <div class="CdsDashboardCustomPopup-modal-submit-section">
                                <button type="button" class="btn btn-dark add-duration-btn" style="{{ $hasDurations ? '' : 'display: none;' }}">
                                    Add Duration
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

<!-- End Content -->
<script>
    $(document).ready(function() {
        $('.add-duration-btn').on('click', function () {
            $('#add-duration-form').show();
            $('#select-duration-form').hide();
        });
        $('.cancel-duration-btn').on('click', function () {
            $('#add-duration-form').hide();
            $('#select-duration-form').show();
        });
        $("#add-duration-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("add-duration-form");
            if (!is_valid) return false;

            var formData = new FormData($(this)[0]);
            var url = $("#add-duration-form").attr('action');

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
                        $("#select-duration-form").show();
                        $("#add-duration-form").hide();
                        $('#radioContainer').html(response.html);
                    } else {
                        if (response.error_type == 'validation') {
                            validation(response.message);
                        } else {
                            errorMessage(response.message);
                        }
                    }
                },
                error: function() {
                    internalError();
                }
            });
        });
        $("#select-duration-form").submit(function(e) {
       
            e.preventDefault();
            var is_valid = formValidation("select-duration-form");
            if (!is_valid) return false;

            var formData = new FormData($(this)[0]);
            var url = $("#select-duration-form").attr('action');

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
                        if (response.error_type == 'validation') {
                            validation(response.message);
                        } else {
                            errorMessage(response.message);
                        }
                    }
                },
                error: function(xhr) {
                    internalError();
    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.error_type === 'validation') {
            validation(xhr.responseJSON.message);
        } else {
            errorMessage('An unexpected error occurred. Please try again.');
        }
                }
            });
        });
    });
</script>
@endsection