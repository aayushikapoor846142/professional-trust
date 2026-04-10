<div class="CDSDashboardProfessionalServices02-modal-content">
    <div class="CDSDashboardProfessionalServices02-modal-header">
        <h3 id="modal-service-name">Edit Configuration</h3>
        <div class="CDSDashboardProfessionalServices02-modal-close" onclick="closeEditModal()">×</div>
    </div>
    <form id="edit-configuration-form" class="js-validate mt-3" action="{{ baseUrl('manage-services/update-configuration/' . $configuration->unique_id) }}" method="post">
        @csrf
        <input type="hidden" name="configuration_id" value="{{ $configuration->unique_id }}">
        <input type="hidden" name="debug_service_name" value="{{ isset($main_service) ? $main_service->name : 'Unknown' }}">
        
        <div class="CDSDashboardProfessionalServices02-form-row">
            <div class="CDSDashboardProfessionalServices02-form-group">
                <label>Applicant Type</label>
                <div class="CDSDashboardProfessionalServices02-type-display">
                    <span class="CDSDashboardProfessionalServices02-type-icon">👤</span>
                    {{ $configuration->subServicesType->name ?? 'Unknown' }}
                </div>
                <small style="color: #64748b;">This field cannot be changed. Create a new configuration to change the applicant type.</small>
            </div>
        </div>

        <div class="CDSDashboardProfessionalServices02-form-row">
            <div class="CDSDashboardProfessionalServices02-form-group js-form-message">
                <label>Professional Fees ($)</label>
                <input type="number" class="CDSDashboardProfessionalServices02-form-control" id="edit-prof-fee" name="professional_fees" 
                       placeholder="0.00" step="0.01" 
                       value="{{ $configuration->professional_fees }}">
            </div>
            <div class="CDSDashboardProfessionalServices02-form-group js-form-message">
                <label>Consultant Fees ($)</label>
                <input type="number" class="CDSDashboardProfessionalServices02-form-control" id="edit-cons-fee" name="consultancy_fees" 
                       placeholder="0.00" step="0.01"
                       value="{{ $configuration->consultancy_fees }}">
            </div>
        </div>

        <div class="CDSDashboardProfessionalServices02-form-group">
            <label>Required Documents</label>
            <!-- <div class="CDSDashboardProfessionalServices02-document-selector">
                @php
                    $selectedDocuments = [];
                    if($configuration->document_folders) {
                        $selectedDocuments = explode(',', $configuration->document_folders);
                    }
                @endphp
                @foreach($documents as $key => $value)
                <div class="CDSDashboardProfessionalServices02-doc-item">
                    <input type="checkbox" id="edit-doc{{$key}}" name="document[]" value="{{$value->id}}"
                           {{ in_array($value->id, $selectedDocuments) ? 'checked' : '' }}>
                    <label for="edit-doc{{$key}}">{{$value->name}}</label>
                </div>
                @endforeach
            </div>
            <span class="text-danger document_members"></span> -->
            
            <!-- document new flow -->
            <div class="CDSDashboardProfessionalServices-list-visa-item">
                <div class="CDSDashboardProfessionalServices-list-multi-select-container js-form-message">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                        <label class="CDSDashboardProfessionalServices-list-multi-select-label">Select Service Types</label>
                        <span class="CDSDashboardProfessionalServices-list-tag CDSDashboardProfessionalServices-list-tag-primary" id="selectedCount" style="background: var(--primary-soft); color: var(--primary); border-color: var(--primary);">
                            0 selected
                        </span>
                    </div>
                    <div class="CDSDashboardProfessionalServices-list-multi-select-actions">
                        <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="EditMultiSelectManager.selectAllServices()">Select All</button>
                        <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="EditMultiSelectManager.clearAllServices()">Clear All</button>
                        <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="EditMultiSelectManager.selectCommonServices()">Common Types</button>
                    </div>
                    <div class="CDSDashboardProfessionalServices-list-selected-items CDSDashboardProfessionalServices-list-empty" id="selectedItems">
                        No service types selected
                    </div>
                    <div class="CDSDashboardProfessionalServices-list-search-wrapper">
                        <svg class="CDSDashboardProfessionalServices-list-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input 
                            type="text" 
                            class="CDSDashboardProfessionalServices-list-multi-select-search" 
                            id="searchDocumenInput" 
                            placeholder="Search service types..."
                            autocomplete="off"
                        >
                        <svg class="CDSDashboardProfessionalServices-list-clear-search" id="clearSearch" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <div class="CDSDashboardProfessionalServices-list-dropdown-list" id="dropdownList"></div>
                    </div>
                    <!-- Hidden input to store selected service IDs -->
                    <input type="hidden" id="selected_service_document_ids" name="document" value="{{ $configuration->document_folders ?? '' }}">
                </div>
            </div>
        </div>
        

        <div class="CDSDashboardProfessionalServices02-form-group">
            <label>Assessment Form Template</label>
            <select class="CDSDashboardProfessionalServices02-form-control" id="edit-assessment" name="form_id">
                <option value="">Select assessment form</option>
                @foreach($forms as $form)
                <option value="{{$form->id}}" {{ $configuration->form_id == $form->id ? 'selected' : '' }}>
                    {{$form->name}}
                </option>
                @endforeach
            </select>
        </div>

        <div class="CDSDashboardProfessionalServices02-action-bar" style="margin: 0; padding: 0; background: none; border: none;">
            <a href="javascript:;" class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-secondary" onclick="closeEditModal()">Cancel</a>
            <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-primary" type="submit">Update Configuration</button>
        </div>
    </form>
