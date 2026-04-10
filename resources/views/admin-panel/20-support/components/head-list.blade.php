<!-- Advanced Ticket Header with Special Filters -->
<div class="CdsTicket-special-filters-page-container">
    <div class="CdsTicket-special-filters-header-section">
        <h1>Support Tickets</h1>
        <p class="CdsTicket-special-filters-results-count">Showing <span id="ticketCount">0</span> tickets</p>
        <div class="CdsTicket-special-filters-search-input-container">
            <form id="header-search-form">
                @csrf
                <input type="text" name="search" id="searchInput" class="CdsTicket-special-filters-search-input" 
                       placeholder="Search by ticket ID, subject, or user...">
                <span class="CdsTicket-special-filters-search-icon">🔍</span>
            </form>
        </div>
    </div>

    <div class="CdsTicket-special-filters-dropdown-container">
        <div class="CdsTicket-special-filters-buttons-row">
            <button class="CdsTicket-special-filters-dropdown-btn" id="statusBtn" onclick="cdsTicketTogglePanel('status')">
                <span>Status <span class="CdsTicket-special-filters-badge" id="statusCount" style="display: none;">0</span></span>
                <span class="CdsTicket-special-filters-arrow">▼</span>
            </button>
            <button class="CdsTicket-special-filters-dropdown-btn" id="priorityBtn" onclick="cdsTicketTogglePanel('priority')">
                <span>Priority <span class="CdsTicket-special-filters-badge" id="priorityCount" style="display: none;">0</span></span>
                <span class="CdsTicket-special-filters-arrow">▼</span>
            </button>
            <button class="CdsTicket-special-filters-dropdown-btn" id="detailsBtn" onclick="cdsTicketTogglePanel('details')">
                <span>Details <span class="CdsTicket-special-filters-badge" id="detailsCount" style="display: none;">0</span></span>
                <span class="CdsTicket-special-filters-arrow">▼</span>
            </button>
        </div>

        <!-- Status Panel -->
        <div class="CdsTicket-special-filters-panel-wrapper" id="statusPanel">
            <div class="CdsTicket-special-filters-panel">
                <div class="CdsTicket-special-filters-panel-header">
                    <h3 class="CdsTicket-special-filters-panel-title">Ticket Status</h3>
                    <button class="CdsTicket-special-filters-close-panel" onclick="cdsTicketClosePanel('status')">✕</button>
                </div>
                <div class="CdsTicket-special-filters-panel-body">
                    <div class="CdsTicket-special-filters-section">
                        <div class="CdsTicket-special-filters-section-title">Current Status</div>
                        <div class="CdsTicket-special-filters-checkbox-item">
                            <input type="checkbox" id="status-open" class="CdsList-filter" data-category="status" value="open">
                            <label for="status-open" class="CdsTicket-special-filters-checkbox-label">
                                <span class="status-indicator status-open"></span>
                                Open
                                <span class="CdsTicket-special-filters-count" id="openCount">({{getTicketCount('open','status')}})</span>
                            </label>
                        </div>
                        <div class="CdsTicket-special-filters-checkbox-item">
                            <input type="checkbox" id="status-in-progress" class="CdsList-filter" data-category="status" value="in_progress">
                            <label for="status-in-progress" class="CdsTicket-special-filters-checkbox-label">
                                <span class="status-indicator status-in-progress"></span>
                                In Progress
                                <span class="CdsTicket-special-filters-count" id="inProgressCount">({{getTicketCount('in_progress','status')}})</span>
                            </label>
                        </div>
                        <div class="CdsTicket-special-filters-checkbox-item">
                            <input type="checkbox" id="status-waiting" class="CdsList-filter" data-category="status" value="waiting_for_customer">
                            <label for="status-waiting" class="CdsTicket-special-filters-checkbox-label">
                                <span class="status-indicator status-waiting"></span>
                                Waiting for Customer
                                <span class="CdsTicket-special-filters-count" id="waitingCount">({{getTicketCount('waiting_for_customer','status')}})</span>
                            </label>
                        </div>
                        <div class="CdsTicket-special-filters-checkbox-item">
                            <input type="checkbox" id="status-resolved" class="CdsList-filter" data-category="status" value="resolved">
                            <label for="status-resolved" class="CdsTicket-special-filters-checkbox-label">
                                <span class="status-indicator status-resolved"></span>
                                Resolved
                                <span class="CdsTicket-special-filters-count" id="resolvedCount">({{getTicketCount('resolved','status')}})</span>
                            </label>
                        </div>
                        <div class="CdsTicket-special-filters-checkbox-item">
                            <input type="checkbox" id="status-closed" class="CdsList-filter" data-category="status" value="closed">
                            <label for="status-closed" class="CdsTicket-special-filters-checkbox-label">
                                <span class="status-indicator status-closed"></span>
                                Closed
                                <span class="CdsTicket-special-filters-count" id="closedCount">({{getTicketCount('closed','status')}})</span>
                            </label>
                        </div>
                    </div>

                    <div class="CdsTicket-special-filters-section">
                        <div class="CdsTicket-special-filters-section-title">Quick Filters</div>
                        <div class="CdsTicket-special-filters-toggle-switch">
                            <label>Unassigned Tickets</label>
                            <label class="CdsTicket-special-filters-switch">
                                <input type="checkbox" id="unassigned" data-category="status">
                                <span class="CdsTicket-special-filters-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="CdsTicket-special-filters-panel-footer">
                    <a href="#" class="CdsTicket-special-filters-clear-all" onclick="cdsTicketClearCategory('status'); return false;">Clear all</a>
                    <div class="CdsTicket-special-filters-action-buttons">
                        <button class="CdsTicket-special-filters-cancel-btn" onclick="cdsTicketClosePanel('status')">Cancel</button>
                        <button class="CdsTicket-special-filters-apply-btn" onclick="cdsTicketApplyFilters('status')">Apply</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Priority Panel -->
        <div class="CdsTicket-special-filters-panel-wrapper" id="priorityPanel">
            <div class="CdsTicket-special-filters-panel">
                <div class="CdsTicket-special-filters-panel-header">
                    <h3 class="CdsTicket-special-filters-panel-title">Priority Level</h3>
                    <button class="CdsTicket-special-filters-close-panel" onclick="cdsTicketClosePanel('priority')">✕</button>
                </div>
                <div class="CdsTicket-special-filters-panel-body">
                    <div class="CdsTicket-special-filters-section">
                        <div class="CdsTicket-special-filters-section-title">Priority</div>
                        <div class="CdsTicket-special-filters-checkbox-item">
                            <input type="checkbox" id="priority-urgent" class="CdsList-Priority" data-category="priority" value="urgent">
                            <label for="priority-urgent" class="CdsTicket-special-filters-checkbox-label">
                                <span class="priority-indicator priority-urgent"></span>
                                Urgent
                                <span class="CdsTicket-special-filters-count" id="urgentCount">({{getTicketCount('urgent','priority')}})</span>
                            </label>
                        </div>
                        <div class="CdsTicket-special-filters-checkbox-item">
                            <input type="checkbox" id="priority-high" class="CdsList-Priority" data-category="priority" value="high">
                            <label for="priority-high" class="CdsTicket-special-filters-checkbox-label">
                                <span class="priority-indicator priority-high"></span>
                                High
                                <span class="CdsTicket-special-filters-count" id="highCount">({{getTicketCount('high','priority')}})</span>
                            </label>
                        </div>
                        <div class="CdsTicket-special-filters-checkbox-item">
                            <input type="checkbox" id="priority-medium" class="CdsList-Priority" data-category="priority" value="medium">
                            <label for="priority-medium" class="CdsTicket-special-filters-checkbox-label">
                                <span class="priority-indicator priority-medium"></span>
                                Medium
                                <span class="CdsTicket-special-filters-count" id="mediumCount">({{getTicketCount('medium','priority')}})</span>
                            </label>
                        </div>
                        <div class="CdsTicket-special-filters-checkbox-item">
                            <input type="checkbox" id="priority-low" class="CdsList-Priority" data-category="priority" value="low">
                            <label for="priority-low" class="CdsTicket-special-filters-checkbox-label">
                                <span class="priority-indicator priority-low"></span>
                                Low
                                <span class="CdsTicket-special-filters-count" id="lowCount">({{getTicketCount('low','priority')}})</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="CdsTicket-special-filters-panel-footer">
                    <a href="#" class="CdsTicket-special-filters-clear-all" onclick="cdsTicketClearCategory('priority'); return false;">Clear all</a>
                    <div class="CdsTicket-special-filters-action-buttons">
                        <button class="CdsTicket-special-filters-cancel-btn" onclick="cdsTicketClosePanel('priority')">Cancel</button>
                        <button class="CdsTicket-special-filters-apply-btn" onclick="cdsTicketApplyFilters('priority')">Apply</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Panel -->
        <div class="CdsTicket-special-filters-panel-wrapper" id="detailsPanel">
            <div class="CdsTicket-special-filters-panel">
                <div class="CdsTicket-special-filters-panel-header">
                    <h3 class="CdsTicket-special-filters-panel-title">Ticket Details</h3>
                    <button class="CdsTicket-special-filters-close-panel" onclick="cdsTicketClosePanel('details')">✕</button>
                </div>
                <div class="CdsTicket-special-filters-panel-body">
                    <div class="CdsTicket-special-filters-section">
                        <div class="CdsTicket-special-filters-section-title">Date Range</div>
                        <div class="CdsTicket-special-filters-date-inputs">
                             {!! FormHelper::formDatepicker([
                                'label' => 'Start Date',
                                'name' => 'start_date',
                                'id' => 'startDate',
                                'class' => 'select2-input ga-country',
                            ]) !!}
                            <!-- <input type="date" class="CdsTicket-special-filters-date-input" id="startDate" data-category="details"> -->
                            <span>to</span>
                             {!! FormHelper::formDatepicker([
                                'label' => 'End Date',
                                'name' => 'end_date',
                                'id' => 'endDate',
                                'class' => 'select2-input ga-country',
                            ]) !!}
                            <!-- <input type="date" class="CdsTicket-special-filters-date-input" id="endDate" data-category="details"> -->
                        </div>
                    </div>

                    <div class="CdsTicket-special-filters-section">
                        <div class="CdsTicket-special-filters-section-title">Additional Filters</div>
                        <div class="CdsTicket-special-filters-toggle-switch">
                            <label>Has Attachments</label>
                            <label class="CdsTicket-special-filters-switch">
                                <input type="checkbox" id="has-attachments" data-category="details">
                                <span class="CdsTicket-special-filters-slider"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="CdsTicket-special-filters-panel-footer">
                    <a href="#" class="CdsTicket-special-filters-clear-all" onclick="cdsTicketClearCategory('details'); return false;">Clear all</a>
                    <div class="CdsTicket-special-filters-action-buttons">
                        <button class="CdsTicket-special-filters-cancel-btn" onclick="cdsTicketClosePanel('details')">Cancel</button>
                        <button class="CdsTicket-special-filters-apply-btn" onclick="cdsTicketApplyFilters('details')">Apply</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Show Active Filters button -->
    <button id="cdsTicketShowFiltersBtn" class="CdsTicket-chip-btn" onclick="cdsTicketOpenFiltersCard()">
        Show active filters
    </button>

    <!-- Active Filters Card -->
    <div id="activeFiltersCard" class="CdsTicket-special-filters-content-area is-hidden">
        <div class="CdsTicket-active-filters-header">
           
            <div class="CdsTicket-active-filters-actions">
                <button class="CdsTicket-chip-btn" onclick="cdsTicketToggleMinimize()">Minimize</button>
                <button class="CdsTicket-chip-btn" onclick="cdsTicketCloseFiltersCard()">Close</button>
            </div>
        </div>
        <div class="CdsTicket-active-filters-body">
            <div id="selectedFilters" class="CdsTicket-special-filters-selected-filters">
                <p style="color: #999;">No filters selected</p>
            </div>
        </div>
    </div>
