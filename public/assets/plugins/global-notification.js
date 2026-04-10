let resizeTimer;
let currentIsMobile = isMobileView(); // track current layout mode
let page = 1;
let last_page = 1;
let loading = false;
let currentView = 'list'; // Track current view: 'list' or 'grid'
let dataloading = false;

$(document).ready(async function () {
    // Set initial button state
    $('#listViewBtn').removeClass('btn-outline-primary').addClass('btn-primary');
    $('#gridViewBtn').removeClass('btn-primary').addClass('btn-outline-primary');
    
    // Set initial container visibility
    $('#casesList').closest('ul').fadeIn(300);
    $('.CDSPostCaseNotifications-grid-view02-container').hide();
    
    window.Echo.leave(`global-notif.` + currentUserId);
    window.Echo.private(`global-notif.${currentUserId}`).listen("GlobalNotification", (e) => {
        const response = e.data;
        console.log(response);
        if (response.action == "post_case") {
            if (response.receiver_id == currentUserId) {
                if(liveChat == "enable"){
                    caseNotif(response.message);
                    if (currentView === 'list') {
                        listCaseData(1, response.case_id);
                    } else {
                        gridCaseData(1, response.case_id);
                    }
                    renderGlobalNotificationMessage();
                }
            }
        }
    });
});

function caseNotif(message) {
    // const notificationTone = document.getElementById("notification-tone");
    // notificationTone.play();
    const caseNotification = document.getElementById('CDSPostCaseNotifications-notification');
    caseNotification.textContent = '✅' + message;
    caseNotification.classList.add('CDSPostCaseNotifications-list-view-show');
    
    setTimeout(() => {
        caseNotification.classList.remove('CDSPostCaseNotifications-list-view-notification');
    }, 10000);
}



function renderGlobalNotificationMessage()
{
    $.ajax({
        type: "GET",
        url: BASEURL + '/get-global-notification',
        data: {
            _token: csrf_token,
        },
        dataType: 'json',
        success: function(data) {
            $(".cds-global-notification").html(data.contents);
            $("#badgeCount").html(data.count);
        },
    });
}


window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
        const newIsMobile = isMobileView();
        // Only reload data if the layout mode changed
        if (newIsMobile !== currentIsMobile) {
            currentIsMobile = newIsMobile;
            // Reload data based on current view
            if (currentView === 'list') {
                listCaseData(1);
            } else {
                gridCaseData(1);
            }
        }
    }, 250);
});

function isMobileView() {
    return window.innerWidth <= 1024;
}

