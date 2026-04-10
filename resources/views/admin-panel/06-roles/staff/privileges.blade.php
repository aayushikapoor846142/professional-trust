@extends('admin-panel.layouts.app')
@section("style")
<style>
.save-btn {
    position: fixed;
    top: 20% !important;
    right: 23px;
    z-index: 99;
}
</style>
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
----

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <form id="form" class="js-validate mt-3" action="{{ baseUrl('/staff/privileges/'.base64_encode($user_id)) }}" method="post">
                @csrf
                <!-- Input Group -->
                <div class="row justify-content-md-between">
                    <div class="col-md-12">
                        <div class="card mb-5">
                            <div class="cds-ty-dashboard-box-body">
                                @foreach($privileges as $module_index => $privilege)
                                <div id="module-{{$module_index}}" class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="mb-0">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <h1 class="page-header-title">{{$privilege->name}}</h1>
                                                </div>
                                                <div class="col-md-6 text-right">
                                                    <div class="custom-checkbox custom-control mr-3 mt-3">
                                                        <input type="checkbox" id="customCheck-{{$module_index}}-all" onclick="checkAll(this,'{{$module_index}}')" class="custom-control-input">
                                                        <label class="custom-control-label" for="customCheck-{{$module_index}}-all">All</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @foreach($privilege->Actions as $action_index => $action)
                                    <div class="col-md-3 mt-3">
                                        <div class="custom-checkbox custom-control">
                                            <input type="checkbox" id="customCheck-{{$module_index.'-'.$action_index}}" name="privileges[{{$privilege->slug}}][]" value="{{ $action->slug }}" {{(isset($staff_privileges[$privilege->slug]) && in_array($action->slug,$staff_privileges[$privilege->slug]))?'checked':'' }} class="p-chk custom-control-input">
                                            <label class="custom-control-label" for="customCheck-{{$module_index.'-'.$action_index}}">
                                                {{$action->name}} 
                                                <div class="text-danger">{{$action->slug}}</div>
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                                <!-- End Card -->                    
                            </div>
                        </div>
                    </div>
                </div>
                <div class="save-btn">
                    <button type="submit" class="btn btn-success add-btn"><i class="tio-save"></i> Save</button>
                </div>
                <!-- End Input Group -->
            </form>
    
			</div>
	
	</div>
  </div>
</div>

  @endsection

@section('javascript')
<script type="text/javascript">
    
$(document).on('ready', function () {

  $("#form").submit(function(e){
      e.preventDefault();
      var formData = $("#form").serialize();
      var url  = $("#form").attr('action');
      $.ajax({
          url:url,
          type:"post",
          data:formData,
          dataType:"json",
          beforeSend:function(){
            showLoader();
          },
          success:function(response){
            hideLoader();
            if(response.status == true){
              successMessage(response.message);
            }else{
              errorMessage(response.message);
            }
          },
          error:function(){
            internalError();
          }
      });
  });
});
function checkAll(e,index){
  if($(e).is(":checked")){
    $("#module-"+index).find(".p-chk").prop("checked",true);
  }else{
    $("#module-"+index).find(".p-chk").prop("checked",false);
  }
}
</script>
@endsection