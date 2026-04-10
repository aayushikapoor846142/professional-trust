@extends('admin-panel.layouts.app')
@php 
$page_arr = [
    'page_title' => 'Roles Previleges',
    'page_description' => 'Manage Roles Previleges',
    'page_type' => 'roles-previleges',
];
@endphp
@section('page-submenu')
{!! pageSubMenu('accounts',$page_arr) !!}
@endsection
@section('content')

    <div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
   <div class="cds-ty-dashboard-box">
        <div class="cds-ty-dashboard-box-body">
            <!-- new accodian -->
            <form action="{{ baseUrl('role-privileges')}}" method="POST" id="form">
                @csrf
                <div class="accordion accordion-flush" id="accordionFlushExample">
                   @if(count($roles) > 0)
                    @foreach($roles as $key => $role)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne{{$key}}" aria-expanded="false" aria-controls="flush-collapseOne">
                            {{capLetter($role)}}
                            </button>
                        </h2>
                        <div id="flush-collapseOne{{$key}}" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                @foreach($records as $record)                                
                                <div class="card-box-round mb-4 modules cdc-form-list cdc-admin-card">
                                    <div class="d-block d-sm-flex header-card justify-content-between">
                                        <div class="float-left">
                                            {{$record->name}} <span class="sub-title">({{$record->slug}})</span>
                                        </div>
                                        {{-- <div class="form-check">
                                           <label class="checkbox required" for="check-{{$key}}-{{$record->id}}">
                                            <input type="checkbox" class="check-all"  value="1" id="check-{{$key}}-{{$record->id}}">
                                            <span class="checkmark"></span></span> Check All</span>
                                            </label>
                                           

                                        </div> --}}
                                        <div class="form-check">
                                            {{--{!! FormHelper::formCheckbox([
                                            'name' => 'check_all',
                                            'value' => 1,
                                            'id' => "check-{$key}-{$record->id}",
                                            'class' => 'check-all',
                                            'required' => true,
                                            'label' => 'Check All',
                                            'labelAttributes' => ['class' => 'checkbox required'],
                                            ]) !!}--}}
                                            <label class="checkbox required" for="check-{{$key}}-{{$record->id}}">
                                            <input type="checkbox" class="check-all"  value="1" id="check-{{$key}}-{{$record->id}}">
                                            <span class="checkmark"></span></span> Check All</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="permissions">
                                        @foreach($record->moduleAction as $action)
                                        <div class="permission-block">
                                            <div class="form-check">
                                                {{--{!! FormHelper::formCheckbox([
                                                'name' => "permission[{$role}][{$record->slug}][]",  // Dynamically set the name attribute
                                                'value' => $action->action,                          // Value of the checkbox
                                                'id' => "check-{$key}-{$record->id}-{$action->id}",  // Dynamically set the ID attribute
                                                'class' => 'action-check',                            // Class for the checkbox
                                                'label' => $action->action,                          // Label text for the checkbox
                                                'labelAttributes' => ['class' => 'checkbox required'],  // Label attributes
                                                'checked' => isset($role_wise_permissions[$role][$record->slug]) && in_array($action->action, $role_wise_permissions[$role][$record->slug]),  // Check if the checkbox should be checked
                                                ]) !!}--}}
                                                <label class="checkbox required" for="check-{{$key}}-{{$record->id}}-{{$action->id}}">
                                                <input type="checkbox" @if(isset($role_wise_permissions[$role][$record->slug]) && in_array($action->action,$role_wise_permissions[$role][$record->slug])) checked @endif class="action-check" name="permission[{{$role}}][{{$record->slug}}][]" value="{{$action->action}}" id="check-{{$key}}-{{$record->id}}-{{$action->id}}">
                                                <span class="checkmark"></span> <span>{{$action->action}}</span>
                                                </label>
                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
    <div class="text-center text-muted">
        Please add roles first to give permission.
    </div>
@endif
                </div>
                     @if(checkPrivilege([
                        'route_prefix' => 'panel.role-privileges',
                        'module' => 'professional-role-privileges',
                        'action' => 'add'
                    ]))
                <div class="text-start">
                    <button type="submit" class="btn add-CdsTYButton-btn-primary">Submit</button>
                </div>
                @endif
            </form>
           
          
        </div>
    </div>
			</div>
	
	</div>
  </div>
</div>
 


@endsection
<!-- End Content -->
@section('javascript')
<script>
    $(document).ready(function() {
        pageLoad();
        $(".check-all").change(function() {
            if ($(this).is(":checked")) {
                $(this).parents(".modules").find(".action-check").prop("checked", true);
            } else {
                $(this).parents(".modules").find(".action-check").prop("checked", false);
            }
        });


        // When any checkbox is clicked
        $('.action-check').click(function() {

            if ($(this).parents('.card-box-round').find('.action-check').length == $(this).parents('.card-box-round').find('.action-check:checked').length) {
                $(this).parents('.card-box-round').find('.check-all').prop('checked', true);
            } else {
                $(this).parents('.card-box-round').find('.check-all').prop('checked', false);
            }

        });

        $("#form").submit(function(e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);
            var url = $("#form").attr('action');

            $.ajax({
                url: url,
                type: "post",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                dataType: "json",
                beforeSend: function() {
                    showLoader();
                },
                success: function(response) {
                    hideLoader();
                    if (response.status == true) {
                        successMessage(response.message);
                        redirect(response.redirect_back);
                    } else {
                        errorMessage(response.message);
                    }
                },
                error: function() {
                    internalError();
                }
            });
        });
    })

    function pageLoad() {
        $('.action-check').each(function() {
            if ($(this).parents('.card-box-round').find('.action-check').length == $(this).parents('.card-box-round').find('.action-check:checked').length) {
                $(this).parents('.card-box-round').find('.check-all').prop('checked', true);
            } else {
                $(this).parents('.card-box-round').find('.check-all').prop('checked', false);
            }
        });

    }
</script>
@endsection