function listCaseData(page = 1, case_id) {
    if (currentView !== 'list') {
        // Reset page counter when switching to list view
        page = 1;
    }
    currentView = 'list'; // Set current view
    console.log('Loading list data, page:', page);
    const is_mobile = isMobileView(); // always fresh check
    let priority = $('.CdsTYDashboard-priority:checked')
        .map(function () {
            return $(this).val();
        }).get();

    let hour_range = $('.CdsTYDashboard-hours-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();
    $.ajax({
        type: "POST",
        url: BASEURL + '/cases/ajax-list?page=' + page,
        data: {
            _token: csrf_token,
            is_mobile: is_mobile,
            type:type,
            case_id:case_id,
            service_id:$('#parent_service_id').val(),
            sub_service_id:$('#sub_service_id').val(),
            search:$("#searchInput").val(),
            priority:priority,
            start_date:$("#startDate").val(),
            end_date:$("#endDate").val(),
            hour_range:hour_range,
            trending_case:$('#trending-case:checked').val(),
            sort_by:$("#sort_by").val()
        },
        dataType: 'json',
        beforeSend: function () {
            // Optional: clear or show loader
           $("#common-skeleton-loader").show();
        },
        success: function (data) {
            $(".norecord").remove();  
            $("#common-skeleton-loader").hide();
            dataloading = false;
            $(".posting-case-more-link").remove();

            last_page = data.last_page;

            if (data.contents.trim() === "") {
                loading = true;
                if (data.current_page === 1) {
                    $("#casesList").html(
                        '<div class="text-center text-danger my-2">No cases found.</div>'
                        );
                }
            } else {
                if (data.current_page === 1) {
                    $("#casesList").html(data.contents);
                } else {
                    $("#casesList").append(data.contents);
                }

                setTimeout(function () {
                    document.querySelectorAll('.CDSPostCaseNotifications-list-view02-feed-item.CDSPostCaseNotifications-list-view02-new')
                        .forEach(function (el) {
                            el.classList.remove('CDSPostCaseNotifications-list-view02-new');
                        });
                }, 15000);

            }
        },
        error: function(xhr, status, error) {
            $("#common-skeleton-loader").hide();
            dataloading = false;
            console.error('Error loading list data:', error);
            if (data.current_page === 1) {
                $("#casesList").html(
                    '<div class="text-center text-danger my-2">Error loading data. Please try again.</div>'
                );
            }
        },
        complete: function () {
            dataloading = false; 
        }
    });
}

function gridCaseData(page = 1, case_id) {
    if (currentView !== 'grid') {
        // Reset page counter when switching to grid view
        page = 1;
    }
    currentView = 'grid'; // Set current view
    console.log('Loading grid data, page:', page);
    const is_mobile = isMobileView(); // always fresh check
    let priority = $('.CdsTYDashboard-priority:checked')
        .map(function () {
            return $(this).val();
        }).get();

    let hour_range = $('.CdsTYDashboard-hours-filter:checked')
        .map(function () {
            return $(this).val();
        }).get();
    $.ajax({
        type: "POST",
        url: BASEURL + '/cases/grid-ajax-list?page=' + page,
        data: {
            _token: csrf_token,
            is_mobile: is_mobile,
            type:type,
            case_id:case_id,
            service_id:$('#parent_service_id').val(),
            sub_service_id:$('#sub_service_id').val(),
            search:$("#searchInput").val(),
            priority:priority,
            start_date:$("#startDate").val(),
            end_date:$("#endDate").val(),
            hour_range:hour_range,
            trending_case:$('#trending-case:checked').val(),
            sort_by:$("#sort_by").val()
        },
        dataType: 'json',
        beforeSend: function () {
            // Optional: clear or show loader
           $("#common-skeleton-loader").show();
        },
        success: function (data) {
            $(".norecord").remove();  
            $("#common-skeleton-loader").hide();
            dataloading = false;
            $(".posting-case-more-link").remove();

            last_page = data.last_page;

            if (data.contents.trim() === "") {
                loading = true;
                if (data.current_page === 1) {
                    $("#gridCasesList").html(
                        '<div class="text-center text-danger my-2">No cases found.</div>'
                        );
                }
            } else {
                if (data.current_page === 1) {
                    $("#gridCasesList").html(data.contents);
                } else {
                    $("#gridCasesList").append(data.contents);
                }

                setTimeout(function () {
                    document.querySelectorAll('.CDSPostCaseNotifications-grid-view02-feed-card.CDSPostCaseNotifications-list-view02-new')
                        .forEach(function (el) {
                            el.classList.remove('CDSPostCaseNotifications-list-view02-new');
                        });
                }, 15000);

            }
        },
        error: function(xhr, status, error) {
            $("#common-skeleton-loader").hide();
            dataloading = false;
            console.error('Error loading grid data:', error);
            if (data.current_page === 1) {
                $("#gridCasesList").html(
                    '<div class="text-center text-danger my-2">Error loading data. Please try again.</div>'
                );
            }
        },
        complete: function () {
            dataloading = false; 
        }
    });
}

// Function to switch between list and grid views
function switchToListView() {
    if (currentView !== 'list') {
        console.log('Switching to List View');
        page = 1; // Reset page counter
        currentView = 'list';
        // Update button states
        $('#listViewBtn').removeClass('btn-outline-primary').addClass('btn-primary');
        $('#gridViewBtn').removeClass('btn-primary').addClass('btn-outline-primary');
        // Show list container, hide grid container with fade effect
        $('.CDSPostCaseNotifications-grid-view02-container').fadeOut(300, function() {
            $('.CDSPostCaseNotifications-compact-list-container').fadeIn(300);
        });
        listCaseData(1);
    }
}

function switchToGridView() {
    if (currentView !== 'grid') {
        console.log('Switching to Grid View');
        page = 1; // Reset page counter
        currentView = 'grid';
        // Update button states
        $('#gridViewBtn').removeClass('btn-outline-primary').addClass('btn-primary');
        $('#listViewBtn').removeClass('btn-primary').addClass('btn-outline-primary');
        // Show grid container, hide list container with fade effect
        $('.CDSPostCaseNotifications-compact-list-container').fadeOut(300, function() {
            $('.CDSPostCaseNotifications-grid-view02-container').fadeIn(300);
        });
        gridCaseData(1);
    }
}

$(window).scroll(function () {
    // Determine which container to use based on current view
    let container;
    if (currentView === 'list') {
        container = $("#casesList");
    } else {
        container = $("#gridCasesList");
    }

    if (container.length === 0) return;

    const containerOffsetTop = container.offset().top;
    const containerHeight = container.outerHeight();
    const windowBottom = $(window).scrollTop() + $(window).height();

    if (windowBottom >= containerOffsetTop + containerHeight - 100) {
        if (page < last_page && !dataloading && page < 3) {
            console.log('Loading more data, current page:', page, 'next page:', page + 1, 'view:', currentView);
            dataloading = true; // ✅ immediately set it before load starts
            page++;
            if (currentView === 'list') {
                listCaseData(page);
            } else {
                gridCaseData(page);
            }
        }
    }
});

// Function to refresh data based on current view when filters change
function refreshCurrentView() {
    page = 1; // Reset to first page
    if (currentView === 'list') {
        listCaseData(1);
    } else {
        gridCaseData(1);
    }
}