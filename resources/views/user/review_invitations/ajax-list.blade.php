@if(count($records) > 0)
@foreach($records as $key => $record)
<div class="custom-table">
    <div class="table-card">
        <div class="table-card-block">
            <div class="table-card-heading">
                Email
            </div>
            <div class="table-card-content">
                
            {{$record->professional->first_name." ".$record->professional->last_name}}/
            {{$record->professional->email}}
            </div>
        </div>
        <div class="table-card-block">
            <div class="table-card-heading">
                Status
            </div>
            <div class="table-card-content">
            {{$record->status}}
            </div>
        </div>
        <div class="table-card-block">
            <div class="table-card-heading">
            Sent Date
        </div>
        <div class="table-card-content">
            {{$record->created_at}}
        </div>
                Action
            </div>
            <div class="table-card-content">
            <div class="btn-group">
            <a class="p-0 btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </a>
            @if($record->status=="accepted")
            @if(checkPrivilege('reviews/review-invitations','write-a-review'))
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                <li>
                    <a class="dropdown-item text-danger" href="{{ url('write-a-review?token='.$record->token) }}">
                        <i class="tio-edit"></i> Give Review
                    </a>
                </li>
            </ul>
            @endif
            @elseif($record->status=="pending")
            <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                @if(checkPrivilege('reviews/review-invitations','accept-invitation'))
                <li>
                    <a class="dropdown-item text-danger" href="{{ url('accept-invitation/'.$record->token) }}">
                        <i class="tio-edit"></i> Accept
                    </a>
                </li>
                @endif
               
            </ul>
            @endif
        </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@else
<div class="text-center">
No Review Invitation
</div>
@endif
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