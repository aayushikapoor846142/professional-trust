@extends('admin-panel.layouts.app')
@section('page-submenu')
@php 
$page_arr = [
    'page_title' => 'Add Immigration Service',
    'page_description' => 'Configure your service offering with individual pricing for each sub-service and applicant type.',
    'page_type' => 'feeds'
];
@endphp
{!! pageSubMenu('manage-services',$page_arr) !!}
@endsection
@section('styles')
<link href="{{ url('assets/css/30-CDS-service-configure.css') }}" rel="stylesheet" />
<link href="{{ url('assets/css/27-CDS-my-services.css') }}" rel="stylesheet" />
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="CDSDashboardProfessionalServices02-accordion">
        <!-- Step 1: Pathway Selection -->
        <div class="CDSDashboardProfessionalServices02-accordion-item">
            <div class="CDSDashboardProfessionalServices02-accordion-header {{ $main_service_id == 0 ? 'CDSDashboardProfessionalServices02-active' : '' }}" onclick="toggleAccordion(this, 1)">
                <div class="CDSDashboardProfessionalServices02-accordion-title">
                    <div class="CDSDashboardProfessionalServices02-step-number {{ $main_service_id == 0 ? 'CDSDashboardProfessionalServices02-active' : '' }}" id="step-1-indicator">1</div>
                    <div class="CDSDashboardProfessionalServices02-step-info">
                        <h3>Immigration Pathway</h3>
                        <p id="step-1-selection">Select the primary immigration pathway</p>
                    </div>
                </div>
                <svg class="CDSDashboardProfessionalServices02-accordion-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div class="CDSDashboardProfessionalServices02-accordion-content {{ $main_service_id == 0 ? 'CDSDashboardProfessionalServices02-active' : '' }}">
                <div class="CDSDashboardProfessionalServices02-content-inner">
                    <div class="CDSDashboardProfessionalServices02-option-grid CDSDashboardProfessionalServices02-main-pathways">
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Sub-Services -->
        <div class="CDSDashboardProfessionalServices02-accordion-item">
            <div class="CDSDashboardProfessionalServices02-accordion-header " onclick="toggleAccordion(this, 2)">
                <div class="CDSDashboardProfessionalServices02-accordion-title">
                    <div class="CDSDashboardProfessionalServices02-step-number " id="step-2-indicator">2</div>
                    <div class="CDSDashboardProfessionalServices02-step-info">
                        <h3>Sub-Services</h3>
                        <p id="step-2-selection">Select specific services to offer</p>
                    </div>
                </div>
                <svg class="CDSDashboardProfessionalServices02-accordion-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div class="CDSDashboardProfessionalServices02-accordion-content ">
                <div class="CDSDashboardProfessionalServices02-content-inner">
                    
                    <input type="text" class="form-control mb-3" id="sub-pathway-search">
                    <a href="javascript:;" class="CdsTYButton-btn-primary btn-subpathway-search">Search</a>
                    <a href="javascript:;" class="CdsTYButton-btn-primary CdsTYButton-border-thick btn-subpathway-clear">Clear</a>

                    <div class="CDSDashboardProfessionalServices02-option-grid mt-3" id="subservices-container">
                        <!-- Dynamically populated -->
                    </div>
                    <div class="CDSDashboardProfessionalServices02-add-services-bar" id="add-services-bar" style="display: none;">
                        <div class="CDSDashboardProfessionalServices02-selected-count">
                            <strong id="selected-services-count">0</strong> services selected
                        </div>
                        <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-primary" onclick="addSelectedServices()">
                            Add Selected Services
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Service Configuration -->
        <div class="CDSDashboardProfessionalServices02-accordion-item">
            <div class="CDSDashboardProfessionalServices02-accordion-header {{ $main_service_id != 0 ? 'CDSDashboardProfessionalServices02-active' : '' }}" onclick="toggleAccordion(this, 3)">

                <div class="CDSDashboardProfessionalServices02-accordion-title">
                    <div class="CDSDashboardProfessionalServices02-step-number {{ $main_service_id != 0 ? 'CDSDashboardProfessionalServices02-active' : '' }}" id="step-3-indicator">3</div>
                    <div class="CDSDashboardProfessionalServices02-step-info">
                        <h3>Service Configuration</h3>
                        <p id="step-3-selection">Configure fees and requirements for each service</p>
                    </div>
                </div>
                <svg class="CDSDashboardProfessionalServices02-accordion-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            <div class="CDSDashboardProfessionalServices02-accordion-content {{ $main_service_id != 0 ? 'CDSDashboardProfessionalServices02-active' : '' }}">
                <div class="CDSDashboardProfessionalServices02-content-inner">
                    <div class="CDSDashboardProfessionalServices02-service-list" id="service-configuration-list" style="display: none;">
                        <div class="CDSDashboardProfessionalServices02-service-list-header">
                            <h4>Services to Configure</h4>
                            <div class="CDSDashboardProfessionalServices02-service-count" id="config-service-count">0</div>
                        </div>
                        <div id="service-config-items">
                            <!-- Service items will be added here -->
                        </div>
                    </div>

                    <!-- <div class="CDSDashboardProfessionalServices02-empty-state" id="empty-config-state">
                        <p>No services added yet. Please select services from Step 2.</p>
                    </div> -->

                    <div class="CDSDashboardProfessionalServices02-summary-section" style="display: none;" id="config-summary">
                        <h3>Configuration Summary</h3>
                        <div class="CDSDashboardProfessionalServices02-summary-grid">
                            <div class="CDSDashboardProfessionalServices02-summary-item">
                                <h4>Total Services</h4>
                                <p id="summary-services">0</p>
                            </div>
                            <div class="CDSDashboardProfessionalServices02-summary-item">
                                <h4>Configured</h4>
                                <p id="summary-configured">0</p>
                            </div>
                            <div class="CDSDashboardProfessionalServices02-summary-item">
                                <h4>Total Variations</h4>
                                <p id="summary-variations">0</p>
                            </div>
                            <div class="CDSDashboardProfessionalServices02-summary-item">
                                <h4>Total Revenue Potential</h4>
                                <p id="summary-revenue">$0.00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

			</div>
	
	</div>
  </div>
