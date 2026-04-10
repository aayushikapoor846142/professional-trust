@foreach($records as $key => $record)
<div class="cdsTYDashboard-table-row">
    <div class="cdsTYDashboard-table-cell cdsCheckbox">
        <div class="custom-checkbox">
            <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record->unique_id }}" id="row-{{$key}}">
            <label class="custom-control-label" for="row-{{$key}}"></label>
        </div>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Name">
        <a href="{{ mainTrustvisoryUrl() . '/' . ltrim('article/'.$record->slug ?? '', '/') }}" target="_blank" rel="noopener noreferrer">
            <div>{{$record->name ?? ''}}</div> 
            <div class="text-danger">{{$record->show_on_home == 1 ?'[Show On Home   ]':''}}</div> 
        </a>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Category/Type">
        <span>{{$record->category->name??''}} <i class="fa fa-arrow-right"></i> {{$record->articleType->name??''}}</span>
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Added by">
        {{$record->userAdded->first_name}} {{$record->userAdded->last_name}}
    </div>
    <div class="cdsTYDashboard-table-cell" data-label="Status">{{$record->status}}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Date">{{ dateFormat($record->created_at ?? '', 'd M Y, h:i A') }}</div>
    <div class="cdsTYDashboard-table-cell" data-label="Action">
         @if(checkPrivilege([
                    'route_prefix' => 'panel.articles',
                    'module' => 'professional-articles',
                    'action' => 'edit'
        ]) || checkPrivilege([
                    'route_prefix' => 'panel.articles',
                    'module' => 'professional-articles',
                    'action' => 'delete'
        ]))  
        <div class="btn-group">
            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                More
            </a>
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">  
                @if(checkPrivilege([
                    'route_prefix' => 'panel.articles',
                    'module' => 'professional-articles',
                    'action' => 'edit'
                ]))              
                <li>
                    <a class="dropdown-item" href="{{ baseUrl('articles/edit/'.$record->unique_id) }}">
                        <i class="tio-edit"></i> Edit
                    </a>
                </li>  
                @endif
                @if(checkPrivilege([
                    'route_prefix' => 'panel.articles',
                    'module' => 'professional-articles',
                    'action' => 'delete'
                ]))             
                <li>
                    <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)"
                        data-href="{{ baseUrl('articles/delete/'.$record->unique_id) }}">
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