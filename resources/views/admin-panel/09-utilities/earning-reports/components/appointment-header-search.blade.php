<div class="CdsTYDashboard-special-filters-page-container appointment-CdsTYDashboard-special-filters-page-container">
        <div class="CdsTYDashboard-special-filters-header-section appointment-CdsTYDashboard-special-filters-header-section">
            <h1>Search Results</h1>
            <p class="CdsTYDashboard-special-filters-results-count appointment-CdsTYDashboard-special-filters-results-count" id="appointment-resultsCountDisplay"></p>
            <div class="CdsTYDashboard-special-filters-search-input-container appointment-CdsTYDashboard-special-filters-search-input-container">
                <input type="text" class="CdsTYDashboard-special-filters-search-input appointment-CdsTYDashboard-special-filters-search-input" placeholder="Search for points..." id="appointment-searchInput">
                <span class="CdsTYDashboard-special-filters-search-icon appointment-CdsTYDashboard-special-filters-search-icon">🔍</span>
            </div>
           
           
        </div>

        <div class="CdsTYDashboard-special-filters-dropdown-container appointment-CdsTYDashboard-special-filters-dropdown-container">
            <div class="CdsTYDashboard-special-filters-buttons-row appointment-CdsTYDashboard-special-filters-buttons-row">
                <button class="CdsTYDashboard-special-filters-dropdown-btn appointment-CdsTYDashboard-special-filters-dropdown-btn" id="appointment-budgetBtn" onclick="appointment_cdsfilterTogglePanel('budget')">
                    <span>Amount <span class="CdsTYDashboard-special-filters-badge appointment-CdsTYDashboard-special-filters-badge" id="appointment-budgetCount" style="display: none;">0</span></span>
                    <span class="CdsTYDashboard-special-filters-arrow appointment-CdsTYDashboard-special-filters-arrow">▼</span>
                </button>
                <button class="CdsTYDashboard-special-filters-dropdown-btn appointment-CdsTYDashboard-special-filters-dropdown-btn" id="appointment-sellerDetailsBtn" onclick="appointment_cdsfilterTogglePanel('sellerDetails')">
                    <span>Details <span class="CdsTYDashboard-special-filters-badge appointment-CdsTYDashboard-special-filters-badge" id="appointment-sellerCount" style="display: none;">0</span></span>
                    <span class="CdsTYDashboard-special-filters-arrow appointment-CdsTYDashboard-special-filters-arrow">▼</span>
                </button>
                <div class="cdsTYDashboardDropdownsDropdown">
                    <button class="cdsTYDashboardDropdownsDropdownBtn appointment-calendar-toggle">📅</button>
                </div>
            </div>

            <!-- Budget Panel -->
            <div class="CdsTYDashboard-special-filters-panel-wrapper appointment-CdsTYDashboard-special-filters-panel-wrapper" id="appointment-budgetPanel">
                <div class="CdsTYDashboard-special-filters-panel appointment-CdsTYDashboard-special-filters-panel">
                    <div class="CdsTYDashboard-special-filters-panel-header appointment-CdsTYDashboard-special-filters-panel-header">
                        <h3 class="CdsTYDashboard-special-filters-panel-title appointment-CdsTYDashboard-special-filters-panel-title">Amount</h3>
                        <button class="CdsTYDashboard-special-filters-close-panel appointment-CdsTYDashboard-special-filters-close-panel" onclick="appointment_cdsfilterClosePanel('budget')">✕</button>
                    </div>
                    <div class="CdsTYDashboard-special-filters-panel-body appointment-CdsTYDashboard-special-filters-panel-body">
                        <div class="CdsTYDashboard-special-filters-section appointment-CdsTYDashboard-special-filters-section">
                            <div class="CdsTYDashboard-special-filters-section-title appointment-CdsTYDashboard-special-filters-section-title">Amount</div>
                            <div class="CdsTYDashboard-special-filters-price-inputs appointment-CdsTYDashboard-special-filters-price-inputs">
                                <input type="number" class="CdsTYDashboard-special-filters-price-input appointment-CdsTYDashboard-special-filters-price-input" id="appointment-minPrice" placeholder="Min" onchange="appointment_cdsfilterUpdatePriceRange()">
                                <span>-</span>
                                <input type="number" class="CdsTYDashboard-special-filters-price-input appointment-CdsTYDashboard-special-filters-price-input" id="appointment-maxPrice" placeholder="Max" onchange="appointment_cdsfilterUpdatePriceRange()">
                            </div>
                            <div class="CdsTYDashboard-special-filters-range-slider appointment-CdsTYDashboard-special-filters-range-slider">
                                <div class="CdsTYDashboard-special-filters-range-progress appointment-CdsTYDashboard-special-filters-range-progress" id="appointment-rangeProgress"></div>
                            </div>
                            <div class="CdsTYDashboard-special-filters-range-input appointment-CdsTYDashboard-special-filters-range-input">
                                <input type="range" id="appointment-minRange" min="0" max="1000"  step="10" oninput="appointment_cdsfilterUpdatePriceInputs()">
                                <input type="range" id="appointment-maxRange" min="0" max="1000"  step="10" oninput="appointment_cdsfilterUpdatePriceInputs()">
                            </div>
                        </div>
                        
                        <div class="CdsTYDashboard-special-filters-section appointment-CdsTYDashboard-special-filters-section">
                            <!-- <div class="CdsTYDashboard-special-filters-section-title appointment-CdsTYDashboard-special-filters-section-title">Package Type</div> -->
                            <div class="CdsTYDashboard-special-filters-checkbox-item appointment-CdsTYDashboard-special-filters-checkbox-item">
                                <input type="checkbox" id="appointment-basic" data-category="appointment-budget" class="CdsTYDashboard-price-range appointment-CdsTYDashboard-price-range" value="under-100">
                                <label for="basic" class="CdsTYDashboard-special-filters-checkbox-label appointment-CdsTYDashboard-special-filters-checkbox-label">
                                    Under $100
                                    <!-- <span class="CdsTYDashboard-special-filters-count appointment-CdsTYDashboard-special-filters-count">(523)</span> -->
                                </label>
                            </div>
                            <div class="CdsTYDashboard-special-filters-checkbox-item appointment-CdsTYDashboard-special-filters-checkbox-item">
                                <input type="checkbox" id="appointment-standard" data-category="appointment-budget" class="CdsTYDashboard-price-range appointment-CdsTYDashboard-price-range" value="100-500">
                                <label for="standard" class="CdsTYDashboard-special-filters-checkbox-label appointment-CdsTYDashboard-special-filters-checkbox-label">
                                    $100 - $500
                                    <!-- <span class="CdsTYDashboard-special-filters-count appointment-CdsTYDashboard-special-filters-count">(412)</span> -->
                                </label>
                            </div>
                            <div class="CdsTYDashboard-special-filters-checkbox-item appointment-CdsTYDashboard-special-filters-checkbox-item">
                                <input type="checkbox" id="appointment-premium" data-category="appointment-budget" class="CdsTYDashboard-price-range appointment-CdsTYDashboard-price-range" value="500-1000">
                                <label for="premium" class="CdsTYDashboard-special-filters-checkbox-label appointment-CdsTYDashboard-special-filters-checkbox-label">
                                    $500 - $1000
                                    <!-- <span class="CdsTYDashboard-special-filters-count appointment-CdsTYDashboard-special-filters-count">(287)</span> -->
                                </label>
                            </div>
                            <div class="CdsTYDashboard-special-filters-checkbox-item appointment-CdsTYDashboard-special-filters-checkbox-item">
                                <input type="checkbox" id="appointment-premium" data-category="appointment-budget" class="CdsTYDashboard-price-range appointment-CdsTYDashboard-price-range" value="over-1000">
                                <label for="premium" class="CdsTYDashboard-special-filters-checkbox-label appointment-CdsTYDashboard-special-filters-checkbox-label">
                                    Over $1000
                                    <!-- <span class="CdsTYDashboard-special-filters-count appointment-CdsTYDashboard-special-filters-count">(287)</span> -->
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="CdsTYDashboard-special-filters-panel-footer appointment-CdsTYDashboard-special-filters-panel-footer">
                        <a href="#" class="CdsTYDashboard-special-filters-clear-all appointment-CdsTYDashboard-special-filters-clear-all" onclick="appointment_cdsfilterClearCategory('budget'); return false;">Clear all</a>
                        <div class="CdsTYDashboard-special-filters-action-buttons appointment-CdsTYDashboard-special-filters-action-buttons">
                            <button class="CdsTYDashboard-special-filters-cancel-btn appointment-CdsTYDashboard-special-filters-cancel-btn" onclick="appointment_cdsfilterClosePanel('budget')">Cancel</button>
                            <button class="CdsTYDashboard-special-filters-apply-btn appointment-CdsTYDashboard-special-filters-apply-btn" onclick="appointment_cdsfilterApplyFilters('budget')">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
             <!-- Seller Details Panel -->
            <div class="CdsTYDashboard-special-filters-panel-wrapper appointment-CdsTYDashboard-special-filters-panel-wrapper" id="appointment-sellerDetailsPanel">
                <div class="CdsTYDashboard-special-filters-panel appointment-CdsTYDashboard-special-filters-panel">
                    <div class="CdsTYDashboard-special-filters-panel-header appointment-CdsTYDashboard-special-filters-panel-header">
                        <h3 class="CdsTYDashboard-special-filters-panel-title appointment-CdsTYDashboard-special-filters-panel-title">Details</h3>
                        
                        <button class="CdsTYDashboard-special-filters-close-panel appointment-CdsTYDashboard-special-filters-close-panel" onclick="appointment_cdsfilterClosePanel('sellerDetails')">✕</button>
                       
                    </div>
                    <div class="CdsTYDashboard-special-filters-panel-body appointment-CdsTYDashboard-special-filters-panel-body">
                        <div class="CdsTYDashboard-special-filters-section appointment-CdsTYDashboard-special-filters-section">
                            <div class="CdsTYDashboard-special-filters-section-title appointment-CdsTYDashboard-special-filters-section-title">Date Range</div>
                            
                            
                        </div>

                        <div class="CdsTYDashboard-special-filters-section appointment-CdsTYDashboard-special-filters-section">
                            <!-- <div class="CdsTYDashboard-special-filters-section-title appointment-CdsTYDashboard-special-filters-section-title">Response Time</div> -->
                            <div class="CdsTYDashboard-special-filters-checkbox-item appointment-CdsTYDashboard-special-filters-checkbox-item">
                                <input type="checkbox" id="appointment-today" data-category="appointment-seller" class="CdsTYDashboard-hours-filter appointment-CdsTYDashboard-hours-filter" value="today">
                                <label for="today" class="CdsTYDashboard-special-filters-checkbox-label appointment-CdsTYDashboard-special-filters-checkbox-label">
                                    Today
                                    <!-- <span class="CdsTYDashboard-special-filters-count appointment-CdsTYDashboard-special-filters-count">(112)</span> -->
                                </label>
                            </div>
                            <div class="CdsTYDashboard-special-filters-checkbox-item appointment-CdsTYDashboard-special-filters-checkbox-item">
                                <input type="checkbox" id="appointment-this_week" data-category="appointment-seller" class="CdsTYDashboard-hours-filter appointment-CdsTYDashboard-hours-filter" value="this_week">
                                <label for="this_week" class="CdsTYDashboard-special-filters-checkbox-label appointment-CdsTYDashboard-special-filters-checkbox-label">
                                   This Week
                                    <!-- <span class="CdsTYDashboard-special-filters-count appointment-CdsTYDashboard-special-filters-count">(245)</span> -->
                                </label>
                            </div>
                            <div class="CdsTYDashboard-special-filters-checkbox-item appointment-CdsTYDashboard-special-filters-checkbox-item">
                                <input type="checkbox" id="appointment-this_month" data-category="appointment-seller" class="CdsTYDashboard-hours-filter appointment-CdsTYDashboard-hours-filter" value="this_month">
                                <label for="this_month" class="CdsTYDashboard-special-filters-checkbox-label appointment-CdsTYDashboard-special-filters-checkbox-label">
                                   This Month
                                    <!-- <span class="CdsTYDashboard-special-filters-count appointment-CdsTYDashboard-special-filters-count">(567)</span> -->
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="CdsTYDashboard-special-filters-panel-footer appointment-CdsTYDashboard-special-filters-panel-footer">
                        <a href="#" class="CdsTYDashboard-special-filters-clear-all appointment-CdsTYDashboard-special-filters-clear-all" onclick="appointment_cdsfilterClearCategory('seller'); return false;">Clear all</a>
                        <div class="CdsTYDashboard-special-filters-action-buttons appointment-CdsTYDashboard-special-filters-action-buttons">
                            <button class="CdsTYDashboard-special-filters-cancel-btn appointment-CdsTYDashboard-special-filters-cancel-btn" onclick="appointment_cdsfilterClosePanel('sellerDetails')">Cancel</button>
                            <button class="CdsTYDashboard-special-filters-apply-btn appointment-CdsTYDashboard-special-filters-apply-btn" onclick="appointment_cdsfilterApplyFilters('sellerDetails')">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
  <div id="appointmentCalendarBox" style="display:none; margin-top:10px;">
                    <div class="CdsTicket-special-filters-date-inputs">
                        {!! FormHelper::formDatepicker([
                    'label' => 'Start Date',
                    'name' => 'start_date',
                    'id' => 'appointmentStartDate',
                    'class' => 'select2-input ga-country',
                ]) !!}
                        <span>to</span>
                        {!! FormHelper::formDatepicker([
                    'label' => 'End Date',
                    'name' => 'end_date',
                    'id' => 'appointmentEndDate',
                    'class' => 'select2-input ga-country',
                ]) !!}
                    </div>
                    <div class="cdsGroupBtn">
                        <a href="#" class="CdsTYDashboard-special-filters-clear-all appointment-CdsTYDashboard-special-filters-clear-all" onclick="appointment_cdsfilterClearCategory('seller'); return false;">Clear all</a>
                        <button class="CdsTYDashboard-special-filters-apply-btn appointment-CdsTYDashboard-special-filters-apply-btn" onclick="appointment_cdsfilterApplyFilters('sellerDetails')">Apply</button>
                    </div>
                </div>
        <!-- Active Filters Card (hidden until filters exist) -->
        <div id="appointment-activeFiltersCard" class="CdsTYDashboard-special-filters-content-area is-hidden appointment-CdsTYDashboard-special-filters-content-area appointment-is-hidden">
            <div class="CdsTYDashboard-active-filters-header appointment-CdsTYDashboard-active-filters-header">
                <h3 style="margin:0;">Active Filters</h3>
                <div class="CdsTYDashboard-active-filters-actions appointment-CdsTYDashboard-active-filters-actions">
                    <button class="CdsTYDashboard-chip-btn appointment-CdsTYDashboard-chip-btn" onclick="appointment_cdsfilterToggleMinimize()">Minimize</button>
                    <button class="CdsTYDashboard-chip-btn appointment-CdsTYDashboard-chip-btn" onclick="appointment_cdsfilterCloseFiltersCard()">Close</button>
                </div>
            </div>
            <div class="CdsTYDashboard-active-filters-body appointment-CdsTYDashboard-active-filters-body">
                <div id="appointment-selectedFilters" class="CdsTYDashboard-special-filters-selected-filters appointment-CdsTYDashboard-special-filters-selected-filters">
                    <p style="color: #999;">No filters selected</p>
                </div>
            </div>
        </div>
    </div>

    <div class="CdsTYDashboard-special-filters-overlay appointment-CdsTYDashboard-special-filters-overlay" id="appointment-overlay" onclick="appointment_cdsfilterCloseAllPanels()"></div>


 <script>
        let appointment_cdsfilterActivePanel = null;
        let appointment_cdsfilterActiveFilters = { service: [], seller: [], budget: [] };
        let appointment_cdsfilterSelectedRating = null;
        let appointment_cdsfilterTempFilters = {};

        // UI state for Active Filters card: 'closed' | 'open' | 'min'
        let appointment_cdsfilterFiltersCardState = 'closed';

        function appointment_cdsfilterOpenFiltersCard() {
            const appointment_card = document.getElementById('appointment-activeFiltersCard');
            const appointment_btn = document.getElementById('appointment_cdsfilterShowFiltersBtn');
            if (appointment_card && appointment_btn) {
                appointment_card.classList.remove('is-hidden', 'is-minimized');
                appointment_cdsfilterFiltersCardState = 'open';
                appointment_btn.style.display = 'none';
            }
        }

        function appointment_cdsfilterCloseFiltersCard() {
            const appointment_card = document.getElementById('appointment-activeFiltersCard');
            const appointment_btn = document.getElementById('appointment_cdsfilterShowFiltersBtn');
            if (appointment_card && appointment_btn) {
                appointment_card.classList.add('is-hidden');
                appointment_cdsfilterFiltersCardState = 'closed';
                const appointment_hasFilters = [...appointment_cdsfilterActiveFilters.service, ...appointment_cdsfilterActiveFilters.seller, ...appointment_cdsfilterActiveFilters.budget].length > 0;
                appointment_btn.style.display = appointment_hasFilters ? 'inline-block' : 'none';
            }
        }

        function appointment_cdsfilterToggleMinimize() {
            const appointment_card = document.getElementById('appointment-activeFiltersCard');
            if (appointment_card) {
                if (appointment_card.classList.contains('is-minimized')) {
                    appointment_card.classList.remove('is-minimized');
                    appointment_cdsfilterFiltersCardState = 'open';
                } else {
                    appointment_card.classList.add('is-minimized');
                    appointment_cdsfilterFiltersCardState = 'min';
                }
            }
        }

        function appointment_cdsfilterTogglePanel(panelType) {
            const appointment_panel = document.getElementById('appointment-' + panelType + 'Panel');
            const appointment_btn = document.getElementById('appointment-' + panelType + 'Btn');
            const appointment_overlay = document.getElementById('appointment-overlay');
            
            if (!appointment_panel || !appointment_btn || !appointment_overlay) return;
            
            appointment_cdsfilterCloseAllPanels();

            if (appointment_cdsfilterActivePanel === panelType) {
                appointment_cdsfilterClosePanel(panelType);
            } else {
                appointment_panel.classList.add('show');
                appointment_btn.classList.add('active');
                appointment_cdsfilterActivePanel = panelType;
                if (window.innerWidth <= 768) appointment_overlay.classList.add('show');
                appointment_cdsfilterTempFilters[panelType] = { ...appointment_cdsfilterActiveFilters[panelType] };
            }
        }

        function appointment_cdsfilterClosePanel(panelType) {
            const appointment_panel = document.getElementById('appointment-' + panelType + 'Panel');
            const appointment_btn = document.getElementById('appointment-' + panelType + 'Btn');
            const appointment_overlay = document.getElementById('appointment-overlay');
            
            if (appointment_panel) appointment_panel.classList.remove('show');
            if (appointment_btn) appointment_btn.classList.remove('active');
            if (appointment_overlay) appointment_overlay.classList.remove('show');
            if (appointment_cdsfilterActivePanel === panelType) appointment_cdsfilterActivePanel = null;
        }

        function appointment_cdsfilterCloseAllPanels() { 
            ['serviceOptions','sellerDetails','budget'].forEach(appointment_cdsfilterClosePanel); 
        }

        function appointment_cdsfilterToggleMore(id, element) {
            const appointment_moreContent = document.getElementById(id);
            if (!appointment_moreContent) return;
            const appointment_isHidden = appointment_moreContent.style.display === 'none';
            appointment_moreContent.style.display = appointment_isHidden ? 'block' : 'none';
            element.textContent = appointment_isHidden ? 'Show less' : '+2 more';
        }

        function appointment_cdsfilterUpdateFilterCount(category) {
            const appointment_count = document.querySelectorAll(`input[data-category="appointment-${category}"]:checked`).length;
            let appointment_badgeId;
            if (category === 'service') appointment_badgeId = 'appointment-serviceCount';
            if (category === 'seller')  appointment_badgeId = 'appointment-sellerCount';
            if (category === 'budget')  appointment_badgeId = 'appointment-budgetCount';
            const appointment_badge = document.getElementById(appointment_badgeId);
            if (!appointment_badge) return;
            if (appointment_count > 0) { 
                appointment_badge.style.display = 'inline-block'; 
                appointment_badge.textContent = appointment_count; 
            } else { 
                appointment_badge.style.display = 'none'; 
            }
        }

        function appointment_cdsfilterSelectRating(element, rating) {
            document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
            if (appointment_cdsfilterSelectedRating === rating) appointment_cdsfilterSelectedRating = null;
            else { element.classList.add('active'); appointment_cdsfilterSelectedRating = rating; }
        }

        function appointment_cdsfilterUpdatePriceRange() {
            const appointment_minPrice = document.getElementById('appointment-minPrice');
            const appointment_maxPrice = document.getElementById('appointment-maxPrice');
            const appointment_minRange = document.getElementById('appointment-minRange');
            const appointment_maxRange = document.getElementById('appointment-maxRange');
            
            if (appointment_minPrice && appointment_maxPrice && appointment_minRange && appointment_maxRange) {
                appointment_minRange.value = appointment_minPrice.value;
                appointment_maxRange.value = appointment_maxPrice.value;
                appointment_cdsfilterUpdateRangeProgress();
            }
        }

        function appointment_cdsfilterUpdatePriceInputs() {
            const appointment_minRange = document.getElementById('appointment-minRange');
            const appointment_maxRange = document.getElementById('appointment-maxRange');
            const appointment_minPrice = document.getElementById('appointment-minPrice');
            const appointment_maxPrice = document.getElementById('appointment-maxPrice');
            
            if (appointment_minRange && appointment_maxRange && appointment_minPrice && appointment_maxPrice) {
                let appointment_minVal = parseInt(appointment_minRange.value), appointment_maxVal = parseInt(appointment_maxRange.value);
                if (appointment_minVal > appointment_maxVal) [appointment_minVal, appointment_maxVal] = [appointment_maxVal, appointment_minVal];
                appointment_minPrice.value = appointment_minVal;
                appointment_maxPrice.value = appointment_maxVal;
                appointment_cdsfilterUpdateRangeProgress();
            }
        }

        function appointment_cdsfilterUpdateRangeProgress() {
            const appointment_minRange = document.getElementById('appointment-minRange');
            const appointment_maxRange = document.getElementById('appointment-maxRange');
            const appointment_progress = document.getElementById('appointment-rangeProgress');
            if (!appointment_minRange || !appointment_maxRange || !appointment_progress) return;
            const appointment_minPercent = (appointment_minRange.value / appointment_minRange.max) * 100;
            const appointment_maxPercent = (appointment_maxRange.value / appointment_maxRange.max) * 100;
            appointment_progress.style.left  = appointment_minPercent + '%';
            appointment_progress.style.right = (100 - appointment_maxPercent) + '%';
        }

        function appointment_cdsfilterClearCategory(category) {
            document.querySelectorAll(`input[data-category="appointment-${category}"]`).forEach(cb => cb.checked = false);
            if (category === 'seller') {
                document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
                appointment_cdsfilterSelectedRating = null;
                // Clear date range inputs as part of clearing details
                const appointment_start = document.getElementById('appointmentStartDate');
                const appointment_end = document.getElementById('appointmentEndDate');
                if (appointment_start) appointment_start.value = '';
                if (appointment_end) appointment_end.value = '';
            }
            if (category === 'budget') {
                const appointment_minPriceInput = document.getElementById('appointment-minPrice');
                const appointment_maxPriceInput = document.getElementById('appointment-maxPrice');
                const appointment_minRange = document.getElementById('appointment-minRange');
                const appointment_maxRange = document.getElementById('appointment-maxRange');
                if (appointment_minPriceInput) appointment_minPriceInput.value = '';
                if (appointment_maxPriceInput) appointment_maxPriceInput.value = '';
                if (appointment_minRange) appointment_minRange.value = appointment_minRange.min;
                if (appointment_maxRange) appointment_maxRange.value = appointment_maxRange.max;
                appointment_cdsfilterUpdateRangeProgress();
            }

            // Clear active filters for this category
            appointment_cdsfilterActiveFilters[category] = [];

            appointment_cdsfilterUpdateFilterCount(category);
            appointment_cdsfilterUpdateSelectedFiltersDisplay(); // keeps card/button state correct

            // Trigger data reload after clearing filters
            if (typeof appointmentLoadData === 'function') {
                appointmentLoadData();
            }
        }

        function appointment_cdsfilterApplyFilters(panelType) {
            let appointment_category = panelType === 'serviceOptions' ? 'service'
                        : panelType === 'sellerDetails' ? 'seller'
                        : panelType === 'budget' ? 'budget' : null;
            if (!appointment_category) return;

            appointment_cdsfilterActiveFilters[appointment_category] = [];
            document.querySelectorAll(`input[data-category="appointment-${appointment_category}"]:checked`).forEach(checkbox => {
                const appointment_label = checkbox.nextElementSibling ? checkbox.nextElementSibling.textContent.trim().split('(')[0].trim() : checkbox.id;
                appointment_cdsfilterActiveFilters[appointment_category].push(appointment_label);
            });

            if (appointment_category === 'seller' && appointment_cdsfilterSelectedRating) {
                appointment_cdsfilterActiveFilters[appointment_category].push(`Rating: ${appointment_cdsfilterSelectedRating}+`);
            }
            if (appointment_category === 'seller') {
                const appointment_startDateVal = (document.getElementById('appointmentStartDate') || {}).value || '';
                const appointment_endDateVal = (document.getElementById('appointmentEndDate') || {}).value || '';
                if (appointment_startDateVal || appointment_endDateVal) {
                    const appointment_from = appointment_startDateVal ? appointment_startDateVal : 'Any';
                    const appointment_to = appointment_endDateVal ? appointment_endDateVal : 'Any';
                    appointment_cdsfilterActiveFilters[appointment_category].push(`Date: ${appointment_from} to ${appointment_to}`);
                }
            }
            if (appointment_category === 'budget') {
                const appointment_minPriceRaw = (document.getElementById('appointment-minPrice') || {}).value || '';
                const appointment_maxPriceRaw = (document.getElementById('appointment-maxPrice') || {}).value || '';
                const appointment_minPrice = String(appointment_minPriceRaw).trim();
                const appointment_maxPrice = String(appointment_maxPriceRaw).trim();
                if (appointment_minPrice !== '' || appointment_maxPrice !== '') {
                    const appointment_from = appointment_minPrice !== '' ? `$${appointment_minPrice}` : 'Any';
                    const appointment_to = appointment_maxPrice !== '' ? `$${appointment_maxPrice}` : 'Any';
                    appointment_cdsfilterActiveFilters[appointment_category].push(`${appointment_from} - ${appointment_to}`);
                }
            }

            appointment_cdsfilterUpdateFilterCount(appointment_category);

            // If card was closed and we just added filters, open it
            const appointment_activeFiltersCard = document.getElementById('appointment-activeFiltersCard');
            const appointment_hadNone = appointment_activeFiltersCard && appointment_activeFiltersCard.classList.contains('is-hidden') &&
                            [...appointment_cdsfilterActiveFilters.service, ...appointment_cdsfilterActiveFilters.seller, ...appointment_cdsfilterActiveFilters.budget].length > 0 &&
                            appointment_cdsfilterFiltersCardState === 'closed';
            appointment_cdsfilterUpdateSelectedFiltersDisplay();
            if (appointment_hadNone) appointment_cdsfilterOpenFiltersCard();

            appointment_cdsfilterClosePanel(panelType);

            // Trigger data reload if available
            if (typeof appointmentLoadData === 'function') {
                appointmentLoadData();
            }
        }

        function appointment_cdsfilterUpdateSelectedFiltersDisplay() {
            const appointment_container = document.getElementById('appointment-selectedFilters');
            const appointment_card = document.getElementById('appointment-activeFiltersCard');
            const appointment_showBtn = document.getElementById('appointment_cdsfilterShowFiltersBtn');

            // Add null checks to prevent errors
            if (!appointment_container || !appointment_card || !appointment_showBtn) {
                console.warn('Required elements not found for filter display update');
                return;
            }

            const appointment_allFilters = [
                ...appointment_cdsfilterActiveFilters.service,
                ...appointment_cdsfilterActiveFilters.seller,
                ...appointment_cdsfilterActiveFilters.budget
            ];

            if (appointment_allFilters.length === 0) {
                appointment_container.innerHTML = '<p style="color: #999;">No filters selected</p>';
                appointment_card.classList.add('is-hidden');
                appointment_cdsfilterFiltersCardState = 'closed';
                appointment_showBtn.style.display = 'none';
                return;
            }

            appointment_container.innerHTML = appointment_allFilters.map(filter => 
                `<div class="CdsTYDashboard-special-filters-tag appointment-CdsTYDashboard-special-filters-tag">
                    ${filter}
                    <span class="CdsTYDashboard-special-filters-remove appointment-CdsTYDashboard-special-filters-remove" onclick="appointment_cdsfilterRemoveFilter('${filter.replace(/'/g, "\\'")}')">×</span>
                </div>`
            ).join('');

            if (appointment_cdsfilterFiltersCardState === 'closed') {
                appointment_card.classList.add('is-hidden');
                appointment_showBtn.style.display = 'inline-block';
            } else {
                appointment_card.classList.remove('is-hidden');
                appointment_showBtn.style.display = 'none';
                if (appointment_cdsfilterFiltersCardState === 'min') appointment_card.classList.add('is-minimized');
                else appointment_card.classList.remove('is-minimized');
            }
        }

        		function appointment_cdsfilterRemoveFilter(filter) {
			['service','seller','budget'].forEach(category => {
				appointment_cdsfilterActiveFilters[category] = appointment_cdsfilterActiveFilters[category].filter(f => f !== filter);

				// Uncheck matching checkboxes for this category
				document.querySelectorAll(`input[data-category="appointment-${category}"]:checked`).forEach(cb => {
					const appointment_labelText = cb.nextElementSibling ? cb.nextElementSibling.textContent.trim().split('(')[0].trim() : cb.id;
					if (appointment_labelText === filter) {
						cb.checked = false;
					}
				});

				// Handle special cases
				if (category === 'seller' && /^Rating:\s*/.test(filter)) {
					appointment_cdsfilterSelectedRating = null;
					document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
				}
				// Date range tag
				if (category === 'seller' && /^Date:\s*/.test(filter)) {
					const appointment_start = document.getElementById('appointmentStartDate');
					const appointment_end = document.getElementById('appointmentEndDate');
					if (appointment_start) appointment_start.value = '';
					if (appointment_end) appointment_end.value = '';
				}
				if (category === 'budget' && /^(\$?\d+|Any)\s*-\s*(\$?\d+|Any)/.test(filter)) {
					const appointment_minPriceInput = document.getElementById('appointment-minPrice');
					const appointment_maxPriceInput = document.getElementById('appointment-maxPrice');
					const appointment_minRange = document.getElementById('appointment-minRange');
					const appointment_maxRange = document.getElementById('appointment-maxRange');
					if (appointment_minPriceInput) appointment_minPriceInput.value = '';
					if (appointment_maxPriceInput) appointment_maxPriceInput.value = '';
					if (appointment_minRange) appointment_minRange.value = appointment_minRange.min;
					if (appointment_maxRange) appointment_maxRange.value = appointment_maxRange.max;
					appointment_cdsfilterUpdateRangeProgress();
				}

				appointment_cdsfilterUpdateFilterCount(category);
			});
			appointment_cdsfilterUpdateSelectedFiltersDisplay();

			// Trigger data reload after removing a filter
			if (typeof appointmentLoadData === 'function') {
				appointmentLoadData();
			}
		}

        // Escape key closes panels
        document.addEventListener('keydown', e => { if (e.key === 'Escape') appointment_cdsfilterCloseAllPanels(); });

        // Resize: remove overlay on desktop
        window.addEventListener('resize', () => { if (window.innerWidth > 768) document.getElementById('appointment-overlay').classList.remove('show'); });

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            // Only run initialization if required elements exist
            const appointment_minRange = document.getElementById('appointment-minRange');
            const appointment_maxRange = document.getElementById('appointment-maxRange');
            const appointment_rangeProgress = document.getElementById('appointment-rangeProgress');
            
            if (appointment_minRange && appointment_maxRange && appointment_rangeProgress) {
                appointment_cdsfilterUpdateRangeProgress();
            }
            
            ['service','seller','budget'].forEach(appointment_cdsfilterUpdateFilterCount);

            const appointment_searchInput = document.getElementById('appointment-searchInput');
            if (appointment_searchInput) {
                let appointment_debounceTimer;
                appointment_searchInput.addEventListener('input', function() {
                    clearTimeout(appointment_debounceTimer);
                    appointment_debounceTimer = setTimeout(() => {
                        if (this.value.length === 0 || this.value.length >= 2) {
                            if (typeof appointmentLoadData === 'function') {
                                appointmentLoadData();
                            }
                        }
                    }, 200);
                });
            }

            // Click outside to close panels
            document.addEventListener('click', function(e) {
                if (appointment_cdsfilterActivePanel) {
                    const appointment_panel = document.getElementById('appointment-' + appointment_cdsfilterActivePanel + 'Panel');
                    const appointment_btn = document.getElementById('appointment-' + appointment_cdsfilterActivePanel + 'Btn');
                    if (appointment_panel && appointment_btn && !appointment_panel.contains(e.target) && !appointment_btn.contains(e.target)) {
                        appointment_cdsfilterCloseAllPanels();
                    }
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

            // Initialize date pickers only if CustomCalendarWidget exists
            if (typeof CustomCalendarWidget !== 'undefined') {
                const appointment_invoicesDatePicker = CustomCalendarWidget.initialize("appointmentStartDate", {
                    inline: false,
                    dateFormat: "Y-m-d",
                    onDateSelect: function(selectedDateStr) {
                        // Destroy and recreate due date picker with new minDate
                        const appointment_dueDateInput = document.getElementById("appointmentEndDate");
                        if (!appointment_dueDateInput) return;
                        
                        const appointment_currentDueDate = appointment_dueDateInput.value;
                        
                        // Destroy existing instance
                        const appointment_wrapper = document.querySelector('#appointmentEndDate').nextElementSibling;
                        if (appointment_wrapper && appointment_wrapper.classList.contains('CDSComponents-Calender-inline01-container')) {
                            appointment_wrapper.remove();
                        }
                        
                        // Reinitialize with new minDate
                        appointment_dueDatePicker = CustomCalendarWidget.initialize("appointmentEndDate", {
                            inline: false,
                            minDate: selectedDateStr,
                            dateFormat: "Y-m-d",
                            defaultDate: appointment_currentDueDate >= selectedDateStr ? appointment_currentDueDate : selectedDateStr
                        });
                        
                        // Update value if current is invalid
                        if (new Date(appointment_currentDueDate) < new Date(selectedDateStr)) {
                            appointment_dueDateInput.value = selectedDateStr;
                        }
                    }
                });

                // Initialize Due Date picker
                let appointment_dueDatePicker = CustomCalendarWidget.initialize("appointmentEndDate", {
                    inline: false,
                    dateFormat: "Y-m-d"
                });
            }
        });

        document.querySelector('.appointment-calendar-toggle').addEventListener('click', function () {
            const box = document.getElementById('appointmentCalendarBox');
            box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
        });
    </script>