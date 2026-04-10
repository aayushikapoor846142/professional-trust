@extends('admin-panel.layouts.app')

@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
<div class="cdsTYDashboard-table-body" id="tableList">
 @include('components.table-pagination01') 	
       
			</div>
	
	</div>
  </div>
</div>


<!-- End Content -->
@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function(){

  $(".next").click(function(){
    if(!$(this).hasClass('disabled')){
      changePage('next');
    }
  });
  $(".previous").click(function(){
    if(!$(this).hasClass('disabled')){
      changePage('prev');
    }
  });
  $("#datatableSearch").keyup(function(){
    var value = $(this).val();
    if(value == ''){
      loadData();
    }
    if(value.length > 3){
      loadData();
    }
  });
  $("#datatableCheckAll").change(function(){
    if($(this).is(":checked")){
      $(".row-checkbox").prop("checked",true);
    }else{
      $(".row-checkbox").prop("checked",false);
    }
    if($(".row-checkbox:checked").length > 0){
      $("#datatableCounterInfo").show();
    }else{
      $("#datatableCounterInfo").hide();
    }
    $("#datatableCounter").html($(".row-checkbox:checked").length);
  });

})
loadData();
function loadData(page=1){
  var search = $("#datatableSearch").val();
    $.ajax({
        type: "POST",
        url: BASEURL + '/tracking/ajax-list?page='+page,
        data:{
            _token:csrf_token,
            search:search
        },
        dataType:'json',
        beforeSend:function(){
            var cols = $("#tableList thead tr > th").length;
            $("#tableList tbody").html('<tr><td colspan="'+cols+'"><center><i class="fa fa-spin fa-spinner fa-3x"></i></center></td></tr>');
            // $("#paginate").html('');
        },
        success: function (data) {
            $("#tableList").html(data.contents);

            if(data.total_records > 0){
              var pageinfo = data.current_page+" of "+data.last_page+" <small class='text-danger'>("+data.total_records+" records)</small>";
              $("#pageinfo").html(pageinfo);
              $("#pageno").val(data.current_page);
              if(data.current_page < data.last_page){
                $(".next").removeClass("disabled");
              }else{
                $(".next").addClass("disabled","disabled");
              }
              if(data.current_page > 1){
                $(".previous").removeClass("disabled");
              }else{
                $(".previous").addClass("disabled","disabled");
              }
              $("#pageno").attr("max",data.last_page);
            }else{
              $(".datatable-custom").find(".norecord").remove();
              var html = '<div class="text-center text-danger norecord">No records available</div>';
              $(".datatable-custom").append(html);
            }
        },
    });
}
function changePage(action){
  var page = parseInt($("#pageno").val());
  if(action == 'prev'){
    page--;
  }
  if(action == 'next'){
    page++;
  }
  if(!isNaN(page)){
    loadData(page);
  }else{
    errorMessage("Invalid Page Number");
  }

}

</script>
@endsection
