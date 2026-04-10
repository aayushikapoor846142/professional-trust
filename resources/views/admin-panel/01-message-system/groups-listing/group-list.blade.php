@extends('admin-panel.layouts.app')
@php 
$page_arr = [
    'page_title' => 'Group Listing',
    'page_description' => 'List of all types of groups',
    'page_type' => 'group-listing',
    'canCreateGroup'=>true
];
@endphp
@section('page-submenu')
{!! pageSubMenu('message',$page_arr) !!}
@endsection
@section('styles')
<link href="{{ url('assets/css/15-CDS-group-list.css') }}" rel="stylesheet" />
@endsection
@section('content')
<div class="CdsDashboardGroups-list-view-main-area">
    <!-- Header Row -->
    <div class="CdsDashboardGroups-list-view-top-header">
        <div class="CdsDashboardGroups-list-view-header-row">
            
            <div class="CdsDashboardGroups-list-view-header-buttons">
                {{-- <button class="CdsDashboardGroups-list-view-btn-secondary">Import</button>
                --}}
            </div>
        </div>
        

       
    </div>

    <!-- Body Rows -->
    <div class="CdsDashboardGroups-list-view-content-wrapper" id="groupGridBody">
        <div class="CdsDashboardGroups-list-view-toolbar">
            <div class="CdsDashboardGroups-list-view-info-text">
                <span id="total_rec"></span>
            </div>
            
        </div>
        <div class="CdsDashboardGroups-list-view-grid-container mb-3"></div>
        <!-- AJAX content loads here -->

        <div class="uap-view-more-link text-center" style="display: none;" id="viewMore">
            <a href="javascript:;" class="CdsTYButton-btn-primary loadMoreBtn">View More <i class="fa fa-chevron-down"></i></a>
        </div>
    </div>

    <div id="common-skeleton-loader" style="display: none;">
        @include('components.loaders.all-groups-loader')
    </div>
</div>


@endsection

@section('javascript')
<script type="text/javascript">
// Global variables
var csrf_token = "{{ csrf_token() }}";
var BASEURL = "{{ baseUrl('/') }}";

let searchTimeout;
let currentFocus = -1;
let searchResults = [];
const $searchInput = $('#search-input');
const $searchClear = $('#search-clear');
const $dropdown = $('#autocomplete-dropdown');
const $loading = $('.CdsDashboardAppointment-system-autocomplete-loading');
const $results = $('.CdsDashboardAppointment-system-autocomplete-results');
const $noResults = $('.CdsDashboardAppointment-system-autocomplete-no-results');
const $recentSection = $('#recent-searches');
const $searchSection = $('.CdsDashboardAppointment-system-autocomplete-search-section');


// Load initial data
 
var page = 1;
var isLoading = false;
var autoLoadCount = 0;
var maxAutoLoads = 2;
var hasMorePages = true;

// initial load
loadData(page);

// Search functionality
$("#search-input").keyup(function() {
    var value = $(this).val();
    
    // Clear previous timeout
    clearTimeout(searchTimeout);
    
    // Set new timeout for search
    searchTimeout = setTimeout(function() {
        if (value == '') {
            loadData(1);
        } else if (value.length > 2) {
            loadData(1);
        }
    }, 500); // 500ms delay
});

