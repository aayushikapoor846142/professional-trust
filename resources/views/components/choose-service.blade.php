@php 
$sub_service_ids = [];
@endphp
@foreach ($services as $row) 
    @if($row->parent_service_id == 0)
        <div id="parent-{{$row->id}}" class="core-service">
            <div class="autocomplete-item parent-service" data-parent="1" data-core="1" data-id="{{$row->id}}" data-description="{{$row->name}}">
                <span>{{$row->name}}</span>
                @if(empty($current_services) || !in_array($row->id,$current_services))
                <button class="btn btn-sm btn-success choose-main-service" data-id="{{$row->id}}">Select Service</button>
                @else
                <button class="btn btn-sm btn-danger remove-main-service" data-id="{{$row->id}}">Remove Service</button>
                @endif
            </div>
        </div>
        @foreach($row->subServices as $subServices)
            @php 
                $sub_service_ids[] = $subServices->id; 
            @endphp
            <div class="autocomplete-item sub-service" data-parent="0" data-core="0" data-id="{{$subServices->id}}" data-description="{{$subServices->name}}">
                <span><i class="fa fa-chevron-right"></i> {{$subServices->name}}</span>
                @if(empty($current_services) || !in_array($subServices->id,$current_services))
                <button class="btn btn-sm btn-success choose-sub-service" data-id="{{$row->id}}">Select Service</button>
                @else
                <button class="btn btn-sm btn-danger remove-sub-service" data-id="{{$row->id}}">Remove Service</button>
                @endif
            </div>
        @endforeach
    @else
        @if(!in_array($row->id,$sub_service_ids))
            <div class="autocomplete-item sub-service" data-parent="0" data-core="0" data-id="{{$row->id}}" data-description="{{$row->name}}">
                <span><i class="fa fa-chevron-right"></i> {{$row->name}} <br><small> <i class="fa-solid fa-circle-dot"></i> {{$row->parentService->name}}</small></span>
                @if(in_array($row->id,$current_services))
                <button class="btn btn-sm btn-success choose-sub-service" data-id="{{$row->id}}">Select Service</button>
                @else
                <button class="btn btn-sm btn-danger remove-sub-service" data-id="{{$row->id}}">Remove Service</button>
                @endif
            </div>
        @endif
    @endif        
@endforeach