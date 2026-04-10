@extends('components.custom-popup',['modalTitle'=>'Appointment Types'])
@section('custom-popup-content')
@php
$hasTypes = count($appointmentTypes);
@endphp

<div class="container">
<div class="row">
    <div class="col-xl-12">
        <div class="card rounded">
            <div class="card-body">

                {{-- Add Appointment Type Form --}}
                <form name="add-type-form" id="add-type-form" class="js-validate" 
                        action="{{ baseUrl('appointment-types/save') }}" 
                        method="post" 
                        style="{{ $hasTypes ? 'display: none;' : '' }}">
                    @csrf
                    <div class="cds-form-header">
                        <h5>Add Appointment Type</h5>
                    </div>
                    <input type="hidden" name="duration_booking_flow_id"  value="{{$appointmentBookingFlow->unique_id ?? ''}}">
                    <div class="row">
                        <div class="col-md-12">
                            {!! FormHelper::formInputText([
                                'name'=>"name",
                                'id' => "name",
                                'label'=>"Enter Name",
                                'required'=>true,
                        
                            ]) !!}
                        </div>
                        <div class="col-md-12">
                            {!! FormHelper::formSelect([
                                'name' => 'duration',
                                'label' => 'Select Duration',
                                'class' => 'select2-input ga-country',                                    
                                'options' => getDuration(),
                                'value_column' => 'id',
                                'label_column' => 'name',
                                'selected' => old('duration') ?? null,
                                'is_multiple' => false,
                                'required' => true
                            ]) !!}
                        </div>
                        <div class="col-md-12">
                            {!! FormHelper::formInputNumber([
                                'name' => 'price',
                                'label' => 'Price',
                                'required' => true,
                                'input_class' => 'ga-pincode',
                                'events' => [
                                    'oninput=validateDigit(this)', 
                                    'onblur=validateDigit(this)'
                                ]
                            ]) !!}
                        </div>
                        <div class="col-md-12">
                            {!! FormHelper::formSelect([
                                'name' => 'currency',
                                'label' => 'Select Currency',
                                'class' => 'select2-input ga-country',                                    
                                'options' => FormHelper::supportCurrency(),
                                'value_column' => 'value',
                                'label_column' => 'label',
                                'selected' => '',
                                'is_multiple' => false
                            ]) !!}
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        @section('custom-popup-footer')
                        <button type="submit" form="add-type-form" class="CdsTYButton-btn-primary">Save</button>
                        @endsection
                        <button type="button" class="btn btn btn-dark cancel-type-btn" >
                            Cancel
                        </button>
                    </div>
                </form>

                {{-- Select Existing Appointment Type --}}
                @if($appointmentTypes)
                <form id="select-type-form" action="{{baseUrl('appointments/appointment-booking-flow/save-appointment-type')}}" class="js-validate" method="post" style="{{ $hasTypes ? '' : 'display: none;' }}">
                    @csrf
                    <div class="cds-form-header">
                        <h5>Choose Existing Appointment Type</h5>
                    </div>
                    <input type="hidden" name="duration_booking_flow_id"  value="{{$appointmentBookingFlow->unique_id ?? ''}}">
                    <div class="row">
                        <div class="col-xl-12" id="typeRadioContainer">
                                <span class="mb-2 d-block">Choose Existing Appointment Type :</span>
                                @php 
                                    $selected = $appointmentBookingFlow->appointment_type_id ?? ''

                                @endphp
                                @include('admin-panel.03-appointments.appointment-system.appointment-booking-flow.components.appointment-type.select', ['appointmentTypes' => $appointmentTypes, 'selected' => $selected])

                        </div>
                    </div>
                    <div class="mt-3 text-start">
                        
                    </div>
                    <div class="mt-4 d-flex flex-wrap gap-2 align-items-center">
                        <button type="submit" class="CdsTYButton-btn-primary">Save</button>
                        <a href="{{baseUrl('appointments/settings')}}" class="btn add-CdsTYButton-btn-primary">Appointment Types</a>

                        <button type="button" class="btn btn btn-dark add-type-btn lh-sm">
                            + Add New Appointment Type
                        </button>
                    </div>
                </form>
                @else
                    <button type="button" class="btn btn btn-dark add-type-btn">
                        + Add New Appointment Type
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
</div>




<!-- End Content -->

<script>
    $(document).ready(function() {
        $('.add-type-btn').on('click', function () {
            $('#add-type-form').show();
            $('#select-type-form').hide();
        });
        $('.cancel-type-btn').on('click', function () {
            $('#add-type-form').hide();
            $('#select-type-form').show();
        });
        $("#add-type-form").submit(function(e) {
            e.preventDefault();
            var is_valid = formValidation("add-type-form");
            if(!is_valid){
                return false;
            }
            var formData = new FormData($(this)[0]);
            var url = $("#add-type-form").attr('action');
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
                        $("#select-type-form").show();
                        $("#add-type-form").hide();
                        $('#typeRadioContainer').html(response.html);
                    } else {

                        if(response.error_type == 'validation'){
                            validation(response.message);
                        }else{
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

        $("#select-type-form").submit(function(e) {
        e.preventDefault();
        var is_valid = formValidation("select-type-form");
        if (!is_valid) return false;

        var formData = new FormData($(this)[0]);
        var url = $("#select-type-form").attr('action');

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
            error: function() {
                internalError();
            }
        });
    });
    });
</script>
@endsection