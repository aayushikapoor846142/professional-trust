@extends('admin-panel.layouts.app')
@section('styles')
<link rel="stylesheet" href="{{ url('assets/css/18-CDS-professional-list.css') }}">

@endsection
@section('content')
<!-- Content -->
<div class="container-fluid">
    <section class="cds-ty-dashboard-breadcrumb-container">
        <div class="cds-main-layout-header">
            <div class="breadcrumb-conatiner">
                <ol class="breadcrumb">
                    <i class="fa-grid-2 fa-regular"></i>
                    <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('/') }}">Dashboard</a></li>
                    <li class="active breadcrumb-item" aria-current="page">{{$pageTitle}}</li>
                </ol>
            </div>
            <div class="cds-heading">
                <div class="cds-heading-icon">
                    <i class="fa-light fa-pen"></i>
                </div>
                <h1>{{$pageTitle}}</h1>
            </div>
        </div>
    </section>
	<!-- filter -->
<div class="office-preview">
            @php
            $statuses = [
                'all' => 'All',
                'pending' => 'Pending',
                'accepted' => 'Accepted',
                'rejected' => 'Rejected'
            ];
            $currentStatus = request()->query('status', 'all');
            @endphp

            <ul  class="nav nav-tabs status-tabs" id="viewerTabs" role="tablist">
                @foreach ($statuses as $key => $label)
                    @php
                        $isActive = ($currentStatus === null && $key === 'all') || $currentStatus === $key;
                        $count = $appointmentsCount[$key] ?? 0;
                    @endphp
                    <li class="{{ $isActive ? 'cds-active' : '' }} nav-item">
                        <a href="{{ baseUrl('associates' . ($key !== 'all' ? '?status=' . $key : '')) }}">
                            {{ $label }} ({{ $count }})
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
	<!-- end filter -->
    <!-- List Section -->
    <div class="CDSProfessionalList-view-list-section">
        <div class="CDSProfessionalList-view-container">
            <div class="CDSProfessionalList-view-list-container">
                <!-- List Item 1 -->
               
            </div>
        </div>
    </div>
	<!-- end new -->
</div>


<!-- End Content -->
@endsection

@section('javascript')
<script type="text/javascript">
	 let p_page = 1;
        let p_last_page = 1;
        let p_loading = false;
let sortColumn = getCookie('sortColumn') || 'created_at';
let sortDirection = getCookie('sortDirection') || 'desc';
  $(document).ready(function() {
    sortColumn = getCookie('sortColumn') || 'created_at';
    sortDirection = getCookie('sortDirection') || 'desc';

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
    $("#search-input").keyup(function() {
        var value = $(this).val();
        if (value == '') {
          loadData();
        }
        if (value.length > 3) {
          loadData();
        }
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
    $("#search-form").submit(function(e) {
      e.preventDefault();
      loadData();
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

  })
  loadData();
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

function sortTable(element) {
    var $el = $(element);
    var currentOrder = $el.attr('data-order');
    var columnName = $el.attr('data-column');
    
    var newOrder = currentOrder === 'asc' ? 'desc' : 'asc';
    $el.attr('data-order', newOrder);
    
    // Reset others
     $('.sort-header').not($el)
        .attr('data-order', 'asc')
        .removeClass('sorted-desc sorted-asc')
        .find('.sort-arrow').text('');
    
    // Update current - fix the arrow text
    
    $el.removeClass('sorted-desc sorted-asc').addClass(newOrder === 'asc' ? 'sorted-asc' : 'sorted-desc');
    
    // Set global sort variables
    sortColumn = columnName;
    sortDirection = newOrder;
    setCookie('sortColumn', sortColumn, 24);
setCookie('sortDirection', sortDirection, 24);
    loadData();
}


  function loadData(p_page = 1) {
    var search = $("#search-input").val();
    
    // Get query parameters from current URL
    const urlParams = new URLSearchParams(window.location.search);
    
    // Prepare data object with existing parameters
    var requestData = {
      _token: csrf_token,
      sort_direction: sortDirection,
      sort_column: sortColumn,
      status: "{{$status}}"
    };
    
    // Add search term if exists
    if (search) {
      requestData.search = search;
    }
    
    // Also check for search term in URL parameters
    const urlSearchTerm = urlParams.get('search');
    if (urlSearchTerm && !search) {
      requestData.search = decodeURIComponent(urlSearchTerm);
    }
    
    // Add filter parameters from URL if they exist
    const immigrationServiceType = urlParams.getAll('immigration_service_type[]');
    if (immigrationServiceType.length > 0) {
      requestData.immigration_service_type = immigrationServiceType.map(value => decodeURIComponent(value));
    }
    
    const yearsOfExperience = urlParams.getAll('years_of_experience[]');
    if (yearsOfExperience.length > 0) {
      requestData.years_of_experience = yearsOfExperience.map(value => decodeURIComponent(value));
    }
    
    const licenseStatus = urlParams.getAll('license_status[]');
    if (licenseStatus.length > 0) {
      requestData.license_status = licenseStatus.map(value => decodeURIComponent(value));
    }
    
    const language = urlParams.getAll('language[]');
    if (language.length > 0) {
      requestData.language = language.map(value => decodeURIComponent(value));
    }
    
    const locationFilter = urlParams.get('location_filter');
    if (locationFilter) {
      requestData.location_filter = decodeURIComponent(locationFilter);
    }
    
    // Clear existing results if it's the first page
    if (p_page === 1) {
      $(".CDSProfessionalList-view-list-container").empty();
    }
    
    $.ajax({
      type: "POST",
      url: BASEURL + '/associates/ajax-list?page=' + p_page,
      data: requestData,
      dataType: 'json',
      beforeSend: function() {
        showLoader();
        // $(".CDSProfessionalList-view-list-container").html("<div class='text-center py-2'><i class='fa fa-spin fa-spinner fa-3x'></i></div>");
        // $("#paginate").html('');
      },
      success: function(data) {
        $(".norecord").remove();  
        hideLoader();
        dataloading = false;
        $(".professional-view-more-link").remove();

        p_last_page = data.last_page;

        if (data.contents.trim() === "") {
          p_loading = true;
          if (data.current_page === 1) {
            $(".CDSProfessionalList-view-list-container").html(
              '<div class="text-center text-danger mt-5">No professional found.</div>'
            );
          }
        } else {
          if (data.current_page === 1) {
            console.log(data.current_page);
            $(".CDSProfessionalList-view-list-container").html(data.contents);
          } else {
            $(".CDSProfessionalList-view-list-container").append(data.contents);
          }
        }
      },
      complete: function () {
        dataloading = false; 
      }
    });
  }
$(window).scroll(function () {
    const container = $(".CDSProfessionalList-view-list-container");

    if (container.length === 0) return;

    const containerOffsetTop = container.offset().top;
    const containerHeight = container.outerHeight();
    const windowBottom = $(window).scrollTop() + $(window).height();

    if (windowBottom >= containerOffsetTop + containerHeight - 100) {
        if (p_page < p_last_page && !dataloading && p_page < 3) {
            dataloading = true; // ✅ immediately set it before load starts
            p_page++;
            loadData(p_page, 'scrollload');
        }
    }
});


</script>
	<script>
	function confirmReject(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to reject?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}
function confirmAccept(e) {
    var url = $(e).attr("data-href");
    Swal.fire({
        title: "Are you sure to accept?",
        text: "You won't be able to revert this!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "btn btn-primary",
        cancelButtonClass: "btn btn-danger ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            redirect(url);
        }
    });
}
   

	</script>
@endsection