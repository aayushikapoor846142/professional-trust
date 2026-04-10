<h5 class="mb-3 mt-5">Selected Services</h5>
<div class="accordion" id="accordionExample">
@foreach($records as $key => $record)

<div class="accordion-item">
    <h2 class="accordion-header" id="heading{{$key}}">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$key}}"
            aria-expanded="true" aria-controls="collapse{{$key}}">
            {{$record->name}}
        </button>
    </h2>
    <div id="collapse{{$key}}" class="accordion-collapse collapse show" aria-labelledby="heading{{$key}}"
        data-bs-parent="#accordionExample">
        <div class="accordion-body">
            <div class="sub-services">
                <ul>                   
                    @foreach ($record->subServices as $subService)
                    @php
                        $subServiceCount = array_count_values($checkedSubServiceIds)[$subService->id] ?? 0;
                        $isDisabled =  in_array($record->id, $checkedParentServiceIds) &&  in_array($subService->id, $checkedSubServiceIds);
                    @endphp

                    <li>
                    
                        <div class="service-name  @if($isDisabled){{'cds-service-checked'}} @endif @if(in_array($subService->id,$subservices)) my-service-checked @endif" >
                         
                            <div class="select-services">
                            @if(!$isDisabled)
                                {!! FormHelper::formCheckbox([
                                    'name' => 'subservices[]',
                                    'id' => "check-{$key}-{$subService->id}",
                                    'required' => true,
                                    'value' => "{$subService->id}",
                                    "checked" =>in_array($subService->id, $subservices),
                                    "is_multiple" => true,
                                    'labelAttributes' => ['class' => 'srv-checkbox checkbox required'],
                                    'onchange' => "chooseService(this, {$subService->id})",                                
                                ]) !!}  
                                @endif                                  
                            </div>
                            <div class="services-title-connect-cases-bx">
                            <label class="checkbox service-checkbox required mt-0" for="check-{{$key}}-{{$subService->id}}">
                                {{$subService->name}} 
                            </label>
                            <div class="services-connect-cases">
                            @if($isDisabled)
                           <a href="{{baseUrl('case-with-professionals/')}}"> <span class="services-connect-cases-count">{{'connected with '.$subServiceCount.' cases'}}</span></a>
                            @endif
                            </div>
                            </div>
                        </div>                          
                    </li>
                    @endforeach                    
                </ul>
            </div>  
        </div>
    </div>
</div>
@endforeach
</div>
<script type="text/javascript">

$(document).ready(function(){
    $(document).on("change", "[name='subservices[]']", function() {
    chooseService(this, $(this).val());
});

function chooseService(e, id) {
    var type = '';
    if ($(e).is(":checked")) {
        type = 'selected';
    } else {
        type = 'unselected';
    }
    $.ajax({
        type: "POST",
        url: BASEURL + '/manage-services/save-my-service',
        data: {
            _token: csrf_token,
            type: type,
            id: id
        },
        dataType: 'json',
        success: function(data) {
            // loadData(1, false);
            successMessage('Service added');
        },
    });
}
$(".row-checkbox").change(function(){
    if($(".row-checkbox:checked").length > 0){
      $("#datatableCounterInfo").show();
    }else{
      $("#datatableCounterInfo").show();
    }
    $("#datatableCounter").html($(".row-checkbox:checked").length);
  });
})
</script>
