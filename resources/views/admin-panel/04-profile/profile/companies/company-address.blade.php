
<div class="cds-register-address-list">

	@foreach($records as $key => $record)
	<!-- Address Item 1 -->
	<div class="address-item" id="personal-address-div-{{$record->id}}">
		<div class="address-header">
			<div class="">
				<span class="bg-warning text-white px-3 py-2 mb-2 d-block">{{ $record->type }}</span>
				<div class="map-thumbnail">
					<i class="fa-sharp fa-solid fa-location-dot" style="color:#000000;"></i>
				</div>
			</div>
			
			<div class="address-details render-address">
				<!-- <span class="badge main-house">
				</span> -->
				<div class="address-name">
					<div class="company-name" data-value="{{$record->company->company_name ?? ''}}">{{$record->company->company_name ?? ''}}</div>
				</div>
				<div class="address-text">
					<div class="country" data-value="{{$record->timezone ?? ''}}"><b>Timezone:</b> {{$record->timezone ?? ''}}</div>
				</div>
				<div class="address-text">
					<div class="address-1" data-value="{{$record->address_1 ?? ''}}">{{$record->address_1 ?? ''}}</div>
					<div class="address-2" data-value="{{$record->address_2 ?? ''}}">{{$record->address_2 ?? ''}}</div>
				</div>
				<div class="address-text">
					<div class="state" data-value="{{$record->state ?? ''}}"> {{$record->state ?? ''}}</div>
					<div class="city" data-value="{{$record->city ?? ''}}"> {{$record->city ?? ''}}</div>

					<div class="pincode" data-value="{{$record->pincode ?? ''}}"> {{$record->pincode ?? ''}}</div>
					<div class="country" data-value="{{$record->country ?? ''}}"> {{$record->country ?? ''}}</div>

				</div>
				<div class="address-action-btns">
					<a href="javascript:;" class="btn btn-warning btn-sm me-2" onclick="openCustomPopup(this)" data-href="<?php echo baseUrl('professional/add-company-address/'.$record->unique_id) ?>">
						<!-- <i class="fa fa-edit"></i>  -->
						Edit Address
					</a>
					<a href="javascript:;" onclick="deleteCompanyAddress(this)" data-id="{{$record->id}}" data-href="{{ baseUrl('delete-professional-location/'.$record->unique_id) }}" class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm">
						<!-- <i class="fa fa-times"></i>  -->
						Delete Address
					</a>
					<a href="{{ baseUrl('working-hours?location='.$record->unique_id) }}" class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-sm">
						Add Working Hours ({{ $record->workingHours->count() }})
					</a>
				</div>
			</div>
		
			<div class="radio">
			
				<input type="radio" name="address" onchange="markAsPrimary('{{ $record->company->unique_id??'' }}', '{{ $record->unique_id }}')" {{ ($record->is_primary == 1 && (!empty($record->is_primary) && $record->is_primary == 1)) ? 'checked' : '' }} />
			</div>
		</div>
	</div>
	@endforeach
</div>

<script type="text/javascript">

</script>