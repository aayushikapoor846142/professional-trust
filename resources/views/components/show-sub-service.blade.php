{{--@if($sub_services_types->isNotEmpty())
<div class="row">
    <div class="col-md-6">
        @foreach($sub_services_types as $service_type)
        <div class="border p-2 mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="">
                    {{$service_type->name}}
                </div>
                <div class="border-start ps-3 ms-3">
                    <button class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm me-2">Delete</button>
                    @if(empty(getProfessionalSubServiceType($record->id,$service_type->id)))
                        <button class="btn btn-secondary btn-sm" onclick="configureSubService('{{$service_id}}','{{$service_type->unique_id}}')">Configure</button>
                    @endif
                </div>
            </div>
            <div class="mt-3 add-subservice-form-{{$service_id}}-{{$service_type->unique_id}}" style="display:none;">
                @include('components.add-sub-services')
            </div>
        </div>
        @endforeach
    </div>
</div>
@else

<b>Not Available</b>

@endif --}}
<div class="row">
    <div class="col-md-12">
        <div class="border p-2 mt-3">
            <ul class="cds-service-types">
               @php  $addedSubServices = []; @endphp
                @foreach($sub_services_types as $service_type)
                    @php $addedSubServices[] = checkSubservices($service_id,$service_type->id); @endphp
                    <li class="service-type-li-{{$service_id}}">
                        {!! FormHelper::formCheckbox([
                            'name' => 'subservices',
                            'label' => $service_type->name,
                            'id' => "check-{$service_id}",
                            'required' => false,
                            'data_attr' => "data-service=".$service_id."",
                            'checkbox_class' => 'service-type-chk',
                            'value' => $service_type->id,
                            'disabled' => checkSubservices($service_id, $service_type->id) ? 'true' : 'false',
                            'checked' => checkSubservices($service_id,$service_type->id) ?? ''
                        ]) !!}  
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="col-md-12">
        <div class="showFormDiv-{{$service_id}}"></div>
        <div class="editFormDiv-{{$service_id}}" ></div>
        @php 
            $filteredArr = array_filter($addedSubServices, function($value) {
                return $value !== 0;
            });

            $filteredArr = array_values($filteredArr);
            $professional_sub_Service_id = implode(',',$filteredArr);
        @endphp
       
    </div>
</div>
<script>
    var service_id = "{{$service_id}}";
    var professional_sub_Service_id = "{{$professional_sub_Service_id}}";
    @if($professional_sub_Service_id != '')
        $.ajax({
            type: "GET",
            url: BASEURL + '/my-services/add-subservice-type-form/'+service_id,
            data: {
                _token: csrf_token,
                value:professional_sub_Service_id,
                is_edit:1
            },
            dataType: 'json',
            success: function(data) {
                $('.editFormDiv-'+service_id).append(data.contents);
            },
        });

    @endif
    $('.service-type-chk').on('change', function () {
        let value = $(this).val();
        let service = $(this).data('service'); // gets data-service
       
        if ($(this).is(':checked')) {
            $.ajax({
                type: "GET",
                url: BASEURL + '/my-services/add-subservice-type-form/'+service,
                data: {
                    _token: csrf_token,
                    value:value,
                    is_edit:0
                },
                dataType: 'json',
                success: function(data) {
                    $('.showFormDiv-'+service).append(data.contents);
                },
            });
        } else {
           $('.add-'+service+'-'+value).remove();
        }

        
    });

    $(".min_fees").blur(function(){
        var min = $(this).attr("min");
        if($(this).val() < min){
            errorMessage("Fees should be minimum "+min);
            $(this).val(min);
        }
    })
    $(".max_fees").blur(function(){
        var min_fees = $("#min_fees").val();
        if($(this).val() < min_fees){
            errorMessage("Max Fees should be greater than "+min_fees);
            $(this).val('');
        }
    })



   
   
</script>