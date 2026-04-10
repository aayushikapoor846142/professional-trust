 <div class="CDSDashboardProfessionalServices02-modal-content">
        <div class="CDSDashboardProfessionalServices02-modal-header">
            <h3 id="modal-service-name">Configure Service</h3>
            <div class="CDSDashboardProfessionalServices02-modal-close" onclick="closeModal()">×</div>
        </div>
        <form id="subtype-pathway-form" class="js-validate mt-3" action="{{ baseUrl('manage-services/save-subtype-pathways') }}" method="post">
            @csrf
            <input type="hidden" name="id" value="{{$record->unique_id}}">
            <input type="hidden" name="debug_service_name" value="{{ isset($main_service) ? $main_service->name : 'Unknown' }}">
            <div class="CDSDashboardProfessionalServices02-type-selector">
                <h4>Select Applicant Types</h4>
                <p style="font-size: 14px; color: #64748b; margin-bottom: 12px;">Choose all types that will share the same fee structuressss</p>
                <div class="CDSDashboardProfessionalServices02-type-pills">
                    @foreach($subServiceType as $value)
                        @php
                            $isSelected = false;
                            if(isset($existingConfigurations)) {
                                $isSelected = $existingConfigurations->where('sub_services_type_id', $value->id)->count() > 0;
                            }
                        @endphp

                        @if(!$isSelected)
                            <div class="CDSDashboardProfessionalServices02-type-pill"
                                onclick="toggleType(this, {{ $value->id }})"
                                data-id="{{ $value->id }}">
                                <span>👤</span> {{ $value->name }}
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
          
             <div class="js-form-message">
              <input type="hidden" id="selected_service_type_ids" name="selected_service_type_ids"/>
            </div>
            <div class="CDSDashboardProfessionalServices02-form-row">
                <div class="CDSDashboardProfessionalServices02-form-group js-form-message">
                    <label>Professional Fees ($)</label>
                    <input type="number" class="CDSDashboardProfessionalServices02-form-control" id="modal-prof-fee" name="professional_fees" 
                           placeholder="0.00" step="0.01"
                           >
                </div>
                <div class="CDSDashboardProfessionalServices02-form-group js-form-message">
                    <label>Consultant Fees ($)</label>
                    <input type="number" class="CDSDashboardProfessionalServices02-form-control" id="modal-cons-fee" name="consultancy_fees" 
                           placeholder="0.00" step="0.01"
                           >
                </div>
            </div>

          
                                                   <div class="CDSDashboardProfessionalServices-list-visa-item">
                                        <div class="CDSDashboardProfessionalServices-list-multi-select-container js-form-message">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                                <label class="CDSDashboardProfessionalServices-list-multi-select-label">Select Document</label>
                                                <span class="CDSDashboardProfessionalServices-list-tag CDSDashboardProfessionalServices-list-tag-primary" id="selectedCount" style="background: var(--primary-soft); color: var(--primary); border-color: var(--primary);">
                                                    0 selected
                                                </span>
                                            </div>
                                            <div class="CDSDashboardProfessionalServices-list-multi-select-actions">
                                                <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="MultiSelectManager.selectAllServices()">Select All</button>
                                                <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="MultiSelectManager.clearAllServices()">Clear All</button>
                                                <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="MultiSelectManager.selectCommonServices()">Common Types</button>
                                            </div>
                                            <div class="CDSDashboardProfessionalServices-list-selected-items CDSDashboardProfessionalServices-list-empty" id="selectedItems">
                                                No document selected
                                            </div>
                                            <div class="CDSDashboardProfessionalServices-list-search-wrapper">
                                                <svg class="CDSDashboardProfessionalServices-list-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                                <input 
                                                    type="text" 
                                                    class="CDSDashboardProfessionalServices-list-multi-select-search" 
                                                    id="searchDocumenInput" 
                                                    placeholder="Search document..."
                                                    autocomplete="off"
                                                >
                                                <svg class="CDSDashboardProfessionalServices-list-clear-search" id="clearSearch" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                <div class="CDSDashboardProfessionalServices-list-dropdown-list" id="dropdownList"></div>
                                            </div>
                                            <!-- Hidden input to store selected service IDs -->
                                            <input type="hidden" id="selected_service_document_ids" name="document" value="">
                                        </div>
                                    </div>

            <!-- end document new form -->

            <div class="CDSDashboardProfessionalServices02-form-group">
                <label>Assessment Form Template</label>
                <select class="CDSDashboardProfessionalServices02-form-control" id="modal-assessment" name="form_id">
                    <option value="">Select assessment form</option>
                    @foreach($forms as $form)
                    <option value="{{$form->id}}">
                        {{$form->name}}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="CDSDashboardProfessionalServices02-action-bar" style="margin: 0; padding: 0; background: none; border: none;">
                <a href="javascript:;" class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-secondary" onclick="closeModal()">Cancel</a>
                <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-primary" type="submit">
                    {{ isset($existingConfigurations) && $existingConfigurations->count() > 0 ? 'Update Configuration' : 'Save Configuration' }}
                </button>
            </div>
        </form>
