@extends('admin-panel.layouts.app')
@section('styles')
<link href="{{ url('assets/css/13-CDS-case-stages.css') }}" rel="stylesheet" />
@endsection
@section('content')
<div class="ch-head">
                            <i class="fas fa-table me-1"></i>
                            {{ $predefined_case_stages->name }}
                        </div>
                        <div class="ch-action">
                            <a href="{{ baseUrl('predefined-case-stages') }}" class="CdsTYButton-btn-primary">
                                Back
                            </a>
                        </div>
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
-------------

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="cds-fs-card-container sortable" id="step-1">
                                    <div class="cds-fs-segment-card " draggable="true"  data-step="1" data-segment="{{ $predefined_case_stages->id }}" data-sort="{{ $predefined_case_stages->sort_order }}">
                                        <div class="cds-fs-card cds-fs-main-segment">
                                            <div class="funnel-main-segment">
                                                <h3>{{ $predefined_case_stages->name }}</h3>
                                                <h3>{{ $predefined_case_stages->fees }}</h3>
                                                <div class="cds-segement-description d-none">{{$predefined_case_stages->short_description}}</div>
                                                <button class="btn btn-info mb-2 btn-sm w-100 btn-view-stages" type="button" data-id="{{ $predefined_case_stages->unique_id }}">View Stage</button>
                                                <a class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm w-100" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('predefined-case-stages/delete/'.$predefined_case_stages->unique_id) }}">
                                                    Delete
                                                </a>
                                                </br>
                                                </br>
                                                AddedBy: {{$predefined_case_stages->userAdded->first_name}} {{$predefined_case_stages->userAdded->last_name}} 
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <div class="cds-fs-sub-segments sortable-sub-segment" id="sub-segment-{{ $predefined_case_stages->unique_id }}">
                                                @foreach($predefined_case_stages->predefinedCaseSubStages as $key => $sub_stages)
                                                    <div draggable="true" class="cds-fs-card cds-fs-sub-segment-card" data-segment="{{ $sub_stages->id }}" data-sort="{{ $sub_stages->sort_order }}">
                                                        <div class="funnel-main-segment">
                                                            <h3>{{ $sub_stages->name }}</h3>
                                                            <div class="cds-sub-segement-description d-none">{{$sub_stages->description}}</div>
                                                            
                                                            <button class="btn btn-info mb-2 btn-sm w-100 btn-view-sub-stages" type="button" data-id="{{ $sub_stages->unique_id }}">View Sub Stages</button>
                                                            
                                                            <button class="btn btn-warning btn-sm w-100 mb-2" type="button" data-id="{{ $sub_stages->unique_id }}" onclick="editSubSegment('{{  $sub_stages->unique_id }}')">Edit Sub stages</button>
                                                            <a class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm w-100" href="javascript:;" onclick="confirmAction(this)" data-href="{{ baseUrl('predefined-case-sub-stages/delete/'.$sub_stages->unique_id) }}">
                                                                Delete
                                                            </a>
                                                           
                                                            </br>   
                                                            </br>
                                                            AddedBy: {{$sub_stages->userAdded->first_name}} {{$sub_stages->userAdded->last_name}} 
                                                        </div>
                                                    </div>
                                                @endforeach
                                                
                                            </div>
                                            <div class="cds-fs-card cds-fs-sub-segment cds-fs-add-sub-segment" onclick="addSubStages('{{ $predefined_case_stages->id }}')">
                                                <i class="fa fa-plus"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                          
			</div>
	
	</div>
  </div>
</div>				

<!-- Add Sub stages -->
<div id="cds-fs-manageSidebar" class="cds-fs-sidebar-form">
    <div class="cds-fs-sidebar-header">
        <h4 class="sub-segment-heading"></h4>
        <button class="cds-fs-close-btn" onclick="cdsFsCloseSidebar('cds-fs-manageSidebar','cds-fs-manageSidebarBackdrop')">×</button>
    </div>
    <div class="cds-fs-sidebar-body" id="cds-fs-manageSubSegmentBody">
    </div>
</div>
<div id="cds-fs-manageSidebarBackdrop" class="cds-fs-sidebar-backdrop" onclick="cdsFsCloseSidebar('cds-fs-addSegmentSidebar','cds-fs-manageSidebarBackdrop')"></div>
<!-- end -->

<!-- view Segment -->

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
<div id="cds-fs-viewSidebarBackdrop" class="cds-fs-sidebar-backdrop" onclick="cdsFsCloseViewSidebar()"></div>
<!-- end -->
@endsection
<!-- End Content -->
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
</script>
@endsection