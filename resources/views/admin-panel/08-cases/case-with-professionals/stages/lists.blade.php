@extends('admin-panel.08-cases.case-with-professionals.my-cases-master')
<style>
    .CdsDashboardCaseStages-list-view-timeline-line::before {
        height: {{ $percentage }}%;
    }
</style>
@section('case-container')

<div class="CdsDashboardCaseStages-list-view-container">
    <!-- <button class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-primary CdsDashboardCaseStages-add-btn">
            <span>Add Stages</span>
        </button>    -->
    <div class="CdsDashboardCaseStages-list-view-progress-overview">
        
        <div class="CdsDashboardCaseStages-list-view-progress-header">
            <div>
                <h2 style="font-size: 20px; margin-bottom: 5px;">Case Progress Overview</h2>
                <p style="color: #6c757d;">Track your application status and pending tasks</p>
            </div>
            <div class="CdsDashboardCaseStages-list-view-progress-stats">
                <div class="CdsDashboardCaseStages-list-view-stat-item">
                    <div class="CdsDashboardCaseStages-list-view-stat-value">{{$percentage}}%</div>
                    <div class="CdsDashboardCaseStages-list-view-stat-label">Complete</div>
                </div>
                <div class="CdsDashboardCaseStages-list-view-stat-item">
                    <div class="CdsDashboardCaseStages-list-view-stat-value">{{$doneTask}}</div>
                    <div class="CdsDashboardCaseStages-list-view-stat-label">Tasks Done</div>
                </div>
                <div class="CdsDashboardCaseStages-list-view-stat-item">
                    <div class="CdsDashboardCaseStages-list-view-stat-value">{{$pendingTask}}</div>
                    <div class="CdsDashboardCaseStages-list-view-stat-label">Tasks Pending</div>
                </div>
                <div class="CdsDashboardCaseStages-list-view-stat-item">
                    <div class="CdsDashboardCaseStages-list-view-stat-value">18d</div>
                    <div class="CdsDashboardCaseStages-list-view-stat-label">Time Left</div>
                </div>
            </div>
            
        </div>
        <div class="CdsDashboardCaseStages-list-view-progress-bar-container">
            <div class="CdsDashboardCaseStages-list-view-progress-bar" style="width: {{ $percentage }}%;"></div>

        </div>
        <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6c757d;">
            <span>Started: April 25, 2024</span>
            <span>Expected: June 10, 2024</span>
        </div>
        <div class="mt-4 d-block d-md-flex justify-content-end flex-wrap gap-2">
            <button onclick="showPopup('<?= baseUrl('case-with-professionals/stages/add/' . $case_id) ?>')" class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-outline">
                <span>🔗</span>
                <span>Add Stages</span>
            </button>
            <button onclick="showPopup('<?php echo baseUrl('case-with-professionals/stages/workflow/'.$case_id) ?>')" class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-outline">
                <span>➕</span>
                <span>Generate Workflow via AI</span>
            </button>
            {{-- <button onclick="showPopup('<?php echo baseUrl('case-with-professionals/stages/workflow/'.$case_id) ?>')" class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-outline">
                <span>➕</span>
                <span>Generate Workflow via AI</span>
            </button> --}}
            <button onclick="showPopup('<?php echo baseUrl('case-with-professionals/stages/add-workflow/'.$case_id) ?>')" class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-outline">
                <span>➕</span>
                <span>Add Predefined Flow</span>
            </button>
        </div>
    </div>
    @if($records->isNotEmpty())
        <div class="CdsDashboardCaseStages-list-view-timeline-container">
            <div class="CdsDashboardCaseStages-list-view-timeline-line"></div>
            <!-- Stage 2: Active -->
            @foreach($records as $key => $stages)
                <div class="CdsDashboardCaseStages-list-view-timeline-stage">
                    @if($stages->status == "complete")
                        <div class="CdsDashboardCaseStages-list-view-timeline-dot CdsDashboardCaseStages-list-view-completed">✓</div>
                    @else
                    <div class="CdsDashboardCaseStages-list-view-timeline-dot CdsDashboardCaseStages-list-view-active">{{$key + 1}}</div>
                    @endif
                    <div class="CdsDashboardCaseStages-list-view-stage-card" style="opacity: {{ $stages->status == 'complete' ? '0.7' : '' }};">
                        <div class="CdsDashboardCaseStages-list-view-stage-header">
                            <div>
                                <h3 class="CdsDashboardCaseStages-list-view-stage-title">{{$stages->name}}</h3>
                                <p class="CdsDashboardCaseStages-list-view-stage-date">Started on {{date('d M Y',strtotime($stages->created_at))}}</p>
                            </div>
                            @if($stages->status == "complete")
                                <span class="CdsDashboardCaseStages-list-view-stage-badge  CdsDashboardCaseStages-list-view-completed">
                                    {{ucwords(str_replace('-', ' ', $stages->status))}}</span>
                            @elseif($stages->status == "in-progress")
                                <span class="CdsDashboardCaseStages-list-view-stage-badge CdsDashboardCaseStages-list-view-active">{{ucwords(str_replace('-', ' ', $stages->status))}}</span>

                            @else
                                <span class="CdsDashboardCaseStages-list-view-stage-badge ">{{ucwords(str_replace('-', ' ', $stages->status))}}</span>
                            @endif
                        </div>
                    
                        @if($stages->caseSubStages->isNotEmpty())
                            <div class="CdsDashboardCaseStages-list-view-list-header">
                                <div class="CdsDashboardCaseStages-list-view-list-header-section">
                                    <div class="CdsDashboardCaseStages-list-view-bulk-select">
                                        {!! FormHelper::formCheckbox([
                                            'id' => 'select-all',
                                            'label' => 'Select All',
                                        ]) !!}
                                        {{--<input type="checkbox" id="select-all">
                                        <label for="select-all">Select All</label>--}}
                                    </div>
                                    <span>{{$stages->caseSubStages->count()}} tasks</span>
                                </div>
                                <div class="CdsDashboardCaseStages-list-view-list-header-section">
                                    <span>Sort by: Priority</span>
                                </div>
                            </div>
                            
                            <div class="CdsDashboardCaseStages-list-view-sub-stages-list">
                                @foreach($stages->caseSubStages as $key => $sub_stages)
                                    <div class="CdsDashboardCaseStages-list-view-sub-stage-row" data-substage = "{{$sub_stages->unique_id}}"  style="opacity: {{ $sub_stages->status == 'complete' ? '0.7' : '' }};">
                                        {!! FormHelper::formCheckbox([]) !!}
                                        {{--<div class="CdsDashboardCaseStages-list-view-sub-stage-checkbox"></div>--}}
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
                                                <div class="CdsDashboardCaseStages-list-view-progress-indicator">
                                                    <div class="CdsDashboardCaseStages-list-view-progress-fill" style="width: {{ $sub_stages->status == 'complete' ? '100%' : '0%' }};"></div>
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
                                                    @if(checkPrivilege([
                                                        'route_prefix' => 'panel.case-with-professionals.stages.sub-stages',
                                                        'module' => 'professional-case-with-professionals.stages.sub-stages',
                                                        'action' => 'edit'
                                                    ]))
                                                        <button class="CdsDashboardCaseStages-list-view-action-btn" title="Edit" onclick="showPopup('<?= baseUrl('case-with-professionals/stages/sub-stages/edit/' . $sub_stages->unique_id) ?>')">✏️</button>
                                                    @endif
                                                    @if(checkPrivilege([
                                                        'route_prefix' => 'panel.case-with-professionals.stages.sub-stages',
                                                        'module' => 'professional-case-with-professionals.stages.sub-stages',
                                                        'action' => 'delete'
                                                    ]))
                                                    <button class="CdsDashboardCaseStages-list-view-action-btn" title="Delete" onclick="confirmAction(this)"
                                                        data-href="{{baseUrl('case-with-professionals/stages/sub-stages/delete/' . $sub_stages->unique_id)}}" >🗑️
                                                    </button>
                                                    @endif
                                                    
                                                        @if(checkPrivilege([
                                                            'route_prefix' => 'panel.case-with-professionals.stages.sub-stages',
                                                            'module' => 'professional-case-with-professionals.stages.sub-stages',
                                                            'action' => 'mark-as-complete'
                                                        ]))
                                                            <button class="CdsDashboardCaseStages-list-view-action-btn" title="Mark As Complete" onclick="markAsSubStageComplete('{{$sub_stages->unique_id}}')"
                                                                >✅
                                                            </button>
                                                        @endif
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
                            @if(checkPrivilege([
                                'route_prefix' => 'panel.case-with-professionals.stages',
                                'module' => 'professional-case-with-professionals.stages',
                                'action' => 'delete'
                            ]))
                                @if($stages->status != 'complete')
                                    <button onclick="confirmAction(this)" data-href="{{ baseUrl('case-with-professionals/stages/delete/'.$stages->unique_id) }}" class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-primary">
                                        <span>🗑️</span>
                                        <span>Delete Stages</span>
                                    </button>
                                @endif
                            @endif
                            @if(checkPrivilege([
                                'route_prefix' => 'panel.case-with-professionals.stages',
                                'module' => 'professional-case-with-professionals.stages',
                                'action' => 'edit'
                            ]))
                                @if($stages->status != 'complete')
                                    <button class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-outline" onclick="showPopup('<?= baseUrl('case-with-professionals/stages/edit/' . $stages->unique_id) ?>')">
                                        <span>✏️</span>
                                        <span>Edit Stage</span>
                                    </button>
                                @endif
                            @endif
                            @if(checkPrivilege([
                                'route_prefix' => 'panel.case-with-professionals.stages.sub-stages',
                                'module' => 'professional-case-with-professionals.stages.sub-stages',
                                'action' => 'add'
                            ]))
                                @if($stages->status != 'complete')
                                    <button class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-outline" onclick="showPopup('<?= baseUrl('case-with-professionals/stages/sub-stages/add/' . $stages->unique_id) ?>')" >
                                        <span>➕</span>
                                        <span>Add Sub Stage</span>
                                    </button>
                                @endif
                            @endif
                            @if($stages->status != 'complete')
                                    <a onclick="markAsStageComplete('{{$stages->unique_id}}')" class="CdsDashboardCaseStages-list-view-btn CdsDashboardCaseStages-list-view-btn-outline" title="Mark as Complete" href="javascript:;"><span>✅</span> Mark as Complete  </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="CdsDashboardCaseStages-list-view-timeline-container">
            <h5>Not stages added</h5>
        </div>
    @endif
