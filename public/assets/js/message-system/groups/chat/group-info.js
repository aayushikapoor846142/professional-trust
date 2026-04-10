/**
 * Group Info Handler - Handle group information and settings
 */

class GroupInfoHandler {
    constructor() {
        this.currentGroup = null;
        this.groupMembers = [];
        this.isAdmin = false;
    }

    /**
     * Initialize group info handler
     */
    init(groupId) {
        this.loadGroupInfo(groupId);
        this.bindEvents();
    }

    /**
     * Load group information
     */
    loadGroupInfo(groupId) {
        $.ajax({
            url: baseUrl + 'group/get-group-info/' + groupId,
            method: 'GET',
            success: (response) => {
                if (response.status) {
                    this.currentGroup = response.group;
                    this.groupMembers = response.members;
                    this.isAdmin = response.is_admin;
                    this.renderGroupInfo();
                }
            }
        });
    }

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Group settings button
        const settingsBtn = document.querySelector('.group-settings-btn');
        if (settingsBtn) {
            settingsBtn.addEventListener('click', () => this.openGroupSettings());
        }

        // Add member button
        const addMemberBtn = document.querySelector('.add-member-btn');
        if (addMemberBtn) {
            addMemberBtn.addEventListener('click', () => this.openAddMemberModal());
        }

        // Leave group button
        const leaveGroupBtn = document.querySelector('.leave-group-btn');
        if (leaveGroupBtn) {
            leaveGroupBtn.addEventListener('click', () => this.leaveGroup());
        }

