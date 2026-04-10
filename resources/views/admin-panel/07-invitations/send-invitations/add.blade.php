@extends('admin-panel.layouts.app')
@section("styles")
<link rel="stylesheet" href="{{ url('assets/css/19-CDS-send-invitation-form.css') }}">
@endsection
@section('content')

<div class="ch-action">
                    <a href="{{ baseUrl('reviews/send-invitations') }}" class="CdsTYButton-btn-primary">
                        <i class="fa-solid fa-left me-1"></i>
                        Back
                    </a>
                </div>
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
            <div class="CdsSendInvitation-card-header">
                <h1 class="CdsSendInvitation-card-title">Send Review Invitation</h1>
                <p class="CdsSendInvitation-card-description">Invite your clients to leave reviews and build your
                    reputation</p>
            </div>

            <div class="CdsSendInvitation-tabs">
                <button class="CdsSendInvitation-tab CdsSendInvitation-active"
                    onclick="cdsSendInvitationSwitchTab('individual')">Individual Invitation</button>
                <button class="CdsSendInvitation-tab" onclick="cdsSendInvitationSwitchTab('bulk')">Bulk Upload
                    (CSV)</button>
            </div>

            <!-- Individual Invitation Tab -->
            <div id="individual-tab" class="CdsSendInvitation-tab-content">
                <div class="CdsSendInvitation-info-box">
                    <svg class="CdsSendInvitation-info-icon" width="20" height="20" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="CdsSendInvitation-info-text">
                        <strong>Smart Invitations:</strong> Select from existing clients or add new ones. Personalize
                        your invitation with service details and platform preferences for better response rates.
                    </div>
                </div>

                <form id="send-invitation-form" method="POST" action="{{ baseUrl('send-invitations/save') }}">
                    @csrf
                    <!-- Client Selection with Dropdown -->
                    <div class="CdsSendInvitation-form-group">
                        <div class="CdsSendInvitation-client-selector-header">
                            <h3 class="CdsSendInvitation-client-selector-title">Select clients to invite</h3>
                            <span class="CdsSendInvitation-client-selector-count" id="selected-count">0 selected</span>
                        </div>
                        <p class="CdsSendInvitation-client-selector-subtitle">Choose from existing clients or add new
                            ones - you may select multiple clients</p>

                        <div class="CdsSendInvitation-client-selector-actions">
                            <button type="button" class="CdsSendInvitation-selector-action-btn"
                                onclick="cdsSendInvitationSelectAllClients()">Select All</button>
                            <button type="button" class="CdsSendInvitation-selector-action-btn"
                                onclick="cdsSendInvitationClearAllClients()">Clear All</button>
                        </div>

                        <div class="CdsSendInvitation-selected-clients" id="selected-clients-container">
                            <!-- Selected clients will appear here as tags -->
                        </div>

                        <div class="CdsSendInvitation-client-selector-box">
                            <div class="CdsSendInvitation-client-selector-input-wrapper">
                                <svg class="CdsSendInvitation-client-selector-input-icon" width="20" height="20"
                                    fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                        clip-rule="evenodd" />
                                </svg>
                                <input type="text" id="client-selector-input"
                                    class="CdsSendInvitation-client-selector-input"
                                    placeholder="Search or select clients..."
                                    onfocus="cdsSendInvitationShowClientDropdown()"
                                    oninput="cdsSendInvitationFilterClients(this.value)">
                            </div>

                            <div class="CdsSendInvitation-client-selector-dropdown" id="client-dropdown">
                                <div class="CdsSendInvitation-client-list" id="client-list">
                                    @foreach($clients as $client)
                                    <div class="CdsSendInvitation-client-option" data-client-id="client-{{$client->following->id}}"
                                        data-client-name="{{$client->following->first_name.' '.$client->following->last_name}}"
                                        data-client-email="{{$client->following->email}}"
                                        onclick="cdsSendInvitationToggleClient(this)">
                                        <div class="CdsSendInvitation-client-checkbox">
                                            <span class="CdsSendInvitation-client-checkbox-icon">✓</span>
                                        </div>
                                        <div class="CdsSendInvitation-client-info">
                                            <div class="CdsSendInvitation-client-details">
                                                <div class="CdsSendInvitation-client-name">
                                                    {{$client->following->first_name.' '.$client->following->last_name}}
                                                </div>
                                                <div class="CdsSendInvitation-client-email">
                                                    {{$client->following->email}}</div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="CdsSendInvitation-add-new-client-option"
                                    onclick="cdsSendInvitationShowAddNewClient()">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Add new client</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden input to store selected client IDs -->
                        <input type="hidden" name="selected_clients" id="selected-clients-input">
                    </div>

                    <!-- Client Details (shown when adding new) -->
                    <div id="new-client-fields">
                        <div class="CdsSendInvitation-form-row">
                            <div class="CdsSendInvitation-form-group">
                                <label class="CdsSendInvitation-form-label" for="client-name">Receiver Name *</label>
                                <input type="text" id="client-name" name="receiver_name"
                                    class="CdsSendInvitation-form-input" placeholder="Enter receiver's full name">
                            </div>

                            <div class="CdsSendInvitation-form-group">
                                <label class="CdsSendInvitation-form-label" for="client-email">Receiver Email *</label>
                                <input type="email" id="client-email" name="receiver_email"
                                    class="CdsSendInvitation-form-input" placeholder="receiver@example.com">
                            </div>
                        </div>
                    </div>


                    <!-- Personal Message -->
                    <div class="CdsSendInvitation-form-group js-form-message">
                        <label class="CdsSendInvitation-form-label" for="personal-message">Personal Message</label>
                        <textarea id="personal-message" name="personal_message"
                            class="CdsSendInvitation-form-input CdsSendInvitation-form-textarea"
                            placeholder="Add a personal touch to your invitation..."></textarea>
                        <div class="CdsSendInvitation-form-help">This message will be included in the email along with
                            the standard invitation</div>
                    </div>



                    <div class="CdsSendInvitation-form-actions">
                        <button type="submit" class="CdsSendInvitation-btn CdsSendInvitation-btn-primary">Send
                            Invitation</button>
                        <button type="button" class="CdsSendInvitation-btn CdsSendInvitation-btn-secondary"
                            onclick="cdsSendInvitationPreviewEmail()">Preview Email</button>
                    </div>
                </form>
            </div>


			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
 <div class="CdsSendInvitation-main-card CdsSendInvitation-card" style="display:block">

            <!-- Bulk Upload Tab -->
            <div id="bulk-tab" class="CdsSendInvitation-tab-content CdsSendInvitation-hidden">
                <div class="CdsSendInvitation-info-box">
                    <svg class="CdsSendInvitation-info-icon" width="20" height="20" fill="currentColor"
                        viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <div class="CdsSendInvitation-info-text">
                        Upload a CSV file with client information to send multiple invitations at once. Maximum 500
                        invitations per upload.
                    </div>
                </div>

                <form id="bulk-upload-form" method="POST" action="{{ baseUrl('send-invitations/bulk-upload-csv') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="CDSFeed-form-group">
                        <label class="CDSFeed-form-label">Upload CSV</label>
                        <div class="CDSFeed-upload-container" id="feedMediaUpload">
                            <div class="CDSFeed-upload-area">
                                <input type="file" class="CDSFeed-file-input" multiple accept="image/*,.pdf,.doc,.docx" style="display: none;">
                                <div class="CDSFeed-upload-icon">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="7 10 12 15 17 10"></polyline>
                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                    </svg>
                                </div>
                                <p class="CDSFeed-upload-text">Drag and drop files here or click to browse</p>
                                <p class="CDSFeed-upload-hint">Supports: JPG, PNG, GIF, PDF, DOC, DOCX (Max 10MB per file)</p>
                            </div>
                            
                            <!-- File Preview Area -->
                            <div class="CDSFeed-file-list" style="display: none;">
                                <!-- Files will be dynamically added here -->
                            </div>
                        </div>
                    </div>

                    <a href="{{ url('sample-csv/client-invitation-csv.csv') }}" class="CdsSendInvitation-csv-template-link">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                        Download CSV template
                    </a>

                    <div class="CdsSendInvitation-form-group" style="margin-top: 2rem;">
                        <label class="CdsSendInvitation-form-label">CSV Format Requirements:</label>
                        <ul style="margin-left: 1.5rem; color: #6c757d; font-size: 0.875rem; line-height: 1.8;">
                            <li>Column 1: Client Name (required)</li>
                            <li>Column 2: Email Address (required)</li>
                            <li>Column 3: Personal Message (optional)</li>
                        </ul>
                    </div>

                    <div class="CdsSendInvitation-form-actions">
                        <button type="submit" class="CdsSendInvitation-btn CdsSendInvitation-btn-primary">Upload and Send</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="CdsSendInvitation-success-card CdsSendInvitation-card"></div>
        <div class="CdsSendInvitation-preview-card CdsSendInvitation-card">
            <div class="CdsSendInvitation-preview-content"></div>
            <div class="CdsSendInvitation-form-actions">
                <button type="button" class="CdsSendInvitation-btn CdsSendInvitation-btn-secondary" onclick="cdsSendInvitationPreviewBack()">Back to Form</button>
            </div>
        </div>
			</div>
	
	</div>
  </div>
