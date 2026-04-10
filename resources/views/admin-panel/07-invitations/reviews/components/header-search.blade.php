<div class="CdsTYDashboard-special-filters-page-container">
    <div class="CdsTYDashboard-special-filters-header-section">
        <h1>Search Results</h1>
        <div class="CdsFeedsSearch row">
            <div class="CdsTYDashboard-special-filters-search-input-container">
                <input type="text" class="CdsTYDashboard-special-filters-search-input" placeholder="Search for Name..." id="searchInput">
                <span class="CdsTYDashboard-special-filters-search-icon">🔍</span>
            </div>
            <div class="CdsTYDashboard-special-filters-dropdown-container">
                <div class="CdsTYDashboard-special-filters-buttons-row">
                    <button class="CdsTYDashboard-special-filters-dropdown-btn" id="serviceOptionsBtn" onclick="cdsfilterTogglePanel('serviceOptions')">
                        <span>Status<span class="CdsTYDashboard-special-filters-badge" id="serviceCount" style="display: none;">0</span></span>
                        <span class="CdsTYDashboard-special-filters-arrow">▼</span>
                    </button>
                
                    <button class="CdsTYDashboard-special-filters-dropdown-btn" id="budgetBtn" onclick="cdsfilterTogglePanel('budget')">
                        <span>Ratings <span class="CdsTYDashboard-special-filters-badge" id="budgetCount" style="display: none;">0</span></span>
                        <span class="CdsTYDashboard-special-filters-arrow">▼</span>
                    </button>
                    <div class="cdsTYDashboardDropdownsDropdown">
                        <button class="cdsTYDashboardDropdownsDropdownBtn calendar-toggle">📅</button>
                    </div>
                </div>
                <!-- Service Options Panel -->
                <div class="CdsTYDashboard-special-filters-panel-wrapper" id="serviceOptionsPanel">
                    <div class="CdsTYDashboard-special-filters-panel">
                        <div class="CdsTYDashboard-special-filters-panel-header">
                            <h3 class="CdsTYDashboard-special-filters-panel-title">Status</h3>
                            <button class="CdsTYDashboard-special-filters-close-panel" onclick="cdsfilterClosePanel('serviceOptions')">✕</button>
                        </div>
                        <div class="CdsTYDashboard-special-filters-panel-body">
                            <div class="CdsTYDashboard-special-filters-section">
                                <div class="CdsTYDashboard-special-filters-section-title">Status</div>
                                @if(!empty($reviewStatus))
                                    @foreach($reviewStatus as $value)
                                        <div class="CdsTYDashboard-special-filters-checkbox-item">
                                            <input type="checkbox" id="status-{{$value}}" value="{{$value}}" class="CdsTYDashboard-status-filter" data-category="service">
                                            <label for="status-{{$value}}" class="CdsTYDashboard-special-filters-checkbox-label">
                                                {{ ucwords(str_replace('_', ' ', $value)) }}
                                                <!-- <span class="CdsTYDashboard-special-filters-count">(353)</span> -->
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="CdsTYDashboard-special-filters-panel-footer">
                            <a href="#" class="CdsTYDashboard-special-filters-clear-all" onclick="cdsfilterClearCategory('service'); return false;">Clear all</a>
                            <div class="CdsTYDashboard-special-filters-action-buttons">
                                <button class="CdsTYDashboard-special-filters-cancel-btn" onclick="cdsfilterClosePanel('serviceOptions')">Cancel</button>
                                <button class="CdsTYDashboard-special-filters-apply-btn" onclick="cdsfilterApplyFilters('serviceOptions')">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>           

                <!-- Budget Panel -->
                <div class="CdsTYDashboard-special-filters-panel-wrapper" id="budgetPanel">
                    <div class="CdsTYDashboard-special-filters-panel">
                        <div class="CdsTYDashboard-special-filters-panel-header">
                            <h3 class="CdsTYDashboard-special-filters-panel-title">Ratings</h3>
                            <button class="CdsTYDashboard-special-filters-close-panel" onclick="cdsfilterClosePanel('budget')">✕</button>
                        </div>
                        <div class="CdsTYDashboard-special-filters-panel-body">
                        <div class="CdsTYDashboard-special-filters-section">
                                <div class="CdsTYDashboard-special-filters-section-title">Seller Rating</div>
                                <div class="CdsTYDashboard-special-filters-rating-filter">
                                    <div class="CdsTYDashboard-special-filters-star-rating" onclick="cdsfilterSelectRating(this, 1)" data-rating="1">
                                        <span class="CdsTYDashboard-special-filters-star">★</span>
                                        <span>1</span>
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-star-rating" onclick="cdsfilterSelectRating(this, 2)" data-rating="2">
                                        <span class="CdsTYDashboard-special-filters-star">★</span>
                                        <span>2</span>
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-star-rating" onclick="cdsfilterSelectRating(this, 3)" data-rating="3">
                                        <span class="CdsTYDashboard-special-filters-star">★</span>
                                        <span>3</span>
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-star-rating" onclick="cdsfilterSelectRating(this, 4)" data-rating="4">
                                        <span class="CdsTYDashboard-special-filters-star">★</span>
                                        <span>4</span>
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-star-rating" onclick="cdsfilterSelectRating(this, 5)" data-rating="5">
                                        <span class="CdsTYDashboard-special-filters-star">★</span>
                                        <span>5</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="CdsTYDashboard-special-filters-panel-footer">
                            <a href="#" class="CdsTYDashboard-special-filters-clear-all" onclick="cdsfilterClearCategory('budget'); return false;">Clear all</a>
                            <div class="CdsTYDashboard-special-filters-action-buttons">
                                <button class="CdsTYDashboard-special-filters-cancel-btn" onclick="cdsfilterClosePanel('budget')">Cancel</button>
                                <button class="CdsTYDashboard-special-filters-apply-btn" onclick="cdsfilterApplyFilters('budget')">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="calendarBox" style="display:none; margin-top:10px;">
                    <div class="CdsTicket-special-filters-date-inputs">
                        {!! FormHelper::formDatepicker([
                            'label' => 'Start Date',
                            'name' => 'start_date',
                            'id' => 'startDate',
                            'class' => 'select2-input ga-country',
                        ]) !!}
                        <span>to</span>
                        {!! FormHelper::formDatepicker([
                            'label' => 'End Date',
                            'name' => 'end_date',
                            'id' => 'endDate',
                            'class' => 'select2-input ga-country',
                        ]) !!}
                    </div>
                    <div class="cdsGroupBtn">
                        <a href="#" class="CdsTYDashboard-special-filters-clear-all" onclick="cdsfilterClearCategory('seller'); return false;">Clear all</a>
                        <button class="CdsTYDashboard-special-filters-apply-btn" onclick="cdsfilterApplyFilters('sellerDetails')">Apply</button>
                    </div>
                </div>
            <!-- Show Active Filters button (only visible when card closed & filters exist) -->
            <button id="cdsfilterShowFiltersBtn" class="CdsTYDashboard-chip-btn" onclick="cdsfilterOpenFiltersCard()">
                Show active filters
            </button>

            <!-- Active Filters Card (hidden until filters exist) -->
            <div id="activeFiltersCard" class="CdsTYDashboard-special-filters-content-area is-hidden">
                <div class="CdsTYDashboard-active-filters-header">
                    <h3 style="margin:0;">Active Filters</h3>
                    <div class="CdsTYDashboard-active-filters-actions">
                        <button class="CdsTYDashboard-chip-btn" onclick="cdsfilterToggleMinimize()">Minimize</button>
                        <button class="CdsTYDashboard-chip-btn" onclick="cdsfilterCloseFiltersCard()">Close</button>
                    </div>
                </div>
                <div class="CdsTYDashboard-active-filters-body">
                    <div id="selectedFilters" class="CdsTYDashboard-special-filters-selected-filters">
                        <p style="color: #999;">No filters selected</p>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</div>

