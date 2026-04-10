@if(isset($selectedSubServices) && $selectedSubServices->count())
    @foreach($selectedSubServices as $subService)
    {{$subService->unique_id}}
    <div class="CDSDashboardProfessionalServices-list-service-card" style="margin-top: 1rem;">
        <div class="CDSDashboardProfessionalServices-list-service-header">
            <div>
                <h3 class="CDSDashboardProfessionalServices-list-service-title">
                    {{ $subService->immigrationService->name ?? ($subService->subServices->name ?? 'N/A') }}
                </h3>
                <!--  -->
                <div class="CDSDashboardProfessionalServices-list-service-meta">
                    @php
                        // Example: check if configuration is pending (customize as needed)
                        $pendingConfig = isset($subService->status) && $subService->status === 'pending';
                        $caseConnected = method_exists($subService, 'isCaseConnected') ? $subService->isCaseConnected() : false;
                    @endphp
                    @if($pendingConfig)
                        <span class="CDSDashboardProfessionalServices-list-tag CDSDashboardProfessionalServices-list-tag-warning">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M7 1.75V7L9.625 8.75M12.25 7C12.25 9.89949 9.89949 12.25 7 12.25C4.10051 12.25 1.75 9.89949 1.75 7C1.75 4.10051 4.10051 1.75 7 1.75C9.89949 1.75 12.25 4.10051 12.25 7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            Service Pending Configure
                        </span>
                    @endif
                    @if($caseConnected)
                        <span class="CDSDashboardProfessionalServices-list-tag CDSDashboardProfessionalServices-list-tag-success">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M11.6667 3.5L5.25 9.91667L2.33333 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            Case Connected
                        </span>
                    @endif
                </div>
            </div>
            @if($subService->is_pin == 0)
                <button onclick="markAsPin('{{ $subService->id }}',1)" class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-text">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M8 1v6m-3.5-2L8 1l3.5 4M1 8v6a1 1 0 001 1h12a1 1 0 001-1V8M5 12h6"></path>
                    </svg>
                    <span class="CDSDashboardProfessionalServices-list-hide-mobile">Pin Service</span>
                </button>
            @else
                <button onclick="markAsPin('{{ $subService->id }}',0)" class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-text">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M8 1v6m-3.5-2L8 1l3.5 4M1 8v6a1 1 0 001 1h12a1 1 0 001-1V8M5 12h6"></path>
                    </svg>
                    <span class="CDSDashboardProfessionalServices-list-hide-mobile">UnPin Service</span>
                </button>
            @endif
        </div>
         <!-- sub pathways -->
            <div class="CDSDashboardProfessionalServices02-service-list" id="service-configuration-list" style="">
                <div class="CDSDashboardProfessionalServices02-service-list-header">
                    <h4>Services to Configure</h4>
                    <div class="CDSDashboardProfessionalServices02-service-count" id="config-service-count">3</div>
                </div>

                <div id="service-config-items">
                    @foreach(getSubPathway($subService->parent_service_id,$subService->service_id) as $value)
                    <div class="CDSDashboardProfessionalServices02-service-item" data-service-id="4281531769" data-parent-service-id="14">
                        <div class="CDSDashboardProfessionalServices02-service-header">
                            <div class="CDSDashboardProfessionalServices02-service-details">
                                <h5>Miscellaneous</h5>
                                <div class="CDSDashboardProfessionalServices02-service-meta">
                                    <div class="CDSDashboardProfessionalServices02-meta-item">
                                        <span class="CDSDashboardProfessionalServices02-configured-badge">Configured</span>
                                    </div>
                                    <div class="CDSDashboardProfessionalServices02-meta-item">
                                        1 configuration
                                    </div>
                                </div>
                            </div>
                            <div class="CDSDashboardProfessionalServices02-service-actions">
                                <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-outline" onclick="configureService(this)" data-unique-id="4281531769" data-service-name="Miscellaneous">
                                Add Configuration
                                </button>
                                <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-danger" onclick="removeServiceFromServer('4281531769')">
                                    Remove
                                </button>
                            </div>
                        </div>
                        <div class="CDSDashboardProfessionalServices02-type-configurations">
                            <div class="CDSDashboardProfessionalServices02-type-config-item">
                                <div class="CDSDashboardProfessionalServices02-type-config-details">
                                    <div class="CDSDashboardProfessionalServices02-type-name">
                                        <span class="CDSDashboardProfessionalServices02-type-icon">👤</span>
                                        free case
                                    </div>
                                    <div class="CDSDashboardProfessionalServices02-config-info">
                                        <div class="CDSDashboardProfessionalServices02-config-info-item">
                                            <label>Professional Fee</label>
                                            <span>$1.00</span>
                                        </div>
                                        <div class="CDSDashboardProfessionalServices02-config-info-item">
                                            <label>Consultant Fee</label>
                                            <span>$123.00</span>
                                        </div>
                                        <div class="CDSDashboardProfessionalServices02-config-info-item">
                                            <label>Total Fee</label>
                                            <span style="color: #5b4be7; font-weight: 600;">
                                                $124.00
                                            </span>
                                        </div>
                                        <div class="CDSDashboardProfessionalServices02-config-info-item">
                                            <label>Assessment Form</label>
                                            <span>My Form</span>
                                        </div>
                                    </div>
                                    <div class="CDSDashboardProfessionalServices02-config-info-item" style="margin-top: 8px;">
                                        <label>Required Documents</label>
                                        <div class="CDSDashboardProfessionalServices02-documents-list">
                                                <span class="CDSDashboardProfessionalServices02-doc-badge">sdsds</span>
                                                <span class="CDSDashboardProfessionalServices02-doc-badge">zxczxc</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="CDSDashboardProfessionalServices02-service-actions">
                                    <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-outline" onclick="editConfiguration('9713652148')">
                                        Edit
                                    </button>
                                    <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-danger" onclick="removeConfiguration('9713652148')">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- end subpathways -->
    </div>
            
        <!-- <div class="CDSDashboardProfessionalServices-list-service-card" style="margin-top: 1rem;">
            <div class="CDSDashboardProfessionalServices-list-service-header">
                <div>
                    <h3 class="CDSDashboardProfessionalServices-list-service-title">
                        {{ $subService->immigrationService->name ?? ($subService->subServices->name ?? 'N/A') }}
                    </h3>
                    <div class="CDSDashboardProfessionalServices-list-service-meta">
                        @php
                            // Example: check if configuration is pending (customize as needed)
                            $pendingConfig = isset($subService->status) && $subService->status === 'pending';
                            $caseConnected = method_exists($subService, 'isCaseConnected') ? $subService->isCaseConnected() : false;
                        @endphp
                        @if($pendingConfig)
                            <span class="CDSDashboardProfessionalServices-list-tag CDSDashboardProfessionalServices-list-tag-warning">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M7 1.75V7L9.625 8.75M12.25 7C12.25 9.89949 9.89949 12.25 7 12.25C4.10051 12.25 1.75 9.89949 1.75 7C1.75 4.10051 4.10051 1.75 7 1.75C9.89949 1.75 12.25 4.10051 12.25 7Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                Service Pending Configure
                            </span>
                        @endif
                        @if($caseConnected)
                            <span class="CDSDashboardProfessionalServices-list-tag CDSDashboardProfessionalServices-list-tag-success">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M11.6667 3.5L5.25 9.91667L2.33333 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                Case Connected
                            </span>
                        @endif
                    </div>
                </div>
                @if($subService->is_pin == 0)
                    <button onclick="markAsPin('{{ $subService->id }}',1)" class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-text">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M8 1v6m-3.5-2L8 1l3.5 4M1 8v6a1 1 0 001 1h12a1 1 0 001-1V8M5 12h6"></path>
                        </svg>
                        <span class="CDSDashboardProfessionalServices-list-hide-mobile">Pin Service</span>
                    </button>
                @else
                    <button onclick="markAsPin('{{ $subService->id }}',0)" class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-text">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M8 1v6m-3.5-2L8 1l3.5 4M1 8v6a1 1 0 001 1h12a1 1 0 001-1V8M5 12h6"></path>
                        </svg>
                        <span class="CDSDashboardProfessionalServices-list-hide-mobile">UnPin Service</span>
                    </button>
                @endif
                <a data-href="{{baseUrl('manage-services/remove-sub-service/'.$subService->unique_id)}}" href="javascript:;" onclick="confirmAction(this)" class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-text">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M8 1v6m-3.5-2L8 1l3.5 4M1 8v6a1 1 0 001 1h12a1 1 0 001-1V8M5 12h6"></path>
                    </svg>
                    <span class="CDSDashboardProfessionalServices-list-hide-mobile">Remove Service</span>
                </a>
            </div>
            
            <div class="CDSDashboardProfessionalServices-list-actions-container">
                <a href = "{{ baseUrl('manage-services/generate-assessments/'.$subService->unique_id)}}" class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-primary">
                    Generate Assessment Form
                </a>
                <button class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-secondary">
                    View Service Types
                    <span class="CDSDashboardProfessionalServices-list-counter">
                        ({{ $subService->professionalServiceTypes->count() }})
                    </span>
                </button>
                <button class="CDSDashboardProfessionalServices-list-btn CDSDashboardProfessionalServices-list-btn-circle" onclick="toggleVisaDetails(this, 'visa-{{ $subService->id }}', '{{ $subService->id }}')">+</button>
            </div>
            
            <div class="CDSDashboardProfessionalServices-list-visa-expand-area" id="visa-{{ $subService->id }}"></div>
        </div> -->
    @endforeach
