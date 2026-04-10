@extends('admin-panel.layouts.app')
@section('styles')
<link href="{{ url('assets/css/12-CDS-support-payment.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('assets/css/common-table-redesign.css') }}">

@endsection

@section('page-submenu')
@php 
$page_arr = [
    'page_title' => 'Global Invoices',
    'page_description' => 'Manage individual and group messages via message centre.',
    'page_type' => 'global-invoices',
];
@endphp
{!! pageSubMenu('earnings',$page_arr) !!}
@endsection

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
            <!-- Payment List -->
            <div class="CDSDashboardContainer-main-content-inner-header">
               @include("admin-panel.09-utilities.invoices.components.header-receipt") 
            </div>
            
            <div class="CDSDashboardContainer-main-content-inner-body">
                <div class="cdsTYDashboard-contribution-history-table-container">
                    <div class="cds-ty-dashboard-box-body">
                        <!-- div table -->
                        <div class="cdsTYDashboard-table-wrapper">
                            <div class="cdsTYDashboard-table">
                                <div class="cdsTYDashboard-table-header">
                                    <div class="cdsTYDashboard-table-cell cdsTYDashboard-table-cell-avatar-information" data-column="first_name" data-order="asc" onclick="sortTable(this)">Customer <span class="sort-arrow"></span></div>
                                    <div class="cdsTYDashboard-table-cell" data-column="invoice_number" data-order="asc" onclick="sortTable(this)">Invoice Number <span class="sort-arrow"></span></div>
                                    <div class="cdsTYDashboard-table-cell" data-column="payment_gateway" data-order="asc">Payment Gateway</div>
                                    <div class="cdsTYDashboard-table-cell" data-column="amount_paid" data-order="asc">Amount Paid</div>
                                    <div class="cdsTYDashboard-table-cell" data-column="paid_status" data-order="asc">Payment Status</div>
                                    <div class="cdsTYDashboard-table-cell sorted-asc" data-column="created_at" data-order="asc" onclick="sortTable(this)">Created At <span class="sort-arrow"></span></div>
                                    <div class="cdsTYDashboard-table-cell cdsTYDashboard-table-cell-action">Actions</div>
                                </div>
                                <div class="cdsTYDashboard-table-body" id="tableList">
                                    <div id="common-skeleton-loader" style="display:none;">
                                        @include('components.loaders.global-invoice-loader')              
                                    </div>
                                </div>
                                <div class="cdsTYDashboard-table-footer">
                                    <!-- Pagination -->
                                    <div class="cdsTYDashboard-table-footer-count">
                                        <span>Page:</span>
                                        <div class="CDSSupportPayment-page-info" id="pageinfo">
                                            <!-- Page info will be updated by Ajax -->
                                        </div>
                                    </div>
                                    <div class="CDSSupportPayment-page-controls">
                                        <button class="CDSSupportPayment-page-button previous" onclick="changePage('prev')">
                                            <i class="fa-solid fa-chevron-left"></i>
                                        </button>
                                        <input type="number" id="pageno" class="CDSSupportPayment-page-button" style="width: 60px;" onblur="changePage('goto')" min="1" />
                                        <button class="CDSSupportPayment-page-button next" onclick="changePage('next')">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- # div table -->
                    </div>
                </div>
            </div>         
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
// Existing table functionality
const cookiePrefix = 'invoices_'; 
let sortColumn = getCookie(cookiePrefix + 'sortColumn') || 'created_at';
let sortDirection = getCookie(cookiePrefix + 'sortDirection') || 'desc';

