<div class="CDSDashboardProfessionalServices-list-visa-item">
    <div class="CDSDashboardProfessionalServices-list-multi-select-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <label class="CDSDashboardProfessionalServices-list-multi-select-label">Select Service Types</label>
            <span class="CDSDashboardProfessionalServices-list-tag CDSDashboardProfessionalServices-list-tag-primary" id="selectedCount_{{ $subservice_id }}" style="background: var(--primary-soft); color: var(--primary); border-color: var(--primary);">
                0 selected
            </span>
        </div>
        <div class="CDSDashboardProfessionalServices-list-multi-select-actions">
            <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="window.selectAllServices_{{ $subservice_id }}()">Select All</button>
            <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="window.clearAllServices_{{ $subservice_id }}()">Clear All</button>
            <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="window.selectCommonServices_{{ $subservice_id }}()">Common Types</button>
        </div>
        <div class="CDSDashboardProfessionalServices-list-selected-items CDSDashboardProfessionalServices-list-empty" id="selectedItems_{{ $subservice_id }}">
            No service types selected
        </div>
        <!-- Show already saved service types as tags -->
        <div class="CDSDashboardProfessionalServices-list-search-wrapper">
            <svg class="CDSDashboardProfessionalServices-list-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" class="CDSDashboardProfessionalServices-list-multi-select-search" id="searchInput_{{ $subservice_id }}" placeholder="Search service types..." autocomplete="off">
            <svg class="CDSDashboardProfessionalServices-list-clear-search" id="clearSearch_{{ $subservice_id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <div class="CDSDashboardProfessionalServices-list-dropdown-list" id="dropdownList_{{ $subservice_id }}"></div>
        </div>
        <!-- Hidden input for selected service type IDs -->
        <input type="hidden" id="selectedServiceTypeIds_{{ $subservice_id }}" name="selected_service_type_ids_{{ $subservice_id }}" value="">
        <!-- Add button -->
        <button type="button" class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-primary" id="addServiceTypesBtn_{{ $subservice_id }}">
            Add
        </button>
    </div>
</div>

@if($serviceDetails && count($serviceDetails))
     @foreach($serviceDetails as $detail)
    <div class="CDSDashboardProfessionalServices-list-visa-item" style="animation-delay: 0.1s;">
        <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--text);">
            Selected Service Details
        </h4>
         
        <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
          
                <div style="background: #f8fbfd; border-radius: 8px; padding: 1.5rem 1.5rem 1rem 1.5rem; min-width: 240px; flex: 1; display: flex; flex-direction: column; align-items: stretch;">
                    <div style="font-weight: 600; color: var(--primary); margin-bottom: 0.5rem; text-align: center;">
                        {{ $detail->subServiceTypes->name ?? 'N/A' }}
                    </div>
                    <a href="javascript:;" onclick="confirmAction(this)" data-href="{{baseUrl('manage-services/remove-sub-service-type/'.$detail->unique_id)}}" class="CdsTYButton-btn-primary CdsTYButton-border-thick">Remove</a>
                    <div class="CDSDashboardProfessionalServices-list-fee-grid" style="margin-bottom: 1.5rem; border: 1px solid #e3eaf3; border-radius: 8px; padding: 1rem;">
                        <div class="CDSDashboardProfessionalServices-list-fee-item">
                            <div class="CDSDashboardProfessionalServices-list-fee-label">Professional fees</div>
                            <div class="CDSDashboardProfessionalServices-list-fee-value">${{ $detail->professional_fees ?? 0 }}</div>
                        </div>
                        <div class="CDSDashboardProfessionalServices-list-fee-item">
                            <div class="CDSDashboardProfessionalServices-list-fee-label">Minimum Fees</div>
                            <div class="CDSDashboardProfessionalServices-list-fee-value">${{ $detail->minimum_fees ?? 0 }}</div>
                        </div>
                        <div class="CDSDashboardProfessionalServices-list-fee-item">
                            <div class="CDSDashboardProfessionalServices-list-fee-label">Maximum Fees</div>
                            <div class="CDSDashboardProfessionalServices-list-fee-value">${{ $detail->maximum_fees ?? 0 }}</div>
                        </div>
                        <div class="CDSDashboardProfessionalServices-list-fee-item">
                            <div class="CDSDashboardProfessionalServices-list-fee-label">Consultancy fees</div>
                            <div class="CDSDashboardProfessionalServices-list-fee-value">${{ $detail->consultancy_fees ?? 0 }}</div>
                        </div>
                    </div>
                    <a class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-primary openEditSubServicesSlideBtn" onclick="openSidebar(this)" style="margin-top: auto;"  data-subserviceid="{{$detail->unique_id}}">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M11.5 2.5l2 2L6 12l-3 1 1-3 7.5-7.5z"></path>
                        </svg>
                        Edit Details
                    </a>
                </div>
           
        </div>
       
        <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <span style="font-size: 0.75rem; color: var(--text-muted);">
                Configure each service type individually
            </span>
        </div>
    </div>
    <!--  -->


    <!--  -->
    @endforeach
@endif


@push('services_script')

@endpush
