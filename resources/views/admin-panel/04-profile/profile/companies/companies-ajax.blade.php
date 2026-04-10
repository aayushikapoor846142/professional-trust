
<div class="cds-register-address-list">
	@foreach($records as $key => $record)
	<!-- Address Item 1 -->
	<div class="address-item" id="personal-address-div-{{$record->id}}">
		<div class="address-header">
			<div class="">
				<img id="showCompanyLogo" class="img-fluid" src="{{ companyLogoDirUrl($record->company_logo) }}" alt="Profile Image" style="height:40px; width:40px;">
				<!-- <div class="map-thumbnail">
					<i class="fa-sharp fa-solid fa-building" style="color:#000000;"></i>
				</div> -->
			</div>
			
			<div class="address-details render-address">
				<!-- <span class="badge main-house">
				</span> -->
				<div class="address-name">
					<div class="company-name" data-value="{{$record->company_name ?? ''}}">{{$record->company_name ?? ''}} {{ $record->id }} {{ $record->user_id }}</div>
				</div>
				<div class="address-action-btns">
					<a href="{{ baseUrl('companies/edit-company/'.$record->unique_id) }}" class="btn btn-warning btn-sm me-2" >
						<!-- <i class="fa fa-edit"></i>  -->
						Edit Company
					</a>
					<a href="javascript:;" onclick="confirmAction(this)" data-id="{{$record->id}}" data-href="{{ baseUrl('companies/delete-company/'.$record->unique_id) }}" class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm">
						<!-- <i class="fa fa-times"></i>  -->
						Delete Company
					</a>
					<a href="{{ baseUrl('companies/manage-address/'.$record->unique_id) }}" class="btn btn-warning btn-sm me-2" >
						<!-- <i class="fa fa-edit"></i>  -->
						Manage Company Address
					</a>
					<div class="radio">
			
						<input type="radio" name="address" onchange="markCompanyAsPrimary('{{ $record->unique_id}}','{{$record->user_id}}')" {{ ($record->is_primary == 1 && (!empty($record->is_primary) && $record->is_primary == 1)) ? 'checked' : '' }} />Mark As Primary
					</div>
				</div>
			</div>
		</div>
	</div>
	@endforeach
</div>

<script type="text/javascript">
	function markCompanyAsPrimary(company_id,user_id){
        $.ajax({
            type: "GET",
            url: "{{baseUrl('/companies/mark-as-primary')}}",
            data:{
                _token:csrf_token,
                company_id:company_id,
                user_id:user_id
            },
            dataType:'json',
            success: function (response) {
                if (response.status == true) {
                    successMessage(response.message);
                    location.reload();
                } else {
                    errorMessage(response.message);
                }
            },
        });
    }
</script>