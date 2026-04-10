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
                    <button class="CdsTYDashboard-special-filters-dropdown-btn" id="budgetBtn" onclick="cdsfilterTogglePanel('budget')">
                        <span>Amount <span class="CdsTYDashboard-special-filters-badge" id="budgetCount" style="display: none;">0</span></span>
                        <span class="CdsTYDashboard-special-filters-arrow">▼</span>
                    </button>
                    <button class="CdsTYDashboard-special-filters-dropdown-btn" id="sellerDetailsBtn" onclick="cdsfilterTogglePanel('sellerDetails')">
                        <span>Details <span class="CdsTYDashboard-special-filters-badge" id="sellerCount" style="display: none;">0</span></span>
                        <span class="CdsTYDashboard-special-filters-arrow">▼</span>
                    </button>
                    <div class="cdsTYDashboardDropdownsDropdown">
                        <button class="cdsTYDashboardDropdownsDropdownBtn calendar-toggle">📅</button>
                    </div>
                </div>
                <div class="">
                    <!-- Budget Panel -->
                    <div class="CdsTYDashboard-special-filters-panel-wrapper" id="budgetPanel">
                        <div class="CdsTYDashboard-special-filters-panel">
                            <div class="CdsTYDashboard-special-filters-panel-header">
                                <h3 class="CdsTYDashboard-special-filters-panel-title">Amount</h3>
                                <button class="CdsTYDashboard-special-filters-close-panel" onclick="cdsfilterClosePanel('budget')">✕</button>
                            </div>
                            <div class="CdsTYDashboard-special-filters-panel-body">
                                <div class="CdsTYDashboard-special-filters-section">
                                    <div class="CdsTYDashboard-special-filters-section-title">Amount</div>
                                    <div class="CdsTYDashboard-special-filters-price-inputs">
                                        <input type="number" class="CdsTYDashboard-special-filters-price-input" id="minPrice" placeholder="Min" onchange="cdsfilterUpdatePriceRange()">
                                        <span>-</span>
                                        <input type="number" class="CdsTYDashboard-special-filters-price-input" id="maxPrice" placeholder="Max" onchange="cdsfilterUpdatePriceRange()">
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-range-slider">
                                        <div class="CdsTYDashboard-special-filters-range-progress" id="rangeProgress"></div>
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-range-input">
                                        <input type="range" id="minRange" min="0" max="1000"  step="10" oninput="cdsfilterUpdatePriceInputs()">
                                        <input type="range" id="maxRange" min="0" max="1000"  step="10" oninput="cdsfilterUpdatePriceInputs()">
                                    </div>
                                </div>
                                
                                <div class="CdsTYDashboard-special-filters-section">
                                    <!-- <div class="CdsTYDashboard-special-filters-section-title">Package Type</div> -->
                                    <div class="CdsTYDashboard-special-filters-checkbox-item">
                                        <input type="checkbox" id="basic" data-category="budget" class="CdsTYDashboard-price-range" value="under-100">
                                        <label for="basic" class="CdsTYDashboard-special-filters-checkbox-label">
                                            Under $100
                                            <!-- <span class="CdsTYDashboard-special-filters-count">(523)</span> -->
                                        </label>
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-checkbox-item">
                                        <input type="checkbox" id="standard" data-category="budget" class="CdsTYDashboard-price-range" value="100-500">
                                        <label for="standard" class="CdsTYDashboard-special-filters-checkbox-label">
                                            $100 - $500
                                            <!-- <span class="CdsTYDashboard-special-filters-count">(412)</span> -->
                                        </label>
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-checkbox-item">
                                        <input type="checkbox" id="premium" data-category="budget" class="CdsTYDashboard-price-range" value="500-1000">
                                        <label for="premium" class="CdsTYDashboard-special-filters-checkbox-label">
                                            $500 - $1000
                                            <!-- <span class="CdsTYDashboard-special-filters-count">(287)</span> -->
                                        </label>
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-checkbox-item">
                                        <input type="checkbox" id="premium" data-category="budget" class="CdsTYDashboard-price-range" value="over-1000">
                                        <label for="premium" class="CdsTYDashboard-special-filters-checkbox-label">
                                            Over $1000
                                            <!-- <span class="CdsTYDashboard-special-filters-count">(287)</span> -->
                                        </label>
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
                    <!-- Seller Details Panel -->
                    <div class="CdsTYDashboard-special-filters-panel-wrapper" id="sellerDetailsPanel">
                        <div class="CdsTYDashboard-special-filters-panel">
                            <div class="CdsTYDashboard-special-filters-panel-header">
                                <h3 class="CdsTYDashboard-special-filters-panel-title">Details</h3>
                                
                                <button class="CdsTYDashboard-special-filters-close-panel" onclick="cdsfilterClosePanel('sellerDetails')">✕</button>
                                
                            </div>
                            <div class="CdsTYDashboard-special-filters-panel-body">
                                <div class="CdsTYDashboard-special-filters-section">
                                    <div class="CdsTYDashboard-special-filters-section-title">Date Range</div>
                                    
                                    
                                </div>

                                <div class="CdsTYDashboard-special-filters-section">
                                    <!-- <div class="CdsTYDashboard-special-filters-section-title">Response Time</div> -->
                                    <div class="CdsTYDashboard-special-filters-checkbox-item">
                                        <input type="checkbox" id="today" data-category="seller" class="CdsTYDashboard-hours-filter" value="today">
                                        <label for="today" class="CdsTYDashboard-special-filters-checkbox-label">
                                            Today
                                            <!-- <span class="CdsTYDashboard-special-filters-count">(112)</span> -->
                                        </label>
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-checkbox-item">
                                        <input type="checkbox" id="this_week" data-category="seller" class="CdsTYDashboard-hours-filter" value="this_week">
                                        <label for="this_week" class="CdsTYDashboard-special-filters-checkbox-label">
                                            This Week
                                            <!-- <span class="CdsTYDashboard-special-filters-count">(245)</span> -->
                                        </label>
                                    </div>
                                    <div class="CdsTYDashboard-special-filters-checkbox-item">
                                        <input type="checkbox" id="this_month" data-category="seller" class="CdsTYDashboard-hours-filter" value="this_month">
                                        <label for="this_month" class="CdsTYDashboard-special-filters-checkbox-label">
                                            This Month
                                            <!-- <span class="CdsTYDashboard-special-filters-count">(567)</span> -->
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="CdsTYDashboard-special-filters-panel-footer">
                                <a href="#" class="CdsTYDashboard-special-filters-clear-all" onclick="cdsfilterClearCategory('seller'); return false;">Clear all</a>
                                <div class="CdsTYDashboard-special-filters-action-buttons">
                                    <button class="CdsTYDashboard-special-filters-cancel-btn" onclick="cdsfilterClosePanel('sellerDetails')">Cancel</button>
                                    <button class="CdsTYDashboard-special-filters-apply-btn" onclick="cdsfilterApplyFilters('sellerDetails')">Apply</button>
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
                    <div id="activeFiltersCard" class="CdsTYDashboard-special-filters-content-area is-hidden mt-3">
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
    </div>    