</div>

<div class="CdsTicket-special-filters-overlay" id="overlay" onclick="cdsTicketCloseAllPanels()"></div>

<style>

</style>

<script>
let cdsTicketActivePanel = null;
let cdsTicketActiveFilters = { status: [], priority: [], details: [] };
let cdsTicketTempFilters = {};
let cdsTicketFiltersCardState = 'closed';

function cdsTicketTogglePanel(panelType) {
    const panel = document.getElementById(panelType + 'Panel');
    const btn = document.getElementById(panelType + 'Btn');
    const overlay = document.getElementById('overlay');
    
    cdsTicketCloseAllPanels();
    
    if (cdsTicketActivePanel === panelType) {
        cdsTicketClosePanel(panelType);
    } else {
        panel.classList.add('show');
        btn.classList.add('active');
        cdsTicketActivePanel = panelType;
        if (window.innerWidth <= 768) overlay.classList.add('show');
        cdsTicketTempFilters[panelType] = { ...cdsTicketActiveFilters[panelType] };
    }
}

function cdsTicketClosePanel(panelType) {
    const panel = document.getElementById(panelType + 'Panel');
    const btn = document.getElementById(panelType + 'Btn');
    const overlay = document.getElementById('overlay');
    
    if (panel) panel.classList.remove('show');
    if (btn) btn.classList.remove('active');
    overlay.classList.remove('show');
    
    if (cdsTicketActivePanel === panelType) cdsTicketActivePanel = null;
}

