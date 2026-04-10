@extends('admin-panel.layouts.app')
@section('styles')
<link href="{{ url('assets/css/13-CDS-case-stages.css') }}" rel="stylesheet" />
@endsection
@section('content')
 <div class="ch-action">
                    <a href="{{ baseUrl('predefined-case-stages') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-left fa-solid me-1"></i>
                        Back
                    </a>
                </div>
   <div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
  <div class="CdsDashboardCaseStages-list-view-stage-card">
            <div class="CdsDashboardCaseStages-list-view-stage-header">
                <div>
                    <h3 class="CdsDashboardCaseStages-list-view-stage-title">{{$predefined_case_stages->name}}</h3>
                    <p class="CdsDashboardCaseStages-list-view-stage-date">Started on {{date('d M Y',strtotime($predefined_case_stages->created_at))}}</p>
                </div>
                @if($predefined_case_stages->status == "complete")
                    <span class="CdsDashboardCaseStages-list-view-stage-badge  CdsDashboardCaseStages-list-view-completed">
                        {{ucwords(str_replace('-', ' ', $predefined_case_stages->status))}}</span>
                @elseif($predefined_case_stages->status == "in-progress")
                    <span class="CdsDashboardCaseStages-list-view-stage-badge CdsDashboardCaseStages-list-view-active">{{ucwords(str_replace('-', ' ', $predefined_case_stages->status))}}</span>

                @else
                    <span class="CdsDashboardCaseStages-list-view-stage-badge ">{{ucwords(str_replace('-', ' ', $predefined_case_stages->status))}}</span>
                @endif
            </div>
        
            @if($predefined_case_stages->predefinedCaseSubStages->isNotEmpty())
                
                <div class="CdsDashboardCaseStages-list-view-sub-stages-list">
                    @foreach($predefined_case_stages->predefinedCaseSubStages as $key => $sub_stages)
                        <div class="CdsDashboardCaseStages-list-view-sub-stage-row" data-substage = "{{$sub_stages->unique_id}}">
                            
                            <div class="CdsDashboardCaseStages-list-view-sub-stage-icon" style="background: #e1f5fe;">🏥</div>
                            <div class="CdsDashboardCaseStages-list-view-sub-stage-content">
                                <div class="CdsDashboardCaseStages-list-view-sub-stage-details">
                                    <h4 class="CdsDashboardCaseStages-list-view-sub-stage-title">{{$sub_stages->name}}</h4>
                                    <p class="CdsDashboardCaseStages-list-view-sub-stage-description">{{$sub_stages->description}}</p>
                                </div>
                                <div class="CdsDashboardCaseStages-list-view-sub-stage-meta">
                                        @if($sub_stages->status == "complete")
                                    <div class="CdsDashboardCaseStages-list-view-deadline-info">
                                        🔒 Locked
                                    </div>
                                @endif
                                    <div class="CdsDashboardCaseStages-list-view-deadline-info">
                                        📅 {{date('d M Y',strtotime($sub_stages->created_at))}}
                                    </div>
                                    @if($sub_stages->userAdded != '')
                                        <div class="CdsDashboardCaseStages-list-view-assigned-info">
                                            <div class="CdsDashboardCaseStages-list-view-assigned-avatar">
                                            {!! getProfileImage($sub_stages->userAdded->unique_id) !!}
                                            </div>
                                            <!-- <div class="CdsDashboardCaseStages-list-view-assigned-avatar">{{ strtoupper(substr($sub_stages->userAdded->first_name ?? '', 0, 1)) }}{{ strtoupper(substr($sub_stages->userAdded->last_name ?? '', 0, 1)) }} -->
                                            <!-- </div> -->
                                            <span class="CdsDashboardCaseStages-list-view-assigned-name">{{$sub_stages->userAdded->first_name ?? ''}} {{$sub_stages->userAdded->last_name ?? ''}}</span>
                                        </div>
                                    @endif
                                    @if($sub_stages->status == "pending")
                                        <span class="CdsDashboardCaseStages-list-view-status-pill CdsDashboardCaseStages-list-view-status-pending">{{$sub_stages->status}}</span>
                                    @endif
                                    @if($sub_stages->status == "complete")
                                        <span class="CdsDashboardCaseStages-list-view-status-pill CdsDashboardCaseStages-list-view-status-completed">{{$sub_stages->status}}</span>
                                    @endif
                                    @if($sub_stages->status == "in-progress")
                                        <span class="CdsDashboardCaseStages-list-view-status-pill CdsDashboardCaseStages-list-view-status-in-progress">{{$sub_stages->status}}</span>
                                    @endif
                                    <div class="CdsDashboardCaseStages-list-view-sub-stage-actions">
                                        @if($sub_stages->status != 'complete')
                                            <button class="CdsDashboardCaseStages-list-view-action-btn" title="Edit" data-id="{{ $sub_stages->unique_id }}" onclick="editSubSegment('{{  $sub_stages->unique_id }}')">✏️</button>
                                        @endif
                                        
                                        <button class="CdsDashboardCaseStages-list-view-action-btn btn-view-sub-stages" title="Edit" data-id="{{ $sub_stages->unique_id }}">👁️</button>

                                        <button class="CdsDashboardCaseStages-list-view-action-btn" title="Delete" onclick="confirmAction(this)" data-href="{{ baseUrl('predefined-case-sub-stages/delete/'.$sub_stages->unique_id) }}" >🗑️
                                        </button>
                                        @if($sub_stages->status != 'complete')
                                        <button class="CdsDashboardCaseStages-list-view-action-btn" title="Mark As Complete" onclick="markAsSubStageComplete('{{$sub_stages->unique_id}}')"
                                            >✅
                                        </button>
                                           @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                
                </div>
            @else
                <div class="CdsDashboardCaseStages-list-view-list-header">
                    <div class="CdsDashboardCaseStages-list-view-list-header-section">
                        No Sub Stages Added
                    </div>
                </div>

            @endif

            <div class="CdsDashboardCaseStages-list-view-action-buttons">
               
                    @if($predefined_case_stages->status != 'complete')
                          @if(checkPrivilege([
                    'route_prefix' => 'panel.predefined-case-stages',
                    'module' => 'professional-predefined-case-stages',
                    'action' => 'delete'
                ]))
                        <a href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('predefined-case-stages/delete/'.$predefined_case_stages->unique_id) }}" class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-primary">
                            <span>🗑️</span>
                            <span>Delete Stages</span>
                        </a>
                        @endif

                    @endif
                             @if(checkPrivilege([
                    'route_prefix' => 'panel.predefined-case-stages',
                    'module' => 'professional-predefined-case-stages',
                    'action' => 'view'
                ]))
                    <button type="button" class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-outline btn-view-stages" data-id="{{ $predefined_case_stages->unique_id }}">
                        <span>👁️</span>
                        <span>View Stage</span>
                    </button>
                    @endif
                    @if($predefined_case_stages->status != 'complete')
                         @if(checkPrivilege([
                    'route_prefix' => 'panel.predefined-case-stages',
                    'module' => 'professional-predefined-case-stages',
                    'action' => 'add-sub-stage'
                ]))
                        <button class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-outline" data-href="{{ baseUrl('predefined-case-sub-stages/add') }}" onclick="openCustomPopup(this,'get',{'stage_id':{{$predefined_case_stages->id}}})" >
                            <span>➕</span>
                            <span>Add Sub Stage</span>
                        </button>
                            @endif
                    @endif
                    @if($predefined_case_stages->status != 'complete')
                           @if(checkPrivilege([
                    'route_prefix' => 'panel.predefined-case-stages',
                    'module' => 'professional-predefined-case-stages',
                    'action' => 'mark-as-complete'
                ]))
                            <a onclick="markAsStageComplete('{{$predefined_case_stages->unique_id}}')" class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-outline" title="Mark as Complete" href="javascript:;"><span>✅</span> Mark as Complete  </a>
                            @endif
                            @endif
            </div>
        </div>
   
			</div>
	
	</div>
  </div>