</div>



<!-- Type Configuration Modal -->
<div class="CDSDashboardProfessionalServices02-type-config-modal" id="type-config-modal">
    
</div>

<!-- Edit Configuration Modal -->
<div class="CDSDashboardProfessionalServices02-type-config-modal" id="edit-config-modal">
    
</div>
@endsection
@section('javascript')
  
<script>
        let serviceData = {
            pathway: null,
            pathwayName: '',
            selectedSubservices: [],
            addedServices: []
        };

        let currentConfigService = null;
        let selectedSubPathway = [];
        let pathwayLoaded = false; // Flag to prevent multiple AJAX calls
        let subPathwayLoaded = false; // Flag to prevent multiple AJAX calls for step 3
        const typeIcons = {
            single: '👤',
            couple: '👫',
            family: '👨‍👩‍👧‍👦',
            group: '👥'
        };

        const typeNames = {
            single: 'Single Applicant',
            couple: 'Couple/Spouse',
            family: 'Family Group',
            group: 'Group Application'
        };

        const subservicesData = {
            worker: [
                { id: 'work-permit', name: 'Work Permit', desc: 'Temporary work authorization' },
                { id: 'lmia', name: 'LMIA', desc: 'Labour Market Impact Assessment' },
                { id: 'express-entry', name: 'Express Entry', desc: 'Federal skilled worker program' },
                { id: 'provincial-nominee', name: 'Provincial Nominee', desc: 'Province-specific programs' }
            ],
            student: [
                { id: 'study-permit', name: 'Study Permit', desc: 'Authorization to study in Canada' },
                { id: 'student-visa', name: 'Student Visa', desc: 'Temporary resident visa for students' },
                { id: 'post-grad-work', name: 'Post-Graduation Work Permit', desc: 'Work after graduation' },
                { id: 'coop-permit', name: 'Co-op Work Permit', desc: 'Work during studies' }
            ],
            temporary: [
                { id: 'visitor-visa', name: 'Visitor Visa', desc: 'Tourist and business visits' },
                { id: 'super-visa', name: 'Super Visa', desc: 'Extended family visits' },
                { id: 'transit-visa', name: 'Transit Visa', desc: 'Passing through Canada' },
                { id: 'temporary-permit', name: 'Temporary Resident Permit', desc: 'Special circumstances' }
            ],
            family: [
                { id: 'spouse-sponsorship', name: 'Spouse Sponsorship', desc: 'Sponsor your spouse or partner' },
                { id: 'parent-sponsorship', name: 'Parent/Grandparent', desc: 'Sponsor parents or grandparents' },
                { id: 'dependent-child', name: 'Dependent Child', desc: 'Sponsor dependent children' },
                { id: 'adoption', name: 'International Adoption', desc: 'Adopt a child from abroad' }
            ]
        };

        function toggleAccordion(header, step) {
            const content = header.nextElementSibling;
            const isActive = header.classList.contains('CDSDashboardProfessionalServices02-active');

            // If step is 2 and main_service_id is not 0, call selectPathway only once
            if (step === 2 && {{ $main_service_id }} != 0 && !pathwayLoaded) {
                pathwayLoaded = true; // Set flag to prevent multiple calls
                
                // Call AJAX directly for step 2 instead of selectPathway
                $.ajax({
                    type: "GET",
                    url: BASEURL + '/manage-services/fetch-sub-pathways/{{ $main_service_id }}',
                    data: {
                        _token: csrf_token,
                        main_service_id:"{{$main_service_id}}"
                    },
                    dataType: 'json',
                    success: function(data) {
                        if(data.status == true){
                            $("#subservices-container").html(data.contents);
                        }else{
                            errorMessage('Please try again');
                        }
                    }
                });
            }

            // Show alert for step 2 if main_service_id is 0 AND no pathway is selected
            if (step === 2 && {{ $main_service_id }} == 0 && !serviceData.pathway) {
                errorMessage('Please select a service first');
                return;
            }

            // If step is 3 and main_service_id is not 0, call displaySubPathway only once
            if (step === 3 && {{ $main_service_id }} != 0 && !subPathwayLoaded) {
                subPathwayLoaded = true; // Set flag to prevent multiple calls
                displaySubPathway("{{ $main_service_id }}");
            }

            // Only show alert for step 3 if main_service_id is 0 and no services added
            if (step === 3 && {{ $main_service_id }} == 0 && serviceData.addedServices.length === 0) {
                errorMessage('Please add at least one service to configure');
                return;
            }

            // Close all accordions
            document.querySelectorAll('.CDSDashboardProfessionalServices02-accordion-header').forEach(h => {
                h.classList.remove('CDSDashboardProfessionalServices02-active');
                h.nextElementSibling.classList.remove('CDSDashboardProfessionalServices02-active');
            });

            // Open clicked accordion if it wasn't already open
            if (!isActive) {
                header.classList.add('CDSDashboardProfessionalServices02-active');
                content.classList.add('CDSDashboardProfessionalServices02-active');
            }
        }

        function selectPathway(unique_id) {

            // call ajax
            $.ajax({
                type: "GET",
                url: BASEURL + '/manage-services/fetch-sub-pathways/'+unique_id,
                data: {
                    _token: csrf_token,
                    main_service_id:"{{$main_service_id}}"
                },
                dataType: 'json',
                success: function(data) {
                    if(data.status == true){
                        $("#subservices-container").html(data.contents);
                    }else{
                        errorMessage('Please try again');
                    }
                }
            });
            // end

            // Clear previous selection
            document.querySelectorAll('.CDSDashboardProfessionalServices02-option-card').forEach(card => {
                card.classList.remove('CDSDashboardProfessionalServices02-selected');
            });

            // Select new pathway
            event.currentTarget.classList.add('CDSDashboardProfessionalServices02-selected');
            
            // Set the pathway data
            serviceData.pathway = unique_id;
            serviceData.pathwayName = event.currentTarget.querySelector('h4').textContent || 'Selected Pathway';

            // Update UI
            document.getElementById('step-1-selection').textContent = serviceData.pathwayName + ' selected';
            document.getElementById('step-1-indicator').classList.add('CDSDashboardProfessionalServices02-completed');
            document.getElementById('step-1-indicator').innerHTML = '✓';

            // Reset services when pathway changes
            serviceData.selectedSubservices = [];
            serviceData.addedServices = [];

            // Auto-open next step
            setTimeout(() => {
                document.querySelectorAll('.CDSDashboardProfessionalServices02-accordion-header')[1].click();
            }, 300);
        }

        function populateSubservices() {
            const container = document.getElementById('subservices-container');
            const services = subservicesData[serviceData.pathway] || [];

            container.innerHTML = services.map(service => `
                <div class="CDSDashboardProfessionalServices02-option-card" onclick="toggleSubservice('${service.id}', '${service.name}', '${service.desc}')">
                    <div class="CDSDashboardProfessionalServices02-option-checkbox"></div>
                    <div class="CDSDashboardProfessionalServices02-option-info">
                        <h4>${service.name}</h4>
                        <p>${service.desc}</p>
                    </div>
                </div>
            `).join('');
        }

        // function toggleSubservice(id, name, desc) {
        //     const card = event.currentTarget;
        //     card.classList.toggle('CDSDashboardProfessionalServices02-selected');

        //     if (card.classList.contains('CDSDashboardProfessionalServices02-selected')) {
        //         serviceData.selectedSubservices.push({ id, name, desc });
        //     } else {
        //         serviceData.selectedSubservices = serviceData.selectedSubservices.filter(s => s.id !== id);
        //     }

        //     updateSubserviceSelection();
        // }

        function toggleSubservice(id) {
            const card = event.currentTarget;
            card.classList.toggle('CDSDashboardProfessionalServices02-selected');

            if (card.classList.contains('CDSDashboardProfessionalServices02-selected')) {
                serviceData.selectedSubservices.push({ id });
                selectedSubPathway.push(id);
            } else {
                serviceData.selectedSubservices = serviceData.selectedSubservices.filter(s => s.id !== id);
            }

            updateSubserviceSelection();
        }

        function updateSubserviceSelection() {
            const count = serviceData.selectedSubservices.length;
            document.getElementById('selected-services-count').textContent = count;
            
            const addBar = document.getElementById('add-services-bar');
            if (count > 0) {
                addBar.style.display = 'flex';
            } else {
                addBar.style.display = 'none';
            }

            document.getElementById('step-2-selection').textContent = 
                count > 0 ? `${count} service${count > 1 ? 's' : ''} selected` : 'Select specific services to offer';
        }

        function addSelectedServices() {
           
            if (selectedSubPathway.length === 0) {
                errorMessage('Please select at least one service');
                return;
            }

            $.ajax({
                type: "POST",
                url: BASEURL + '/manage-services/save-pathways',
                data: {
                    _token: csrf_token,
                    selectedSubPathway:selectedSubPathway
                },
                dataType: 'json',
                success: function(data) {
                    if(data.status == true){
                        window.location.href= data.redirect_url;
                    }
                    // $("#service-configuration-list").show();
                    // $("#service-config-items").html(data.contents);
                }
            });

            // Add selected services to configuration list
            serviceData.selectedSubservices.forEach(service => {
                if (!serviceData.addedServices.find(s => s.id === service.id)) {
                    serviceData.addedServices.push({
                        ...service,
                        configurations: []
                    });
                }
            });

            // Update UI
            document.getElementById('step-2-indicator').classList.add('CDSDashboardProfessionalServices02-completed');
            document.getElementById('step-2-indicator').innerHTML = '✓';
            // updateServiceList();

            // Auto-open configuration step
            setTimeout(() => {
                document.querySelectorAll('.CDSDashboardProfessionalServices02-accordion-header')[2].click();
            }, 300);
        }


        function updateServiceList() {
            const container = document.getElementById('service-config-items');
            const listContainer = document.getElementById('service-configuration-list');
            const emptyState = document.getElementById('empty-config-state');
            
            if (serviceData.addedServices.length === 0) {
                listContainer.style.display = 'none';
                emptyState.style.display = 'block';
                document.getElementById('config-summary').style.display = 'none';
                return;
            }

            listContainer.style.display = 'block';
            emptyState.style.display = 'none';
            document.getElementById('config-summary').style.display = 'block';
            document.getElementById('config-service-count').textContent = serviceData.addedServices.length;

            container.innerHTML = serviceData.addedServices.map((service, index) => {
                const configCount = service.configurations.length;
                const totalFees = service.configurations.reduce((sum, config) => 
                    sum + config.fees.professional + config.fees.consultant, 0
                );

                return `
                    <div class="CDSDashboardProfessionalServices02-service-item">
                        <div class="CDSDashboardProfessionalServices02-service-header">
                            <div class="CDSDashboardProfessionalServices02-service-details">
                                <h5>${service.name}</h5>
                                <div class="CDSDashboardProfessionalServices02-service-meta">
                                    ${configCount > 0 ? `
                                        <div class="CDSDashboardProfessionalServices02-meta-item">
                                            <span class="CDSDashboardProfessionalServices02-configured-badge">Configured</span>
                                        </div>
                                        <div class="CDSDashboardProfessionalServices02-meta-item">
                                            ${configCount} configuration${configCount !== 1 ? 's' : ''}
                                        </div>
                                    ` : `
                                        <div class="CDSDashboardProfessionalServices02-meta-item" style="color: #ef4444;">
                                            Not configured
                                        </div>
                                    `}
                                </div>
                            </div>
                            <div class="CDSDashboardProfessionalServices02-service-actions">
                                <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-outline" onclick="configureService(${index})">
                                    Add Configuration
                                </button>
                                <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-danger" onclick="removeService(${index})">
                                    Remove
                                </button>
                            </div>
                        </div>
                        ${configCount > 0 ? `
                            <div class="CDSDashboardProfessionalServices02-type-configurations">
                                ${service.configurations.map((config, configIndex) => `
                                    <div class="CDSDashboardProfessionalServices02-type-config-item">
                                        <div class="CDSDashboardProfessionalServices02-type-config-details">
                                            <div class="CDSDashboardProfessionalServices02-type-name">
                                                ${config.types.map(type => 
                                                    `<span class="CDSDashboardProfessionalServices02-type-icon">${typeIcons[type]}</span>`
                                                ).join(' ')}
                                                ${config.types.map(type => typeNames[type]).join(' • ')}
                                            </div>
                                            <div class="CDSDashboardProfessionalServices02-config-info">
                                                <div class="CDSDashboardProfessionalServices02-config-info-item">
                                                    <label>Professional Fee</label>
                                                    <span>$${config.fees.professional.toFixed(2)}</span>
                                                </div>
                                                <div class="CDSDashboardProfessionalServices02-config-info-item">
                                                    <label>Consultant Fee</label>
                                                    <span>$${config.fees.consultant.toFixed(2)}</span>
                                                </div>
                                                <div class="CDSDashboardProfessionalServices02-config-info-item">
                                                    <label>Total Fee</label>
                                                    <span style="color: #5b4be7; font-weight: 600;">
                                                        $${(config.fees.professional + config.fees.consultant).toFixed(2)}
                                                    </span>
                                                </div>
                                                <div class="CDSDashboardProfessionalServices02-config-info-item">
                                                    <label>Assessment Form</label>
                                                    <span>${config.assessmentForm || 'Not selected'}</span>
                                                </div>
                                            </div>
                                            ${config.documents.length > 0 ? `
                                                <div class="CDSDashboardProfessionalServices02-config-info-item" style="margin-top: 8px;">
                                                    <label>Required Documents</label>
                                                    <div class="CDSDashboardProfessionalServices02-documents-list">
                                                        ${config.documents.map(doc => 
                                                            `<span class="CDSDashboardProfessionalServices02-doc-badge">${doc}</span>`
                                                        ).join('')}
                                                    </div>
                                                </div>
                                            ` : ''}
                                        </div>
                                        <div class="CDSDashboardProfessionalServices02-service-actions">
                                            <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-outline" onclick="editConfiguration(${index}, ${configIndex})">
                                                Edit
                                            </button>
                                            <button class="CDSDashboardProfessionalServices02-btn CDSDashboardProfessionalServices02-btn-sm CDSDashboardProfessionalServices02-btn-danger" onclick="removeConfiguration(${index}, ${configIndex})">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                `;
            }).join('');

            updateSummary();
        }

        // function configureService(index) {
        //     currentConfigService = { serviceIndex: index, configIndex: null };
        //     const service = serviceData.addedServices[index];
            
        //     // Update modal
        //     document.getElementById('modal-service-name').textContent = `Configure ${service.name}`;
            
        //     // Reset modal
        //     document.querySelectorAll('.CDSDashboardProfessionalServices02-type-pill').forEach(pill => {
        //         pill.classList.remove('CDSDashboardProfessionalServices02-active');
        //     });
            
        //     document.getElementById('modal-prof-fee').value = '';
        //     document.getElementById('modal-cons-fee').value = '';
        //     document.getElementById('modal-assessment').value = '';
        //     document.querySelectorAll('.CDSDashboardProfessionalServices02-doc-item input').forEach((input, idx) => {
        //         input.checked = idx < 2; // Default first two
        //     });
            
        //     // Show modal
        //     document.getElementById('type-config-modal').classList.add('CDSDashboardProfessionalServices02-active');
        // }
        function configureService(button) {
           
            const uniqueId = button.getAttribute('data-unique-id');
            const serviceName = button.getAttribute('data-service-name');

            $.ajax({
                type: "GET",
                url: BASEURL + '/manage-services/add-subtype-pathways/'+uniqueId,
                data: {
                    _token: csrf_token,
                },
                dataType: 'json',
                success: function(data) {
                    $("#type-config-modal").html(data.contents);
                    // document.querySelectorAll('.CDSDashboardProfessionalServices02-type-pill').forEach(pill => {
                    //     pill.classList.remove('CDSDashboardProfessionalServices02-active');
                    // });
                    // Show modal
                    document.getElementById('type-config-modal').classList.add('CDSDashboardProfessionalServices02-active');
                }
            });

            // currentConfigService = { uniqueId: uniqueId };

            // // Update modal title
            // document.getElementById('modal-service-name').textContent = `Configure ${serviceName}`;

            // // Reset modal inputs
            // document.getElementById('modal-prof-fee').value = '';
            // document.getElementById('modal-cons-fee').value = '';
            // document.getElementById('modal-assessment').value = '';

            // // Reset document checkboxes
            // document.querySelectorAll('.CDSDashboardProfessionalServices02-doc-item input').forEach((input, idx) => {
            //     input.checked = idx < 2;
            // });

            // Reset pills
          
        }


        function editConfiguration(serviceIndex, configIndex) {
            currentConfigService = { serviceIndex, configIndex };
            const service = serviceData.addedServices[serviceIndex];
            const config = service.configurations[configIndex];
            
            // Update modal
            document.getElementById('modal-service-name').textContent = `Edit ${service.name} Configuration`;
            
            // Reset modal
            document.querySelectorAll('.CDSDashboardProfessionalServices02-type-pill').forEach(pill => {
                pill.classList.remove('CDSDashboardProfessionalServices02-active');
            });
            
            // Restore configuration
            config.types.forEach(type => {
                const pill = Array.from(document.querySelectorAll('.CDSDashboardProfessionalServices02-type-pill')).find(p => 
                    p.textContent.toLowerCase().includes(type)
                );
                if (pill) pill.classList.add('CDSDashboardProfessionalServices02-active');
            });
            
            document.getElementById('modal-prof-fee').value = config.fees.professional || '';
            document.getElementById('modal-cons-fee').value = config.fees.consultant || '';
            document.getElementById('modal-assessment').value = config.assessmentForm || '';
            
            // Restore documents
            document.querySelectorAll('.CDSDashboardProfessionalServices02-doc-item input').forEach(input => {
                input.checked = config.documents.includes(input.nextElementSibling.textContent);
            });
            
            // Show modal
            document.getElementById('type-config-modal').classList.add('CDSDashboardProfessionalServices02-active');
        }

        function toggleType(pill, type) {
            pill.classList.toggle('CDSDashboardProfessionalServices02-active');
            // Get all selected pills
            const selected = Array.from(document.querySelectorAll('.CDSDashboardProfessionalServices02-type-pill.CDSDashboardProfessionalServices02-active'))
                .map(p => p.getAttribute('data-id'));

            // Update hidden input
            const hiddenInput = document.getElementById('selected_service_type_ids');
            hiddenInput.value = selected;
        }

        function saveServiceConfig() {
            const service = serviceData.addedServices[currentConfigService.serviceIndex];
            
            // Get selected types
            const selectedTypes = [];
            document.querySelectorAll('.CDSDashboardProfessionalServices02-type-pill.CDSDashboardProfessionalServices02-active').forEach(pill => {
                if (pill.textContent.includes('Single')) selectedTypes.push('single');
                else if (pill.textContent.includes('Couple')) selectedTypes.push('couple');
                else if (pill.textContent.includes('Family')) selectedTypes.push('family');
                else if (pill.textContent.includes('Group')) selectedTypes.push('group');
            });
            
            if (selectedTypes.length === 0) {
                alert('Please select at least one applicant type');
                return;
            }
            
            // Create configuration
            const configuration = {
                types: selectedTypes,
                fees: {
                    professional: parseFloat(document.getElementById('modal-prof-fee').value) || 0,
                    consultant: parseFloat(document.getElementById('modal-cons-fee').value) || 0
                },
                documents: Array.from(document.querySelectorAll('.CDSDashboardProfessionalServices02-doc-item input:checked')).map(input => 
                    input.nextElementSibling.textContent
                ),
                assessmentForm: document.getElementById('modal-assessment').value
            };
            
            // Save or update configuration
            if (currentConfigService.configIndex !== null) {
                // Update existing
                service.configurations[currentConfigService.configIndex] = configuration;
            } else {
                // Add new
                service.configurations.push(configuration);
            }
            
            // Update UI
            updateServiceList();
            closeModal();
        }

        // function removeConfiguration(serviceIndex, configIndex) {
        //     if (confirm('Are you sure you want to remove this configuration?')) {
        //         serviceData.addedServices[serviceIndex].configurations.splice(configIndex, 1);
        //         updateServiceList();
        //     }
        // }

          // Function to remove configuration
    function removeConfiguration(id) {
        Swal.fire({
            title: "Are you sure you want to remove this configuration?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes",
            confirmButtonClass: "CdsTYButton-btn-primary",
            cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
            buttonsStyling: false,
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: BASEURL + '/manage-services/remove-configuration/' + id,
                    type: "post",
                    data: {
                        _token: csrf_token
                    },
                    dataType: "json",
                    beforeSend: function() {
                        showLoader();
                    },
                    success: function(response) {
                        hideLoader();
                        if (response.status == true) {
                            successMessage(response.message);
                            // Reload the configurations section
                            location.reload();
                        } else {
                            errorMessage(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        hideLoader();
                        errorMessage('An error occurred while removing the configuration. Please try again.');
                        console.error('AJAX Error:', error);
                    }
                });
            }
        })
       
    }
       
    function removeServiceFromServer(uniqueId) {

        Swal.fire({
        title: "Are you sure you want to remove this service and all its configurations?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        confirmButtonClass: "CdsTYButton-btn-primary",
        cancelButtonClass: "CdsTYButton-btn-primary CdsTYButton-border-thick ml-1",
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
                $.ajax({
                type: "GET",
                url: BASEURL + '/manage-services/remove-sub-service/' + uniqueId,
                data: {
                    _token: csrf_token
                },
                dataType: 'json',
                success: function(data) {
                    if(data.status == true){
                        // Reload the current page to refresh the data
                        location.reload();
                    }else{
                        errorMessage(data.message || 'Error removing service');
                    }
                },
                error: function() {
                    errorMessage('Error removing service');
                }
            });
        }
    });
        
    }

    function closeModal() {
        document.getElementById('type-config-modal').classList.remove('CDSDashboardProfessionalServices02-active');
    }

    function updateSummary() {
        const total = serviceData.addedServices.length;
        const configured = serviceData.addedServices.filter(s => s.configurations.length > 0).length;
        const variations = serviceData.addedServices.reduce((sum, s) => 
            sum + s.configurations.reduce((configSum, config) => configSum + config.types.length, 0), 0
        );
        const totalRevenue = serviceData.addedServices.reduce((sum, s) => 
            sum + s.configurations.reduce((configSum, config) => 
                configSum + (config.fees.professional + config.fees.consultant) * config.types.length, 0
            ), 0
        );
        
        document.getElementById('summary-services').textContent = total;
        document.getElementById('summary-configured').textContent = configured;
        document.getElementById('summary-variations').textContent = variations;
        document.getElementById('summary-revenue').textContent = `$${totalRevenue.toFixed(2)}`;
        
        // Update step 3 indicator
        if (configured === total && total > 0) {
            document.getElementById('step-3-indicator').classList.add('CDSDashboardProfessionalServices02-completed');
            document.getElementById('step-3-indicator').innerHTML = '✓';
            document.getElementById('step-3-selection').textContent = `All ${total} services configured`;
        } else if (configured > 0) {
            document.getElementById('step-3-selection').textContent = `${configured} of ${total} services configured`;
        } else {
            document.getElementById('step-3-selection').textContent = 'Configure fees and requirements for each service';
        }
    }

    function resetForm() {
        if (confirm('Are you sure you want to cancel? All changes will be lost.')) {
            location.reload();
        }
    }

    function saveService() {
        const unconfigured = serviceData.addedServices.filter(s => s.configurations.length === 0);
        
        if (unconfigured.length > 0) {
            alert(`Please configure all services. ${unconfigured.length} service(s) have no configurations.`);
            return;
        }
        
        if (serviceData.addedServices.length === 0) {
            alert('Please add at least one service');
            return;
        }

        // Prepare final data
        const finalData = {
            pathway: serviceData.pathway,
            pathwayName: serviceData.pathwayName,
            services: serviceData.addedServices.map(service => ({
                id: service.id,
                name: service.name,
                configurations: service.configurations
            }))
        };

        console.log('Saving service configuration:', finalData);
        
        const totalConfigs = serviceData.addedServices.reduce((sum, s) => sum + s.configurations.length, 0);
        const totalVariations = serviceData.addedServices.reduce((sum, s) => 
            sum + s.configurations.reduce((configSum, config) => configSum + config.types.length, 0), 0
        );
        
        alert(`Successfully saved ${serviceData.addedServices.length} service(s) with ${totalConfigs} configurations and ${totalVariations} total type variations!`);
    }

    // Close modal when clicking outside
    document.getElementById('type-config-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close edit modal when clicking outside
    document.getElementById('edit-config-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
</script>
<script>
    loadPathways();
    function loadPathways() {
        $.ajax({
            type: "GET",
            url: BASEURL + '/manage-services/fetch-pathways',
            data: {
                _token: csrf_token,
                main_service_id:"{{$main_service_id}}"
            },
            dataType: 'json',
            success: function(data) {
                if(data.status == true){
                    $(".CDSDashboardProfessionalServices02-main-pathways").html(data.contents);
                }else{
                    errorMessage('Please try again');
                }
            }
        });
    }
    @if($main_service_id != 0)
        // selectPathway("{{$main_service_id}}");
        displaySubPathway("{{$main_service_id}}");
    @endif
    function displaySubPathway(main_service_id){
        $.ajax({
            type: "GET",
            url: BASEURL + '/manage-services/display-sub-pathway',
            data: {
                _token: csrf_token,
                main_service_id:main_service_id
            },
            dataType: 'json',
            success: function(data) {
                $("#service-configuration-list").show();
                $("#service-config-items").html(data.contents);
                $("#config-service-count").html(data.count);
            }
        });
    }

    function editConfiguration(configurationId) {
        $.ajax({
            type: "GET",
            url: BASEURL + '/manage-services/edit-configuration/' + configurationId,
            data: {
                _token: csrf_token,
            },
            dataType: 'json',
            success: function(data) {
                $("#edit-config-modal").html(data.contents);
                // Show edit modal
                document.getElementById('edit-config-modal').classList.add('CDSDashboardProfessionalServices02-active');
            },
            error: function() {
                errorMessage('Error loading configuration for editing');
            }
        });
    }
</script>
   
@endsection