function cdsTicketCloseAllPanels() {
    ['status', 'priority', 'details'].forEach(cdsTicketClosePanel);
}

function cdsTicketOpenFiltersCard() {
    const card = document.getElementById('activeFiltersCard');
    const btn = document.getElementById('cdsTicketShowFiltersBtn');
    card.classList.remove('is-hidden', 'is-minimized');
    cdsTicketFiltersCardState = 'open';
    btn.style.display = 'none';
}

function cdsTicketCloseFiltersCard() {
    const card = document.getElementById('activeFiltersCard');
    const btn = document.getElementById('cdsTicketShowFiltersBtn');
    card.classList.add('is-hidden');
    cdsTicketFiltersCardState = 'closed';
    
    const hasFilters = [...cdsTicketActiveFilters.status, ...cdsTicketActiveFilters.priority, ...cdsTicketActiveFilters.details].length > 0;
    btn.style.display = hasFilters ? 'inline-block' : 'none';
}

function cdsTicketToggleMinimize() {
    const card = document.getElementById('activeFiltersCard');
    if (card.classList.contains('is-minimized')) {
        card.classList.remove('is-minimized');
        cdsTicketFiltersCardState = 'open';
    } else {
        card.classList.add('is-minimized');
        cdsTicketFiltersCardState = 'min';
    }
}

