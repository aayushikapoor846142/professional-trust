
@extends('admin-panel.layouts.app')

@section('content')
<!-- Content -->
<section class="cdsTYOnboardingDashboard-breadcrumb-section">
        <div class="cdsTYOnboardingDashboard-breadcrumb-section-header"><div class="cdsTYOnboardingDashboard-page-title">
                 <h2>Manage Cards</h2>
            </div>
            <div class="breadcrumb-container">
                <ol class="breadcrumb">
                    <i class="fa-regular fa-grid-2"></i>
                    <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ baseUrl('payment-methods') }}">Payment Methods</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$pageTitle}}</li>
                </ol>
              
            </div>
        </div>
		
    </section>
<section class="cdsTYOnboardingDashboard-sub-action">

<div class="ch-action">
                  <a href="{{ baseUrl('payment-methods/add-card/'.$record->unique_id) }}" class="CdsTYButton-btn-primary">
                      <i class="fa-plus fa-solid me-1"></i>
                      Add New Card
                  </a>
              </div>
</section>


<section class="cdsTYOnboardingDashboard-manage-cards-action">
    <div class="cds-ty-dashboard-box cdsTYOnboardingDashboard-manage-cards-action-box">
                
                <div class="cds-ty-dashboard-box-body">
                    
					 <div class="cdsTYDashboard-manage-cards-list-outer">
					 <div class="cdsTYDashboard-manage-cards-list " id="tableList" >
				
					 </div>
					@include('components.table-pagination01') 
					
					 </div>
					
					
                </div>
                   </div>
    
</section>
<!-- End Content -->
@endsection

@section('javascript')
<script type="text/javascript">
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
    $("#datatableSearch").keyup(function() {
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

  function loadData(page = 1) {
    var search = $("#search-input").val();
    $.ajax({
      type: "POST",
      url: BASEURL + '/payment-methods/cards-ajax-list',
      data: {
        _token: csrf_token,
        search: search
      },
      dataType: 'json',
      beforeSend: function() {
        var cols = $("#tableList thead tr > th").length;
        $("#tableList tbody").html('<tr><td colspan="' + cols + '"><center><i class="fa fa-spin fa-spinner fa-3x"></i></center></td></tr>');
        // $("#paginate").html('');
      },
      success: function(data) {
        $("#tableList").html(data.contents);
        
        if (data.contents && data.contents.length > 0) {
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
          $(".datatable-custom").find(".norecord").remove();
          var html = '<div class="cdsTYDashboard-empty-list norecord">No records available</div>';
          $(".datatable-custom").append(html);
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
          url: BASEURL + '/faq-categories/delete/' + id,
          data: {
            _token: csrf_token,
            //user_id:id,
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
              if (result.type = "category_link") {
                Swal.fire({
                  title: "Error!",
                  text: "You can't delete this faq-categories as it is already Linked",
                  type: "error",
                  confirmButtonClass: 'CdsTYButton-btn-primary',
                  buttonsStyling: false,
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
            }
          },
        });
      }
    })
  }
</script>
@endsection