</div>
   
<div id="cds-fs-viewSegmentSidebar" class="cds-fs-sidebar-form">
    <div class="cds-fs-sidebar-header">
        <h4>View Stages</h4>
        <button class="cds-fs-close-btn" onclick="cdsFsCloseViewSidebar()">×</button>
    </div>
    <div class="cds-fs-sidebar-body" id="cds-fs-viewSegmentBody">
        <!-- Content will be loaded dynamically -->
        <div class="text-center">Loading...</div>
    </div>
</div>

<!-- end -->

<!-- add sub stages -->
<div id="cds-fs-manageSidebar" class="cds-fs-sidebar-form">
    <div class="cds-fs-sidebar-header">
        <h4 class="sub-segment-heading"></h4>
        <button class="cds-fs-close-btn" onclick="cdsFsCloseSidebar('cds-fs-manageSidebar','cds-fs-manageSidebarBackdrop')">×</button>
    </div>
    <div class="cds-fs-sidebar-body" id="cds-fs-manageSubSegmentBody">
    </div>
</div>

<!-- end -->
@endsection

@section('javascript')
<script>
    $(document).ready(function() {
        const subSegmentContainers = document.querySelectorAll(".sortable-sub-segment");

    subSegmentContainers.forEach(container => {
        let dragSourceContainer = null;

        container.addEventListener("dragover", (e) => {
            e.preventDefault();
            const afterElement = getHorizontalDragAfterElement(container, e.clientX);
            const dragging = container.querySelector(".dragging-sub");
            if (!dragging) return;

            if (afterElement == null) {
                container.appendChild(dragging);
            } else {
                container.insertBefore(dragging, afterElement);
            }
        });

        container.querySelectorAll(".cds-fs-sub-segment-card").forEach(card => {
            card.setAttribute("draggable", true);

            card.addEventListener("dragstart", () => {
                card.classList.add("dragging-sub");
                dragSourceContainer = card.closest(".sortable-sub-segment");
                card.closest(".cds-fs-segment-card").dragging = false;
                
            });

            card.addEventListener("dragend", () => {
                card.classList.remove("dragging-sub");
                card.closest(".cds-fs-segment-card").dragging = true
                let sortedSegments = [];
                const cards = dragSourceContainer.querySelectorAll(".cds-fs-sub-segment-card");
                cards.forEach((el, index) => {
                    const segmentId = el.dataset.segment;
                    sortedSegments.push({ id: segmentId, position: index + 1 });
                });
                $.ajax({
                    url: "{{ baseUrl('predefined-case-sub-stages/update-sorting') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        segments: sortedSegments
                    },
                    success: function (response) {
                        // successMessage(response.message);
                    },
                    error: function () {
                        errorMessage("Failed to update sub-segment sorting.");
                    }
                });
            });
        });
    });

    function getHorizontalDragAfterElement(container, x) {
        const draggableElements = [...container.querySelectorAll(".cds-fs-sub-segment-card:not(.dragging-sub)")];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = x - box.left - box.width / 2;
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    });

    function addSubStages(stage_id){
        var url = '{{ baseUrl('predefined-case-sub-stages/add') }}';
        $.ajax({
            url: url,
            type: "GET",
            data:{
                stage_id:stage_id
            },
            dataType:"json",
            success: function (response) {
                $("#cds-fs-manageSubSegmentBody").html(response.contents);
                $(".sub-segment-heading").html("Add Sub Stages");
                cdsFsOpenSidebar('cds-fs-manageSidebar','cds-fs-manageSidebarBackdrop');
            },
            error: function () {
                errorMessage('Failed to load segment details.</div>');
            }
        });
    }

    function cdsFsOpenSidebar(sidebarElement,sidebarBackdrop) {
        $('#'+sidebarElement).addClass('open');
        $('#'+sidebarBackdrop).addClass('show');
    }

    function cdsFsCloseSidebar(sidebarElement,sidebarBackdrop) {
        $('#'+sidebarElement).removeClass('open');
        $('#'+sidebarBackdrop).removeClass('show');
    }

    
