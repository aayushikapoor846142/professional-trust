@extends('components.custom-popup',['modalTitle'=>'Services'])
@section('custom-popup-content')
<div class="cds-form-container mb-0">
    <div class="row">
        
        <div class="col-xl-12">
            <div class="card rounded">
                <div class="card-body">
                    @if(!empty($groupedServices))
                        <form id="service-form" class="js-validate" action="{{baseUrl('appointments/appointment-booking-flow/save-services')}}" method="post">
                            @csrf
                            <input type="hidden" name="duration_booking_flow_id"  value="{{$appointmentBookingFlow->unique_id ?? ''}}">
                            <div class="row cdsSelectservice">
                                <div class="col-xl-12">
                                @foreach ($groupedServices as $parent => $subServices)
                                    <div>
                                        <span class="mb-2 d-block">{{ $parent }} :</span>
                                        @php
                                            $options = [];
                                            foreach ($subServices as $service) {
                                                $options[] = [
                                                    'value' => $service['id'],
                                                    'label' => $service['name'],
                                                    'key' => $service['id'],
                                                ];
                                            }
                                        @endphp
                                        {!! FormHelper::formMultipleCheckbox([
                                            'name' => 'service[]',
                                            'required' => true,
                                            'options' => $options,
                                            'value_column' => 'value',
                                            'label_column' => 'label',
                                            'selected' => $appointmentBookingFlow != "" ? explode(',', $appointmentBookingFlow->service_id) : [],
                                        ]) !!}
                                    </div>
                                @endforeach
                                </div>        
                            </div>
                            <div class="cdsTYMainsite-form-floating-button d-flex gap-2">
                                <a href="{{baseUrl('my-services')}}" class="btn add-CdsTYButton-btn-primary">Go to My Services</a>
                                <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
                            </div>
                        </form>
                        @else
                        <div class="alert alert-danger text-center">No service available</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- End Content -->

<script>
$(document).ready(function() {
    initEditor("additional_info_textarea");

    $("#service-form").submit(function(e) {

        e.preventDefault();
        var is_valid = formValidation("form");
        if (!is_valid) {
            return false;
        }
        var formData = new FormData($(this)[0]);
        var url = $("#service-form").attr('action');

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
                    $.each(response.message, function(key, val) {
                       errorMessage(val);
                    });
                    //validation(response.message);
                                    
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