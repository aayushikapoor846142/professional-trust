@foreach($records as $key => $record)

<div class="card mb-3" >
    <div class="card-header stage-header p-2">
        <div class="row" >
            <div class="col-md-4" >
                <h5 class="cards-title pt-3 pb-2 mb-0">{{ ucwords($record->name) }}</h5>
                <a href="{{baseUrl('case-with-professionals/stages/add-substage')}}" class="CdsTYButton-btn-primary">Sub Stages</a>
            </div>
            <div class="col-md-8" >
                <div class="btn-group">
                    <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                        More
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="defaultDropdown">
                        <li>
                            <a class="dropdown-item" href="javascript:;" onclick="showPopup('<?= baseUrl('case-with-professionals/stages/edit/' . $record->unique_id) ?>')">
                                <i class="tio-edit"></i> Edit
                            </a>
                        </li>
                        <li>
                        <a class="dropdown-item text-danger" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('case-with-professionals/stages/delete/'.$record->unique_id) }}">
                             Delete
                        </a>
                    </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body stage-body p-2">
      <p class="card-text">{{ $record->short_description }}</p>
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