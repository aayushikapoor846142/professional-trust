@extends('admin-panel.layouts.app')
@php 
$page_arr = [
    'page_title' => 'Predefined Templates',
    'page_description' => 'Browse and use pre-built form templates',
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

            <!-- Filters Section -->
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
                <button class="CdsCustomForm-create-btn" onclick="window.location.href='{{ baseUrl('forms') }}'">
                    Forms
                </button>
            </div>

         <!-- Search Section -->
                      <div class="CdsCustomForm-search-section">
                          <form id="search-form" class="CdsCustomForm-search-wrapper">
                              @csrf
                              <input type="text" 
                                    class="CdsCustomForm-search-input" 
                                    name="search" 
                                    id="search-input" 
                                    placeholder="Search Forms..."
                                    autocomplete="off">
                              <button type="submit" class="CdsCustomForm-search-btn">
                                  <i class="fa fa-search"></i>
                              </button>
                          </form>
                      </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
                    <!-- List Container -->
                    <div class="CdsCustomForm-list-container" id="tableList">
                        <!-- Dynamic content will be loaded here --><div id="common-skeleton-loader" style="display:none;">
                        @include('components.loaders.form-loader')              
                    </div>
                    </div>@include('components.table-pagination01') 							
                    
                    <!-- Empty State -->
                    <div class="CdsCustomForm-empty-state" id="emptyState" style="display: none;">
                        <div class="CdsCustomForm-empty-icon">📋</div>
                        <h3>No forms found</h3>
                        <p>Create your first form to get started</p>
                    </div>
              	</div>
	
	</div>
  </div>
</div>


@endsection

@section('javascript')
<script type="text/javascript">
    let currentFilter = 'all';
    let formCounts = {
        all: 0,
        step_form: 0,
        single_form: 0
    };

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
        var search = $("#search-input").val();
        $.ajax({
            type: "POST",
            url: BASEURL + '/forms/predefined-ajaxtemplates?page=' + page,
            data: {
                _token: csrf_token,
                search: search,
                form_type: currentFilter
            },
            dataType: 'json',
            beforeSend: function() {
                $("#tableList").html('');
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