</div>

<div class="CdsTYDashboard-special-filters-overlay" id="overlay" onclick="cdsfilterCloseAllPanels()"></div>


 <script>
        let cdsfilterActivePanel = null;
        let cdsfilterActiveFilters = { service: [], seller: [], budget: [] };
        let cdsfilterSelectedRating = null;
        let cdsfilterTempFilters = {};

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
                cdsfilterTempFilters[panelType] = { ...cdsfilterActiveFilters[panelType] };
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

        function cdsfilterToggleMore(id, element) {
            const moreContent = document.getElementById(id);
            if (!moreContent) return;
            const isHidden = moreContent.style.display === 'none';
            moreContent.style.display = isHidden ? 'block' : 'none';
            element.textContent = isHidden ? 'Show less' : '+2 more';
        }

        function cdsfilterUpdateFilterCount(category) {
            const count = document.querySelectorAll(`input[data-category="${category}"]:checked`).length;
            let badgeId;
            if (category === 'service') badgeId = 'serviceCount';
            if (category === 'seller')  badgeId = 'sellerCount';
            if (category === 'budget')  badgeId = 'budgetCount';
            const badge = document.getElementById(badgeId);
            if (!badge) return;
            if (count > 0) { badge.style.display = 'inline-block'; badge.textContent = count; }
            else { badge.style.display = 'none'; }
        }

        function cdsfilterSelectRating(element, rating) {
            document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
            if (cdsfilterSelectedRating === rating) cdsfilterSelectedRating = null;
            else { element.classList.add('active'); cdsfilterSelectedRating = rating; }
        }

        function cdsfilterUpdatePriceRange() {
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;
            document.getElementById('minRange').value = minPrice;
            document.getElementById('maxRange').value = maxPrice;
            cdsfilterUpdateRangeProgress();
        }

        function cdsfilterUpdatePriceInputs() {
            const minRange = document.getElementById('minRange');
            const maxRange = document.getElementById('maxRange');
            let minVal = parseInt(minRange.value), maxVal = parseInt(maxRange.value);
            if (minVal > maxVal) [minVal, maxVal] = [maxVal, minVal];
            document.getElementById('minPrice').value = minVal;
            document.getElementById('maxPrice').value = maxVal;
            cdsfilterUpdateRangeProgress();
        }

        function cdsfilterUpdateRangeProgress() {
            const minRange = document.getElementById('minRange');
            const maxRange = document.getElementById('maxRange');
            const progress = document.getElementById('rangeProgress');
            if (!minRange || !maxRange || !progress) return;
            const minPercent = (minRange.value / minRange.max) * 100;
            const maxPercent = (maxRange.value / maxRange.max) * 100;
            progress.style.left  = minPercent + '%';
            progress.style.right = (100 - maxPercent) + '%';
        }

        function cdsfilterClearCategory(category) {
            document.querySelectorAll(`input[data-category="${category}"]`).forEach(cb => cb.checked = false);
            if (category === 'seller') {
                document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
                cdsfilterSelectedRating = null;
                // Clear date range inputs as part of clearing details
                const start = document.getElementById('startDate');
                const end = document.getElementById('endDate');
                if (start) start.value = '';
                if (end) end.value = '';
            }
            if (category === 'budget') {
                const minPriceInput = document.getElementById('minPrice');
                const maxPriceInput = document.getElementById('maxPrice');
                const minRange = document.getElementById('minRange');
                const maxRange = document.getElementById('maxRange');
                if (minPriceInput) minPriceInput.value = '';
                if (maxPriceInput) maxPriceInput.value = '';
                if (minRange) minRange.value = minRange.min;
                if (maxRange) maxRange.value = maxRange.max;
                cdsfilterUpdateRangeProgress();
            }

            // Clear active filters for this category
            cdsfilterActiveFilters[category] = [];

            cdsfilterUpdateFilterCount(category);
            cdsfilterUpdateSelectedFiltersDisplay(); // keeps card/button state correct

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
            document.querySelectorAll(`input[data-category="${category}"]:checked`).forEach(checkbox => {
                const label = checkbox.nextElementSibling ? checkbox.nextElementSibling.textContent.trim().split('(')[0].trim() : checkbox.id;
                cdsfilterActiveFilters[category].push(label);
            });

            if (category === 'seller' && cdsfilterSelectedRating) {
                cdsfilterActiveFilters[category].push(`Rating: ${cdsfilterSelectedRating}+`);
            }
            if (category === 'seller') {
                const startDateVal = (document.getElementById('startDate') || {}).value || '';
                const endDateVal = (document.getElementById('endDate') || {}).value || '';
                if (startDateVal || endDateVal) {
                    const from = startDateVal ? startDateVal : 'Any';
                    const to = endDateVal ? endDateVal : 'Any';
                    cdsfilterActiveFilters[category].push(`Date: ${from} to ${to}`);
                }
            }
            if (category === 'budget') {
                const minPriceRaw = (document.getElementById('minPrice') || {}).value || '';
                const maxPriceRaw = (document.getElementById('maxPrice') || {}).value || '';
                const minPrice = String(minPriceRaw).trim();
                const maxPrice = String(maxPriceRaw).trim();
                if (minPrice !== '' || maxPrice !== '') {
                    const from = minPrice !== '' ? `$${minPrice}` : 'Any';
                    const to = maxPrice !== '' ? `$${maxPrice}` : 'Any';
                    cdsfilterActiveFilters[category].push(`${from} - ${to}`);
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

				// Handle special cases
				if (category === 'seller' && /^Rating:\s*/.test(filter)) {
					cdsfilterSelectedRating = null;
					document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
				}
				// Date range tag
				if (category === 'seller' && /^Date:\s*/.test(filter)) {
					const start = document.getElementById('startDate');
					const end = document.getElementById('endDate');
					if (start) start.value = '';
					if (end) end.value = '';
				}
				if (category === 'budget' && /^(\$?\d+|Any)\s*-\s*(\$?\d+|Any)/.test(filter)) {
					const minPriceInput = document.getElementById('minPrice');
					const maxPriceInput = document.getElementById('maxPrice');
					const minRange = document.getElementById('minRange');
					const maxRange = document.getElementById('maxRange');
					if (minPriceInput) minPriceInput.value = '';
					if (maxPriceInput) maxPriceInput.value = '';
					if (minRange) minRange.value = minRange.min;
					if (maxRange) maxRange.value = maxRange.max;
					cdsfilterUpdateRangeProgress();
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
            cdsfilterUpdateRangeProgress();
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