</div>
 

@endsection
<!-- End Content -->
@section('javascript')
<script src="{{url('assets/js/custom-file-upload.js')}}"></script>
<link rel="stylesheet" href="{{ url('assets/css/custom-file-upload.css') }}">
<script>
    // Store selected clients
    let cdsSendInvitationSelectedClients = new Set();

    // Toggle client selection
    function cdsSendInvitationPreviewBack(){
        $(".CdsSendInvitation-card").hide();
        $(".CdsSendInvitation-main-card").show();
        $(".CdsSendInvitation-preview-card .CdsSendInvitation-preview-content").html('');
    }
    function cdsSendInvitationToggleClient(element) {
        event.stopPropagation();
        const clientId = element.dataset.clientId;

        if (cdsSendInvitationSelectedClients.has(clientId)) {
            cdsSendInvitationSelectedClients.delete(clientId);
            element.classList.remove('CdsSendInvitation-selected');
        } else {
            cdsSendInvitationSelectedClients.add(clientId);
            element.classList.add('CdsSendInvitation-selected');
        }

        cdsSendInvitationUpdateSelectedDisplay();
        cdsSendInvitationUpdateHiddenInput();
        
    }

    // Update selected clients display
    function cdsSendInvitationUpdateSelectedDisplay() {
        const container = document.getElementById('selected-clients-container');
        const count = document.getElementById('selected-count');

        container.innerHTML = '';
        count.textContent = cdsSendInvitationSelectedClients.size + ' selected';

        cdsSendInvitationSelectedClients.forEach(clientId => {
            // Find the client element in the DOM to get the dynamic data
            const clientElement = document.querySelector(`[data-client-id="${clientId}"]`);
            if (clientElement) {
                // Use data attributes if available, otherwise fall back to text content
                const clientName = clientElement.dataset.clientName ||
                    clientElement.querySelector('.CdsSendInvitation-client-name').textContent.trim();
                const clientEmail = clientElement.dataset.clientEmail ||
                    clientElement.querySelector('.CdsSendInvitation-client-email').textContent.trim();

                const tag = document.createElement('div');
                tag.className = 'CdsSendInvitation-selected-client-tag';
                tag.innerHTML = `
                ${clientName}
                <span class="CdsSendInvitation-remove-btn" onclick="cdsSendInvitationRemoveSelectedClient('${clientId}')">×</span>
            `;
                container.appendChild(tag);
            }
        });
    }

    // Update hidden input with selected client IDs
    function cdsSendInvitationUpdateHiddenInput() {
        const selectedClientsData = cdsSendInvitationGetSelectedClientsData();
        const hiddenInput = document.getElementById('selected-clients-input');
        hiddenInput.value = JSON.stringify(selectedClientsData);
        if(selectedClientsData.length > 0){
            $("#new-client-fields").hide();
        }else{
            $("#new-client-fields").show();
        }
    }

    // Remove selected client
    function cdsSendInvitationRemoveSelectedClient(clientId) {
        cdsSendInvitationSelectedClients.delete(clientId);
        const element = document.querySelector(`[data-client-id="${clientId}"]`);
        if (element) {
            element.classList.remove('CdsSendInvitation-selected');
        }
        cdsSendInvitationUpdateSelectedDisplay();
        cdsSendInvitationUpdateHiddenInput();
    }

    // Select all clients
    function cdsSendInvitationSelectAllClients() {
        event.preventDefault();
        const allClients = document.querySelectorAll('.CdsSendInvitation-client-option[data-client-id]');
        allClients.forEach(client => {
            const clientId = client.dataset.clientId;
            cdsSendInvitationSelectedClients.add(clientId);
            client.classList.add('CdsSendInvitation-selected');
        });
        cdsSendInvitationUpdateSelectedDisplay();
        cdsSendInvitationUpdateHiddenInput();
    }

    // Clear all selections
    function cdsSendInvitationClearAllClients() {
        event.preventDefault();
        cdsSendInvitationSelectedClients.clear();
        const allClients = document.querySelectorAll('.CdsSendInvitation-client-option[data-client-id]');
        allClients.forEach(client => {
            client.classList.remove('CdsSendInvitation-selected');
        });
        cdsSendInvitationUpdateSelectedDisplay();
        cdsSendInvitationUpdateHiddenInput();
    }

    // Show client dropdown
    function cdsSendInvitationShowClientDropdown() {
        const dropdown = document.getElementById('client-dropdown');
        dropdown.classList.add('CdsSendInvitation-show');
    }

    // Hide client dropdown
    function cdsSendInvitationHideClientDropdown() {
        const dropdown = document.getElementById('client-dropdown');
        dropdown.classList.remove('CdsSendInvitation-show');
        // Clear the search input when closing dropdown
        const searchInput = document.getElementById('client-selector-input');
        if (searchInput) {
            searchInput.value = '';
            // Reset all client visibility
            const clients = document.querySelectorAll('.CdsSendInvitation-client-option[data-client-id]');
            clients.forEach(client => {
                client.style.display = 'flex';
            });
        }
    }

    // Click outside to close dropdown
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.CdsSendInvitation-client-selector-box')) {
            cdsSendInvitationHideClientDropdown();
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            cdsSendInvitationHideClientDropdown();
        }
    });

    // Filter clients
    function cdsSendInvitationFilterClients(searchTerm) {
        const clients = document.querySelectorAll('.CdsSendInvitation-client-option[data-client-id]');
        const term = searchTerm.toLowerCase();

        clients.forEach(client => {
            const name = client.querySelector('.CdsSendInvitation-client-name').textContent.toLowerCase();
            const email = client.querySelector('.CdsSendInvitation-client-email').textContent.toLowerCase();

            if (name.includes(term) || email.includes(term)) {
                client.style.display = 'flex';
            } else {
                client.style.display = 'none';
            }
        });

        // Show dropdown when typing
        if (searchTerm) {
            cdsSendInvitationShowClientDropdown();
        }
    }

    // Show add new client form
    function cdsSendInvitationShowAddNewClient() {
        event.stopPropagation();
        const newClientFields = document.getElementById('new-client-fields');
        newClientFields.style.display = 'block';
        document.getElementById('client-name').focus();
        cdsSendInvitationHideClientDropdown();
    }

    // Tab switching
    function cdsSendInvitationSwitchTab(tab) {
        const tabs = document.querySelectorAll('.CdsSendInvitation-tab');
        const individualTab = document.getElementById('individual-tab');
        const bulkTab = document.getElementById('bulk-tab');

        tabs.forEach(t => t.classList.remove('CdsSendInvitation-active'));

        if (tab === 'individual') {
            tabs[0].classList.add('CdsSendInvitation-active');
            individualTab.classList.remove('CdsSendInvitation-hidden');
            bulkTab.classList.add('CdsSendInvitation-hidden');
        } else {
            tabs[1].classList.add('CdsSendInvitation-active');
            individualTab.classList.add('CdsSendInvitation-hidden');
            bulkTab.classList.remove('CdsSendInvitation-hidden');
        }
    }

    // File upload handling
    // document.getElementById('csv-file').addEventListener('change', function (e) {
    //     const file = e.target.files[0];
    //     if (file) {
    //         const uploadArea = document.querySelector('.CdsSendInvitation-upload-area');
    //         uploadArea.innerHTML = `
    //         <div class="CdsSendInvitation-upload-icon">✅</div>
    //         <div class="CdsSendInvitation-upload-text">${file.name}</div>
    //         <div class="CdsSendInvitation-upload-hint">Click to change file</div>
    //     `;
    //     }
    // });

    // Get selected clients data for form submission
    function cdsSendInvitationGetSelectedClientsData() {
        const selectedClientsData = [];

        cdsSendInvitationSelectedClients.forEach(clientId => {
            const clientElement = document.querySelector(`[data-client-id="${clientId}"]`);
            if (clientElement) {
                const clientData = {
                    id: clientId.replace('client-', ''), // Extract the numeric ID
                    name: clientElement.dataset.clientName ||
                        clientElement.querySelector('.CdsSendInvitation-client-name').textContent.trim(),
                    email: clientElement.dataset.clientEmail ||
                        clientElement.querySelector('.CdsSendInvitation-client-email').textContent.trim()
                };
                selectedClientsData.push(clientData);
            }
        });

        return selectedClientsData;
    }

    // Form submission handler
    document.getElementById('send-invitation-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const selectedClients = cdsSendInvitationGetSelectedClientsData();
        const personalMessage = document.getElementById('personal-message').value;

        // Check if adding new client
        const newClientFields = document.getElementById('new-client-fields');
        if (newClientFields.style.display !== 'none') {
            const newClientName = document.getElementById('client-name').value;
            const newClientEmail = document.getElementById('client-email').value;

            if (newClientName && newClientEmail) {
                // Add new client to the submission
                selectedClients.push({
                    id: 'new',
                    name: newClientName,
                    email: newClientEmail
                });
            }
        }

        if (selectedClients.length === 0) {
            errorMessage('Please select at least one client or add a new client');
            return;
        }
        document.getElementById('selected-clients-input').value = JSON.stringify(selectedClients);
        $.ajax({
            url: $("#send-invitation-form").attr("action"),
            type: "post",
            data: $("#send-invitation-form").serialize(),
            dataType: "json",
            beforeSend: function () {
                showLoader();
            },
            success: function (data) {
                hideLoader();
                if (data.status == true) {
                    successMessage(data.message);
                    $(".CdsSendInvitation-card").hide();
                    $(".CdsSendInvitation-success-card").show();
                    $(".CdsSendInvitation-success-card").html(data.success_contents);
                    $("#send-invitation-form")[0].reset();
                    cdsSendInvitationClearAllClients();
                } else {
                    if(data.error_type == 'validation'){
                        validation(data.message);
                    }else{
                        errorMessage(data.message);
                    }
                }
            },
            error: function () {
                internalError();
            }
        });
        // Submit the form
        // this.submit();
    });

    // Preview Email function
    function cdsSendInvitationPreviewEmail() {
        const selectedClients = cdsSendInvitationGetSelectedClientsData();
        const personalMessage = document.getElementById('personal-message').value;

        if (selectedClients.length === 0) {
            errorMessage('Please select at least one client to preview');
            return;
        }

        // Send AJAX request to preview
        $.ajax({
            url: '{{ baseUrl("send-invitations/show-email-preview") }}',
            type: "post",
            data: $("#send-invitation-form").serialize(),
            dataType: "json",
            beforeSend: function () {
                showLoader();
            },
            success: function (data) {
                hideLoader();
                if (data.status == true) {
                    $(".CdsSendInvitation-card").hide();
                    $(".CdsSendInvitation-preview-card").show();
                    $(".CdsSendInvitation-preview-card .CdsSendInvitation-preview-content").html(data.success_contents);
                } else {
                    if(data.error_type == 'validation'){
                        validation(data.message);
                    }else{
                        errorMessage(data.message);
                    }
                }
            },
            error: function () {
                internalError();
            }
        });
    }

    

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        // Add CSRF token to page if not already present
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    });

