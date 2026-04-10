<div class="cds-ty-dashboard-box">
    <div class="cds-ty-dashboard-box-header">
        {{ $pageTitle }}
    </div>
    <div class="cds-ty-dashboard-box-body">
        <form id="form" class="js-validate mt-3" action="{{ baseUrl('connections/invitations/save') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-sm-12 col-xl-12">
                {!! FormHelper::formSelect([
                    'name' => 'email',
                    'id' => 'email',
                    'label' => 'Users',
                    'class' => 'select2-input ga-country',
                    'options' => $all_users->map(function($user) {
                        return [
                            'value' => $user->email ,
                            'label' => ' (' . $user->first_name .' '. $user->last_name . ') '. $user->email ,
                        ];
                    })->toArray(),
                    'value_column' => 'value',
                    'label_column' => 'label',
                    'is_multiple' => false,
                    'required' => true,
                ]) !!}
                </div>                    
            </div>
            <div class="text-start">
                <button type="submit" class="CdsTYButton-btn-primary add-btn">Save</button>
            </div>
        </form>
    </div>
</div>
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
                        errorMessage(response.message);
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