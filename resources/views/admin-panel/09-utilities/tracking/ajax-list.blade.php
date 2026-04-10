@foreach($records as $key => $record)
<div class="custom-table">
    <div class="custom-control custom-checkbox text-center">
        <input type="checkbox" class="custom-control-input row-checkbox" value="{{ $record['unique_id'] }}"
            id="row-{{$key}}">
        <label class="custom-control-label" for="row-{{$key}}"></label>
    </div>
    <div class="table-card">
        <div class="table-card-block">
            <div class="table-card-heading">
                Name
            </div>
            <div class="table-card-content">
                {{$record['owner_name'] ?? ''}}
            </div>
        </div>
        <div class="table-card-block">
            <div class="table-card-heading">
                Company Name
            </div>
            <div class="table-card-content">
                {{$record['name'] ?? ''}}
            </div>
        </div>
        <div class="table-card-block">
            <div class="table-card-heading">
                Email
            </div>
            <div class="table-card-content">
                {{$record['email'] ?? ''}}
            </div>
        </div>
        <div class="table-card-block">
            <div class="table-card-heading">
                State
            </div>
            <div class="table-card-content">
                {{$record->state ?? ''}}
            </div>
        </div>
        <div class="table-card-block">
            <div class="table-card-heading">
                City
            </div>
            <div class="table-card-content">
                {{$record->city ?? ''}}
            </div>
        </div>
        <div class="table-card-block">
            <div class="table-card-heading">
                Pincode
            </div>
            <div class="table-card-content">
                {{$record->pincode ?? ''}}
            </div>
        </div>
        <div class="table-card-block">
            <div class="table-card-heading">
                Action
            </div>
            <div class="table-card-content">
                <div class="btn-group">
                    <a class="p-0 btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown"
                        data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="defaultDropdown">

                        <li>
                            <a class="dropdown-item" href="{{ baseUrl('tracking/view/'.$record['unique_id']) }}">
                                <i class="tio-edit"></i> View
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
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