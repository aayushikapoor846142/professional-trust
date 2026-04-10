@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Name">{{$record->name ?? ''}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Added by">{{$record->user->first_name}} {{$record->user->last_name}}</div>
     <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
         @if(checkPrivilege([
                      'route_prefix' => 'panel.document-folders',
                      'module' => 'professional-document-folders',
                      'action' => 'edit'
                  ]) || checkPrivilege([
                      'route_prefix' => 'panel.document-folders',
                      'module' => 'professional-document-folders',
                      'action' => 'delete'
                  ]))
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                @if(checkPrivilege([
                      'route_prefix' => 'panel.document-folders',
                      'module' => 'professional-document-folders',
                      'action' => 'edit'
                  ]))   
                <li>
                    <a class="dropdown-item"  onclick="openCustomPopup(this)" data-href="{{ baseUrl('document-folders/edit/'.$record->unique_id) }}" data-href=""> <i class="tio-edit"></i> Edit </a>
                </li>
                @endif
                @if(checkPrivilege([
                      'route_prefix' => 'panel.document-folders',
                      'module' => 'professional-document-folders',
                      'action' => 'delete'
                  ]))   
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('document-folders/delete/'.$record->unique_id) }}">
                        Delete
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @else
         - 
        @endif
    </div>  
</div>

@endforeach
<script type="text/javascript">

$(document).ready(function(){
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
