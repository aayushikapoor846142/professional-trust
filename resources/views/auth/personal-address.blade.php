<!-- @foreach($records as $key => $record)
<div class="render-address">
	<div><i class="fa fa-location"></i></div>
	<div class="address-1" data-value="{{$record->address_1 ?? ''}}"> {{$record->address_1 ?? ''}}</div>
	<div class="address-2" data-value="{{$record->address_2 ?? ''}}"> {{$record->address_2 ?? ''}}</div>
	<div class="country" data-value="{{$record->country ?? ''}}"> {{$record->country ?? ''}}</div>
	<div class="state" data-value="{{$record->state ?? ''}}"> {{$record->state ?? ''}}</div>
	<div class="city" data-value="{{$record->city ?? ''}}"> {{$record->city ?? ''}}</div>
	<div class="pincode" data-value="{{$record->pincode ?? ''}}"> {{$record->pincode ?? ''}}</div>

	<a href="javascript:;" onclick="editPersonalAddress($(this),'{{$record->unique_id}}')">
		<i class="fa fa-edit"></i> Edit
	</a>

</div>

@endforeach -->
<div class="cds-register-address-list render-address">
	@if($records->isNotEmpty())
	@foreach($records as $key => $record)

	<div class="address-item">
		<div class="address-header">
			<div class="map-thumbnail">
				<i class="fa-sharp fa-solid fa-location-dot"></i>
			</div>
			<div class="address-details">
				<div class="address-text">
					<div class="address-1" data-value="{{$record->address_1 ?? ''}}"> {{$record->address_1 ?? ''}}</div>
					<div class="address-2" data-value="{{$record->address_2 ?? ''}}"> {{$record->address_2 ?? ''}}</div>
				</div>
				<div class="address-text">
					<div class="city" data-value="{{$record->city ?? ''}}"> {{$record->city ?? ''}}</div>
					<div class="state" data-value="{{$record->state ?? ''}}"> {{$record->state ?? ''}}</div>
					<div class="pincode" data-value="{{$record->pincode ?? ''}}"> {{$record->pincode ?? ''}}</div>
					<div class="country" data-value="{{$record->country ?? ''}}"> {{$record->country ?? ''}}</div>
				</div>
				<div class="address-action-btns">
					<a href="javascript:;" onclick="showPopup('<?php echo baseUrl('professional/add-personal-address/'.$record->unique_id) ?>')">
						<!-- <i class="fa fa-edit"></i>  -->
						Edit Address
					</a>
				</div>
			</div>
		</div>
	</div>
	@endforeach
	@endif
</div>