function cdsTicketUpdateFilterCount(category) {
    const inputs = Array.from(document.querySelectorAll(`input[data-category="${category}"]:checked`))
        .filter(cb => cb.id !== 'unassigned');
    const count = inputs.length;
    const badgeId = category + 'Count';
    const badge = document.getElementById(badgeId);
    
    if (!badge) return;
    
    if (count > 0) {
        badge.style.display = 'inline-block';
        badge.textContent = count;
    } else {
        badge.style.display = 'none';
    }
}

function cdsTicketClearCategory(category) {
    document.querySelectorAll(`input[data-category="${category}"]`).forEach(cb => cb.checked = false);
    
    if (category === 'details') {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
    }
    
    // Clear active filters for this category so chips are removed
    cdsTicketActiveFilters[category] = [];
    
    cdsTicketUpdateFilterCount(category);
    cdsTicketUpdateSelectedFiltersDisplay();
    
    // Reload data after clearing filters
    loadData();
}

function cdsTicketApplyFilters(category) {
    cdsTicketActiveFilters[category] = [];
    
    // Collect checked items
    document.querySelectorAll(`input[data-category="${category}"]:checked`).forEach(checkbox => {
        const label = cdsTicketGetLabelForInput(checkbox);
        
        // Include value for backend processing
        cdsTicketActiveFilters[category].push({
            label: label,
            value: checkbox.value || checkbox.id
        });
    });
    
    // Handle date range for details
    if (category === 'details') {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        if (startDate || endDate) {
            const dateLabel = startDate && endDate ? 
                `${startDate} to ${endDate}` : 
                startDate ? `From ${startDate}` : `Until ${endDate}`;
            
            cdsTicketActiveFilters[category].push({
                label: dateLabel,
                value: { start: startDate, end: endDate }
            });
        }
    }
    
    cdsTicketUpdateFilterCount(category);
    
    // Open card if it was closed and we have filters
    const hasFilters = [...cdsTicketActiveFilters.status, ...cdsTicketActiveFilters.priority, ...cdsTicketActiveFilters.details].length > 0;
    if (hasFilters && cdsTicketFiltersCardState === 'closed') {
        cdsTicketOpenFiltersCard();
    }
    
    cdsTicketUpdateSelectedFiltersDisplay();
    cdsTicketClosePanel(category);
    
    // Trigger search/filter update
    applyTicketFilters();
}