</div>

<script>
    // Form submission handling for edit configuration
    $(document).ready(function() {
        $("#edit-configuration-form").submit(function(e) {
            e.preventDefault();
            
            var is_valid = formValidation("edit-configuration-form");
            if(!is_valid){
                return false;
            }
            
            var formData = new FormData($(this)[0]);
            var url = $("#edit-configuration-form").attr('action');
            
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
                            // Get the configuration ID from the hidden input
                            var configurationId = $('input[name="configuration_id"]').val();
                            var debugServiceName = $('input[name="debug_service_name"]').val();
                            
                            
                            
                            // Find the specific configuration item and update it
                            var targetConfigurationItem = $('.CDSDashboardProfessionalServices02-type-config-item').filter(function() {
                                return $(this).find('button[onclick*="' + configurationId + '"]').length > 0;
                            });
                            
                            console.log('Found target configuration item:', targetConfigurationItem.length > 0);
                            
                            if (targetConfigurationItem.length > 0) {
                                // Replace the entire configuration item with the updated content
                                targetConfigurationItem.replaceWith(response.contents);
                                console.log('Successfully updated configuration:', configurationId);
                            } else {
                                // Fallback: reload the page
                                console.warn('Could not find configuration item with ID:', configurationId);
                                location.reload();
                            }
                        }
                        // Close modal after successful submission
                        closeEditModal();
                    } else {
                        // $.each(response.message, function (index, value) {
                        //     if(index == 'document'){
                        //         $('.document_members').html(value);
                        //     }
                        // });
                        validation(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    hideLoader();
                    errorMessage('An error occurred while updating the configuration. Please try again.');
                    console.error('AJAX Error:', error);
                }
            });
        });
    });

    function closeEditModal() {
        document.getElementById('edit-config-modal').classList.remove('CDSDashboardProfessionalServices02-active');
    }
</script>

<script>
    // Create a namespace for the edit multi-select functionality
    window.EditMultiSelectManager = window.EditMultiSelectManager || {
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
                console.log('Edit multi-select already initialized, skipping...');
                return;
            }

            console.log('Initializing edit multi-select...');
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

            // Populate selectedServices from hidden input (existing configuration)
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
            console.log('Edit multi-select initialized successfully');
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
                        <span class="CDSDashboardProfessionalServices-list-tag-remove" onclick="EditMultiSelectManager.removeService(${service.id})">×</span>
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
                    <div class="CDSDashboardProfessionalServices-list-dropdown-item ${isSelected ? 'CDSDashboardProfessionalServices-list-selected' : ''}" onclick="EditMultiSelectManager.toggleService(${service.id})">
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
            console.log('Edit multi-select reset');
        }
    };

    // Initialize when script loads
    console.log('Edit script loaded, service types:', EditMultiSelectManager.serviceTypes);
    
    // Reset state for new modal
    EditMultiSelectManager.reset();
    
    // Add a small delay to ensure DOM elements are ready (since this is loaded in a modal)
    setTimeout(() => {
        EditMultiSelectManager.init();
    }, 100);

</script> 