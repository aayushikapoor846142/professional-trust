@extends('admin-panel.layouts.app')
@php 
$page_arr = [
    'page_title' => 'Forms Management',
    'page_description' => 'Create, manage, and organize your professional forms',
    'page_type' => 'forms-management',
    'canCreateForm' => checkPrivilege([
        'route_prefix' => 'panel.forms',
        'module' => 'professional-forms',
        'action' => 'add'
    ]),
    'canGenerateAI' => checkPrivilege([
        'route_prefix' => 'panel.forms',
        'module' => 'professional-forms',
        'action' => 'add'
    ]),
    'canUseTemplates' => checkPrivilege([
        'route_prefix' => 'panel.forms',
        'module' => 'professional-forms',
        'action' => 'add'
    ]),
];
@endphp
@section('page-submenu')
{!! pageSubMenu('cases', $page_arr) !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/17-CDS-custom-form.css') }}">
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 @include("admin-panel.08-cases.forms.components.header-search") 
            <!-- Filters Section -->
 <div class="CdsCustomForm-search-actions-container">
                     
                      <!-- Bulk Actions Bar -->
                      @if(checkPrivilege([
                          'route_prefix' => 'panel.forms',
                          'module' => 'professional-forms',
                          'action' => 'delete'
                      ]))
                      <div class="CdsCustomForm-bulk-actions hidden" id="bulkActionsBar">
                          <div class="CdsCustomForm-selected-count">
                              <span id="selectedCount">0</span> Selected
                          </div>
                          <div class="CdsCustomForm-bulk-actions-buttons">
                              <button class="CdsCustomForm-delete-btn" 
                                      onclick="deleteMultiple(this)" 
                                      data-href="{{ baseUrl('forms/delete-multiple') }}">
                                  Delete
                              </button>
                          </div>
                      </div>
                      @endif
                  </div>
               <div class="CdsCustomForm-filters-section">
                <div class="CdsCustomForm-filter-chips">
                    <button class="CdsCustomForm-filter-chip CdsCustomForm-active" onclick="cdsCustomFormFilterForms('all')">
                        All Forms
                        <span class="CdsCustomForm-count" id="allFormsCount">{{ $all_form_counts??0 }}</span>
                    </button>
                    <button class="CdsCustomForm-filter-chip" onclick="cdsCustomFormFilterForms('step_form')">
                        Step Forms
                        <span class="CdsCustomForm-count" id="stepFormsCount">{{ $step_form_counts??0 }}</span>
                    </button>
                    <button class="CdsCustomForm-filter-chip" onclick="cdsCustomFormFilterForms('single_form')">
                        Single Forms
                        <span class="CdsCustomForm-count" id="singleFormsCount">{{ $single_form_counts??0 }}</span>
                    </button>
                </div>
            </div>
 <!-- Master Checkbox Row -->
    <div class="CdsCustomForm-list-item" style="background: #f8f9fa; font-weight: 600;">
        <div class="d-flex align-items-center">
            {!! FormHelper::formCheckbox([
                'id' => 'datatableCheckAll',
                   'checkbox_class' => 'datatableCheckAll',
            ]) !!}
            <label class="form-label mb-0" for="datatableCheckAll">Select All</label>
        </div>
        
    </div>
			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                  <!-- List Container -->
                    <div class="CdsCustomForm-list-container" id="tableList">
                        <!-- Dynamic content will be loaded here -->
                    </div>
                    <div id="common-skeleton-loader" style="display:none;">
                        @include('components.loaders.form-loader')              
                    </div>
                    <!-- Empty State -->
                    <div class="CdsCustomForm-empty-state" id="emptyState" style="display: none;">
                        <div class="CdsCustomForm-empty-icon">📋</div>
                        <h3>No forms found</h3>
                        <p>Create your first form to get started</p>
                    </div>  <div class="cds-ty-dashboard-box-footer">
                    <!-- Pagination -->
                    <div class="row align-items-sm-center justify-content-center justify-content-sm-between">
                        <div class="col-md-4 col-sm-6 mb-2 mb-sm-0">
                            <div class="d-flex align-items-center justify-content-center justify-content-sm-start">
                                <span class="mr-2">Page:</span>
                                <span id="pageinfo"></span>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 pull-right">
                            <div class="justify-content-center justify-content-sm-end">
                                <nav id="datatablePagination" aria-label="Activity pagination">
                                    <div class="dataTables_paginate paging_simple_numbers" id="datatable_paginate">
                                        <ul id="datatable_pagination" class="justify-md-content-end datatable-custom-pagination pagination">
                                            <li class="disabled page-item paginate_item previous">
                                                <a class="btn btn-primary page-link paginate_button" aria-controls="datatable" data-dt-idx="0" tabindex="0" id="datatable_previous"><span aria-hidden="true">Prev</span></a>
                                            </li>
                                            <li class="me-2 ms-2 page-item paginate_item">
                                                <input onblur="changePage('goto')" min="1" type="number" id="pageno" class="form-control text-center" />
                                            </li>
                                            <li class="disabled next page-item paginate_item">
                                                <a class="btn btn-primary page-link paginate_button" aria-controls="datatable" data-dt-idx="3" tabindex="0"><span aria-hidden="true">Next</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <!-- End Pagination -->
                </div>
        
			</div>
	
	</div>
  </div>
</div>


<!-- End Content -->
@endsection

@section('javascript')
<script type="text/javascript">
    let currentFilter = 'all';
    let formCounts = {
        all: 0,
        step_form: 0,
        single_form: 0
    };
    
    // Auto-open AI modal if parameters are present
    $(document).ready(function() {
        const urlParams = new URLSearchParams(window.location.search);
        const autoOpenAi = urlParams.get('auto_open_ai');
        const mainServiceId = urlParams.get('main_service_id');
        const subServiceId = urlParams.get('sub_service_id');
        
        if (autoOpenAi === '1' && mainServiceId && subServiceId) {
            console.log('Auto-opening AI modal with:', { mainServiceId, subServiceId });
            
            // Store the values globally so they can be accessed by the modal
            window.autoFillMainServiceId = mainServiceId;
            window.autoFillSubServiceId = subServiceId;
            window.autoFillProcessStarted = false;
            
            // Auto-open the AI modal with prefilled data
            setTimeout(function() {
                showPopup('{{ baseUrl('forms/generate-via-ai') }}');
                
                // Clean up URL parameters after modal is opened
                const url = new URL(window.location);
                url.searchParams.delete('auto_open_ai');
                url.searchParams.delete('main_service_id');
                url.searchParams.delete('sub_service_id');
                window.history.replaceState({}, document.title, url);
                
                // Set up a MutationObserver to watch for when the modal content is loaded
                const modalObserver = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                            // Check if the modal content has been loaded
                            if ($("#popupModal #service_id").length && $("#popupModal #sub_service_id").length) {
                                console.log('Modal content detected, starting auto-fill process');
                                modalObserver.disconnect(); // Stop observing
                                
                                // Start the auto-fill process
                                startAutoFill();
                            }
                        }
                    });
                });
                
                // Start observing the modal
                if (document.getElementById('popupModal')) {
                    modalObserver.observe(document.getElementById('popupModal'), {
                        childList: true,
                        subtree: true
                    });
                    console.log('MutationObserver started');
                } else {
                    console.log('popupModal element not found, using fallback');
                }
                
                // Fallback: if observer doesn't work, try after a delay
                setTimeout(function() {
                    if (modalObserver && modalObserver.takeRecords) {
                        modalObserver.disconnect();
                        console.log('MutationObserver fallback triggered');
                        startAutoFill();
                    }
                }, 3000);
                
                // Also listen for the modal shown event as a backup
                $(document).on('shown.bs.modal', '#popupModal', function() {
                    console.log('Modal shown event triggered');
                    // Give it a moment for content to be fully loaded
                    setTimeout(function() {
                        if (window.autoFillMainServiceId && !window.autoFillProcessStarted) {
                            window.autoFillProcessStarted = true;
                            startAutoFill();
                        }
                    }, 500);
                });
            }, 500);
            
            // Function to handle auto-filling
            function startAutoFill() {
                if (window.autoFillProcessStarted) {
                    console.log('Auto-fill process already started, skipping...');
                    return;
                }
                
                window.autoFillProcessStarted = true;
                console.log('Starting auto-fill process...');
                
                // Wait for initSelect to complete
                setTimeout(function() {
                    if (window.autoFillMainServiceId && $("#service_id").length) {
                        console.log('Setting main service ID:', window.autoFillMainServiceId);
                        $("#service_id").val(window.autoFillMainServiceId).trigger('change');
                        
                        // Wait for the AJAX call to complete and sub-service options to load
                        setTimeout(function() {
                            if (window.autoFillSubServiceId && $("#sub_service_id").length) {
                                // Check if sub-service options have been loaded
                                const subServiceOptions = $("#sub_service_id option");
                                if (subServiceOptions.length > 1) {
                                    console.log('Setting sub service ID:', window.autoFillSubServiceId);
                                    $("#sub_service_id").val(window.autoFillSubServiceId).trigger('change');
                                    
                                    // Clear the global variables after use
                                    delete window.autoFillMainServiceId;
                                    delete window.autoFillSubServiceId;
                                } else {
                                    console.log('Sub-service options not loaded yet, waiting...');
                                    // Wait a bit more and try again
                                    setTimeout(function() {
                                        if (window.autoFillSubServiceId && $("#sub_service_id").length) {
                                            console.log('Setting sub service ID (retry):', window.autoFillSubServiceId);
                                            $("#sub_service_id").val(window.autoFillSubServiceId).trigger('change');
                                            
                                            // Clear the global variables after use
                                            delete window.autoFillMainServiceId;
                                            delete window.autoFillSubServiceId;
                                        }
                                    }, 1000);
                                }
                            } else {
                                console.log('Sub-service ID field not found');
                            }
                        }, 2000); // Wait for AJAX to complete
                    } else {
                        console.log('Main service ID field not found or value not set');
                    }
                }, 1500); // Wait for initSelect to complete
            }
        }
    });

    $(document).ready(function() {
        $(".next").click(function() {
            if (!$(this).hasClass('disabled')) {
                changePage('next');
            }
        });
        
        $(".previous").click(function() {
            if (!$(this).hasClass('disabled')) {
                changePage('prev');
            }
        });
        
        $("#search-input").keyup(function() {
            var value = $(this).val();
            if (value == '') {
                loadData();
            }
            if (value.length > 3) {
                loadData();
            }
        });
        
        $("#search-form").submit(function(e) {
            e.preventDefault();
            loadData();
        });
    });

    loadData();

    function loadData(page = 1) {
        var search = $("#searchInput").val();
        $.ajax({
            type: "POST",
            url: BASEURL + '/forms/ajax-list?page=' + page,
            data: {
                _token: csrf_token,
                search: search,
                form_type: currentFilter,
                form_type_filter:$("#form_type").val()
            },
            dataType: 'json',
            beforeSend: function() {
                $("#common-skeleton-loader").show();
            },
            success: function(data) {
                $("#tableList").html(data.contents);
                $("#common-skeleton-loader").hide();
                if (data.total_records > 0) {
                    $("#emptyState").hide();
                    $("#tableList").show();
                    
                    // Update pagination
                    var pageinfo = data.current_page + " of " + data.last_page + " <small class='text-danger'>(" + data.total_records + " records)</small>";
                    $("#pageinfo").html(pageinfo);
                    $("#pageno").val(data.current_page);
                    
                    if (data.current_page < data.last_page) {
                        $(".next").removeClass("disabled");
                    } else {
                        $(".next").addClass("disabled");
                    }
                    
                    if (data.current_page > 1) {
                        $(".previous").removeClass("disabled");
                    } else {
                        $(".previous").addClass("disabled");
                    }
                    
                    $("#pageno").attr("max", data.last_page);
                    
                    // Update counts
                    updateFormCounts(data);
                } else {
                    $("#tableList").hide();
                    $("#emptyState").show();
                    $("#pageinfo").html("0 of 0");
                }
                
                // Initialize checkbox functionality
                initializeCheckboxes();
            },
        });
    }

    function updateFormCounts(data) {
        // Update the total badge
        $("#totalRecordsBadge").text(data.total_records + " Total");
        
        // Update form counts if provided
        if (data.form_counts) {
            $("#allFormsCount").text(data.form_counts.all || 0);
            $("#stepFormsCount").text(data.form_counts.step_form || 0);
            $("#singleFormsCount").text(data.form_counts.single_form || 0);
        }
    }

    function initializeCheckboxes() {
        // Master checkbox
        $(".datatableCheckAll").off('change').on('change', function() {
            if ($(this).is(":checked")) {
                $(".row-checkbox").prop("checked", true);
            } else {
                $(".row-checkbox").prop("checked", false);
            }
            updateSelectionCount();
        });
        
        // Individual checkboxes
        $(".row-checkbox").off('change').on('change', function() {
            updateSelectionCount();
        });
        
        // Row click to select
        $(".CdsCustomForm-list-item").off('click').on('click', function(e) {
            if (!$(e.target).closest('.CdsCustomForm-action-btn, .CdsCustomForm-checkbox, .dropdown').length) {
                const checkbox = $(this).find('.row-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked'));
                updateSelectionCount();
            }
        });
    }

    function updateSelectionCount() {
        const checkedCount = $(".row-checkbox:checked").length;
        $("#datatableCounter").html(checkedCount);
        $("#selectedCount").html(checkedCount);
        
        // Show/hide bulk actions bar
        if (checkedCount > 0) {
            $("#bulkActionsBar").removeClass('hidden');
            // Animate the appearance
            $("#bulkActionsBar").css('opacity', '0').animate({ opacity: 1 }, 200);
        } else {
            $("#bulkActionsBar").addClass('hidden');
        }
        
        // Update master checkbox
        const totalCheckboxes = $(".row-checkbox").length;
        if (checkedCount === totalCheckboxes && totalCheckboxes > 0) {
            $("#datatableCheckAll").prop("checked", true);
        } else {
            $("#datatableCheckAll").prop("checked", false);
        }
    }

    function changePage(action) {
        var page = parseInt($("#pageno").val());
        if (action == 'prev') {
            page--;
        }
        if (action == 'next') {
            page++;
        }
        if (!isNaN(page)) {
            loadData(page);
        } else {
            errorMessage("Invalid Page Number");
        }
    }

    // Filter functionality
    function cdsCustomFormFilterForms(type) {
        // Update active filter
        $('.CdsCustomForm-filter-chip').removeClass('CdsCustomForm-active');
        event.target.closest('.CdsCustomForm-filter-chip').classList.add('CdsCustomForm-active');
        
        currentFilter = type;
        loadData(1); // Reset to first page when filtering
    }

    // View toggle
    function cdsCustomFormSetView(view) {
        $('.CdsCustomForm-view-btn').removeClass('CdsCustomForm-active');
        event.target.closest('.CdsCustomForm-view-btn').classList.add('CdsCustomForm-active');
        
        // Here you can implement different view modes
        console.log('Switching to', view, 'view');
        
        // You might want to save the view preference and reload data accordingly
        // localStorage.setItem('formsViewMode', view);
    }
    

</script>
@endsection