function cdsTicketUpdateSelectedFiltersDisplay() {
    const container = document.getElementById('selectedFilters');
    const card = document.getElementById('activeFiltersCard');
    const showBtn = document.getElementById('cdsTicketShowFiltersBtn');
    
    const allFilters = [
        ...cdsTicketActiveFilters.status.map(f => f.label || f),
        ...cdsTicketActiveFilters.priority.map(f => f.label || f),
        ...cdsTicketActiveFilters.details.map(f => f.label || f)
    ];
    
    if (allFilters.length === 0) {
        container.innerHTML = '<p style="color: #999;">No filters selected</p>';
        card.classList.add('is-hidden');
        cdsTicketFiltersCardState = 'closed';
        showBtn.style.display = 'none';
        return;
    }
    
    container.innerHTML = allFilters.map(filter => {
        const label = typeof filter === 'string' ? filter : (filter && filter.label ? String(filter.label) : String(filter));
        const escaped = label.replace(/'/g, "\\'");
        return `<div class="CdsTicket-special-filters-tag">
            ${label}
            <span class="CdsTicket-special-filters-remove" onclick="cdsTicketRemoveFilter('${escaped}')">×</span>
        </div>`;
    }).join('');
    
    if (cdsTicketFiltersCardState === 'closed') {
        card.classList.add('is-hidden');
        showBtn.style.display = 'inline-block';
    } else {
        card.classList.remove('is-hidden');
        showBtn.style.display = 'none';
        if (cdsTicketFiltersCardState === 'min') {
            card.classList.add('is-minimized');
        } else {
            card.classList.remove('is-minimized');
        }
    }
}

// Helper to derive the display label for a given checkbox input
function cdsTicketGetLabelForInput(checkbox) {
    // 1) Try immediate sibling (works for standard checkboxes)
    let text = '';
    const nextEl = checkbox.nextElementSibling;
    if (nextEl) {
        text = (nextEl.textContent || '').trim();
    }

    // 2) Try explicit <label for="id"> association
    if (!text && checkbox.id) {
        const forLabel = document.querySelector(`label[for="${checkbox.id}"]`);
        if (forLabel) {
            text = (forLabel.textContent || '').trim();
        }
    }

    // 3) Try toggle container title label (for switch-style inputs)
    if (!text) {
        const toggleContainer = checkbox.closest('.CdsTicket-special-filters-toggle-switch');
        if (toggleContainer) {
            const titleLabel = toggleContainer.querySelector(':scope > label');
            if (titleLabel) {
                text = (titleLabel.textContent || '').trim();
            }
        }
    }

    // 4) Fallback to input id
    if (!text) {
        text = checkbox.id || '';
    }

    // Remove counts like "(12)"
    const parenIndex = text.indexOf('(');
    if (parenIndex !== -1) {
        text = text.substring(0, parenIndex).trim();
    }
    return text;
}

// Uncheck the checkbox (or clear date) that corresponds to a removed filter chip
function cdsTicketUncheckByLabel(filterLabel) {
    let matched = false;

    // Uncheck matching checkboxes/toggles by comparing their display labels
    document.querySelectorAll('input[data-category]')
        .forEach(cb => {
            const label = cdsTicketGetLabelForInput(cb);
            if (label === filterLabel) {
                cb.checked = false;
                matched = true;
                const category = cb.getAttribute('data-category');
                cdsTicketUpdateFilterCount(category);
            }
        });

    // If this is a date range label, clear the date inputs
    if (!matched && (filterLabel.includes(' to ') || filterLabel.startsWith('From ') || filterLabel.startsWith('Until '))) {
        const start = document.getElementById('startDate');
        const end = document.getElementById('endDate');
        if (start && end) {
            start.value = '';
            end.value = '';
            cdsTicketUpdateFilterCount('details');
        }
    }
}

function cdsTicketRemoveFilter(filterLabel) {
    // Also uncheck the corresponding input/select in the filter panels
    cdsTicketUncheckByLabel(filterLabel);

    ['status', 'priority', 'details'].forEach(category => {
        cdsTicketActiveFilters[category] = cdsTicketActiveFilters[category]
            .filter(f => (f.label || f) !== filterLabel);
        cdsTicketUpdateFilterCount(category);
    });
    
    cdsTicketUpdateSelectedFiltersDisplay();
    applyTicketFilters();
    loadData();
}

// Function to apply filters (integrate with your backend)
function applyTicketFilters() {
    const filters = {
        status: cdsTicketActiveFilters.status.map(f => f.value || f),
        priority: cdsTicketActiveFilters.priority.map(f => f.value || f),
        details: cdsTicketActiveFilters.details.map(f => f.value || f),
        search: document.getElementById('searchInput').value
    };
    loadData();
    // Make AJAX call to your backend
    // fetch('/tickets/filter', {
    //     method: 'POST',
    //     headers: {
    //         'Content-Type': 'application/json',
    //         'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
    //     },
    //     body: JSON.stringify(filters)
    // })
    // .then(response => response.json())
    // .then(data => {
    //     // Update ticket list with filtered results
    //     updateTicketList(data);
    // });
}

// Placeholder function - implement based on your needs
function updateTicketList(data) {
    // Update your ticket list display
    console.log('Updating tickets with:', data);
    // Update count
    if (document.getElementById('ticketCount')) {
        document.getElementById('ticketCount').textContent = data.count || 0;
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize counts
    ['status', 'priority', 'details'].forEach(cdsTicketUpdateFilterCount);
    
    // Hide show filters button initially
    document.getElementById('cdsTicketShowFiltersBtn').style.display = 'none';
    
    // Search input handler
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyTicketFilters();
            }, 500);
        });
    }
    
    // Escape key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cdsTicketCloseAllPanels();
        }
    });
    
    // Click outside handler
    document.addEventListener('click', function(e) {
        if (cdsTicketActivePanel) {
            const panel = document.getElementById(cdsTicketActivePanel + 'Panel');
            const btn = document.getElementById(cdsTicketActivePanel + 'Btn');
            if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) {
                cdsTicketCloseAllPanels();
            }
        }
    });
    
    // Stop propagation for panels
    document.querySelectorAll('.CdsTicket-special-filters-panel-wrapper').forEach(panel => {
        panel.addEventListener('click', e => e.stopPropagation());
    });
    
    document.querySelectorAll('.CdsTicket-special-filters-dropdown-btn').forEach(btn => {
        btn.addEventListener('click', e => e.stopPropagation());
    });

    // Special: Unassigned Tickets toggle should only reload data
    // const unassignedToggle = document.getElementById('unassigned');
    // if (unassignedToggle) {
    //     unassignedToggle.addEventListener('change', function() {
    //         loadData();
    //     });
    // }



     const invoicesDatePicker = CustomCalendarWidget.initialize("startDate", {
    inline: false,
    dateFormat: "Y-m-d",
    onDateSelect: function(selectedDateStr) {
        // Destroy and recreate due date picker with new minDate
        const dueDateInput = document.getElementById("endDate");
        const currentDueDate = dueDateInput.value;
        
        // Destroy existing instance
        const wrapper = document.querySelector('#endDate').nextElementSibling;
        if (wrapper && wrapper.classList.contains('CDSComponents-Calender-inline01-container')) {
            wrapper.remove();
        }
        
        // Reinitialize with new minDate
        dueDatePicker = CustomCalendarWidget.initialize("endDate", {
            inline: false,
            minDate: selectedDateStr,
            dateFormat: "Y-m-d",
            defaultDate: currentDueDate >= selectedDateStr ? currentDueDate : selectedDateStr
        });
        
        // Update value if current is invalid
        if (new Date(currentDueDate) < new Date(selectedDateStr)) {
            dueDateInput.value = selectedDateStr;
        }
    }
});

// Initialize Due Date picker
let dueDatePicker = CustomCalendarWidget.initialize("endDate", {
    inline: false,
    dateFormat: "Y-m-d"
});

// Set initial due date to today
// document.getElementById("endDate").value = new Date().toISOString().split('T')[0];
});
</script>