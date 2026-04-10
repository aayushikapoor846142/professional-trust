const CreateGroupForm = {
    init() {
        this.bindEvents();
        this.initializeFileUpload();
    },
    
    bindEvents() {
        // Form submission
        $(document).on("submit", "#create-group-form", (e) => {
            this.handleFormSubmit(e);
        });
        
        // Member search
        $(document).on("keyup", "#member-search", (e) => {
            this.handleMemberSearch(e);
        });
        
        // Member selection
        $(document).on("change", ".member-checkbox", (e) => {
            this.handleMemberSelection(e);
        });
        
        // Group type selection
        $(document).on("change", "input[name='group_type']", (e) => {
            this.handleGroupTypeChange(e);
        });
    },
    
    handleFormSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        $.ajax({
            url: $(e.target).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: () => {
                this.showLoading();
            },
            success: (response) => {
                if (response.status) {
                    this.showSuccess(response.message);
                    this.closeModal();
                    this.refreshGroupList();
                } else {
                    this.showError(response.message);
                }
            },
            error: (xhr, status, error) => {
                this.showError('An error occurred while creating the group.');
                console.error('Error creating group:', error);
            },
            complete: () => {
                this.hideLoading();
            }
        });
    },
    
    handleMemberSearch(e) {
        const searchTerm = e.target.value.toLowerCase();
        const $memberItems = $('.member-item');
        
        $memberItems.each(function() {
            const memberName = $(this).data('name');
            if (memberName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    },
    
    handleMemberSelection(e) {
        const $checkbox = $(e.currentTarget);
        const $memberItem = $checkbox.closest('.member-item');
        
        if ($checkbox.is(':checked')) {
            $memberItem.addClass('selected');
        } else {
            $memberItem.removeClass('selected');
        }
    },
    
    handleGroupTypeChange(e) {
        const groupType = e.target.value;
        
        // Update UI based on group type
        if (groupType === 'Private') {
            $('.privacy-notice').show();
        } else {
            $('.privacy-notice').hide();
        }
    },
    
    initializeFileUpload() {
        // Initialize file upload functionality
        if (typeof FileUploadHandler !== 'undefined') {
            FileUploadHandler.init();
        }
    },
    
    showLoading() {
        $('.form-submit-btn').prop('disabled', true).text('Creating...');
    },
    
    hideLoading() {
        $('.form-submit-btn').prop('disabled', false).text('Create Group');
    },
    
    showSuccess(message) {
        // Show success message
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else {
            alert(message);
        }
    },
    
    showError(message) {
        // Show error message
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            alert(message);
        }
    },
    
    closeModal() {
        $('.modal').modal('hide');
    },
    
    refreshGroupList() {
        // Refresh group list if on group listing page
        if (typeof GroupList !== 'undefined') {
            GroupList.loadData();
        }
    }
}; 