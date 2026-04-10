@extends('admin-panel.layouts.app')

@section('content')
 <div class="ch-action">
                    <a href="{{ baseUrl('company-locations') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div>
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <form id="form" class="js-validate mt-3" action="{{ baseUrl('company-locations/save') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-form-label input-label">Address 1<span class="text-danger">*</span></label>
                                    <div class="js-form-message">
                                        <input type="text" class="form-control google-address" name="address_1"
                                            id="address_1" placeholder="Enter address" aria-label="Enter address"
                                            data-msg="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-form-label input-label">Address 2</label>
                                    <div class="js-form-message">
                                        <input type="text" class="form-control" name="address_2"
                                            id="address_2" placeholder="Enter address" aria-label="Enter address"
                                            data-msg="">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-form-label input-label">Country<span class="text-danger">*</span></label>
                                    <div class="js-form-message">
                                        <select class="form-control" name="country" id="country">
                                            <option value="">Select Coutry</option>
                                            @foreach ($countries as $country)
                                            <option value="{{ $country->name }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-form-label input-label">State<span class="text-danger">*</span></label>
                                    <div class="js-form-message">
                                        <input type="text" class="form-control required" name="state"
                                            id="state" placeholder="Enter state" aria-label="Enter state"
                                            data-msg="" oninput="validateName(this)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-form-label input-label">City<span class="text-danger">*</span></label>
                                    <div class="js-form-message">
                                        <input type="text" class="form-control required" name="city"
                                            id="city" placeholder="Enter city" aria-label="Enter city"
                                            data-msg="" oninput="validateName(this)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-form-label input-label">Pincode<span class="text-danger">*</span></label>
                                    <div class="js-form-message">
                                        <input type="text" class="form-control required" name="pincode"
                                            id="pincode" placeholder="Enter pincode" aria-label="Enter pincode"
                                            data-msg="" oninput="validateDigit(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-start mt-4">
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