function loadData(pageNum = 1, isAutoLoad = false) {
    if (isLoading || !hasMorePages) return;

    isLoading = true;
    var search = $("#search-input").val();
    const pathParts = window.location.pathname.split('/').filter(Boolean);
    const lastPart = pathParts[pathParts.length - 1];
    let getUrl;

    if (lastPart == 'my-joined-group-list' || lastPart == 'my-created-group-list') {
        getUrl = BASEURL + '/group/my-groups-ajax-list?page=' + pageNum;
    } else if (lastPart == 'received-request') {
        getUrl = BASEURL + '/group/group-received-req-ajax-list?page=' + pageNum;
    } else {
        getUrl = BASEURL + '/group/ajax-list?page=' + pageNum;
    }

    $.ajax({
        type: "POST",
        url: getUrl,
        data: {
            _token: csrf_token,
            search: search,
            type: lastPart,
        },
        dataType: 'json',
        beforeSend: function() {
            $("#common-skeleton-loader").show();
        },
        success: function(data) {
            $("#common-skeleton-loader").hide();
            page = data.current_page;
            hasMorePages = data.current_page < data.last_page;

            if (pageNum === 1) {
                if (data.contents.trim() === "" && data.total_records < 1) {
                    $(".CdsDashboardGroups-list-view-grid-container").html('<div class="text-center text-gray-500">No groups found</div>');
                    $('#total_rec').html(data.total_records + ' records found');
                    $('#viewMore').hide();
                } else {
                    $(".CdsDashboardGroups-list-view-grid-container").html(data.contents);
                    $('#total_rec').html(data.total_records + ' records found');
                }
            } else {
                $(".CdsDashboardGroups-list-view-grid-container").append(data.contents);
            }

            // Increment auto load counter if autoLoad
            if (isAutoLoad) {
                autoLoadCount++;
            }

            // Show/hide View More based on auto load limit
            if (autoLoadCount >= maxAutoLoads && hasMorePages) {
                showViewMoreButton();
            } else {
                $('#viewMore').hide();
            }

            if (!hasMorePages) {
                $('#viewMore').hide();
            }

            isLoading = false;
        },
        complete: function() {
            $('#showLoader').hide();
            isLoading = false;
        },
        error: function() {
            console.error('Error loading groups');
            $('#showLoader').hide();
            isLoading = false;
        }
    });
}

function showViewMoreButton() {
    var html = '<div class="CdsDashboardGroups-view-more text-center" id="viewMore">' +
        '<button onclick="loadMoreGroups()" class="CdsTYButton-btn-primary loadMoreBtn">View More <i class="fa fa-chevron-down"></i></button>' +
        '</div>';
    $('#viewMore').remove();
    $(".CdsDashboardGroups-list-view-grid-container").after(html);
}

function loadMoreGroups() {
    if (hasMorePages && !isLoading) {
        loadData(page + 1, false);
    }
}

// infinite scroll
$(window).scroll(function() {
    if (autoLoadCount < maxAutoLoads && hasMorePages && !isLoading) {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            loadData(page + 1, true);
        }
    }
});

function confirmAction(element) {
    const href = element.getAttribute('data-href');
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            window.location.href = href;
        }
    });
}

// Hide dropdown
function hideDropdown() {
    $dropdown.removeClass('show');
    currentFocus = -1;
}

// Select an item

function acceptJoinRequest(member_id,id) {
    $.ajax({
        type: 'get',
        url: "{{ baseUrl('group/add-group-member') }}/" + member_id,
        dataType: 'json',
        success: function(response) {
            if (response.status == true) {
                successMessage(response.message);
                fetchGroupChatBotMessages(id);
                $('#join-request-' + response.unique_id).remove();
                if ((response.group_join_rqst_count) > 0) {
                    $('.join-rqst-counter').html(response.group_join_rqst_count);
                    window.location.reload();
                } else {
                    $('.join-rqst-counter').html('');
                }
            } else {
                errorMessage(response.message);
            }
            // console.log(data.contents);
            // $('.to-connet-div').html(data.contents);
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}


function rejectJoinRequest(member_id,id) {
    $.ajax({
        type: 'get',
        url: "{{ baseUrl('group/reject-group-member') }}/" + member_id,
        dataType: 'json',
        success: function(response) {
            if (response.status == true) {
                successMessage(response.message);
                // fetchChats();
                fetchGroupChatBotMessages(id);
                $('#join-request-' + response.unique_id).remove();
                window.location.reload();

            } else {
                errorMessage(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('Error fetching messages:', error);
        }
    });
}
</script>
@endsection