</div>



@endsection

@section('javascript')
<script type="text/javascript">
// $(document).ready(function() {
//     loadData();
// });
// function loadData() {
//     $("#case-stages-loader").show();
//     var case_id = "{{$case_id}}";
//     $.ajax({
//         type: "POST",
//         url: BASEURL + '/case-with-professionals/stages/ajax-list',
//         data: {
//             _token: csrf_token,
//             case_id: case_id
//         },
//         dataType: 'json',
//         success: function(data) {
//             $("#stages-list").html(data.contents);

//         },
//         complete: function() {
//             $("#case-stages-loader").hide(); 
//         }
//     });
// }
function generateWorkFlow(){
    var case_id = "{{$case_id}}";
    $.ajax({
        url: '{{ baseUrl('case-with-professionals/stages/generate-workflow') }}',
        type: "post",
        data: {
            _token:csrf_token,
            case_id:case_id
        },
        dataType: "json",
        beforeSend: function() {
            showLoader();
        },
        success: function(response) {
            hideLoader();
            // if (response.status == true) {
            //     successMessage(response.message);
            //     location.reload();
            //     // $("#description").val(response.message);
            // } else {
            //     errorMessage(response.message);
            // }
        },
        error: function() {
            internalError();
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
                url: "{{baseUrl('/case-with-professionals/stages/sub-stages/mark-as-complete')}}",
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
                url: "{{baseUrl('/case-with-professionals/stages/mark-as-complete')}}",
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

// sub stages sorting 
$(".substages-sortable-container").sortable({
            handle: ".drag-handle",
            placeholder: "sortable-placeholder",
            opacity: 0.7, 
            cursor: "move",
            tolerance: "pointer",
            update: function(event, ui) {
                let subStageId = $(".substages-sortable-container > .drop-sub-stages-card").map(function(){
        return $(this).data("id");
    }).get();


        $.ajax({
            url: "{{ baseUrl('case-with-professionals/stages/sub-stages/update-sorting') }}",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), 
                subStageId: subStageId
            },
            success: function(response) {
                if (response.status === 'success') {
                    successMessage(response.message);
                    location.reload();
                } else {
                    validation(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
                
            }
        });
    }
}).disableSelection();
      

// sorting stages
$(".stages-sortable-container").sortable({
            handle: ".drag-handle",
            placeholder: "sortable-placeholder",
            opacity: 0.7, 
            cursor: "move",
            tolerance: "pointer",
            update: function(event, ui) {
                let stageId = $(".stages-sortable-container > .drop-stages-card").map(function(){
        return $(this).data("id");
    }).get();


        $.ajax({
            url: "{{ baseUrl('case-with-professionals/stages/update-sorting') }}",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), 
                stageId: stageId
            },
            success: function(response) {
                if (response.status === 'success') {
                    successMessage(response.message);
                    location.reload();
                } else {
                    validation(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
                
            }
        });
    }
}).disableSelection();


</script>
    <script>
        // Enhanced JavaScript for List View
        class CaseManagementListView {
            constructor() {
                this.selectedTasks = new Set();
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.animateProgress();
                this.initializeBulkOperations();
                this.initializeSorting();
            }

            setupEventListeners() {
                // Row click to expand details
                // document.querySelectorAll('.CdsDashboardCaseStages-list-view-sub-stage-row').forEach(row => {
                //     row.addEventListener('click', (e) => {
                //         if (!e.target.closest('.CdsDashboardCaseStages-list-view-sub-stage-checkbox') && 
                //             !e.target.closest('.CdsDashboardCaseStages-list-view-action-btn')) {
                //             this.expandRowDetails(row);
                //         }
                //     });
                // });

                // Checkbox functionality
                document.querySelectorAll('.CdsDashboardCaseStages-list-view-sub-stage-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.toggleTaskSelection(checkbox);
                    });
                });

                // Action buttons
                document.querySelectorAll('.CdsDashboardCaseStages-list-view-action-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        const action = btn.getAttribute('title');
                        this.handleAction(action, btn.closest('.CdsDashboardCaseStages-list-view-sub-stage-row'));
                    });
                });

                // Select all checkbox
                const selectAll = document.getElementById('select-all');
                if (selectAll) {
                    selectAll.addEventListener('change', (e) => {
                        this.toggleSelectAll(e.target.checked);
                    });
                }
            }

            animateProgress() {
                const progressBar = document.querySelector('.CdsDashboardCaseStages-list-view-progress-bar');
                if (progressBar) {
                    // setTimeout(() => {
                    //     progressBar.style.width = '65%';
                    // }, 500);
                }

                // Animate individual progress indicators
                document.querySelectorAll('.CdsDashboardCaseStages-list-view-progress-fill').forEach(fill => {
                    const width = fill.style.width;
                    fill.style.width = '0%';
                    setTimeout(() => {
                        fill.style.width = width;
                    }, 600);
                });
            }

            
            // expandRowDetails(row) {
            //     const isExpanded = row.classList.contains('expanded');
                
            //     // Close all other expanded rows
            //     document.querySelectorAll('.CdsDashboardCaseStages-list-view-sub-stage-row.expanded').forEach(r => {
            //         r.classList.remove('expanded');
            //         const details = r.querySelector('.CdsDashboardCaseStages-list-view-expanded-details');
            //         if (details) details.remove();
            //     });

            //     if (!isExpanded) {
            //         row.classList.add('expanded');
            //         const expandedDetails = document.createElement('div');
            //         expandedDetails.className = 'CdsDashboardCaseStages-list-view-expanded-details';
            //         expandedDetails.innerHTML = `
            //             <div style="padding: 20px; background: #f8f9fa; margin-top: 12px; border-radius: 8px;">
            //                 <h5 style="margin-bottom: 12px;">Additional Details</h5>
            //                 <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            //                     <div>
            //                         <label style="font-size: 12px; color: #6c757d;">Last Updated</label>
            //                         <p style="margin: 4px 0;">2 hours ago</p>
            //                     </div>
            //                     <div>
            //                         <label style="font-size: 12px; color: #6c757d;">Priority</label>
            //                         <p style="margin: 4px 0;">High</p>
            //                     </div>
            //                     <div>
            //                         <label style="font-size: 12px; color: #6c757d;">Documents</label>
            //                         <p style="margin: 4px 0;">3 files attached</p>
            //                     </div>
            //                     <div>
            //                         <label style="font-size: 12px; color: #6c757d;">Comments</label>
            //                         <p style="margin: 4px 0;">5 comments</p>
            //                     </div>
            //                 </div>
            //                 <div style="margin-top: 16px;">
            //                     <label style="font-size: 12px; color: #6c757d;">Notes</label>
            //                     <p style="margin: 4px 0; color: #495057;">Please ensure all forms are filled accurately. Contact legal team if assistance needed.</p>
            //                 </div>
            //             </div>
            //         `;
            //         row.appendChild(expandedDetails);
            //     }
            // }

            toggleTaskSelection(checkbox) {
                const row = checkbox.closest('.CdsDashboardCaseStages-list-view-sub-stage-row');
                const taskId = row.querySelector('.CdsDashboardCaseStages-list-view-sub-stage-title').textContent;
                
                if (checkbox.classList.contains('checked')) {
                    checkbox.classList.remove('checked');
                    checkbox.innerHTML = '';
                    this.selectedTasks.delete(taskId);
                } else {
                    checkbox.classList.add('checked');
                    checkbox.innerHTML = '✓';
                    this.selectedTasks.add(taskId);
                }

                this.updateBulkActionButtons();
            }

            toggleSelectAll(checked) {
                const checkboxes = document.querySelectorAll('.CdsDashboardCaseStages-list-view-sub-stage-checkbox');
                checkboxes.forEach(checkbox => {
                    const row = checkbox.closest('.CdsDashboardCaseStages-list-view-sub-stage-row');
                    if (!row.classList.contains('completed')) {
                        if (checked) {
                            checkbox.classList.add('checked');
                            checkbox.innerHTML = '✓';
                            const taskId = row.querySelector('.CdsDashboardCaseStages-list-view-sub-stage-title').textContent;
                            this.selectedTasks.add(taskId);
                        } else {
                            checkbox.classList.remove('checked');
                            checkbox.innerHTML = '';
                            this.selectedTasks.clear();
                        }
                    }
                });
                
                this.updateBulkActionButtons();
            }

            updateBulkActionButtons() {
                // Show/hide bulk action buttons based on selection
                if (this.selectedTasks.size > 0) {
                    console.log(`${this.selectedTasks.size} tasks selected`);
                }
            }

            handleAction(action, row) {
                const taskTitle = row.querySelector('.CdsDashboardCaseStages-list-view-sub-stage-title').textContent;
                
                switch(action) {
                    case 'Edit':
                        console.log(`Editing: ${taskTitle}`);
                        break;
                    case 'Comment':
                        console.log(`Adding comment to: ${taskTitle}`);
                        break;
                    case 'View Details':
                    case 'View':
                        console.log(`Viewing: ${taskTitle}`);
                        break;
                    case 'Download':
                        console.log(`Downloading: ${taskTitle}`);
                        break;
                    case 'Pay Now':
                        console.log(`Processing payment for: ${taskTitle}`);
                        break;
                    case 'Schedule':
                        console.log(`Scheduling: ${taskTitle}`);
                        break;
                    case 'More':
                        this.showMoreOptions(row);
                        break;
                }
            }

            showMoreOptions(row) {
                // Show context menu with additional options
                console.log('Showing more options...');
            }

            initializeBulkOperations() {
                // Add bulk operation buttons to the header
                const style = document.createElement('style');
                style.textContent = `
                    .CdsDashboardCaseStages-list-view-sub-stage-checkbox.checked {
                        background: #667eea;
                        border-color: #667eea;
                        color: white;
                    }
                    .CdsDashboardCaseStages-list-view-expanded-details {
                        grid-column: 1 / -1;
                    }
                    .CdsDashboardCaseStages-list-view-sub-stage-row.expanded {
                        background: white;
                        display: grid;
                        // grid-template-columns: auto 1fr;
                        gap: 16px;
                    }
                `;
                document.head.appendChild(style);
            }

            initializeSorting() {
                // Add sorting functionality
                const sortOptions = ['Priority', 'Due Date', 'Progress', 'Assignee', 'Status'];
                // Implementation would add dropdown for sorting
            }
        }

        // Initialize the list view
        const listView = new CaseManagementListView();
    </script>

    <script>
    $(document).ready(function () {
        // Prevent row collapse on actionable element click
        $('.CdsDashboardCaseStages-list-view-sub-stage-row').on('click', function (e) {
            if (
                $(e.target).is('a') ||
                $(e.target).closest('a').length ||
                $(e.target).is('button') ||
                $(e.target).closest('button').length ||
                $(e.target).is('input') ||
                $(e.target).closest('input').length
            ) {
                return;
            }

            expandRowDetails(this);
        });

        // // Button click inside expanded row
        // $(document).on('click', '.btn-complete-substage', function (e) {
        //     e.stopPropagation();
        //     let id = $(this).data('id');
        //     // Additional logic here
        // });

        // // Download link click inside expanded row
        // $(document).on('pointerdown', '.download-link', function (e) {
        //     e.stopPropagation();
        // });
    });

    function expandRowDetails(row) {
        const isExpanded = row.classList.contains('expanded');

        // Close all other expanded rows
        document.querySelectorAll('.CdsDashboardCaseStages-list-view-sub-stage-row.expanded').forEach(r => {
            r.classList.remove('expanded');
            const details = r.querySelector('.CdsDashboardCaseStages-list-view-expanded-details');
            if (details) details.remove();
        });

        if (!isExpanded) {
            row.classList.add('expanded');
            var case_id = "{{ $case_id }}";
            var substageId = $(row).data('substage');

            $.ajax({
                type: "GET",
                url: BASEURL + '/case-with-professionals/stages/fill-sub-stage/' + substageId,
                dataType: 'json',
                success: function (data) {
                    row.insertAdjacentHTML('beforeend', data.contents);
                },
                error: function () {
                    console.error("Failed to load sub-stage content.");
                }
            });
        }
    }
</script>

@endsection