</div>
<script>
    function toggleType(pill, type) {
        pill.classList.toggle('CDSDashboardProfessionalServices02-active');
        // Get all selected pills
        const selected = Array.from(document.querySelectorAll('.CDSDashboardProfessionalServices02-type-pill.CDSDashboardProfessionalServices02-active'))
            .map(p => p.getAttribute('data-id'));

        // Update hidden input
        const hiddenInput = document.getElementById('selected_service_type_ids');
        hiddenInput.value = selected.join(',');
    }

    // Form submission handling
    $(document).ready(function() {
        $("#subtype-pathway-form").submit(function(e) {
            e.preventDefault();
            
            var is_valid = formValidation("subtype-pathway-form");
            if(!is_valid){
                return false;
            }
            
            var formData = new FormData($(this)[0]);
            var url = $("#subtype-pathway-form").attr('action');
            
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
                        location.reload();
                        if (response.contents) {
                            // Update the configurations section with new content
                            // Get the current service ID from the hidden input
                            var currentServiceId = $('input[name="id"]').val();
                            var debugServiceName = $('input[name="debug_service_name"]').val();
                            
                            
                            // Find the specific service item and update only its configuration section
                            // Use a more precise selector to find the exact service item
                            var targetServiceItem = $('.CDSDashboardProfessionalServices02-service-item').filter(function() {
                                return $(this).find('button[data-unique-id="' + currentServiceId + '"]').length > 0;
                            });
                            
                            console.log('Found target service item:', targetServiceItem.length > 0);
                            
                            if (targetServiceItem.length > 0) {
                                // Update only the configuration section of the correct service
                                targetServiceItem.find('.CDSDashboardProfessionalServices02-type-configurations').html(response.contents);
                                
                                // Also update the service meta to show it's configured
                                targetServiceItem.find('.CDSDashboardProfessionalServices02-service-meta .CDSDashboardProfessionalServices02-meta-item').html(
                                    '<span class="CDSDashboardProfessionalServices02-configured-badge">Configured</span>'
                                );
                                
                                console.log('Successfully updated configuration for service:', currentServiceId);
                            } else {
                                // Fallback: if we can't find the specific service, reload the page
                                console.warn('Could not find service item with ID: ' + currentServiceId);
                                console.warn('Available service items:', $('.CDSDashboardProfessionalServices02-service-item').map(function() {
                                    return $(this).find('button[data-unique-id]').attr('data-unique-id');
                                }).get());
                                location.reload();
                            }
                        }
                        // Close modal after successful submission
                        closeModal();
                        // Reset multi-select for next modal
                        MultiSelectManager.reset();
                    } else {
                        // $.each(response.message, function (index, value) {
                        //     if(index == 'document'){
                        //         $('.document_members').html(value);
                        //     }
                        // });
                            validation(response.message);
                        // validation(response.message);
                        // errorMessage(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    hideLoader();
                    errorMessage('An error occurred while saving the configuration. Please try again.');
                    console.error('AJAX Error:', error);
                }
            });
        });
    });

  
</script>