</script>


<!-- Bulk Upload Csv -->
<script>
     // Initialize file upload manager by ID
    const feedUploader = new FileUploadManager('#feedMediaUpload', {
        maxFileSize: 10 * 1024 * 1024, // 10MB
        maxFiles: 1, // Maximum 5 files
        allowedTypes: [
            'csv', 
        ],
        onFileAdded: function(fileData) {
            console.log('File added:', fileData.name);
        },
        onFileRemoved: function(fileData) {
            console.log('File removed:', fileData.name);
        },
        onError: function(message) {
            // Custom error handling
            errorMessage(message);
        }
    });
    
    // Initialize the uploader
    feedUploader.init();

    // Bulk upload functions
    document.getElementById('bulk-upload-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Add files from uploader
        const files = feedUploader.getFiles();
        if(files.length == 0){
            errorMessage("CSV file is required");
            return false;
        }
        files.forEach((file, index) => {
            formData.append(`csv_file`, file);
        });
        $.ajax({
            url: '{{ baseUrl("send-invitations/bulk-upload-csv") }}',
            type: "post",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            beforeSend: function () {
                showLoader();
            },
            success: function (data) {
                hideLoader();
                if (data.status == true) {
                    feedUploader.reset();
                    $(".CdsSendInvitation-card").hide();
                    $(".CdsSendInvitation-success-card").show();
                    $(".CdsSendInvitation-success-card").html(data.success_contents);
                } else {
                    if(data.error_type == 'validation'){
                        validation(data.message);
                    }else{
                        errorMessage(data.message);
                    }
                }
            },
            error: function () {
                internalError();
            }
        });
    });
    
</script>
@stack("send_invitation_scripts")
@endsection
