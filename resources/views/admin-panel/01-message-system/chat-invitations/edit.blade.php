@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('case-centre') !!}
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">

----------------
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
<form id="form" class="js-validate" action="{{ baseUrl('/company-locations/update/'.$record->unique_id) }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 col-xl-4 google-address">
                                {!! FormHelper::formInputText([
                                    'name'=>"address_1",
                                    'id'=>"address_1",
                                    "label"=> "Address 1",
                                    'input_class'=>"google-address",
                                    "value"=> $record->address_1,
                                    "required"=>true,
                                ])!!}
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                {!! FormHelper::formInputText([
                                    'name'=>"address_2",
                                    'id'=>"address_2",
                                    "label"=> "Address 2",
                                    'input_class'=>"google-address",
                                    "value"=> $record->address_2,
                                    "required"=>true,
                                ])!!}
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                {!! FormHelper::formSelect([
                                    'name' => 'country',
                                    'id' => 'country',
                                    'label' => 'Country',
                                    'class' => 'select2-input ga-country',
                                    'options' => $countries,
                                    'value_column' => 'name',
                                    'label_column' => 'name',
                                    'selected' => $record->country ?? null,
                                    'is_multiple' => false,
                                    'required' => true,
                                ]) !!}                        
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                {!! FormHelper::formInputText([
                                    'name'=>"state",
                                    'id'=>"state",
                                    "label"=> "State",
                                    'events'=>['oninput=validateName(this)'],
                                    "value"=> $record->state,
                                    "required"=>true,
                                ])!!}
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                {!! FormHelper::formInputText([
                                    'name'=>"city",
                                    'id'=>"city",
                                    "label"=> "City",
                                    'events'=>['oninput=validateName(this)'],
                                    "value"=> $record->city,
                                    "required"=>true,
                                ])!!}
                            </div>
                            <div class="col-sm-6 col-xl-4">
                                {!! FormHelper::formInputText([
                                    'name'=>"pincode",
                                    'id'=>"pincode",
                                    "label"=> "Pincode",
                                    "events"=>['oninput=validateZipCode(this)', 'onblur=validateZipCode(this)'],
                                    "value"=> $record->pincode,
                                    "required"=>true,
                                ])!!}
                            </div>
                        </div>
                        <div class="text-start">
                            <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
                        </div>
                    </form>
			</div>
	
	</div>
  </div>
</div>

@endsection
<!-- End Content -->
@section('javascript')
<script src="https://maps.googleapis.com/maps/api/js?key={{ apiKeys('GOOGLE_API_KEY') }}&libraries=places"></script>
<script>
    $(document).ready(function() {
        // google address
        google.maps.event.addDomListener(window, 'load', initGoogleAddress);

        function initGoogleAddress() {
            $(".google-address").each(function() {
                var address = $(this).attr("id");

                var autocomplete = new google.maps.places.Autocomplete(
                    document.getElementById(address), {
                        types: ['geocode']
                    });
                autocomplete.addListener('place_changed', function() {
                    var place = autocomplete.getPlace();
                    if (!place.geometry) {
                        document.getElementById(address).textContent = "No details available for input: '" + place.name + "'";
                        return;
                    }

                    var address = '';
                    if (place.address_components) {
                        address = [
                            (place.address_components[0] && place.address_components[0].short_name || ''),
                            (place.address_components[1] && place.address_components[1].short_name || ''),
                            (place.address_components[2] && place.address_components[2].short_name || '')
                        ].join(' ');
                    }

                    // document.getElementById('address').textContent = 'Address: ' + place.formatted_address;
                });
            })

        }

        // end google address
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