<script>
    // Create a namespace for the multi-select functionality
    window.MultiSelectManager = window.MultiSelectManager || {
        isInitialized: false,
        selectedServices: [],
        searchTimeout: null,
        serviceTypes: @json($documents->map(function($doc) {
            return [
                'id' => $doc->id,
                'name' => $doc->name,
                'selected' => false
            ];
        })),

        init: function() {
            // Prevent multiple initializations
            if (this.isInitialized) {
                console.log('Multi-select already initialized, skipping...');
                return;
            }

            console.log('Initializing multi-select...');
            const searchInput = document.getElementById('searchDocumenInput');
            const dropdownList = document.getElementById('dropdownList');
            const clearBtn = document.getElementById('clearSearch');
            const selectedItemsContainer = document.getElementById('selectedItems');
            const hiddenInput = document.getElementById('selected_service_document_ids');

            console.log('Elements found:', {
                searchInput: !!searchInput,
                dropdownList: !!dropdownList,
                clearBtn: !!clearBtn,
                selectedItemsContainer: !!selectedItemsContainer,
                hiddenInput: !!hiddenInput
            });

            if (!searchInput || !dropdownList || !hiddenInput) {
                console.error('Required elements not found');
                return;
            }

            // Reset state for new modal
            this.selectedServices = [];
            this.searchTimeout = null;

            // Populate selectedServices from hidden input
            const selectedIds = hiddenInput.value.split(',').filter(id => id.trim() !== '').map(id => parseInt(id));
            this.selectedServices = this.serviceTypes.filter(service => selectedIds.includes(service.id));
            
            console.log('Initialized with selected services:', this.selectedServices);

            this.renderSelectedItems();
            this.renderDropdownItems();

            // Remove existing event listeners to prevent duplicates
            searchInput.removeEventListener('input', this.handleSearch.bind(this));
            searchInput.removeEventListener('focus', this.handleFocus.bind(this));
            clearBtn.removeEventListener('click', this.clearSearchInput.bind(this));
            document.removeEventListener('click', this.handleDocumentClick.bind(this));

            // Add event listeners
            searchInput.addEventListener('input', this.handleSearch.bind(this));
            searchInput.addEventListener('focus', this.handleFocus.bind(this));
            clearBtn.addEventListener('click', this.clearSearchInput.bind(this));
            document.addEventListener('click', this.handleDocumentClick.bind(this));

            this.isInitialized = true;
            console.log('Multi-select initialized successfully');
        },

        handleFocus: function() {
            console.log('Search input focused');
            this.showDropdown();
        },

        handleDocumentClick: function(e) {
            if (!e.target.closest('.CDSDashboardProfessionalServices-list-search-wrapper') && !e.target.closest('.CDSDashboardProfessionalServices-list-selected-items')) {
                this.hideDropdown();
            }
        },

        handleSearch: function(e) {
            const value = e.target.value;
            console.log('Search input:', value);
            const clearBtn = document.getElementById('clearSearch');
            
            clearBtn.classList.toggle('CDSDashboardProfessionalServices-list-visible', value.length > 0);
            
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.renderDropdownItems(value);
            }, 300);
        },

        clearSearchInput: function() {
            const searchInput = document.getElementById('searchDocumenInput');
            const clearBtn = document.getElementById('clearSearch');
            
            searchInput.value = '';
            clearBtn.classList.remove('CDSDashboardProfessionalServices-list-visible');
            this.renderDropdownItems();
        },

        renderSelectedItems: function() {
            const container = document.getElementById('selectedItems');
            const countElement = document.getElementById('selectedCount');
            if (!container) return;

            if (this.selectedServices.length === 0) {
                container.innerHTML = 'No service types selected';
                container.classList.add('CDSDashboardProfessionalServices-list-empty');
            } else {
                container.classList.remove('CDSDashboardProfessionalServices-list-empty');
                container.innerHTML = this.selectedServices.map(service => `
                    <span class="CDSDashboardProfessionalServices-list-selected-tag">
                        ${service.name}
                        <span class="CDSDashboardProfessionalServices-list-tag-remove" onclick="MultiSelectManager.removeService(${service.id})">×</span>
                    </span>
                `).join('');
            }

            // Update count
            if (countElement) {
                countElement.textContent = `${this.selectedServices.length} selected`;
            }

            // Update hidden input
            this.updateHiddenInput();
        },

        renderDropdownItems: function(searchTerm = '') {
            const dropdownList = document.getElementById('dropdownList');
            if (!dropdownList) {
                console.error('Dropdown list element not found');
                return;
            }

            console.log('Rendering dropdown items with search term:', searchTerm);
            console.log('Available service types:', this.serviceTypes);

            const filteredServices = this.serviceTypes.filter(service =>
                service.name.toLowerCase().includes(searchTerm.toLowerCase())
            );

            console.log('Filtered services:', filteredServices);

            if (filteredServices.length === 0) {
                dropdownList.innerHTML = '<div class="CDSDashboardProfessionalServices-list-no-results">No matching service types found</div>';
                return;
            }

            dropdownList.innerHTML = filteredServices.map(service => {
                const isSelected = this.selectedServices.some(s => s.id === service.id);
                return `
                    <div class="CDSDashboardProfessionalServices-list-dropdown-item ${isSelected ? 'CDSDashboardProfessionalServices-list-selected' : ''}" onclick="MultiSelectManager.toggleService(${service.id})">
                        <input type="checkbox" class="CDSDashboardProfessionalServices-list-dropdown-checkbox" ${isSelected ? 'checked' : ''}>
                        <span class="CDSDashboardProfessionalServices-list-option-label">${service.name}</span>
                    </div>
                `;
            }).join('');
        },

        toggleService: function(serviceId) {
            const service = this.serviceTypes.find(s => s.id === serviceId);
            const index = this.selectedServices.findIndex(s => s.id === serviceId);

            if (index > -1) {
                this.selectedServices.splice(index, 1);
            } else {
                this.selectedServices.push(service);
            }

            this.renderSelectedItems();
            this.renderDropdownItems(document.getElementById('searchDocumenInput')?.value || '');
        },

        removeService: function(serviceId) {
            this.selectedServices = this.selectedServices.filter(s => s.id !== serviceId);
            this.renderSelectedItems();
            this.renderDropdownItems(document.getElementById('searchDocumenInput')?.value || '');
        },

        showDropdown: function() {
            const dropdownList = document.getElementById('dropdownList');
            if (dropdownList) {
                dropdownList.classList.add('CDSDashboardProfessionalServices-list-show');
                console.log('Dropdown shown');
            } else {
                console.error('Dropdown list element not found for showing');
            }
        },

        hideDropdown: function() {
            const dropdownList = document.getElementById('dropdownList');
            if (dropdownList) {
                dropdownList.classList.remove('CDSDashboardProfessionalServices-list-show');
            }
        },

        selectAllServices: function() {
            this.selectedServices = [...this.serviceTypes];
            this.renderSelectedItems();
            this.renderDropdownItems(document.getElementById('searchDocumenInput')?.value || '');
        },

        clearAllServices: function() {
            this.selectedServices = [];
            this.renderSelectedItems();
            this.renderDropdownItems(document.getElementById('searchDocumenInput')?.value || '');
        },

        selectCommonServices: function() {
            // Select the first 4 service types as "common"
            this.selectedServices = this.serviceTypes.slice(0, 4);
            this.renderSelectedItems();
            this.renderDropdownItems(document.getElementById('searchDocumenInput')?.value || '');
        },

        getSelectedServiceIds: function() {
            return this.selectedServices.map(service => service.id);
        },

        updateHiddenInput: function() {
            const hiddenInput = document.getElementById('selected_service_document_ids');
            if (hiddenInput) {
                hiddenInput.value = this.getSelectedServiceIds().join(',');
                console.log('Updated hidden input with IDs:', hiddenInput.value);
            }
        },

        reset: function() {
            this.isInitialized = false;
            this.selectedServices = [];
            this.searchTimeout = null;
            console.log('Multi-select reset');
        }
    };

    // Initialize when script loads
    console.log('Script loaded, service types:', MultiSelectManager.serviceTypes);
    
    // Reset state for new modal
    MultiSelectManager.reset();
    
    // Add a small delay to ensure DOM elements are ready (since this is loaded in a modal)
    setTimeout(() => {
        MultiSelectManager.init();
    }, 100);

</script>