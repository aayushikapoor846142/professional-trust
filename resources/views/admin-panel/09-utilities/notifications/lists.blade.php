@extends('admin-panel.layouts.app')

@section('content')

@endsection

@section('javascript')
<script type="text/javascript">
 
  loadData(1);

  function loadData(pageno = 1) {
		page = pageno;
    	var search = $("#datatableSearch").val();
    	$.ajax({
			type: "POST",
			url: BASEURL + '/notifications/ajax-list?page=' + page,
			data: {
				_token: csrf_token,
				search: search
			},
      		dataType: 'json',
			beforeSend: function() {
				
				// $("#paginate").html('');
			},
      		success: function(data) {
				$(".notification-view-more-link").remove();
                last_page = data.last_page;
                if (data.contents.trim() === "") {
                        loading = true; // Prevent further requests
                        if (data.current_page === 1) {
                            $("#notificationPanels").html('<div class="text-center text-danger">No Record Found</div>');
                        }else{
                            $("#notificationPanels").html('');
                        }
                } else {
                   
                    if (data.current_page === 1) {
                        $(".notification-view-more-link").remove();
                       
                        if (data.contents.trim() === "") {
                            $("#notificationPanels").html('<div class="text-center text-danger">No Record Found</div>');
                        }else{
                            $("#notificationPanels").html(data.contents);
                        }
                    } else {
                        $("#notificationPanels").append(data.contents);
                    }
                    var next_page = data.current_page + 1;
                    if (data.last_page >= next_page) {
                        loading = false; // Allow further requests
                    } else {
                        loading = true; // Prevent further requests
                    }
                    if(data.last_page == 0){
                        $(".no-record-available").removeClass('d-none');
                        $(".load-more").html('No more data to load...');
                    }
                  
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

  function confirmDelete(id) {
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!',
      confirmButtonClass: 'CdsTYButton-btn-primary',
      cancelButtonClass: 'CdsTYButton-btn-primary CdsTYButton-border-thick ml-1',
      buttonsStyling: false,
    }).then(function(result) {
      if (result.value) {
        $.ajax({
          type: "POST",
          url: BASEURL + '/support-payments/delete-user',
          data: {
            _token: csrf_token,
            user_id: id,
          },
          dataType: 'json',
          success: function(result) {
            if (result.status == true) {
              Swal.fire({
                type: "success",
                title: 'Deleted!',
                text: 'User has been deleted.',
                confirmButtonClass: 'btn btn-success',
              }).then(function() {

                window.location.href = result.redirect;
              });
            } else {
              Swal.fire({
                title: "Error!",
                text: "Error while deleting",
                type: "error",
                confirmButtonClass: 'CdsTYButton-btn-primary',
                buttonsStyling: false,
              });
            }
          },
        });
      }
    })
  }
</script>
@endsection