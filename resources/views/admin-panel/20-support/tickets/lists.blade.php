@extends('admin-panel.layouts.app')
@section('styles')
<link rel="stylesheet" href="{{ asset('assets/css/24-CDS-ticket-system.css') }}">
@endsection
@section('page-submenu')
@php 
$page_arr = [
    'page_title' => 'Support Tickets',
    'page_description' => 'Manage individual and group messages via message centre.',
    'page_type' => 'support-tickets',
    'ticket' => $ticket,
];
@endphp
{!! pageSubMenu('support-tickets',$page_arr) !!}
@endsection
@section('content')

   <div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
<div class="CDSDashboardContainer-main-content">
<div class="CDSDashboardContainer-main-content-inner">   
<div class="CDSDashboardContainer-main-content-inner-header">@include("admin-panel.20-support.components.head-list")   </div>
<div class="CDSDashboardContainer-main-content-inner-body">
 <div class="CdsTicket-content-area">
        <div class="CdsTicket-tickets-container" id="tableList">
             <div id="common-skeleton-loader" style="display:none;">
            @include('components.loaders.support-ticket-loader')              
        </div>
        </div>
       
       @include('components.table-pagination01')
    </div>









   </div>
<div class="CDSDashboardContainer-main-content-inner-footer">   </div>

</div>





</div>
 </div>
@endsection
@section('javascript')
<script type="text/javascript">
const cookiePrefix = 'tickets_'; 
let sortColumn = getCookie(cookiePrefix + 'sortColumn') || 'created_at';
let sortDirection = getCookie(cookiePrefix + 'sortDirection') || 'desc';

$(document).ready(function() {
    $("#search-input").keyup(function() {
        var value = $(this).val();
        if (value == '') {
            loadData();
        }
        if (value.length > 2) {
            loadData();
        }
    });
    $("#status-filter, #priority-filter, #category-filter").change(function() {
        loadData();
    });
    $("#search-form").submit(function(e) {
        e.preventDefault();
        loadData();
    });
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
    loadData();
});
function loadData(page = 1) {
    var search = $("#searchInput").val();
  
    var category = $("#category-filter").val();
    // Get all checked checkbox values inside .CdsTickerStatus-filter
   // Get all checked checkbox values with class CdsTickerStatus-filter
    let statusCheckedValues = $('.CdsList-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();

    let priorityCheckedValues = $('.CdsList-Priority:checked')
        .map(function () {
            return $(this).val();
        }).get();
       
    let categoryCheckedValues = $('.CdsList-category:checked')
        .map(function () {
            return $(this).val();
        }).get();
        
    let startDate = $("#startDate").val();
    let endDate = $("#endDate").val();
        
    let unassigned = $('#unassigned:checked').val();
     let has_attachments = $('#has-attachments:checked').val();
    $.ajax({
        url: '{{ baseUrl('tickets/ajax-list') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            search: search,
            status: statusCheckedValues,
            unassigned:unassigned,
            priority: priorityCheckedValues,
            category_id: categoryCheckedValues,
            start_date:startDate,
            end_date:endDate,
            has_attachments:has_attachments,
            sort_column: sortColumn,
            sort_direction: sortDirection,
            page: page
        },
        beforeSend: function() {
            $("#common-skeleton-loader").show();
        },
        success: function(response) {
            if (response.contents) {
                $("#tableList").html(response.contents);
                $("#common-skeleton-loader").hide();
                updatePagination(response);
            }
        },
        error: function() {
            console.log('Error loading tickets');
        }
    });
}
function updatePagination(response) {
    $("#pageinfo").text(response.current_page + ' of ' + response.last_page);
    if (response.current_page == 1) {
        $(".previous").addClass('disabled');
    } else {
        $(".previous").removeClass('disabled');
    }
    if (response.current_page == response.last_page) {
        $(".next").addClass('disabled');
    } else {
        $(".next").removeClass('disabled');
    }
    $("#pageno").val(response.current_page);
}
function changePage(type) {
    var currentPage = parseInt($("#pageno").val()) || 1;
    if (type === 'next') {
        currentPage++;
    } else if (type === 'prev') {
        currentPage--;
    }
    loadData(currentPage);
}
function resetFilters() {
    $("#search-input").val('');
    $("#status-filter").val('');
    $("#priority-filter").val('');
    $("#category-filter").val('');
    loadData();
}
function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
</script>
@endsection 