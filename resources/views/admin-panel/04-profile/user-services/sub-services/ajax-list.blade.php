@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Form">{{$record->forms->name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Sub Service Types">{{$record->subServiceTypes->name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Professional Fees">{{$record->professional_fees ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Consultancy Fees">{{$record->consultancy_fees ?? ''}}</div>
    @php
        $subServiceCount = array_count_values($checkedProfServiceIds)[$record->id] ?? 0;
        $isDisabled =  in_array($record->id, $checkedProfServiceIds);
    @endphp
    <div class="cdsTYDashboard-table-cell" data-label="Action">
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                @if(checkPrivilege([
                    'route_prefix' => 'panel.my-services.sub-services',
                    'module' => 'professional-my-services.sub-services',
                    'action' => 'edit'
                ]))
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('my-services/edit-sub-services/'.$record->unique_id) }}">
                        Edit
                    </a>
                </li>   
                @endif             
                @if(!$isDisabled)
                  @if(checkPrivilege([
                    'route_prefix' => 'panel.my-services.sub-services',
                    'module' => 'professional-my-services.sub-services',
                    'action' => 'delete'
                ]))
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                        data-href="{{ baseUrl('my-services/delete-sub-services/'.$record->unique_id) }}">
                        Delete
                    </a>
                </li>
                @endif
                @endif
            </ul>
        </div>
    </div>  
</div>

{{--<tr>
    <td class="table-column-pr-0">
        <div class="custom-control custom-checkbox text-center">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}"
                id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </td>
    <td data-title="Form">
        <div class="d-flex">
            {{$record->forms->name ?? ''}}
        </div>
    </td>
    <td data-title="Sub Service Types">
        <div class="d-flex">
            {{$record->subServiceTypes->name ?? ''}}
        </div>
    </td>
    <td data-title="Professional Fees">
        <div class="d-flex">
            {{$record->professional_fees ?? ''}}
        </div>
    </td>
    <td data-title="Consultancy Fees">
        <div class="d-flex">
            {{$record->consultancy_fees ?? ''}}
        </div>
    </td>
    @php
        $subServiceCount = array_count_values($checkedProfServiceIds)[$record->id] ?? 0;
        $isDisabled =  in_array($record->id, $checkedProfServiceIds);
    @endphp
    <td data-title="Action">
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('my-services/edit-sub-services/'.$record->unique_id) }}">
                        <i class="tio-edit"></i> Edit
                    </a>
                </li>
                
                @if(!$isDisabled)

                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                        data-href="{{ baseUrl('my-services/delete-sub-services/'.$record->unique_id) }}">
                         Delete
                    </a>
                </li>
                @endif
                
                
            </ul>
        </div>
    </td>
</tr>--}}
@endforeach
<script type="text/javascript">
$(document).ready(function() {
    $(".row-checkbox").change(function() {
        if ($(".row-checkbox:checked").length > 0) {
            $("#datatableCounterInfo").show();
        } else {
            $("#datatableCounterInfo").show();
        }
        $("#datatableCounter").html($(".row-checkbox:checked").length);
    });
})
</script>