@endif


<script>
    function toggleVisaDetails(button, containerId, subserviceId) {
        const container = document.getElementById(containerId);
        const isExpanded = container.classList.contains('CDSDashboardProfessionalServices-list-show');
        
        if (isExpanded) {
            container.classList.remove('CDSDashboardProfessionalServices-list-show');
            button.classList.remove('CDSDashboardProfessionalServices-list-active');
            setTimeout(() => {
                if (!container.classList.contains('CDSDashboardProfessionalServices-list-show')) {
                    container.innerHTML = '';
                }
            }, 400);
        } else {
            container.classList.add('CDSDashboardProfessionalServices-list-show');
            button.classList.add('CDSDashboardProfessionalServices-list-active');
            // AJAX load subservice types
            container.innerHTML = '<div class="CDSDashboardProfessionalServices-list-loading">Loading...</div>';
            $.ajax({
                url: BASEURL + '/manage-services/get-subservice-type',
                type: 'GET',
                data: { subservice_id: subserviceId },
                success: function(response) {
                    if (response.status && response.contents) {
                        container.innerHTML = response.contents;
                        setTimeout(() => {
                            initSubServiceScript(response.subservice_id,response.types);
                        }, 200);
                    } else {
                        container.innerHTML = '<div class="CDSDashboardProfessionalServices-list-empty">No service types found.</div>';
                    }
                },
                error: function() {
                    container.innerHTML = '<div class="CDSDashboardProfessionalServices-list-empty">Error loading service types.</div>';
                }
            });
        }
    }

    
