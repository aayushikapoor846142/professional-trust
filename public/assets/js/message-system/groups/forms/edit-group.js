/**
 * Edit Group Form Handler
 */

class EditGroupForm {
    constructor() {
        this.groupId = null;
        this.form = null;
        this.avatarPreview = null;
        this.init();
    }

    /**
     * Initialize the form
     */
    init() {
        this.groupId = this.getGroupIdFromUrl();
        this.form = document.getElementById('edit-group-form');
        this.avatarPreview = document.getElementById('avatar-preview');
        
        if (this.form) {
            this.bindEvents();
            this.loadGroupData();
        }
    }

    /**
     * Get group ID from URL
     */
    getGroupIdFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('group_id') || this.extractGroupIdFromPath();
    }

    /**
     * Extract group ID from path
     */
    extractGroupIdFromPath() {
        const path = window.location.pathname;
        const matches = path.match(/edit-group\/([^\/]+)/);
        return matches ? matches[1] : null;
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Form submission
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Avatar upload
        const avatarInput = document.getElementById('group_avatar');
        if (avatarInput) {
            avatarInput.addEventListener('change', (e) => this.handleAvatarChange(e));
        }

        // Remove avatar
        const removeAvatarBtn = document.getElementById('remove-avatar-btn');
        if (removeAvatarBtn) {
            removeAvatarBtn.addEventListener('click', () => this.removeAvatar());
        }

        // Privacy toggle
        const privacyToggle = document.getElementById('is_private');
        if (privacyToggle) {
            privacyToggle.addEventListener('change', () => this.togglePrivacySettings());
        }

        // Permission toggles
        const permissionToggles = document.querySelectorAll('.permission-toggle');
        permissionToggles.forEach(toggle => {
            toggle.addEventListener('change', () => this.updatePermissionSettings());
        });

        // Form validation
        this.form.addEventListener('input', () => this.validateForm());
    }

    /**
     * Load group data
     */
    loadGroupData() {
        if (!this.groupId) return;

        $.ajax({
            url: baseUrl + 'group/get-group-data/' + this.groupId,
            method: 'GET',
            success: (response) => {
                if (response.status) {
                    this.populateForm(response.group);
                } else {
                    ChatUtils.showNotification('Failed to load group data', 'error');
                }
            },
            error: () => {
                ChatUtils.showNotification('Failed to load group data', 'error');
            }
        });
    }

    /**
     * Populate form with group data
     */
    populateForm(group) {
        // Basic information
        this.form.querySelector('[name="group_name"]').value = group.name;
        this.form.querySelector('[name="group_description"]').value = group.description || '';
        this.form.querySelector('[name="is_private"]').checked = group.is_private;

        // Permissions
        this.form.querySelector('[name="members_can_add_members"]').checked = group.members_can_add_members;
        this.form.querySelector('[name="members_can_send_messages"]').checked = group.members_can_send_messages;
        this.form.querySelector('[name="members_can_edit_info"]').checked = group.members_can_edit_info;

        // Avatar
        if (group.avatar) {
            this.displayAvatar(group.avatar);
        }

        // Update form state
        this.togglePrivacySettings();
        this.updatePermissionSettings();
    }

    /**
     * Handle form submission
     */
    handleSubmit(event) {
        event.preventDefault();

        if (!this.validateForm()) {
            return;
        }

        const formData = new FormData(this.form);
        const submitBtn = this.form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.textContent = 'Updating...';

        $.ajax({
            url: baseUrl + 'group/update-group',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: (response) => {
                if (response.status) {
                    ChatUtils.showNotification('Group updated successfully');
                    this.redirectToGroup(response.group_id);
                } else {
                    ChatUtils.showNotification(response.message || 'Failed to update group', 'error');
                }
            },
            error: () => {
                ChatUtils.showNotification('Failed to update group', 'error');
            },
            complete: () => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    /**
     * Validate form
     */
    validateForm() {
        let isValid = true;
        const errors = [];

        // Group name validation
        const groupName = this.form.querySelector('[name="group_name"]').value.trim();
        if (!groupName) {
            errors.push('Group name is required');
            isValid = false;
        } else if (groupName.length < 3) {
            errors.push('Group name must be at least 3 characters long');
            isValid = false;
        } else if (groupName.length > 50) {
            errors.push('Group name must be less than 50 characters');
            isValid = false;
        }

        // Description validation
        const description = this.form.querySelector('[name="group_description"]').value.trim();
        if (description && description.length > 500) {
            errors.push('Description must be less than 500 characters');
            isValid = false;
        }

        // Display errors
        this.displayErrors(errors);

        return isValid;
    }

    /**
     * Display validation errors
     */
    displayErrors(errors) {
        // Clear previous errors
        this.form.querySelectorAll('.error-message').forEach(el => el.remove());
        this.form.querySelectorAll('.form-control.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        if (errors.length === 0) return;

        // Display errors
        errors.forEach(error => {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message text-danger mt-1';
            errorDiv.textContent = error;
            this.form.appendChild(errorDiv);
        });

        // Mark invalid fields
        if (errors.some(error => error.includes('Group name'))) {
            const nameField = this.form.querySelector('[name="group_name"]');
            nameField.classList.add('is-invalid');
        }

        if (errors.some(error => error.includes('Description'))) {
            const descField = this.form.querySelector('[name="group_description"]');
            descField.classList.add('is-invalid');
        }
    }

    /**
     * Handle avatar change
     */
    handleAvatarChange(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file
        const errors = ChatUtils.validateFile(file, 5 * 1024 * 1024, ['jpg', 'jpeg', 'png', 'gif']);
        if (errors.length > 0) {
            ChatUtils.showNotification(errors[0], 'error');
            event.target.value = '';
            return;
        }

        // Preview image
        const reader = new FileReader();
        reader.onload = (e) => {
            this.displayAvatar(e.target.result);
        };
        reader.readAsDataURL(file);
    }

    /**
     * Display avatar preview
     */
    displayAvatar(src) {
        if (!this.avatarPreview) return;

        this.avatarPreview.innerHTML = `
            <img src="${src}" alt="Group Avatar" class="avatar-preview-img">
            <button type="button" class="remove-avatar-btn" onclick="editGroupForm.removeAvatar()">
                <i class="fa-solid fa-times"></i>
            </button>
        `;
        this.avatarPreview.style.display = 'block';
    }

    /**
     * Remove avatar
     */
    removeAvatar() {
        const avatarInput = document.getElementById('group_avatar');
        if (avatarInput) {
            avatarInput.value = '';
        }

        if (this.avatarPreview) {
            this.avatarPreview.style.display = 'none';
            this.avatarPreview.innerHTML = '';
        }

        // Add hidden input to indicate avatar removal
        let removeAvatarInput = this.form.querySelector('[name="remove_avatar"]');
        if (!removeAvatarInput) {
            removeAvatarInput = document.createElement('input');
            removeAvatarInput.type = 'hidden';
            removeAvatarInput.name = 'remove_avatar';
            removeAvatarInput.value = '1';
            this.form.appendChild(removeAvatarInput);
        }
    }

    /**
     * Toggle privacy settings visibility
     */
    togglePrivacySettings() {
        const isPrivate = this.form.querySelector('[name="is_private"]').checked;
        const privacySettings = document.getElementById('privacy-settings');
        
        if (privacySettings) {
            privacySettings.style.display = isPrivate ? 'block' : 'none';
        }
    }

    /**
     * Update permission settings
     */
    updatePermissionSettings() {
        const canAddMembers = this.form.querySelector('[name="members_can_add_members"]').checked;
        const canSendMessages = this.form.querySelector('[name="members_can_send_messages"]').checked;
        const canEditInfo = this.form.querySelector('[name="members_can_edit_info"]').checked;

        // Update permission descriptions
        this.updatePermissionDescription('add-members-desc', canAddMembers);
        this.updatePermissionDescription('send-messages-desc', canSendMessages);
        this.updatePermissionDescription('edit-info-desc', canEditInfo);
    }

    /**
     * Update permission description
     */
    updatePermissionDescription(elementId, isEnabled) {
        const descElement = document.getElementById(elementId);
        if (descElement) {
            descElement.className = isEnabled ? 'text-success' : 'text-muted';
            descElement.textContent = isEnabled ? 'Enabled' : 'Disabled';
        }
    }

    /**
     * Redirect to group
     */
    redirectToGroup(groupId) {
        setTimeout(() => {
            window.location.href = baseUrl + 'group-chat/' + groupId;
        }, 1000);
    }

    /**
     * Cancel editing
     */
    cancelEdit() {
        if (confirm('Are you sure you want to cancel? All changes will be lost.')) {
            window.history.back();
        }
    }

    /**
     * Delete group
     */
    deleteGroup() {
        if (!confirm('Are you sure you want to delete this group? This action cannot be undone.')) {
            return;
        }

        $.ajax({
            url: baseUrl + 'group/delete-group',
            method: 'POST',
            data: {
                group_id: this.groupId,
                _token: csrfToken
            },
            success: (response) => {
                if (response.status) {
                    ChatUtils.showNotification('Group deleted successfully');
                    window.location.href = baseUrl + 'message-centre';
                } else {
                    ChatUtils.showNotification(response.message || 'Failed to delete group', 'error');
                }
            },
            error: () => {
                ChatUtils.showNotification('Failed to delete group', 'error');
            }
        });
    }
}

// Initialize edit group form
const editGroupForm = new EditGroupForm();

// Export for global access
window.EditGroupForm = editGroupForm; 