$(document).ready(function() {
    // Find the matching header cell
    const $el = $(".cdsTYDashboard-table-cell[data-column='" + sortColumn + "']");

    if ($el.length) {
        // Remove any existing arrow classes and text
        $('.sort-header').removeClass('sorted-asc sorted-desc')
                         .attr('data-order', 'asc')
                         .find('.sort-arrow').text('');
        // Apply current sort direction
        $el.attr('data-order', sortDirection)
           .addClass(sortDirection === 'asc' ? 'sorted-asc' : 'sorted-desc');
    }

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
    
    $("#datatableSearch").keyup(function() {
        var value = $(this).val();
        if (value == '') {
            loadData();
        }
        if (value.length > 3) {
            loadData();
        }
    });
    
    $("#datatableCheckAll").change(function() {
        if ($(this).is(":checked")) {
            $(".row-checkbox").prop("checked", true);
        } else {
            $(".row-checkbox").prop("checked", false);
        }
        if ($(".row-checkbox:checked").length > 0) {
            $("#datatableCounterInfo").show();
        } else {
            $("#datatableCounterInfo").hide();
        }
        $("#datatableCounter").html($(".row-checkbox:checked").length);
    });
});

loadData();

function sortTable(element) {
    var $el = $(element);
    var currentOrder = $el.attr('data-order');
    var columnName = $el.attr('data-column');
    
    var newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
    $el.attr('data-order', newOrder);
    
    // Reset others
    $('.sort-header').not($el)
        .attr('data-order', 'asc')
        .removeClass('sorted-desc sorted-asc');
    
    // Update current
    $el.removeClass('sorted-desc sorted-asc').addClass(newOrder === 'asc' ? 'sorted-asc' : 'sorted-desc');
    
    // Set global sort variables
    sortColumn = columnName;
    sortDirection = newOrder;
    setCookie(cookiePrefix + 'sortColumn', columnName, 24);
    setCookie(cookiePrefix + 'sortDirection', newOrder, 24);

    loadData();
}

