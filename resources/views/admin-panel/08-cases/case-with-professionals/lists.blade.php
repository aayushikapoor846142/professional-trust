@extends('admin-panel.layouts.app')

@section('page-submenu')
{!! pageSubMenu('cases') !!}
@endsection
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/CDS-my-cases.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/20-1-CDS-cases-list.css') }}">
<style>
    .CdsDashboardCommon-system-view-toggle {
        display: flex;
        gap: 8px;
        margin-left: 16px;
    }

    .view-toggle-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        background: #667eea;
        color: #ffffff;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        font-weight: 500;
    }

    .view-toggle-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #475569;
    }

    .view-toggle-btn.active {
        background: #ffffff;
        border-color: #667eea;
        color: #667eea;
    }

    .view-toggle-btn i {
        font-size: 16px;
    }

    .view-toggle-btn span {
        display: inline-block;
    }

    /* Compact view styles */
    .compact-view .CdsTYDashboardCaselist-expanded-list-item {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
        padding: 16px;
    }

    .compact-view .CdsTYDashboardCaselist-expanded-list-item > div {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* List view styles */
    .list-view .CdsTYDashboardCaselist-expanded-list-item {
        display: block;
    }

    .list-view .CdsTYDashboardCaselist-expanded-list-item > div {
        border-bottom: 1px solid #e2e8f0;
        padding: 16px;
        margin-bottom: 0;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .CdsDashboardCommon-system-view-toggle {
            margin-left: 8px;
        }
        
        .view-toggle-btn span {
            display: none;
        }
        
        .view-toggle-btn {
            padding: 8px;
        }
    }
</style>
@endsection

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
<div class="CdsDashboardCommon-system-header">
        <!-- <h1 class="CdsDashboardCommon-system-title">{{ $pageTitle }}</h1>
        <p class="CdsDashboardCommon-system-subtitle">Comprehensive case tracking system</p> -->
        <form id="search-form" class="CdsTYDashboardCaselist-expanded-list-search-box">
            @csrf
            <div class="CdsDashboardCommon-system-controls">
                <div class="CdsDashboardCommon-system-search">
                    <span class="CdsDashboardCommon-system-search-icon">🔍</span>
                    <input type="text" class="CdsDashboardCommon-system-search-input"
                        placeholder="Search by case title or case id" id="search-input" name="search" >
                  
                </div>
                
                <!-- View Toggle Buttons -->
                <div class="CdsDashboardCommon-system-view-toggle">
                    <button type="button" class="view-toggle-btn" id="list-view-btn" data-tab="list">
                        <i class="fas fa-list"></i>
                        <span>List View</span>
                    </button>
                    <button type="button" class="view-toggle-btn" id="compact-view-btn" data-tab="compact">
                        <i class="fas fa-th-large"></i>
                        <span>Compact View</span>
                    </button>
                </div>
                
            </div>
        </form>
    </div>
   
    @include('admin-panel.08-cases.case-with-professionals.header-search')

			 </div>
           	<div id="list" class="tab-content">
                @include('admin-panel.08-cases.case-with-professionals.list-view-lists')
            </div>
            <div id="compact" class="tab-content">
                @include('admin-panel.08-cases.case-with-professionals.compact-lists')
            </div>
	
	</div>
  </div>
</div>

@endsection

@section('javascript')
<script type="text/javascript">
    // Global variables
    let currentFilter = 'all';
    let page = 1;
    let last_page = 1;
    let loading = false;
    let dataloading = true;
    let currentView = 'list'; // Default view

    // Global loadData function
    function loadData(page = 1, loadType = 'normal', dataView = '') {
        $("#my-cases-loader").show();
        var search = $("#searchInput").val();

        var service_id = $('#parent_service_id').val();
         
        var sub_service_id = $('#sub_service_id').val();

        var data = $("#search-form").serialize();
        data += '&filter=' + currentFilter;
        data += '&service_id=' + service_id;
        data += '&sub_service_id=' + sub_service_id;
        data += '&search=' + search;
        data += '&start_date=' + $("#startDate").val();
        data += '&end_date=' + $("#endDate").val();
        
        // Add data-view value if provided
        if (dataView) {
            data += '&data_view=' + encodeURIComponent(dataView);
        }

        // // Add filter parameters from header-search
        // if (typeof cdsfilterActiveFilters !== 'undefined') {
        //     if (cdsfilterActiveFilters.service && cdsfilterActiveFilters.service.length > 0) {
        //         data += '&service_filters=' + JSON.stringify(cdsfilterActiveFilters.service);
        //     }
        //     if (cdsfilterActiveFilters.seller && cdsfilterActiveFilters.seller.length > 0) {
        //         data += '&seller_filters=' + JSON.stringify(cdsfilterActiveFilters.seller);
        //     }
        //     if (cdsfilterActiveFilters.budget && cdsfilterActiveFilters.budget.length > 0) {
        //         data += '&budget_filters=' + JSON.stringify(cdsfilterActiveFilters.budget);
        //     }
        // }

        $.ajax({
            type: "POST",
            url: BASEURL + '/case-with-professionals/ajax-list?page=' + page,
            data: data,
            dataType: 'json',
            beforeSend: function () {
                dataloading = true;
            },
            success: function (data) {
                dataloading = false;
                $(".my-cases-view-more-link").remove();

                last_page = data.last_page;

                // Update stats
                if (data.stats) {
                    $("#total-cases").text(data.stats.total || 0);
                    $("#active-cases").text(data.stats.active || 0);
                }

                if (data.contents.trim() === "") {
                    loading = true;
                    if (data.current_page === 1) {
                        $(".myCasesContainer").html(
                            '<div class="text-center text-danger mt-5">No cases found.</div>'
                            );
                    }
                } else {
                    if (data.current_page === 1 || loadType === 'filter') {
                        $(".myCasesContainer").html(data.contents);
                    } else {
                        $(".myCasesContainer").append(data.contents);
                    }

                    // Initialize progress animations for new content
                    initializeProgressAnimations();
                }
            },
            complete: function () {
                $("#my-cases-loader").hide();
            }
        });
    }

    // Progress animation function
    function initializeProgressAnimations() {
        const progressBars = document.querySelectorAll(
            '.CdsTYDashboardCaselist-expanded-list-progress-fill:not(.animated)');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                    const width = entry.target.style.width;
                    entry.target.style.width = '0';
                    entry.target.classList.add('animated');
                    setTimeout(() => {
                        entry.target.style.width = width;
                    }, 100);
                }
            });
        });

        progressBars.forEach(bar => observer.observe(bar));
    }

    // Global serviceList function
    function serviceList(service_id, id) {
        $.ajax({
            url: BASEURL + '/cases/fetch-sub-service',
            data: {
                service_id: service_id
            },
            dataType: "json",
            beforeSend: function() {
                console.log('AJAX request started');
                $("#" + id).html('');
            },
            success: function(response) {
                console.log('AJAX response received:', response);
                if (response.status == true) {
                    $("#" + id).html(response.options);
                    // Clear sub-service selection and refresh cases list when main service changes
                    $("#" + id).val('').trigger('change');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                console.error('Response text:', xhr.responseText);
            }
        });
    }

    // Global checkbox functionality
    let selectedTasks = new Set();

    function toggleTaskSelection(checkbox) {
        const row = checkbox.closest('.CdsDashboardCaseStages-list-view-sub-stage-row');
        if (!row) return;
        
        const taskTitle = row.querySelector('.CdsDashboardCaseStages-list-view-sub-stage-title');
        const taskId = taskTitle ? taskTitle.textContent : checkbox.value;
        
        if (checkbox.classList.contains('checked')) {
            checkbox.classList.remove('checked');
            checkbox.innerHTML = '';
            selectedTasks.delete(taskId);
        } else {
            checkbox.classList.add('checked');
            checkbox.innerHTML = '✓';
            selectedTasks.add(taskId);
        }

        updateBulkActionButtons();
    }

    function updateBulkActionButtons() {
        // Show/hide bulk action buttons based on selection
        if (selectedTasks.size > 0) {
            console.log(`${selectedTasks.size} tasks selected`);
        }
    }

    // Add CSS for checkbox styling
    function addCheckboxStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .CdsDashboardCaseStages-list-view-sub-stage-checkbox.checked {
                background: #667eea;
                border-color: #667eea;
                color: white;
            }
        `;
        document.head.appendChild(style);
    }

    $(document).ready(function () {
        // Add checkbox styles
        addCheckboxStyles();

        // Filter pills
        const filterPills = document.querySelectorAll('.CdsTYDashboardCaselist-expanded-list-filter-pill');
        filterPills.forEach(pill => {
            pill.addEventListener('click', () => {
                filterPills.forEach(p => p.classList.remove(
                    'CdsTYDashboardCaselist-expanded-list-active'));
                pill.classList.add('CdsTYDashboardCaselist-expanded-list-active');
                currentFilter = pill.getAttribute('data-filter');
                page = 1; // Reset to first page
                loadData(1, 'filter');
            });
        });

        // Search functionality
        $("#search-input").keyup(function () {
            var value = $(this).val();
            if (value == '') {
                loadData();
            }
            if (value.length > 3) {
                loadData();
            }
        });

        // Header search functionality
        $("#searchInput").keyup(function () {
            var value = $(this).val();
            if (value == '') {
                loadData();
            }
            if (value.length > 3) {
                loadData();
            }
        });

        $("#search-form").submit(function (e) {
            e.preventDefault();
            loadData();
        });

        // Initial load
        loadData();


        // Infinite scroll
        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() >= $(".myCasesContainer").height() - 100) {
                if (page < last_page && !dataloading) {
                    if (page < 3) {
                        loading = true;
                        page++;
                        loadData(page, 'scrollload');
                    }
                }
            }
        });

        // Checkbox functionality
        document.querySelectorAll('.CdsDashboardCaseStages-list-view-sub-stage-checkbox').forEach(checkbox => {
            checkbox.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleTaskSelection(checkbox);
            });
        });

        // Single event handler for main service change
        $('#parent_service_id').on('change', function () {
            var service_id = $(this).val();
            if (service_id) {
                serviceList(service_id, 'sub_service_id');
            } else {
                // If no service selected, clear sub-service and refresh
                $('#sub_service_id').html('<option value="">All SubService</option>');
            }
        });

        // View toggle functionality
        // $('.view-toggle-btn').on('click', function() {
        //     const view = $(this).data('view');
            
        //     // Update button states
        //     $('.view-toggle-btn').removeClass('active');
        //     $(this).addClass('active');
            
        //     // Update current view
        //     currentView = view;
            
        //     // Apply view classes to container
        //     const container = $('.myCasesContainer');
        //     container.removeClass('list-view compact-view');
        //     container.addClass(view + '-view');
            
        //     // Store preference in localStorage
        //     localStorage.setItem('cases-view-preference', view);
            
        //     // Pass the data-view value to loadData function
        //     loadData(1, 'view-change', view);
        // });

        // // Load saved view preference
        // const savedView = localStorage.getItem('cases-view-preference');
        // if (savedView) {
        //     currentView = savedView;
        //     $('.view-toggle-btn').removeClass('active');
        //     $('#' + savedView + '-view-btn').addClass('active');
        //     $('.myCasesContainer').removeClass('list-view compact-view').addClass(savedView + '-view');
        // }
    });
    // switch tab

$(document).ready(function () {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'list'; 
       
        function switchToTab(tab) {
            $('.CdsDashboardCommon-system-view-toggle button').removeClass('active');
            $('.tab-content').addClass('d-none');
            $('.view-toggle-btn[data-tab="' + tab + '"]').addClass('active');
            $('#' + tab).removeClass('d-none');
        }

        switchToTab(activeTab);

        $('.view-toggle-btn').on('click', function (e) {
            e.preventDefault();
            const tab = $(this).data('tab');

            switchToTab(tab);

            const url = new URL(window.location.href);
            url.searchParams.set('tab', tab);
            window.history.replaceState({}, '', url);
        });
    });
    // end
</script>
@endsection
