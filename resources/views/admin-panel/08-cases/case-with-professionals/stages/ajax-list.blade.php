@foreach($records as $key => $record)

<div class="card mb-3">
    <div class="card-header stage-header p-0">
        <div class="row">
            <div class="col-md-12">
                <div class="cds-substage-head-block">
                    <div class="cds-substage-head-tile">
                        <h5 class="cards-title pt-0 pb-0 mb-0">{{ ucwords($record->name) }}</h5>
                    </div>
                    <div class="cds-substage-head-edit-bx">
                        <a href="javascript:;" onclick="showPopup('<?= baseUrl('case-with-professionals/stages/sub-stages/add/' . $record->unique_id) ?>')" class="CdsTYButton-btn-primary sub-stage-btn">Sub Stages</a>
                        <div class="btn-group">
                            <a class="btn btn-sm btn-white dropdown-toggle" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </a>
                            <ul class="dropdown-menu table-stage-task-edit" aria-labelledby="defaultDropdown">
                                <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="showPopup('<?= baseUrl('case-with-professionals/stages/edit/' . $record->unique_id) ?>')"> Edit <i class="fa fa-pencil" aria-hidden="true"></i> </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('case-with-professionals/stages/delete/'.$record->unique_id) }}">
                                        Delete <i class="fa fa-trash" aria-hidden="true"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body stage-body p-2">
                    <p class="card-text">{{ $record->short_description }}</p>
                </div>
                @if($record->caseSubStages->isNotEmpty())
                <div class="substagelists p-2">
                    <div class="table-responsive custom-table-container">
                        <table class="table table-bordered noborder">
                            <thead class="custom-thead">
                                <tr>
                                    <th colspan="4" class="bg-light">Stage Tasks</th>
                                </tr>
                            </thead>
                            <tbody class="custom-scrollable-tbody">
                                @foreach($record->caseSubStages as $value)
                                <tr>
                                    <td><i class="tio tio-circle"></i> {{$value->name}} &nbsp;</td>
                                    <td>{{ucwords(str_replace('-', ' ', $value->stage_type))}}</td>
                                    <td class="csd-pending-edit-bx">
                                        <span class="pending-status">{{$value->status}}</span>
                                        <span class="edit-block-dots">
                                            <a class="btn btn-sm btn-white dropdown-toggle table-stage-task-btn" href="javascript:void(0)" id="defaultDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </a>
                                            <ul class="dropdown-menu table-stage-task-edit" aria-labelledby="defaultDropdown">
                                                <li>
                                                    <a href="javascript:;" class="btn btn-sm js-nav-tooltip-link" onclick="showPopup('<?= baseUrl('case-with-professionals/stages/sub-stages/edit/' . $value->unique_id) ?>')" title="Click to Edit">
                                                        Edit <i class="fa fa-pencil"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a
                                                        class="btn btn-sm"
                                                        href="javascript:;"
                                                        onclick="confirmAction(this)"
                                                        data-href="{{baseUrl('case-with-professionals/stages/sub-stages/delete/' . $value->unique_id)}}"
                                                        data-original-title="Click to Delete"
                                                    >
                                                        Delete <i class="fa fa-trash"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a onclick="markAsSubStageComplete('{{$value->unique_id}}')" class="btn btn-sm" title="Mark as Complete" href="javascript:;"> Mark as Complete <i class="fa fa-check"></i> </a>
                                                </li>
                                            </ul>
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
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