@extends('admin-panel.layouts.app')

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <div class="ch-head">
            <i class="fas fa-table me-1"></i>
            Fill form data
          </div>
          <div class="ch-action">
              <a href="{{ baseUrl('members-list') }}" class="CdsTYButton-btn-primary">
                  Back
              </a>
          </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
  <form id="form" class="js-validate" action="{{ baseUrl('create-member') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-6">
                     {!! FormHelper::formSelect([
                            'name' => 'group_id',
                            'id' => 'group_id',
                            'label' => 'Select Group ',
                            'class' => 'select2-input ',
                            'options' => $groups,
                            'value_column' => 'id',
                            'label_column' => 'name',
                            'is_multiple' => false
                            ]) !!}
                   
                </div>
                <div class="col-md-6">
                        {!! FormHelper::formSelect([
                            'name' => 'member_id[]',
                            'id' => 'member_id',
                            'label' => 'Select Members ',
                            'class' => 'select2-input ',
                            'options' => $members,
                            'value_column' => 'id',
                            'label_column' => 'first_name',
                            'is_multiple' => true
                            ]) !!}
                  
                </div>
                
            </div>
            <div class="form-group text-start mt-4">
                <button type="submit" class="btn add-CdsTYButton-btn-primary">Save</button>
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
                $(".google-address").each(function(){
                    var address = $(this).attr("id");
                    
                    var autocomplete = new google.maps.places.Autocomplete(
                    document.getElementById(address), { types: ['geocode'] });
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
                if(!is_valid){
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