        // Delete group button (admin only)
        const deleteGroupBtn = document.querySelector('.delete-group-btn');
        if (deleteGroupBtn) {
            deleteGroupBtn.addEventListener('click', () => this.deleteGroup());
        }
    }

    /**
     * Render group information
     */
    renderGroupInfo() {
        if (!this.currentGroup) return;

        // Update group header
        const groupName = document.getElementById('headerGroupName');
        if (groupName) {
            groupName.textContent = this.currentGroup.name;
        }

        // Update group avatar
        const groupAvatar = document.querySelector('.group-avatar img');
        if (groupAvatar) {
            groupAvatar.src = this.currentGroup.avatar || '/assets/images/default-group.png';
        }

        // Update member count
        const memberCount = document.querySelector('.member-count');
        if (memberCount) {
            memberCount.textContent = `${this.groupMembers.length} members`;
        }

        // Render member list
        this.renderMemberList();
    }

    /**
     * Render member list
     */
    renderMemberList() {
        const memberList = document.getElementById('group-members-list');
        if (!memberList) return;

        memberList.innerHTML = this.groupMembers.map(member => `
            <div class="member-item" data-member-id="${member.id}">
                <div class="member-avatar">
                    <img src="${member.avatar || '/assets/images/default-avatar.png'}" alt="${member.name}">
                    <div class="member-status ${member.is_online ? 'online' : 'offline'}"></div>
                </div>
                <div class="member-info">
                    <div class="member-name">${member.name}</div>
                    <div class="member-role">${member.is_admin ? 'Admin' : 'Member'}</div>
                    <div class="member-status-text">${member.is_online ? 'Online' : 'Offline'}</div>
                </div>
                ${this.isAdmin && !member.is_own ? `
                    <div class="member-actions">
                        <button type="button" class="action-btn" onclick="groupInfoHandler.promoteMember(${member.id})">
                            <i class="fa-solid fa-crown"></i>
                        </button>
                        <button type="button" class="action-btn" onclick="groupInfoHandler.removeMember(${member.id})">
                            <i class="fa-solid fa-user-minus"></i>
                        </button>
                    </div>
                ` : ''}
            </div>
        `).join('');
    }

    /**
     * Open group settings
     */
    openGroupSettings() {
        if (!this.isAdmin) {
            ChatUtils.showNotification('Only admins can change group settings', 'warning');
            return;
        }

        const settingsModal = document.getElementById('group-settings-modal');
        if (settingsModal) {
            settingsModal.style.display = 'block';
            this.populateSettingsForm();
        }
    }

    /**
     * Populate settings form
     */
    populateSettingsForm() {
        if (!this.currentGroup) return;

        const form = document.getElementById('group-settings-form');
        if (!form) return;

        form.querySelector('[name="group_name"]').value = this.currentGroup.name;
        form.querySelector('[name="group_description"]').value = this.currentGroup.description || '';
        form.querySelector('[name="is_private"]').checked = this.currentGroup.is_private;
        form.querySelector('[name="members_can_add_members"]').checked = this.currentGroup.members_can_add_members;
        form.querySelector('[name="members_can_send_messages"]').checked = this.currentGroup.members_can_send_messages;
    }

    /**
     * Save group settings
     */
    saveGroupSettings(formData) {
        $.ajax({
            url: baseUrl + 'group/update-settings',
            method: 'POST',
            data: formData,
            success: (response) => {
                if (response.status) {
                    this.currentGroup = response.group;
                    this.renderGroupInfo();
                    ChatUtils.showNotification('Group settings updated');
                    this.closeSettingsModal();
                } else {
                    ChatUtils.showNotification(response.message || 'Failed to update settings', 'error');
                }
            },
            error: () => {
                ChatUtils.showNotification('Failed to update settings', 'error');
            }
        });
    }

    /**
     * Close settings modal
     */
    closeSettingsModal() {
        const modal = document.getElementById('group-settings-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    /**
     * Open add member modal
     */
    openAddMemberModal() {
        if (!this.isAdmin && !this.currentGroup.members_can_add_members) {
            ChatUtils.showNotification('You cannot add members to this group', 'warning');
            return;
        }

        const modal = document.getElementById('add-member-modal');
        if (modal) {
            modal.style.display = 'block';
            this.loadAvailableUsers();
        }
    }

    /**
     * Load available users
     */
    loadAvailableUsers() {
        $.ajax({
            url: baseUrl + 'group/get-available-users',
            method: 'GET',
            data: { group_id: this.currentGroup.id },
            success: (response) => {
                if (response.status) {
                    this.renderAvailableUsers(response.users);
                }
            }
        });
    }

    /**
     * Render available users
     */
    renderAvailableUsers(users) {
        const userList = document.getElementById('available-users-list');
        if (!userList) return;

        userList.innerHTML = users.map(user => `
            <div class="user-item" data-user-id="${user.id}">
                <div class="user-avatar">
                    <img src="${user.avatar || '/assets/images/default-avatar.png'}" alt="${user.name}">
                </div>
                <div class="user-info">
                    <div class="user-name">${user.name}</div>
                    <div class="user-email">${user.email}</div>
                </div>
                <div class="user-action">
                    <button type="button" class="btn btn-primary btn-sm" onclick="groupInfoHandler.addMember(${user.id})">
                        Add
                    </button>
                </div>
            </div>
        `).join('');
    }

    /**
     * Add member to group
     */
    addMember(userId) {
        $.ajax({
            url: baseUrl + 'group/add-member',
            method: 'POST',
            data: {
                group_id: this.currentGroup.id,
                user_id: userId,
                _token: csrfToken
            },
            success: (response) => {
                if (response.status) {
                    ChatUtils.showNotification('Member added successfully');
                    this.loadGroupInfo(this.currentGroup.id);
                    this.closeAddMemberModal();
                } else {
                    ChatUtils.showNotification(response.message || 'Failed to add member', 'error');
                }
            },
            error: () => {
                ChatUtils.showNotification('Failed to add member', 'error');
            }
        });
    }

    /**
     * Remove member from group
     */
    removeMember(memberId) {
        if (!confirm('Are you sure you want to remove this member?')) return;

        $.ajax({
            url: baseUrl + 'group/remove-member',
            method: 'POST',
            data: {
                group_id: this.currentGroup.id,
                member_id: memberId,
                _token: csrfToken
            },
            success: (response) => {
                if (response.status) {
                    ChatUtils.showNotification('Member removed successfully');
                    this.loadGroupInfo(this.currentGroup.id);
                } else {
                    ChatUtils.showNotification(response.message || 'Failed to remove member', 'error');
                }
            },
            error: () => {
                ChatUtils.showNotification('Failed to remove member', 'error');
            }
        });
    }

    /**
     * Promote member to admin
     */
    promoteMember(memberId) {
        if (!confirm('Are you sure you want to promote this member to admin?')) return;

        $.ajax({
            url: baseUrl + 'group/promote-member',
            method: 'POST',
            data: {
                group_id: this.currentGroup.id,
                member_id: memberId,
                _token: csrfToken
            },
            success: (response) => {
                if (response.status) {
                    ChatUtils.showNotification('Member promoted to admin');
                    this.loadGroupInfo(this.currentGroup.id);
                } else {
                    ChatUtils.showNotification(response.message || 'Failed to promote member', 'error');
                }
            },
            error: () => {
                ChatUtils.showNotification('Failed to promote member', 'error');
            }
        });
    }

    /**
     * Leave group
     */
    leaveGroup() {
        if (!confirm('Are you sure you want to leave this group?')) return;

        $.ajax({
            url: baseUrl + 'group/leave-group',
            method: 'POST',
            data: {
                group_id: this.currentGroup.id,
                _token: csrfToken
            },
            success: (response) => {
                if (response.status) {
                    ChatUtils.showNotification('You have left the group');
                    window.location.href = baseUrl + 'message-centre';
                } else {
                    ChatUtils.showNotification(response.message || 'Failed to leave group', 'error');
                }
            },
            error: () => {
                ChatUtils.showNotification('Failed to leave group', 'error');
            }
        });
    }

    /**
     * Delete group (admin only)
     */
    deleteGroup() {
        if (!this.isAdmin) {
            ChatUtils.showNotification('Only admins can delete the group', 'warning');
            return;
        }

        if (!confirm('Are you sure you want to delete this group? This action cannot be undone.')) return;

        $.ajax({
            url: baseUrl + 'group/delete-group',
            method: 'POST',
            data: {
                group_id: this.currentGroup.id,
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

    /**
     * Close add member modal
     */
    closeAddMemberModal() {
        const modal = document.getElementById('add-member-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    /**
     * Get group statistics
     */
    getGroupStats() {
        $.ajax({
            url: baseUrl + 'group/get-stats/' + this.currentGroup.id,
            method: 'GET',
            success: (response) => {
                if (response.status) {
                    this.renderGroupStats(response.stats);
                }
            }
        });
    }

    /**
     * Render group statistics
     */
    renderGroupStats(stats) {
        const statsContainer = document.getElementById('group-stats');
        if (!statsContainer) return;

        statsContainer.innerHTML = `
            <div class="stat-item">
                <div class="stat-value">${stats.total_messages}</div>
                <div class="stat-label">Total Messages</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">${stats.total_members}</div>
                <div class="stat-label">Members</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">${stats.active_members}</div>
                <div class="stat-label">Active Members</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">${stats.created_date}</div>
                <div class="stat-label">Created</div>
            </div>
        `;
    }
}

// Initialize group info handler
const groupInfoHandler = new GroupInfoHandler();

// Export for global access
window.GroupInfoHandler = groupInfoHandler; 