</script>
<script>
function initSubServiceScript(subservice_id, types) {

    const serviceTypes = types;
    console.log(serviceTypes);
    let selectedServices = serviceTypes.filter(s => s.selected);
    let searchTimeout = null;

    function updateHiddenInput() {
        const hiddenInput = document.getElementById('selectedServiceTypeIds_' + subservice_id);
        if (hiddenInput) {
            hiddenInput.value = selectedServices.map(s => s.id).join(',');
        }
    }

    window[`toggleService_${subservice_id}`] = function(serviceId) {
        const service = serviceTypes.find(s => s.id === serviceId);
        const index = selectedServices.findIndex(s => s.id === serviceId);

        if (index > -1) {
            selectedServices.splice(index, 1);
        } else {
            selectedServices.push(service);
        }

        renderSelectedItems(subservice_id, selectedServices);
        renderDropdownItems(
            document.getElementById(`searchInput_${subservice_id}`)?.value || '',
            subservice_id,
            selectedServices
        );
    };

    window[`removeService_${subservice_id}`] = function(serviceId) {
        selectedServices = selectedServices.filter(s => s.id !== serviceId);
        renderSelectedItems(subservice_id, selectedServices);
        renderDropdownItems(
            document.getElementById(`searchInput_${subservice_id}`)?.value || '',
            subservice_id,
            selectedServices
        );
    };

    window[`selectAllServices_${subservice_id}`] = function() {
        selectedServices = [...serviceTypes];
        renderSelectedItems(subservice_id, selectedServices);
        renderDropdownItems(
            document.getElementById(`searchInput_${subservice_id}`)?.value || '',
            subservice_id,
            selectedServices
        );
    };

    window[`clearAllServices_${subservice_id}`] = function() {
        selectedServices = [];
        renderSelectedItems(subservice_id, selectedServices);
        renderDropdownItems(
            document.getElementById(`searchInput_${subservice_id}`)?.value || '',
            subservice_id,
            selectedServices
        );
    };

    window[`selectCommonServices_${subservice_id}`] = function() {
        selectedServices = serviceTypes.slice(0, 4);
        renderSelectedItems(subservice_id, selectedServices);
        renderDropdownItems(
            document.getElementById(`searchInput_${subservice_id}`)?.value || '',
            subservice_id,
            selectedServices
        );
    };

    const searchInput = document.getElementById(`searchInput_${subservice_id}`);
    const clearBtn = document.getElementById(`clearSearch_${subservice_id}`);
    const dropdownList = document.getElementById(`dropdownList_${subservice_id}`);
    const addBtn = document.getElementById('addServiceTypesBtn_' + subservice_id);

    if (addBtn) {
        addBtn.addEventListener('click', function() {
            const selectedIds = selectedServices.map(s => s.id);
            // Example AJAX call (adjust URL/data as needed)
            $.ajax({
                url:  BASEURL + '/manage-services/add-service-types', // <-- adjust as needed
                type: 'POST',
                data: {
                    _token: csrf_token,
                    subservice_id: subservice_id,
                    selected_service_type_ids: selectedIds
                },
                success: function(response) {
                    // handle response
                    successMessage('Service types added!');
                    location.reload();
                },
                error: function() {
                    errorMessage('Error adding service types.');
                }
            });
        });
    }

    if (searchInput && clearBtn && dropdownList) {
        searchInput.addEventListener('input', function(e) {
            clearBtn.classList.toggle('CDSDashboardProfessionalServices-list-visible', e.target.value.length > 0);
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                renderDropdownItems(e.target.value, subservice_id, selectedServices);
            }, 300);
        });

        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            clearBtn.classList.remove('CDSDashboardProfessionalServices-list-visible');
            renderDropdownItems('', subservice_id, selectedServices);
        });

        searchInput.addEventListener('focus', function() {
            renderDropdownItems('', subservice_id, selectedServices);
            dropdownList.classList.add('CDSDashboardProfessionalServices-list-show');
        });
    }

    document.addEventListener('click', function(e) {
        if (!dropdownList || !searchInput) return;
        if (!dropdownList.contains(e.target) && e.target !== searchInput) {
            dropdownList.classList.remove('CDSDashboardProfessionalServices-list-show');
        }
    });

    renderSelectedItems(subservice_id, selectedServices);
    renderDropdownItems('', subservice_id, selectedServices);

      function renderSelectedItems(subservice_id,selectedServices) {
        const container = document.getElementById('selectedItems_'+subservice_id);
        const countElement = document.getElementById('selectedCount_'+subservice_id);
        if (!container) return;

        if (selectedServices.length === 0) {
            container.innerHTML = 'No service types selected';
            container.classList.add('CDSDashboardProfessionalServices-list-empty');
        } else {
            container.classList.remove('CDSDashboardProfessionalServices-list-empty');
            container.innerHTML = selectedServices.map(service => `
                <span class="CDSDashboardProfessionalServices-list-selected-tag">
                    ${service.name}
                    <span class="CDSDashboardProfessionalServices-list-tag-remove" onclick="window.removeService_${subservice_id}(${service.id})">&times;</span>
                </span>
            `).join('');
        }

        if (countElement) {
            countElement.textContent = `${selectedServices.length} selected`;
        }
        updateHiddenInput();
    }

    function renderDropdownItems(searchTerm, subservice_id, selectedServices) {
        const dropdownList = document.getElementById('dropdownList_'+subservice_id);
        if (!dropdownList) return;

        const filteredServices = serviceTypes.filter(service =>
            service.name.toLowerCase().includes(searchTerm.toLowerCase())
        );

        if (filteredServices.length === 0) {
            dropdownList.innerHTML = '<div class="CDSDashboardProfessionalServices-list-no-results">No matching service types found</div>';
        } else {
            dropdownList.innerHTML = filteredServices.map(service => {
                const isSelected = selectedServices.some(s => s.id === service.id);
                return `
                    <div class="CDSDashboardProfessionalServices-list-dropdown-item ${isSelected ? 'CDSDashboardProfessionalServices-list-selected' : ''}" onclick="window.toggleService_${subservice_id }(${service.id})">
                        <input type="checkbox" class="CDSDashboardProfessionalServices-list-dropdown-checkbox" ${isSelected ? 'checked' : ''}>
                        <span class="CDSDashboardProfessionalServices-list-option-label">${service.name}</span>
                    </div>
                `;
            }).join('');
        }
        dropdownList.classList.add('CDSDashboardProfessionalServices-list-show');
    }
}

 
        function openSidebar(e) {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            const overlay = document.getElementById('overlay');
            
            const subserviceid = $(e).data('subserviceid'); 
            $.ajax({
                type: "GET",
                url: BASEURL + '/manage-services/add-servicetype-detail/' + subserviceid,
                data: {
                    _token: csrf_token,
                },
                dataType: 'json',
                success: function(data) {
                    $(".CDSDashboardProfessionalServices-list-sidebar").html(data.contents);
                    sidebar.classList.add('CDSDashboardProfessionalServices-list-active');
                    if (window.innerWidth >= 1024) {
                        container.classList.add('CDSDashboardProfessionalServices-list-sidebar-open');
                    } else {
                        overlay.classList.add('CDSDashboardProfessionalServices-list-active');
                        document.body.style.overflow = 'hidden';
                    }
                }
            });
           
        }

        function closeSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.getElementById('container');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.remove('CDSDashboardProfessionalServices-list-active');
            container.classList.remove('CDSDashboardProfessionalServices-list-sidebar-open');
            overlay.classList.remove('CDSDashboardProfessionalServices-list-active');
            document.body.style.overflow = '';
        }


// end button

</script>
@stack('services_script')