function editSubSegment(sub_stage_id){
    var url = '{{ baseUrl('predefined-case-sub-stages/edit') }}/'+sub_stage_id;
    $.ajax({
        url: url,
        type: "GET",
        data:{
            sub_stage_id:sub_stage_id,
        },
        dataType:"json",
        success: function (response) {
            $("#cds-fs-manageSubSegmentBody").html(response.contents);
            $(".sub-segment-heading").html("Edit Sub Stage");
            cdsFsOpenSidebar('cds-fs-manageSidebar','cds-fs-manageSidebarBackdrop');
        },
        error: function () {
            errorMessage('Failed to load segment details.</div>');
        }
    });
}

function cdsFsOpenViewSidebar() {
    $('#cds-fs-viewSegmentSidebar').addClass('open');
    $('#cds-fs-viewSidebarBackdrop').addClass('show');
}

function cdsFsCloseViewSidebar() {
    $('#cds-fs-viewSegmentSidebar').removeClass('open');
    $('#cds-fs-viewSidebarBackdrop').removeClass('show');
}

$(document).on('click', '.btn-view-sub-stages', function () {
    var id = $(this).data("id");
    var url = '{{ baseUrl('predefined-case-sub-stages/view/') }}/' + id;

    cdsFsOpenViewSidebar();

    $("#cds-fs-viewSegmentBody").html('<div class="text-center">Loading...</div>');
    $.ajax({
        url: url,
        type: "GET",
        dataType:"json",
        success: function (response) {
            $("#cds-fs-viewSegmentBody").html(response.contents);
        },
        error: function () {
            $("#cds-fs-viewSegmentBody").html('<div class="text-danger">Failed to load segment details.</div>');
        }
    });
});

$(document).on('click', '.btn-view-stages', function () {
    var id = $(this).data("id");
    var url = '{{ baseUrl('predefined-case-stages/views/') }}/' + id;

    cdsFsOpenViewSidebar();

    $("#cds-fs-viewSegmentBody").html('<div class="text-center">Loading...</div>');
    $.ajax({
        url: url,
        type: "GET",
        dataType:"json",
        success: function (response) {
            $("#cds-fs-viewSegmentBody").html(response.contents);
        },
        error: function () {
            $("#cds-fs-viewSegmentBody").html('<div class="text-danger">Failed to load segment details.</div>');
        }
    });
});

function markAsStageComplete(id) {

    Swal.fire({
        title: "Are you sure to Want to mark as complete?",
        // text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "{{baseUrl('/predefined-case-stages/mark-as-complete')}}",
                data: {
                    _token: csrf_token,
                    id: id,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        successMessage(response.message);
                        location.reload();
                    } else {
                        errorMessage(response.message);
                    }
                },
            });
        }
    });


}

function markAsSubStageComplete(id) {


    Swal.fire({
        title: "Are you sure to Want to mark as complete?",
        // text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#198754",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-success",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function(result) {
        if (result.value) {
            $.ajax({
                type: "POST",
                url: "{{baseUrl('/predefined-case-sub-stages/mark-as-complete')}}",
                data: {
                    _token: csrf_token,
                    id: id,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        successMessage(response.message);
                        location.reload();
                    } else {
                        errorMessage(response.message);
                    }
                },
            });
        }
    });


}
</script>
@endsection