function setCookie(name, value, hours = 24) {
    const expires = new Date(Date.now() + hours * 60 * 60 * 1000).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/`;
}

function getCookie(name) {
    return document.cookie
        .split('; ')
        .find(row => row.startsWith(name + '='))
        ?.split('=')[1];
}

function loadData(page = 1) {
     var search = $("#searchInput").val();
     let status = $('.CdsTYDashboard-status-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();

    let price_range = $('.CdsTYDashboard-price-range:checked')
        .map(function () {
            return $(this).val();
        }).get();
  let hour_range = $('.CdsTYDashboard-hours-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();

        let min_range = $("#minPrice").val();
        let max_range = $("#maxPrice").val();
           let startDate = $("#startDate").val();
    let endDate = $("#endDate").val();
    $.ajax({
        type: "POST",
        url: BASEURL + '/invoices/ajax-list?page=' + page,
        data: {
            _token: csrf_token,
            search: search,
            sort_direction: sortDirection,
            sort_column: sortColumn,
            status:status,
    price_range:price_range,
    min_range:min_range,
    max_range:max_range,
    start_date:startDate,
    end_date:endDate,
    hour_range:hour_range
        },
        dataType: 'json',
        beforeSend: function() {
            $("#common-skeleton-loader").show();
        },
        success: function(data) {
            $(".norecord").remove(); 
            $("#tableList").html(data.contents);
            $("#common-skeleton-loader").hide();
            
            // Initialize dropdowns after content loads
            initializeTableDropdowns();
            
            if (data.total_records > 0) {
                var pageinfo = data.current_page + " of " + data.last_page + " <small class='text-danger'>(" + data.total_records + " records)</small>";
                $("#pageinfo").html(pageinfo);
                $("#pageno").val(data.current_page);
                if (data.current_page < data.last_page) {
                    $(".next").removeClass("disabled");
                } else {
                    $(".next").addClass("disabled", "disabled");
                }
                if (data.current_page > 1) {
                    $(".previous").removeClass("disabled");
                } else {
                    $(".previous").addClass("disabled", "disabled");
                }
                $("#pageno").attr("max", data.last_page);
            } else {
                $(".cdsTYDashboard-table").find(".norecord").remove();
                var html = '<div class="cdsTYDashboard-empty-list norecord">No records available</div>';
                $(".cdsTYDashboard-table-body").append(html);
            }
        },
    });
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

// Smart Dropdown Class
class TableSmartDropdown {
    constructor(element) {
        this.dropdown = element;
        this.button = element.querySelector('.cdsTYDashboardDropdownsDropdownBtn');
        this.menu = element.querySelector('.cdsTYDashboardDropdownsDropdownMenu');
        this.dropdownId = element.getAttribute('data-dropdown-id');
        this.isOpen = false;
        
        this.init();
    }

    init() {
        if (!this.button || !this.menu) return;
        
        // Toggle dropdown on button click
        this.button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggle();
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!this.dropdown.contains(e.target)) {
                this.close();
            }
        });

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        // Recalculate position on window resize
        window.addEventListener('resize', () => {
            if (this.isOpen) {
                this.positionMenu();
            }
        });

        // Recalculate position on scroll
        window.addEventListener('scroll', () => {
            if (this.isOpen) {
                this.positionMenu();
            }
        }, true);
    }

    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    open() {
        // Close all other dropdowns
        document.querySelectorAll('.cdsTYDashboardDropdownsDropdown').forEach(dropdown => {
            if (dropdown !== this.dropdown && dropdown._dropdownInstance) {
                dropdown._dropdownInstance.close();
            }
        });

        this.isOpen = true;
        this.button.classList.add('cdsTYDashboardDropdownsActive');
        this.menu.classList.add('cdsTYDashboardDropdownsShow');
        this.positionMenu();
    }

    close() {
        this.isOpen = false;
        this.button.classList.remove('cdsTYDashboardDropdownsActive');
        this.menu.classList.remove('cdsTYDashboardDropdownsShow');
    }

    positionMenu() {
        // Reset position classes
        this.menu.classList.remove(
            'cdsTYDashboardDropdownsPositionBottom',
            'cdsTYDashboardDropdownsPositionTop',
            'cdsTYDashboardDropdownsPositionBottomLeft',
            'cdsTYDashboardDropdownsPositionTopLeft',
            'cdsTYDashboardDropdownsPositionBottomRight',
            'cdsTYDashboardDropdownsPositionTopRight'
        );

        // Get viewport dimensions
        const viewport = {
            width: window.innerWidth,
            height: window.innerHeight,
            scrollY: window.scrollY
        };

        // Get button and menu dimensions
        const buttonRect = this.button.getBoundingClientRect();
        const menuRect = this.menu.getBoundingClientRect();

        // Calculate available space
        const space = {
            top: buttonRect.top,
            bottom: viewport.height - buttonRect.bottom,
            left: buttonRect.left,
            right: viewport.width - buttonRect.right
        };

        // Determine vertical position
        let verticalPosition = 'bottom';
        if (space.bottom < menuRect.height + 20) {
            if (space.top > space.bottom) {
                verticalPosition = 'top';
            }
        }

        // Determine horizontal position (default to right-aligned for action menus)
        let horizontalPosition = 'right';
        if (space.right < 20) {
            horizontalPosition = 'left';
        }

        // Apply position class
        let positionClass = '';
        if (verticalPosition === 'top') {
            positionClass = horizontalPosition === 'left' ? 'cdsTYDashboardDropdownsPositionTopLeft' : 'cdsTYDashboardDropdownsPositionTopRight';
        } else {
            positionClass = horizontalPosition === 'left' ? 'cdsTYDashboardDropdownsPositionBottomLeft' : 'cdsTYDashboardDropdownsPositionBottomRight';
        }
        
        this.menu.classList.add(positionClass);
    }
}

// Function to initialize dropdowns (can be called after dynamic content loads)
function initializeTableDropdowns() {
    document.querySelectorAll('.cdsTYDashboardDropdownsDropdown').forEach(dropdown => {
        if (!dropdown._dropdownInstance) {
            dropdown._dropdownInstance = new TableSmartDropdown(dropdown);
        }
    });
}

// Initialize dropdowns on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeTableDropdowns();
});
</script>
@endsection