<div class="CdsTYDashboard-special-filters-overlay" id="overlay" onclick="cdsfilterCloseAllPanels()"></div>


 <script>
        let cdsfilterActivePanel = null;
        let cdsfilterActiveFilters = { service: [], seller: [], budget: [] };
        let cdsfilterSelectedRating = null;

        // UI state for Active Filters card: 'closed' | 'open' | 'min'
        let cdsfilterFiltersCardState = 'closed';

        function cdsfilterOpenFiltersCard() {
            const card = document.getElementById('activeFiltersCard');
            const btn = document.getElementById('cdsfilterShowFiltersBtn');
            card.classList.remove('is-hidden', 'is-minimized');
            cdsfilterFiltersCardState = 'open';
            btn.style.display = 'none';
        }

        function cdsfilterCloseFiltersCard() {
            const card = document.getElementById('activeFiltersCard');
            const btn = document.getElementById('cdsfilterShowFiltersBtn');
            card.classList.add('is-hidden');
            cdsfilterFiltersCardState = 'closed';
            const hasFilters = [...cdsfilterActiveFilters.service, ...cdsfilterActiveFilters.seller, ...cdsfilterActiveFilters.budget].length > 0;
            btn.style.display = hasFilters ? 'inline-block' : 'none';
        }

        function cdsfilterToggleMinimize() {
            const card = document.getElementById('activeFiltersCard');
            if (card.classList.contains('is-minimized')) {
                card.classList.remove('is-minimized');
                cdsfilterFiltersCardState = 'open';
            } else {
                card.classList.add('is-minimized');
                cdsfilterFiltersCardState = 'min';
            }
        }

        function cdsfilterTogglePanel(panelType) {
            const panel = document.getElementById(panelType + 'Panel');
            const btn = document.getElementById(panelType + 'Btn');
            const overlay = document.getElementById('overlay');
            cdsfilterCloseAllPanels();

            if (cdsfilterActivePanel === panelType) {
                cdsfilterClosePanel(panelType);
            } else {
                panel.classList.add('show');
                btn.classList.add('active');
                cdsfilterActivePanel = panelType;
                if (window.innerWidth <= 768) overlay.classList.add('show');
            }
        }

        function cdsfilterClosePanel(panelType) {
            const panel = document.getElementById(panelType + 'Panel');
            const btn = document.getElementById(panelType + 'Btn');
            const overlay = document.getElementById('overlay');
            if (panel) panel.classList.remove('show');
            if (btn) btn.classList.remove('active');
            overlay.classList.remove('show');
            if (cdsfilterActivePanel === panelType) cdsfilterActivePanel = null;
        }

        function cdsfilterCloseAllPanels() { ['serviceOptions','sellerDetails','budget'].forEach(cdsfilterClosePanel); }



        function cdsfilterUpdateFilterCount(category) {
            let count = 0;
            let badgeId;
            
            if (category === 'service') {
                badgeId = 'serviceCount';
                count = document.querySelectorAll(`input[data-category="${category}"]:checked`).length;
            } else if (category === 'seller') {
                badgeId = 'sellerCount';
                count = document.querySelectorAll(`input[data-category="${category}"]:checked`).length;
                // Add date filter count
                const startDate = document.getElementById('startDate');
                const endDate = document.getElementById('endDate');
                if (startDate && startDate.value) count++;
                if (endDate && endDate.value) count++;
            } else if (category === 'budget') {
                badgeId = 'budgetCount';
                // Count selected rating
                if (cdsfilterSelectedRating) count = 1;
            }
            
            const badge = document.getElementById(badgeId);
            if (!badge) return;
            if (count > 0) { 
                badge.style.display = 'inline-block'; 
                badge.textContent = count; 
            } else { 
                badge.style.display = 'none'; 
            }
        }

        function cdsfilterSelectRating(element, rating) {
            document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
            if (cdsfilterSelectedRating === rating) cdsfilterSelectedRating = null;
            else { element.classList.add('active'); cdsfilterSelectedRating = rating; }
        }



        function cdsfilterClearCategory(category) {
            document.querySelectorAll(`input[data-category="${category}"]`).forEach(cb => cb.checked = false);
            
            if (category === 'seller') {
                // Clear date range inputs as part of clearing details
                const start = document.getElementById('startDate');
                const end = document.getElementById('endDate');
                if (start) start.value = '';
                if (end) end.value = '';
            }
            
            if (category === 'budget') {
                // Clear rating selection
                document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
                cdsfilterSelectedRating = null;
            }

            // Clear active filters for this category
            cdsfilterActiveFilters[category] = [];

            cdsfilterUpdateFilterCount(category);
            cdsfilterUpdateSelectedFiltersDisplay();

            // Trigger data reload after clearing filters
            if (typeof loadData === 'function') {
                loadData();
            }
        }

        function cdsfilterApplyFilters(panelType) {
            let category = panelType === 'serviceOptions' ? 'service'
                        : panelType === 'sellerDetails' ? 'seller'
                        : panelType === 'budget' ? 'budget' : null;
            if (!category) return;

            cdsfilterActiveFilters[category] = [];
            
            // Handle checkboxes for service and seller categories
            if (category === 'service' || category === 'seller') {
                document.querySelectorAll(`input[data-category="${category}"]:checked`).forEach(checkbox => {
                    const label = checkbox.nextElementSibling ? checkbox.nextElementSibling.textContent.trim().split('(')[0].trim() : checkbox.id;
                    cdsfilterActiveFilters[category].push(label);
                });
            }

            // Handle ratings for budget category
            if (category === 'budget' && cdsfilterSelectedRating) {
                cdsfilterActiveFilters[category].push(`Rating: ${cdsfilterSelectedRating} stars`);
            }
            
            // Handle date filters for seller category
            if (category === 'seller') {
                const startDateVal = (document.getElementById('startDate') || {}).value || '';
                const endDateVal = (document.getElementById('endDate') || {}).value || '';
                if (startDateVal || endDateVal) {
                    const from = startDateVal ? startDateVal : 'Any';
                    const to = endDateVal ? endDateVal : 'Any';
                    cdsfilterActiveFilters[category].push(`Date: ${from} to ${to}`);
                }
            }

            cdsfilterUpdateFilterCount(category);

            // If card was closed and we just added filters, open it
            const hadNone = document.getElementById('activeFiltersCard').classList.contains('is-hidden') &&
                            [...cdsfilterActiveFilters.service, ...cdsfilterActiveFilters.seller, ...cdsfilterActiveFilters.budget].length > 0 &&
                            cdsfilterFiltersCardState === 'closed';
            cdsfilterUpdateSelectedFiltersDisplay();
            if (hadNone) cdsfilterOpenFiltersCard();

            cdsfilterClosePanel(panelType);

            // Trigger data reload if available
            if (typeof loadData === 'function') {
                loadData();
            }
        }

        function cdsfilterUpdateSelectedFiltersDisplay() {
            const container = document.getElementById('selectedFilters');
            const card = document.getElementById('activeFiltersCard');
            const showBtn = document.getElementById('cdsfilterShowFiltersBtn');

            const allFilters = [
                ...cdsfilterActiveFilters.service,
                ...cdsfilterActiveFilters.seller,
                ...cdsfilterActiveFilters.budget
            ];

            if (allFilters.length === 0) {
                container.innerHTML = '<p style="color: #999;">No filters selected</p>';
                card.classList.add('is-hidden');
                cdsfilterFiltersCardState = 'closed';
                showBtn.style.display = 'none';
                return;
            }

            container.innerHTML = allFilters.map(filter => 
                `<div class="CdsTYDashboard-special-filters-tag">
                    ${filter}
                    <span class="CdsTYDashboard-special-filters-remove" onclick="cdsfilterRemoveFilter('${filter.replace(/'/g, "\\'")}')">×</span>
                </div>`
            ).join('');

            if (cdsfilterFiltersCardState === 'closed') {
                card.classList.add('is-hidden');
                showBtn.style.display = 'inline-block';
            } else {
                card.classList.remove('is-hidden');
                showBtn.style.display = 'none';
                if (cdsfilterFiltersCardState === 'min') card.classList.add('is-minimized');
                else card.classList.remove('is-minimized');
            }
        }

        function cdsfilterRemoveFilter(filter) {
            ['service','seller','budget'].forEach(category => {
                cdsfilterActiveFilters[category] = cdsfilterActiveFilters[category].filter(f => f !== filter);

                // Uncheck matching checkboxes for this category
                document.querySelectorAll(`input[data-category="${category}"]:checked`).forEach(cb => {
                    const labelText = cb.nextElementSibling ? cb.nextElementSibling.textContent.trim().split('(')[0].trim() : cb.id;
                    if (labelText === filter) {
                        cb.checked = false;
                    }
                });

                // Handle rating filter removal
                if (category === 'budget' && /^Rating:\s*\d+\s*stars/.test(filter)) {
                    cdsfilterSelectedRating = null;
                    document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
                }
                
                // Handle date range tag removal
                if (category === 'seller' && /^Date:\s*/.test(filter)) {
                    const start = document.getElementById('startDate');
                    const end = document.getElementById('endDate');
                    if (start) start.value = '';
                    if (end) end.value = '';
                }

                cdsfilterUpdateFilterCount(category);
            });
            cdsfilterUpdateSelectedFiltersDisplay();

            // Trigger data reload after removing a filter
            if (typeof loadData === 'function') {
                loadData();
            }
        }

        // Escape key closes panels
        document.addEventListener('keydown', e => { if (e.key === 'Escape') cdsfilterCloseAllPanels(); });

        // Resize: remove overlay on desktop
        window.addEventListener('resize', () => { if (window.innerWidth > 768) document.getElementById('overlay').classList.remove('show'); });

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            ['service','seller','budget'].forEach(cdsfilterUpdateFilterCount);

            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                let debounceTimer;
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        if (this.value.length === 0 || this.value.length >= 2) {
                            loadData();
                        }
                    }, 200);
                });
            }

            // Click outside to close panels
            document.addEventListener('click', function(e) {
                if (cdsfilterActivePanel) {
                    const panel = document.getElementById(cdsfilterActivePanel + 'Panel');
                    const btn = document.getElementById(cdsfilterActivePanel + 'Btn');
                    if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) cdsfilterCloseAllPanels();
                }
            });

            // Stop propagation inside panel
            document.querySelectorAll('.CdsTYDashboard-special-filters-panel-wrapper').forEach(panel => {
                panel.addEventListener('click', e => e.stopPropagation());
            });
            // Stop propagation on button click
            document.querySelectorAll('.CdsTYDashboard-special-filters-dropdown-btn').forEach(btn => {
                btn.addEventListener('click', e => e.stopPropagation());
            });

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

        document.querySelector('.calendar-toggle').addEventListener('click', function () {
            const box = document.getElementById('calendarBox');
            box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
        });
    </script>