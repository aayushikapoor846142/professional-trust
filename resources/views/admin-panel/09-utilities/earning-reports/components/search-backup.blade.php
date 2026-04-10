<div class="CdsTYDashboard-special-filters-page-container CdsTYDashboard-appointment-special-filters-page-container">
        <div class="CdsTYDashboard-special-filters-header-section CdsTYDashboard-appointment-special-filters-header-section">
            <h1>Search Results</h1>
            <p class="CdsTYDashboard-special-filters-results-count CdsTYDashboard-appointment-special-filters-results-count" id="appointmentResultsCountDisplay"></p>
            <div class="CdsTYDashboard-special-filters-search-input-container CdsTYDashboard-appointment-special-filters-search-input-container">
                <input type="text" class="CdsTYDashboard-special-filters-search-input CdsTYDashboard-appointment-special-filters-search-input" placeholder="Search for points..." id="appointmentSearchInput">
                <span class="CdsTYDashboard-special-filters-search-icon CdsTYDashboard-appointment-special-filters-search-icon">🔍</span>
            </div>
            <div class="CdsTicket-special-filters-date-inputs CdsTicket-appointment-special-filters-date-inputs">
                    {!! FormHelper::formDatepicker([
                    'label' => 'Start Date',
                    'name' => 'start_date',
                    'id' => 'appointmentStartDate',
                    'class' => 'select2-input ga-country',
                ]) !!}
                <!-- <input type="date" class="CdsTicket-special-filters-date-input CdsTicket-appointment-special-filters-date-input" id="appointmentStartDate" data-category="details"> -->
                <span>to</span>
                    {!! FormHelper::formDatepicker([
                    'label' => 'End Date',
                    'name' => 'end_date',
                    'id' => 'appointmentEndDate',
                    'class' => 'select2-input ga-country',
                ]) !!}
                <!-- <input type="date" class="CdsTicket-special-filters-date-input CdsTicket-appointment-special-filters-date-input" id="appointmentEndDate" data-category="details"> -->
            </div>
            <a href="#" class="CdsTYDashboard-special-filters-clear-all CdsTYDashboard-appointment-special-filters-clear-all" onclick="appointmentCdsfilterClearCategory('seller'); return false;">Clear all</a>
            <button class="CdsTYDashboard-special-filters-apply-btn CdsTYDashboard-appointment-special-filters-apply-btn" onclick="appointmentCdsfilterApplyFilters('sellerDetails')">Apply</button>
        </div>

        <div class="CdsTYDashboard-special-filters-dropdown-container CdsTYDashboard-appointment-special-filters-dropdown-container">
            <div class="CdsTYDashboard-special-filters-buttons-row CdsTYDashboard-appointment-special-filters-buttons-row">
                <button class="CdsTYDashboard-special-filters-dropdown-btn CdsTYDashboard-appointment-special-filters-dropdown-btn" id="appointmentBudgetBtn" onclick="appointmentCdsfilterTogglePanel('budget')">
                    <span>Amount <span class="CdsTYDashboard-special-filters-badge CdsTYDashboard-appointment-special-filters-badge" id="appointmentBudgetCount" style="display: none;">0</span></span>
                    <span class="CdsTYDashboard-special-filters-arrow CdsTYDashboard-appointment-special-filters-arrow">▼</span>
                </button>
                <button class="CdsTYDashboard-special-filters-dropdown-btn CdsTYDashboard-appointment-special-filters-dropdown-btn" id="appointmentSellerDetailsBtn" onclick="appointmentCdsfilterTogglePanel('sellerDetails')">
                    <span>Details <span class="CdsTYDashboard-special-filters-badge CdsTYDashboard-appointment-special-filters-badge" id="appointmentSellerCount" style="display: none;">0</span></span>
                    <span class="CdsTYDashboard-special-filters-arrow CdsTYDashboard-appointment-special-filters-arrow">▼</span>
                </button>
            </div>

            <!-- Budget Panel -->
            <div class="CdsTYDashboard-special-filters-panel-wrapper CdsTYDashboard-appointment-special-filters-panel-wrapper" id="budgetPanel">
                <div class="CdsTYDashboard-special-filters-panel CdsTYDashboard-appointment-special-filters-panel">
                    <div class="CdsTYDashboard-special-filters-panel-header CdsTYDashboard-appointment-special-filters-panel-header">
                        <h3 class="CdsTYDashboard-special-filters-panel-title CdsTYDashboard-appointment-special-filters-panel-title">Amount</h3>
                        <button class="CdsTYDashboard-special-filters-close-panel CdsTYDashboard-appointment-special-filters-close-panel" onclick="appointmentCdsfilterClosePanel('budget')">✕</button>
                    </div>
                    <div class="CdsTYDashboard-special-filters-panel-body CdsTYDashboard-appointment-special-filters-panel-body">
                        <div class="CdsTYDashboard-special-filters-section CdsTYDashboard-appointment-special-filters-section">
                            <div class="CdsTYDashboard-special-filters-section-title CdsTYDashboard-appointment-special-filters-section-title">Amount</div>
                            <div class="CdsTYDashboard-special-filters-price-inputs CdsTYDashboard-appointment-special-filters-price-inputs">
                                <input type="number" class="CdsTYDashboard-special-filters-price-input CdsTYDashboard-appointment-special-filters-price-input" id="appointmentMinPrice" placeholder="Min" onchange="appointmentCdsfilterUpdatePriceRange()">
                                <span>-</span>
                                <input type="number" class="CdsTYDashboard-special-filters-price-input CdsTYDashboard-appointment-special-filters-price-input" id="appointmentMaxPrice" placeholder="Max" onchange="appointmentCdsfilterUpdatePriceRange()">
                            </div>
                            <div class="CdsTYDashboard-special-filters-range-slider CdsTYDashboard-appointment-special-filters-range-slider">
                                <div class="CdsTYDashboard-special-filters-range-progress CdsTYDashboard-appointment-special-filters-range-progress" id="appointmentRangeProgress"></div>
                            </div>
                            <div class="CdsTYDashboard-special-filters-range-input CdsTYDashboard-appointment-special-filters-range-input">
                                <input type="range" id="appointmentMinRange" min="0" max="1000"  step="10" oninput="appointmentCdsfilterUpdatePriceInputs()">
                                <input type="range" id="appointmentMaxRange" min="0" max="1000"  step="10" oninput="appointmentCdsfilterUpdatePriceInputs()">
                            </div>
                        </div>
                        
                        <div class="CdsTYDashboard-special-filters-section CdsTYDashboard-appointment-special-filters-section">
                            <!-- <div class="CdsTYDashboard-special-filters-section-title CdsTYDashboard-appointment-special-filters-section-title">Package Type</div> -->
                            <div class="CdsTYDashboard-special-filters-checkbox-item CdsTYDashboard-appointment-special-filters-checkbox-item">
                                <input type="checkbox" id="appointmentBasic" data-category="budget" class="CdsTYDashboard-price-range CdsTYDashboard-appointment-price-range" value="under-100">
                                <label for="appointmentBasic" class="CdsTYDashboard-special-filters-checkbox-label CdsTYDashboard-appointment-special-filters-checkbox-label">
                                    Under $100
                                    <!-- <span class="CdsTYDashboard-special-filters-count CdsTYDashboard-appointment-special-filters-count">(523)</span> -->
                                </label>
                            </div>
                            <div class="CdsTYDashboard-special-filters-checkbox-item CdsTYDashboard-appointment-special-filters-checkbox-item">
                                <input type="checkbox" id="appointmentStandard" data-category="budget" class="CdsTYDashboard-price-range CdsTYDashboard-appointment-price-range" value="100-500">
                                <label for="appointmentStandard" class="CdsTYDashboard-special-filters-checkbox-label CdsTYDashboard-appointment-special-filters-checkbox-label">
                                    $100 - $500
                                    <!-- <span class="CdsTYDashboard-special-filters-count CdsTYDashboard-appointment-special-filters-count">(412)</span> -->
                                </label>
                            </div>
                            <div class="CdsTYDashboard-special-filters-checkbox-item CdsTYDashboard-appointment-special-filters-checkbox-item">
                                <input type="checkbox" id="appointmentPremium" data-category="budget" class="CdsTYDashboard-price-range CdsTYDashboard-appointment-price-range" value="500-1000">
                                <label for="appointmentPremium" class="CdsTYDashboard-special-filters-checkbox-label CdsTYDashboard-appointment-special-filters-checkbox-label">
                                    $500 - $1000
                                    <!-- <span class="CdsTYDashboard-special-filters-count CdsTYDashboard-appointment-special-filters-count">(287)</span> -->
                                </label>
                            </div>
                            <div class="CdsTYDashboard-special-filters-checkbox-item CdsTYDashboard-appointment-special-filters-checkbox-item">
                                <input type="checkbox" id="appointmentPremiumOver" data-category="budget" class="CdsTYDashboard-price-range CdsTYDashboard-appointment-price-range" value="over-1000">
                                <label for="appointmentPremiumOver" class="CdsTYDashboard-special-filters-checkbox-label CdsTYDashboard-appointment-special-filters-checkbox-label">
                                    Over $1000
                                    <!-- <span class="CdsTYDashboard-special-filters-count CdsTYDashboard-appointment-special-filters-count">(287)</span> -->
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="CdsTYDashboard-special-filters-panel-footer CdsTYDashboard-appointment-special-filters-panel-footer">
                        <a href="#" class="CdsTYDashboard-special-filters-clear-all CdsTYDashboard-appointment-special-filters-clear-all" onclick="appointmentCdsfilterClearCategory('budget'); return false;">Clear all</a>
                        <div class="CdsTYDashboard-special-filters-action-buttons CdsTYDashboard-appointment-special-filters-action-buttons">
                            <button class="CdsTYDashboard-special-filters-cancel-btn CdsTYDashboard-appointment-special-filters-cancel-btn" onclick="appointmentCdsfilterClosePanel('budget')">Cancel</button>
                            <button class="CdsTYDashboard-appointment-special-filters-apply-btn" onclick="appointmentCdsfilterApplyFilters('budget')">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
             <!-- Seller Details Panel -->
            <div class="CdsTYDashboard-special-filters-panel-wrapper CdsTYDashboard-appointment-special-filters-panel-wrapper" id="sellerDetailsPanel">
                <div class="CdsTYDashboard-special-filters-panel CdsTYDashboard-appointment-special-filters-panel">
                    <div class="CdsTYDashboard-special-filters-panel-header CdsTYDashboard-appointment-special-filters-panel-header">
                        <h3 class="CdsTYDashboard-special-filters-panel-title CdsTYDashboard-appointment-special-filters-panel-title">Details</h3>
                        
                        <button class="CdsTYDashboard-special-filters-close-panel CdsTYDashboard-appointment-special-filters-close-panel" onclick="appointmentCdsfilterClosePanel('sellerDetails')">✕</button>
                       
                    </div>
                    <div class="CdsTYDashboard-special-filters-panel-body CdsTYDashboard-appointment-special-filters-panel-body">
                        <div class="CdsTYDashboard-special-filters-section CdsTYDashboard-appointment-special-filters-section">
                            <div class="CdsTYDashboard-special-filters-section-title CdsTYDashboard-appointment-special-filters-section-title">Date Range</div>
                            
                            
                        </div>

                        <div class="CdsTYDashboard-special-filters-section CdsTYDashboard-appointment-special-filters-section">
                            <!-- <div class="CdsTYDashboard-special-filters-section-title CdsTYDashboard-appointment-special-filters-section-title">Response Time</div> -->
                            <div class="CdsTYDashboard-special-filters-checkbox-item CdsTYDashboard-appointment-special-filters-checkbox-item">
                                <input type="checkbox" id="appointmentToday" data-category="seller" class="CdsTYDashboard-hours-filter CdsTYDashboard-appointment-hours-filter" value="today">
                                <label for="appointmentToday" class="CdsTYDashboard-special-filters-checkbox-label CdsTYDashboard-appointment-special-filters-checkbox-label">
                                    Today
                                    <!-- <span class="CdsTYDashboard-special-filters-count CdsTYDashboard-appointment-special-filters-count">(112)</span> -->
                                </label>
                            </div>
                            <div class="CdsTYDashboard-special-filters-checkbox-item CdsTYDashboard-appointment-special-filters-checkbox-item">
                                <input type="checkbox" id="appointmentThisWeek" data-category="seller" class="CdsTYDashboard-hours-filter CdsTYDashboard-appointment-hours-filter" value="this_week">
                                <label for="appointmentThisWeek" class="CdsTYDashboard-special-filters-checkbox-label CdsTYDashboard-appointment-special-filters-checkbox-label">
                                   This Week
                                    <!-- <span class="CdsTYDashboard-special-filters-count CdsTYDashboard-appointment-special-filters-count">(245)</span> -->
                                </label>
                            </div>
                            <div class="CdsTYDashboard-special-filters-checkbox-item CdsTYDashboard-appointment-special-filters-checkbox-item">
                                <input type="checkbox" id="appointmentThisMonth" data-category="seller" class="CdsTYDashboard-hours-filter CdsTYDashboard-appointment-hours-filter" value="this_month">
                                <label for="appointmentThisMonth" class="CdsTYDashboard-special-filters-checkbox-label CdsTYDashboard-appointment-special-filters-checkbox-label">
                                   This Month
                                    <!-- <span class="CdsTYDashboard-special-filters-count CdsTYDashboard-appointment-special-filters-count">(567)</span> -->
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="CdsTYDashboard-special-filters-panel-footer CdsTYDashboard-appointment-special-filters-panel-footer">
                        <a href="#" class="CdsTYDashboard-special-filters-clear-all CdsTYDashboard-appointment-special-filters-clear-all" onclick="appointmentCdsfilterClearCategory('seller'); return false;">Clear all</a>
                        <div class="CdsTYDashboard-special-filters-action-buttons CdsTYDashboard-appointment-special-filters-action-buttons">
                            <button class="CdsTYDashboard-special-filters-cancel-btn CdsTYDashboard-appointment-special-filters-cancel-btn" onclick="appointmentCdsfilterClosePanel('sellerDetails')">Cancel</button>
                            <button class="CdsTYDashboard-appointment-special-filters-apply-btn" onclick="appointmentCdsfilterApplyFilters('sellerDetails')">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Show Active Filters button (only visible when card closed & filters exist) -->
        <button id="appointmentCdsfilterShowFiltersBtn" class="CdsTYDashboard-chip-btn CdsTYDashboard-appointment-chip-btn" onclick="appointmentCdsfilterOpenFiltersCard()">
            Show active filters
        </button>

        <!-- Active Filters Card (hidden until filters exist) -->
        <div id="appointmentActiveFiltersCard" class="CdsTYDashboard-special-filters-content-area CdsTYDashboard-appointment-special-filters-content-area is-hidden">
            <div class="CdsTYDashboard-active-filters-header CdsTYDashboard-appointment-active-filters-header">
                <h3 style="margin:0;">Active Filters</h3>
                <div class="CdsTYDashboard-active-filters-actions CdsTYDashboard-appointment-active-filters-actions">
                    <button class="CdsTYDashboard-chip-btn CdsTYDashboard-appointment-chip-btn" onclick="appointmentCdsfilterToggleMinimize()">Minimize</button>
                    <button class="CdsTYDashboard-appointment-chip-btn" onclick="appointmentCdsfilterCloseFiltersCard()">Close</button>
                </div>
            </div>
            <div class="CdsTYDashboard-active-filters-body CdsTYDashboard-appointment-active-filters-body">
                <div id="appointmentSelectedFilters" class="CdsTYDashboard-special-filters-selected-filters CdsTYDashboard-appointment-special-filters-selected-filters">
                    <p style="color: #999;">No filters selected</p>
                </div>
            </div>
        </div>
    </div>

    <div class="CdsTYDashboard-special-filters-overlay CdsTYDashboard-appointment-special-filters-overlay" id="appointmentOverlay" onclick="appointmentCdsfilterCloseAllPanels()"></div>


 <script>
        let appointmentCdsfilterActivePanel = null;
        let appointmentCdsfilterActiveFilters = { service: [], seller: [], budget: [] };
        let appointmentCdsfilterSelectedRating = null;
        let appointmentCdsfilterTempFilters = {};

        // UI state for Active Filters card: 'closed' | 'open' | 'min'
        let appointmentCdsfilterFiltersCardState = 'closed';

        function appointmentCdsfilterOpenFiltersCard() {
            const card = document.getElementById('appointmentActiveFiltersCard');
            const btn = document.getElementById('appointmentCdsfilterShowFiltersBtn');
            card.classList.remove('is-hidden', 'is-minimized');
            appointmentCdsfilterFiltersCardState = 'open';
            btn.style.display = 'none';
        }

        function appointmentCdsfilterCloseFiltersCard() {
            const card = document.getElementById('appointmentActiveFiltersCard');
            const btn = document.getElementById('appointmentCdsfilterShowFiltersBtn');
            card.classList.add('is-hidden');
            appointmentCdsfilterFiltersCardState = 'closed';
            const hasFilters = [...appointmentCdsfilterActiveFilters.service, ...appointmentCdsfilterActiveFilters.seller, ...appointmentCdsfilterActiveFilters.budget].length > 0;
            btn.style.display = hasFilters ? 'inline-block' : 'none';
        }

        function appointmentCdsfilterToggleMinimize() {
            const card = document.getElementById('appointmentActiveFiltersCard');
            if (card.classList.contains('is-minimized')) {
                card.classList.remove('is-minimized');
                appointmentCdsfilterFiltersCardState = 'open';
            } else {
                card.classList.add('is-minimized');
                appointmentCdsfilterFiltersCardState = 'min';
            }
        }

        function appointmentCdsfilterTogglePanel(panelType) {
            console.log('appointmentCdsfilterTogglePanel called with:', panelType);
            let panelId, btnId;
            
            // Map panel types to actual IDs
            if (panelType === 'budget') {
                panelId = 'budgetPanel';
                btnId = 'appointmentBudgetBtn';
            } else if (panelType === 'sellerDetails') {
                panelId = 'sellerDetailsPanel';
                btnId = 'appointmentSellerDetailsBtn';
            }
            
            console.log('Looking for panel:', panelId, 'and button:', btnId);
            
            const panel = document.getElementById(panelId);
            const btn = document.getElementById(btnId);
            const overlay = document.getElementById('appointmentOverlay');
            
            console.log('Found panel:', panel);
            console.log('Found button:', btn);
            console.log('Found overlay:', overlay);
            
            appointmentCdsfilterCloseAllPanels();

            if (appointmentCdsfilterActivePanel === panelType) {
                console.log('Closing panel:', panelType);
                appointmentCdsfilterClosePanel(panelType);
            } else {
                console.log('Opening panel:', panelType);
                panel.classList.add('show');
                btn.classList.add('active');
                appointmentCdsfilterActivePanel = panelType;
                if (window.innerWidth <= 768) overlay.classList.add('show');
                appointmentCdsfilterTempFilters[panelType] = { ...appointmentCdsfilterActiveFilters[panelType] };
                console.log('Panel classes after opening:', panel.className);
            }
        }

        function appointmentCdsfilterClosePanel(panelType) {
            let panelId, btnId;
            
            // Map panel types to actual IDs
            if (panelType === 'budget') {
                panelId = 'budgetPanel';
                btnId = 'appointmentBudgetBtn';
            } else if (panelType === 'sellerDetails') {
                panelId = 'sellerDetailsPanel';
                btnId = 'appointmentSellerDetailsBtn';
            }
            
            const panel = document.getElementById(panelId);
            const btn = document.getElementById(btnId);
            const overlay = document.getElementById('appointmentOverlay');
            if (panel) panel.classList.remove('show');
            if (btn) btn.classList.remove('active');
            overlay.classList.remove('show');
            if (appointmentCdsfilterActivePanel === panelType) appointmentCdsfilterActivePanel = null;
        }

        function appointmentCdsfilterCloseAllPanels() { ['serviceOptions','sellerDetails','budget'].forEach(appointmentCdsfilterClosePanel); }

        function cdsfilterToggleMore(id, element) {
            const moreContent = document.getElementById(id);
            if (!moreContent) return;
            const isHidden = moreContent.style.display === 'none';
            moreContent.style.display = isHidden ? 'block' : 'none';
            element.textContent = isHidden ? 'Show less' : '+2 more';
        }

        function appointmentCdsfilterUpdateFilterCount(category) {
            const count = document.querySelectorAll(`input[data-category="${category}"]:checked`).length;
            let badgeId;
            if (category === 'service') badgeId = 'appointmentServiceCount';
            if (category === 'seller')  badgeId = 'appointmentSellerCount';
            if (category === 'budget')  badgeId = 'appointmentBudgetCount';
            const badge = document.getElementById(badgeId);
            if (!badge) return;
            if (count > 0) { badge.style.display = 'inline-block'; badge.textContent = count; }
            else { badge.style.display = 'none'; }
        }

        function appointmentCdsfilterSelectRating(element, rating) {
            document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
            if (appointmentCdsfilterSelectedRating === rating) appointmentCdsfilterSelectedRating = null;
            else { element.classList.add('active'); appointmentCdsfilterSelectedRating = rating; }
        }

        function appointmentCdsfilterUpdatePriceRange() {
            const minPrice = document.getElementById('appointmentMinPrice').value;
            const maxPrice = document.getElementById('appointmentMaxPrice').value;
            document.getElementById('appointmentMinRange').value = minPrice;
            document.getElementById('appointmentMaxRange').value = maxPrice;
            appointmentCdsfilterUpdateRangeProgress();
        }

        function appointmentCdsfilterUpdatePriceInputs() {
            const minRange = document.getElementById('appointmentMinRange');
            const maxRange = document.getElementById('appointmentMaxRange');
            let minVal = parseInt(minRange.value), maxVal = parseInt(maxRange.value);
            if (minVal > maxVal) [minVal, maxVal] = [maxVal, minVal];
            document.getElementById('appointmentMinPrice').value = minVal;
            document.getElementById('appointmentMaxPrice').value = maxVal;
            appointmentCdsfilterUpdateRangeProgress();
        }

        function appointmentCdsfilterUpdateRangeProgress() {
            const minRange = document.getElementById('appointmentMinRange');
            const maxRange = document.getElementById('appointmentMaxRange');
            const progress = document.getElementById('appointmentRangeProgress');
            if (!minRange || !maxRange || !progress) return;
            const minPercent = (minRange.value / minRange.max) * 100;
            const maxPercent = (maxRange.value / maxRange.max) * 100;
            progress.style.left  = minPercent + '%';
            progress.style.right = (100 - maxPercent) + '%';
        }

        function appointmentCdsfilterClearCategory(category) {
            document.querySelectorAll(`input[data-category="${category}"]`).forEach(cb => cb.checked = false);
            if (category === 'seller') {
                document.querySelectorAll('.CdsTYDashboard-appointment-special-filters-star-rating').forEach(el => el.classList.remove('active'));
                appointmentCdsfilterSelectedRating = null;
                // Clear date range inputs as part of clearing details
                const start = document.getElementById('appointmentStartDate');
                const end = document.getElementById('appointmentEndDate');
                if (start) start.value = '';
                if (end) end.value = '';
            }
            if (category === 'budget') {
                const minPriceInput = document.getElementById('appointmentMinPrice');
                const maxPriceInput = document.getElementById('appointmentMaxPrice');
                const minRange = document.getElementById('appointmentMinRange');
                const maxRange = document.getElementById('appointmentMaxRange');
                if (minPriceInput) minPriceInput.value = '';
                if (maxPriceInput) maxPriceInput.value = '';
                if (minRange) minRange.value = minRange.min;
                if (maxRange) maxRange.value = maxRange.max;
                appointmentCdsfilterUpdateRangeProgress();
            }

            // Clear active filters for this category
            appointmentCdsfilterActiveFilters[category] = [];

            appointmentCdsfilterUpdateFilterCount(category);
            appointmentCdsfilterUpdateSelectedFiltersDisplay(); // keeps card/button state correct

            // Trigger data reload after clearing filters
            if (typeof appointmentLoadData === 'function') {
                appointmentLoadData();
            }
        }

        function appointmentCdsfilterApplyFilters(panelType) {
            let category = panelType === 'serviceOptions' ? 'service'
                        : panelType === 'sellerDetails' ? 'seller'
                        : panelType === 'budget' ? 'budget' : null;
            if (!category) return;

            appointmentCdsfilterActiveFilters[category] = [];
            document.querySelectorAll(`input[data-category="${category}"]:checked`).forEach(checkbox => {
                const label = checkbox.nextElementSibling ? checkbox.nextElementSibling.textContent.trim().split('(')[0].trim() : checkbox.id;
                appointmentCdsfilterActiveFilters[category].push(label);
            });

            if (category === 'seller' && appointmentCdsfilterSelectedRating) {
                appointmentCdsfilterActiveFilters[category].push(`Rating: ${appointmentCdsfilterSelectedRating}+`);
            }
            if (category === 'seller') {
                const startDateVal = (document.getElementById('appointmentStartDate') || {}).value || '';
                const endDateVal = (document.getElementById('appointmentEndDate') || {}).value || '';
                if (startDateVal || endDateVal) {
                    const from = startDateVal ? startDateVal : 'Any';
                    const to = endDateVal ? endDateVal : 'Any';
                    appointmentCdsfilterActiveFilters[category].push(`Date: ${from} to ${to}`);
                }
            }
            if (category === 'budget') {
                const minPriceRaw = (document.getElementById('appointmentMinPrice') || {}).value || '';
                const maxPriceRaw = (document.getElementById('appointmentMaxPrice') || {}).value || '';
                const minPrice = String(minPriceRaw).trim();
                const maxPrice = String(maxPriceRaw).trim();
                if (minPrice !== '' || maxPrice !== '') {
                    const from = minPrice !== '' ? `$${minPrice}` : 'Any';
                    const to = maxPrice !== '' ? `$${maxPrice}` : 'Any';
                    appointmentCdsfilterActiveFilters[category].push(`${from} - ${to}`);
                }
            }

            appointmentCdsfilterUpdateFilterCount(category);

            // If card was closed and we just added filters, open it
            const hadNone = document.getElementById('appointmentActiveFiltersCard').classList.contains('is-hidden') &&
                            [...appointmentCdsfilterActiveFilters.service, ...appointmentCdsfilterActiveFilters.seller, ...appointmentCdsfilterActiveFilters.budget].length > 0 &&
                            appointmentCdsfilterFiltersCardState === 'closed';
            appointmentCdsfilterUpdateSelectedFiltersDisplay();
            if (hadNone) appointmentCdsfilterOpenFiltersCard();

            appointmentCdsfilterClosePanel(panelType);

            // Trigger data reload if available
            if (typeof appointmentLoadData === 'function') {
                appointmentLoadData();
            }
        }

        function appointmentCdsfilterUpdateSelectedFiltersDisplay() {
            const container = document.getElementById('appointmentSelectedFilters');
            const card = document.getElementById('appointmentActiveFiltersCard');
            const showBtn = document.getElementById('appointmentCdsfilterShowFiltersBtn');

            const allFilters = [
                ...appointmentCdsfilterActiveFilters.service,
                ...appointmentCdsfilterActiveFilters.seller,
                ...appointmentCdsfilterActiveFilters.budget
            ];

            if (allFilters.length === 0) {
                container.innerHTML = '<p style="color: #999;">No filters selected</p>';
                card.classList.add('is-hidden');
                appointmentCdsfilterFiltersCardState = 'closed';
                showBtn.style.display = 'none';
                return;
            }

            container.innerHTML = allFilters.map(filter => 
                `<div class="CdsTYDashboard-appointment-special-filters-tag">
                    ${filter}
                    <span class="CdsTYDashboard-appointment-special-filters-remove" onclick="appointmentCdsfilterRemoveFilter('${filter.replace(/'/g, "\\'")}')">×</span>
                </div>`
            ).join('');

            if (appointmentCdsfilterFiltersCardState === 'closed') {
                card.classList.add('is-hidden');
                showBtn.style.display = 'inline-block';
            } else {
                card.classList.remove('is-hidden');
                showBtn.style.display = 'none';
                if (appointmentCdsfilterFiltersCardState === 'min') card.classList.add('is-minimized');
                else card.classList.remove('is-minimized');
            }
        }

        		function appointmentCdsfilterRemoveFilter(filter) {
			['service','seller','budget'].forEach(category => {
				appointmentCdsfilterActiveFilters[category] = appointmentCdsfilterActiveFilters[category].filter(f => f !== filter);

				// Uncheck matching checkboxes for this category
				document.querySelectorAll(`input[data-category="${category}"]:checked`).forEach(cb => {
					const labelText = cb.nextElementSibling ? cb.nextElementSibling.textContent.trim().split('(')[0].trim() : cb.id;
					if (labelText === filter) {
						cb.checked = false;
					}
				});

				// Handle special cases
				if (category === 'seller' && /^Rating:\s*/.test(filter)) {
					appointmentCdsfilterSelectedRating = null;
					document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
				}
				// Date range tag
				if (category === 'seller' && /^Date:\s*/.test(filter)) {
					const start = document.getElementById('appointmentStartDate');
					const end = document.getElementById('appointmentEndDate');
					if (start) start.value = '';
					if (end) end.value = '';
				}
				if (category === 'budget' && /^(\$?\d+|Any)\s*-\s*(\$?\d+|Any)/.test(filter)) {
					const minPriceInput = document.getElementById('appointmentMinPrice');
					const maxPriceInput = document.getElementById('appointmentMaxPrice');
					const minRange = document.getElementById('appointmentMinRange');
					const maxRange = document.getElementById('appointmentMaxRange');
					if (minPriceInput) minPriceInput.value = '';
					if (maxPriceInput) maxPriceInput.value = '';
					if (minRange) minRange.value = minRange.min;
					if (maxRange) maxRange.value = maxRange.max;
					appointmentCdsfilterUpdateRangeProgress();
				}

				appointmentCdsfilterUpdateFilterCount(category);
			});
			appointmentCdsfilterUpdateSelectedFiltersDisplay();

			// Trigger data reload after removing a filter
			if (typeof appointmentLoadData === 'function') {
				appointmentLoadData();
			}
		}

        // Escape key closes panels
        document.addEventListener('keydown', e => { if (e.key === 'Escape') appointmentCdsfilterCloseAllPanels(); });

        // Resize: remove overlay on desktop
        window.addEventListener('resize', () => { if (window.innerWidth > 768) document.getElementById('appointmentOverlay').classList.remove('show'); });

        // Init
        document.addEventListener('DOMContentLoaded', () => {
            appointmentCdsfilterUpdateRangeProgress();
            ['service','seller','budget'].forEach(appointmentCdsfilterUpdateFilterCount);

            const searchInput = document.getElementById('appointmentSearchInput');
            if (searchInput) {
                let debounceTimer;
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        if (this.value.length === 0 || this.value.length >= 2) {
                            appointmentLoadData();
                        }
                    }, 200);
                });
            }

            // Click outside to close panels
            document.addEventListener('click', function(e) {
                if (appointmentCdsfilterActivePanel) {
                    let panelId, btnId;
                    
                    // Map panel types to actual IDs
                    if (appointmentCdsfilterActivePanel === 'budget') {
                        panelId = 'budgetPanel';
                        btnId = 'appointmentBudgetBtn';
                    } else if (appointmentCdsfilterActivePanel === 'sellerDetails') {
                        panelId = 'sellerDetailsPanel';
                        btnId = 'appointmentSellerDetailsBtn';
                    }
                    
                    const panel = document.getElementById(panelId);
                    const btn = document.getElementById(btnId);
                    if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) appointmentCdsfilterCloseAllPanels();
                }
            });

            // Stop propagation inside panel
            document.querySelectorAll('.CdsTYDashboard-appointment-special-filters-panel-wrapper').forEach(panel => {
                panel.addEventListener('click', e => e.stopPropagation());
            });
            // Stop propagation on button click
            document.querySelectorAll('.CdsTYDashboard-appointment-special-filters-dropdown-btn').forEach(btn => {
                btn.addEventListener('click', e => e.stopPropagation());
            });

              const invoicesDatePicker = CustomCalendarWidget.initialize("appointmentStartDate", {
    inline: false,
    dateFormat: "Y-m-d",
    onDateSelect: function(selectedDateStr) {
        // Destroy and recreate due date picker with new minDate
        const dueDateInput = document.getElementById("appointmentEndDate");
        const currentDueDate = dueDateInput.value;
        
        // Destroy existing instance
        const wrapper = document.querySelector('#appointmentEndDate').nextElementSibling;
        if (wrapper && wrapper.classList.contains('CDSComponents-Calender-inline01-container')) {
            wrapper.remove();
        }
        
        // Reinitialize with new minDate
        dueDatePicker = CustomCalendarWidget.initialize("appointmentEndDate", {
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
let dueDatePicker = CustomCalendarWidget.initialize("appointmentEndDate", {
    inline: false,
    dateFormat: "Y-m-d"
});

// Set initial due date to today
// document.getElementById("appointmentEndDate").value = new Date().toISOString().split('T')[0];

        });

        // Add CSS styles for panel functionality
        const style = document.createElement('style');
        style.textContent = `
            .CdsTYDashboard-appointment-special-filters-panel-wrapper {
                position: absolute;
                top: 100%;
                left: 0;
                z-index: 1000;
                display: none;
                background: white;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                min-width: 300px;
                margin-top: 5px;
            }
            
            .CdsTYDashboard-appointment-special-filters-panel-wrapper.show {
                display: block !important;
            }
            
            .CdsTYDashboard-appointment-special-filters-dropdown-container {
                position: relative;
            }
            
            .CdsTYDashboard-appointment-special-filters-dropdown-btn.active {
                background-color: #f0f0f0;
                border-color: #007bff;
            }
            
            .CdsTYDashboard-appointment-special-filters-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            
            .CdsTYDashboard-appointment-special-filters-overlay.show {
                display: block;
            }
        `;
        document.head.appendChild(style);
    </script>