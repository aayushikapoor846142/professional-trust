<div class="CdsTYDashboard-special-filters-page-container">
    <div class="CdsTYDashboard-special-filters-header-section">
        <h1>Search Results</h1>
        <div class="CdsFeedsSearch row">
            <div class="CdsTYDashboard-special-filters-search-input-container cdsFlexfull">
                <input type="text" class="CdsTYDashboard-special-filters-search-input" placeholder="Search for Name..." id="searchInput">
                <span class="CdsTYDashboard-special-filters-search-icon">🔍</span>
            </div>
            <div class="CdsTYDashboard-special-filters-dropdown-container">
                <div class="CdsTYDashboard-special-filters-buttons-row">
                    <button class="CdsTYDashboard-special-filters-dropdown-btn" id="serviceOptionsBtn" onclick="cdsfilterTogglePanel('serviceOptions')">
                        <span>Form Type<span class="CdsTYDashboard-special-filters-badge" id="serviceCount" style="display: none;">0</span></span>
                        <span class="CdsTYDashboard-special-filters-arrow">▼</span>
                    </button>
                </div>
                <!-- Service Options Panel -->
                <div class="CdsTYDashboard-special-filters-panel-wrapper" id="serviceOptionsPanel">
                    <div class="CdsTYDashboard-special-filters-panel">
                        <div class="CdsTYDashboard-special-filters-panel-header">
                            <h3 class="CdsTYDashboard-special-filters-panel-title">Form Type</h3>
                            <button class="CdsTYDashboard-special-filters-close-panel" onclick="cdsfilterClosePanel('serviceOptions')">✕</button>
                        </div>
                        <div class="CdsTYDashboard-special-filters-panel-body">
                            <div class="CdsTYDashboard-special-filters-section">
                                <div class="CdsTYDashboard-special-filters-section-title">Form Type</div>
                                    {!! FormHelper::formSelect([
                                    'name' => 'form_type',
                                    'id' => 'form_type',
                                    'label' => 'Form Type',
                                    'options' => [
                                        ['value' => 'single_form', 'label' => 'Single Form'],
                                        ['value' => 'step_form', 'label' => 'Step Form'],
                                    ],
                                    'value_column' => 'value',
                                    'label_column' => 'label',
                                ]) !!}   
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

        function cdsfilterCloseAllPanels() { 
            ['serviceOptions'].forEach(cdsfilterClosePanel); 
        }

        function cdsfilterUpdateFilterCount(category) {
            let count = 0;
            let badgeId;
            
            if (category === 'service') {
                badgeId = 'serviceCount';
                // Count form type dropdown selection
                const formTypeSelect = document.getElementById('form_type');
                if (formTypeSelect && formTypeSelect.value) count++;
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

        function cdsfilterClearCategory(category) {
            if (category === 'service') {
                // Clear form type dropdown
                const formTypeSelect = document.getElementById('form_type');
                if (formTypeSelect) formTypeSelect.value = '';
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
            let category = panelType === 'serviceOptions' ? 'service' : null;
            if (!category) return;
            
            cdsfilterActiveFilters[category] = [];
            
            // Handle form type dropdown for service category
            if (category === 'service') {
                const formTypeSelect = document.getElementById('form_type');
                if (formTypeSelect && formTypeSelect.value) {
                    const selectedOption = formTypeSelect.options[formTypeSelect.selectedIndex];
                    const formTypeText = selectedOption ? selectedOption.text : 'Form Type';
                    cdsfilterActiveFilters[category].push(`Form Type: ${formTypeText}`);
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

                // Handle service category specifically (form type dropdown)
                if (category === 'service' && filter.startsWith('Form Type: ')) {
                    const formTypeSelect = document.getElementById('form_type');
                    if (formTypeSelect) formTypeSelect.value = '';
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
            ['service'].forEach(cdsfilterUpdateFilterCount);
            
            // Initialize form type filter if it has a value
            const formTypeSelect = document.getElementById('form_type');
            if (formTypeSelect && formTypeSelect.value) {
                const selectedOption = formTypeSelect.options[formTypeSelect.selectedIndex];
                const formTypeText = selectedOption ? selectedOption.text : 'Form Type';
                cdsfilterActiveFilters.service = [`Form Type: ${formTypeText}`];
                cdsfilterUpdateSelectedFiltersDisplay();
            }

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

            // Add event handler for form type dropdown
            if (formTypeSelect) {
                formTypeSelect.addEventListener('change', function() {
                    // Update service filter count when form type changes
                    cdsfilterUpdateFilterCount('service');
                    cdsfilterUpdateSelectedFiltersDisplay();
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
        });
    </script>