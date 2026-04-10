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
                            <span>Discussion Category<span class="CdsTYDashboard-special-filters-badge" id="serviceCount" style="display: none;">0</span></span>
                            <span class="CdsTYDashboard-special-filters-arrow">▼</span>
                        </button>
                        <button class="CdsTYDashboard-special-filters-dropdown-btn" id="mostTrendingCommentsBtn" onclick="cdsfilterToggleMostTrendingComments()">
                            <span>Most Trending Comments</span>
                        </button>
                        <div class="cdsTYDashboardDropdownsDropdown">
                            <button class="cdsTYDashboardDropdownsDropdownBtn" data-original-text="Discussion Type">Type</button>
                            <div class="cdsTYDashboardDropdownsDropdownMenu">
                                <div class="cdsTYDashboardDropdownsDropdownItem" data-value="all">
                                    <span class="cdsTYDashboardDropdownsDropdownItemIcon">👤</span>
                                    All
                                </div>
                                <div class="cdsTYDashboardDropdownsDropdownItem" data-value="private">
                                    <span class="cdsTYDashboardDropdownsDropdownItemIcon">👤</span>
                                    Private
                                </div>
                                <div class="cdsTYDashboardDropdownsDropdownItem" data-value="public">
                                    <span class="cdsTYDashboardDropdownsDropdownItemIcon">👤</span>
                                    Public
                                </div>
                            </div>
                        </div>
                        <div class="cdsTYDashboardDropdownsDropdown">
                            <button class="cdsTYDashboardDropdownsDropdownBtn calendar-toggle" data-original-text="Discussion Type">📅</button>
                        </div>
                    </div>
                    <!-- Service Options Panel -->
                    <div class="CdsTYDashboard-special-filters-panel-wrapper" id="serviceOptionsPanel">
                        <div class="CdsTYDashboard-special-filters-panel">
                            <div class="CdsTYDashboard-special-filters-panel-header">
                                <h3 class="CdsTYDashboard-special-filters-panel-title">Discussion Category</h3>
                                <button class="CdsTYDashboard-special-filters-close-panel" onclick="cdsfilterClosePanel('serviceOptions')">✕</button>
                            </div>
                            <div class="CdsTYDashboard-special-filters-panel-body">
                                <div class="CdsTYDashboard-special-filters-section">
                                    <div class="CdsTYDashboard-special-filters-section-title">Discussion Category</div>
                                    @if(!empty($categories))
                                        @foreach($categories as $value)
                                            <div class="CdsTYDashboard-special-filters-checkbox-item">
                                                <input type="checkbox" id="status-{{$value->name}}" value="{{$value->id}}" class="CdsTYDashboard-status-filter" data-category="service">
                                                <label for="status-{{$value->name}}" class="CdsTYDashboard-special-filters-checkbox-label">
                                                    {{ ucwords(str_replace('_', ' ', $value->name)) }}
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
                </div>
                <!-- calender -->
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
                <!-- end calender -->
                <!-- Show Active Filters button (only visible when card closed & filters exist) -->
                <button id="cdsfilterShowFiltersBtn" class="CdsTYDashboard-chip-btn" onclick="cdsfilterOpenFiltersCard()">
                    Show active filters
                </button>
                <!-- Active Filters Card (hidden until filters exist) -->
                <div id="activeFiltersCard" class="CdsTYDashboard-special-filters-content-area is-hidden" style="display: none;">
                    <div class="CdsTYDashboard-active-filters-header">
                        <h3 style="margin:0;">Active Filters</h3>
                        <div class="CdsTYDashboard-active-filters-actions">
                            <button class="CdsTYDashboard-chip-btn" onclick="cdsfilterClearAllFilters()">Clear All</button>
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
        let cdsfilterActiveFilters = { service: [], seller: [], budget: [], mostLike: [], mostTrendingComments: [], feedPostBy: [] };
        let cdsfilterFiltersCardState = 'closed';

        function cdsfilterOpenFiltersCard() {
            const card = document.getElementById('activeFiltersCard');
            const btn = document.getElementById('cdsfilterShowFiltersBtn');
            card.classList.remove('is-hidden', 'is-minimized');
            card.style.display = 'block';
            cdsfilterFiltersCardState = 'open';
            btn.style.display = 'none';
        }

        function cdsfilterCloseFiltersCard() {
            const card = document.getElementById('activeFiltersCard');
            const btn = document.getElementById('cdsfilterShowFiltersBtn');
            card.classList.add('is-hidden');
            card.style.display = 'none';
            cdsfilterFiltersCardState = 'closed';
            const hasFilters = [...cdsfilterActiveFilters.service, ...cdsfilterActiveFilters.seller, ...cdsfilterActiveFilters.budget, ...cdsfilterActiveFilters.mostLike, ...cdsfilterActiveFilters.mostTrendingComments, ...cdsfilterActiveFilters.feedPostBy].length > 0;
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

        function cdsfilterToggleMostLike() {
            const btn = document.getElementById('mostLikeOptionsBtn');
            const isActive = btn.classList.contains('active');
            
            console.log('Most Like button clicked. Current state:', isActive);
            
            if (isActive) {
                // Deactivate Most Like
                btn.classList.remove('active');
                cdsfilterActiveFilters.mostLike = [];
                console.log('Most Like filter deactivated');
            } else {
                // Activate Most Like
                btn.classList.add('active');
                cdsfilterActiveFilters.mostLike = ['Most Like: Active'];
                console.log('Most Like filter activated');
                
                // Automatically open the active filters card when adding a filter
                if (cdsfilterFiltersCardState === 'closed') {
                    cdsfilterOpenFiltersCard();
                }
            }
            
            console.log('Updated mostLike filters:', cdsfilterActiveFilters.mostLike);
            
            // Update the active filters display
            cdsfilterUpdateSelectedFiltersDisplay();
            
            // Reset pagination when filters are applied
            if (typeof resetPaginationForFilters === 'function') {
                resetPaginationForFilters();
            }
            
            // Call loadData function when Most Like is toggled
            if (typeof loadData === 'function') {
                console.log('Calling loadData function');
                loadData();
            } else {
                console.log('loadData function not found');
            }
        }

        function cdsfilterToggleMostTrendingComments() {
            const btn = document.getElementById('mostTrendingCommentsBtn');
            const isActive = btn.classList.contains('active');
            
            console.log('Most Trending Comments button clicked. Current state:', isActive);
            
            if (isActive) {
                // Deactivate Most Trending Comments
                btn.classList.remove('active');
                cdsfilterActiveFilters.mostTrendingComments = [];
                console.log('Most Trending Comments filter deactivated');
            } else {
                // Activate Most Trending Comments
                btn.classList.add('active');
                cdsfilterActiveFilters.mostTrendingComments = ['Most Trending Comments: Active'];
                console.log('Most Trending Comments filter activated');
                
                // Automatically open the active filters card when adding a filter
                if (cdsfilterFiltersCardState === 'closed') {
                    cdsfilterOpenFiltersCard();
                }
            }
            
            console.log('Updated mostTrendingComments filters:', cdsfilterActiveFilters.mostTrendingComments);
            
            // Update the active filters display
            cdsfilterUpdateSelectedFiltersDisplay();
            
            // Reset pagination when filters are applied
            if (typeof resetPaginationForFilters === 'function') {
                resetPaginationForFilters();
            }
            
            // Call loadData function when Most Trending Comments is toggled
            if (typeof loadData === 'function') {
                console.log('Calling loadData function');
                loadData();
            } else {
                console.log('loadData function not found');
            }
        }

        function cdsfilterUpdateFilterCount(category) {
            let count = 0;
            let badgeId;
            
            if (category === 'service') {
                badgeId = 'serviceCount';
                // Count discussion category checkboxes
                const discussionCategoryCheckboxes = document.querySelectorAll('input[data-category="service"]:checked');
                count = discussionCategoryCheckboxes.length;
            }
            
            if (category === 'seller') {
                badgeId = 'sellerCount';
                // Count date inputs
                const startDate = document.getElementById('startDate');
                const endDate = document.getElementById('endDate');
                if (startDate && startDate.value) count++;
                if (endDate && endDate.value) count++;
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
                // Clear discussion category checkboxes
                document.querySelectorAll('input[data-category="service"]:checked').forEach(checkbox => {
                    checkbox.checked = false;
                });
            }

            if (category === 'seller') {
                document.querySelectorAll('.CdsTYDashboard-special-filters-star-rating').forEach(el => el.classList.remove('active'));
                // cdsfilterSelectedRating = null;
                // Clear date range inputs as part of clearing details
                const start = document.getElementById('startDate');
                const end = document.getElementById('endDate');
                if (start) start.value = '';
                if (end) end.value = '';
            }

            if (category === 'mostLike') {
                // Clear Most Like button state
                const mostLikeBtn = document.getElementById('mostLikeOptionsBtn');
                if (mostLikeBtn) mostLikeBtn.classList.remove('active');
            }

            if (category === 'mostTrendingComments') {
                // Clear Most Trending Comments button state
                const mostTrendingCommentsBtn = document.getElementById('mostTrendingCommentsBtn');
                if (mostTrendingCommentsBtn) mostTrendingCommentsBtn.classList.remove('active');
            }

            if (category === 'feedPostBy') {
                // Clear Feed Post By dropdown state
                const dropdownElement = document.querySelector('.cdsTYDashboardDropdownsDropdown');
                if (dropdownElement) {
                    const button = dropdownElement.querySelector('.cdsTYDashboardDropdownsDropdownBtn');
                    const originalText = button.getAttribute('data-original-text') || 'Discussion Type';
                    button.textContent = originalText;
                    
                    // Remove active class from all items
                    dropdownElement.querySelectorAll('.cdsTYDashboardDropdownsDropdownItem').forEach(menuItem => {
                        menuItem.classList.remove('cdsTYDashboardDropdownsActive');
                    });
                }
            }

            // Clear active filters for this category
            cdsfilterActiveFilters[category] = [];

            cdsfilterUpdateFilterCount(category);
            cdsfilterUpdateSelectedFiltersDisplay();

            // Reset pagination when filters are cleared
            if (typeof resetPaginationForFilters === 'function') {
                resetPaginationForFilters();
            }

            // Trigger data reload after clearing filters
            if (typeof loadData === 'function') {
                loadData();
            }
        }

        function cdsfilterClearAllFilters() {
            // Clear all categories
            ['service', 'seller', 'budget', 'mostLike', 'mostTrendingComments', 'feedPostBy'].forEach(category => {
                cdsfilterClearCategory(category);
            });
            
            // Reset pagination when all filters are cleared
            if (typeof resetPaginationForFilters === 'function') {
                resetPaginationForFilters();
            }
            
            // Trigger data reload after clearing all filters
            if (typeof loadData === 'function') {
                loadData();
            }
        }

        function testActiveFilters() {
            console.log('Testing active filters...');
            console.log('Current filters:', cdsfilterActiveFilters);
            console.log('Card state:', cdsfilterFiltersCardState);
            
            // Force add a test filter
            cdsfilterActiveFilters.mostLike = ['Most Like: Test'];
            
            // Force open the card
            cdsfilterFiltersCardState = 'open';
            
            // Update display
            cdsfilterUpdateSelectedFiltersDisplay();
            
            console.log('After test - filters:', cdsfilterActiveFilters);
            console.log('After test - card state:', cdsfilterFiltersCardState);
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

            // if (category === 'seller' && cdsfilterSelectedRating) {
            //     cdsfilterActiveFilters[category].push(`Rating: ${cdsfilterSelectedRating}+`);
            // }
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

            // Reset pagination when filters are applied
            if (typeof resetPaginationForFilters === 'function') {
                resetPaginationForFilters();
            }

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
                ...cdsfilterActiveFilters.budget,
                ...cdsfilterActiveFilters.mostLike,
                ...cdsfilterActiveFilters.mostTrendingComments,
                ...cdsfilterActiveFilters.feedPostBy
            ];

            console.log('Updating filters display. All filters:', allFilters);
            console.log('Card state:', cdsfilterFiltersCardState);

            if (allFilters.length === 0) {
                container.innerHTML = '<p style="color: #999;">No filters selected</p>';
                card.classList.add('is-hidden');
                card.style.display = 'none';
                cdsfilterFiltersCardState = 'closed';
                showBtn.style.display = 'none';
                console.log('No filters, hiding card and showing button');
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
                card.style.display = 'none';
                showBtn.style.display = 'inline-block';
                console.log('Card closed, hiding card and showing button');
            } else {
                card.classList.remove('is-hidden');
                card.style.display = 'block';
                showBtn.style.display = 'none';
                if (cdsfilterFiltersCardState === 'min') card.classList.add('is-minimized');
                else card.classList.remove('is-minimized');
                console.log('Card open, showing card and hiding button');
            }
        }

        function cdsfilterRemoveFilter(filter) {
            ['service','seller','budget','mostLike','mostTrendingComments','feedPostBy'].forEach(category => {
                cdsfilterActiveFilters[category] = cdsfilterActiveFilters[category].filter(f => f !== filter);

                // Handle service category specifically (discussion category checkboxes)
                if (category === 'service') {
                    // Find and uncheck the matching discussion category checkbox
                    const checkboxes = document.querySelectorAll('input[data-category="service"]');
                    checkboxes.forEach(checkbox => {
                        const label = checkbox.nextElementSibling ? checkbox.nextElementSibling.textContent.trim() : checkbox.id;
                        if (label === filter) {
                            checkbox.checked = false;
                        }
                    });
                }

                // Handle seller category specifically (date inputs)
                if (category === 'seller' && filter.startsWith('Date: ')) {
                    const start = document.getElementById('startDate');
                    const end = document.getElementById('endDate');
                    if (start) start.value = '';
                    if (end) end.value = '';
                }

                // Handle mostLike category specifically
                if (category === 'mostLike' && filter.startsWith('Most Like: ')) {
                    const mostLikeBtn = document.getElementById('mostLikeOptionsBtn');
                    if (mostLikeBtn) mostLikeBtn.classList.remove('active');
                }

                // Handle mostTrendingComments category specifically
                if (category === 'mostTrendingComments' && filter.startsWith('Most Trending Comments: ')) {
                    const mostTrendingCommentsBtn = document.getElementById('mostTrendingCommentsBtn');
                    if (mostTrendingCommentsBtn) mostTrendingCommentsBtn.classList.remove('active');
                }

                // Handle feedPostBy category specifically
                if (category === 'feedPostBy' && filter.startsWith('Discussion Type: ')) {
                    // Reset the dropdown to "All"
                    const dropdownElement = document.querySelector('.cdsTYDashboardDropdownsDropdown');
                    if (dropdownElement) {
                        const button = dropdownElement.querySelector('.cdsTYDashboardDropdownsDropdownBtn');
                        const originalText = button.getAttribute('data-original-text') || 'Discussion Type';
                        button.textContent = originalText;
                        
                        // Remove active class from all items
                        dropdownElement.querySelectorAll('.cdsTYDashboardDropdownsDropdownItem').forEach(menuItem => {
                            menuItem.classList.remove('cdsTYDashboardDropdownsActive');
                        });
                    }
                }

                            cdsfilterUpdateFilterCount(category);
        });
        cdsfilterUpdateSelectedFiltersDisplay();

        // Reset pagination when filters are removed
        if (typeof resetPaginationForFilters === 'function') {
            resetPaginationForFilters();
        }

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
            console.log('DOMContentLoaded event fired');
            console.log('loadData function available:', typeof loadData === 'function');
            console.log('resetPaginationForFilters function available:', typeof resetPaginationForFilters === 'function');
            
            ['service', 'seller'].forEach(cdsfilterUpdateFilterCount);
            
            // Initialize discussion category filters if there are existing filters
            if (cdsfilterActiveFilters.service.length > 0) {
                // The filters will be automatically displayed by cdsfilterUpdateSelectedFiltersDisplay()
                cdsfilterUpdateSelectedFiltersDisplay();
            }

            // Initialize Most Like button state if there are existing filters
            if (cdsfilterActiveFilters.mostLike.length > 0) {
                const mostLikeBtn = document.getElementById('mostLikeOptionsBtn');
                if (mostLikeBtn) mostLikeBtn.classList.add('active');
            }

            // Initialize Most Trending Comments button state if there are existing filters
            if (cdsfilterActiveFilters.mostTrendingComments.length > 0) {
                const mostTrendingCommentsBtn = document.getElementById('mostTrendingCommentsBtn');
                if (mostTrendingCommentsBtn) mostTrendingCommentsBtn.classList.add('active');
            }

            // Initialize Feed Post By dropdown state if there are existing filters
            if (cdsfilterActiveFilters.feedPostBy.length > 0) {
                const dropdownElement = document.querySelector('.cdsTYDashboardDropdownsDropdown');
                if (dropdownElement) {
                    const button = dropdownElement.querySelector('.cdsTYDashboardDropdownsDropdownBtn');
                    const filterText = cdsfilterActiveFilters.feedPostBy[0];
                    if (filterText && filterText.startsWith('Discussion Type: ')) {
                        const selectedText = filterText.replace('Discussion Type: ', '');
                        button.textContent = selectedText;
                        
                        // Find and activate the corresponding menu item
                        const menuItems = dropdownElement.querySelectorAll('.cdsTYDashboardDropdownsDropdownItem');
                        menuItems.forEach(item => {
                            if (item.textContent.trim() === selectedText) {
                                item.classList.add('cdsTYDashboardDropdownsActive');
                            }
                        });
                    }
                }
            }

            const searchInput = document.getElementById('searchInput');
            console.log('Search input element found:', searchInput);
            if (searchInput) {
                let debounceTimer;
                searchInput.addEventListener('input', function() {
                    console.log('Search input event triggered, value:', this.value);
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        if (this.value.length === 0 || this.value.length >= 2) {
                            console.log('Calling loadData from search input, length:', this.value.length);
                            // Reset pagination when searching
                            if (typeof resetPaginationForFilters === 'function') {
                                console.log('Resetting pagination for search');
                                resetPaginationForFilters();
                            } else {
                                console.log('resetPaginationForFilters function not found');
                            }
                            if (typeof loadData === 'function') {
                                console.log('Calling loadData function');
                                loadData();
                            } else {
                                console.log('loadData function not found');
                            }
                        }
                    }, 200);
                });
                console.log('Search input event listener added successfully');
            } else {
                console.error('Search input element not found!');
            }
            
            // Also try jQuery-based event listener as backup
            if (typeof $ !== 'undefined') {
                console.log('jQuery is available, adding jQuery-based search listener');
                let jqueryDebounceTimer;
                $("#searchInput").on('input', function() {
                    console.log('jQuery search input event triggered, value:', $(this).val());
                    clearTimeout(jqueryDebounceTimer);
                    jqueryDebounceTimer = setTimeout(() => {
                        const value = $(this).val();
                        if (value.length === 0 || value.length >= 2) {
                            console.log('Calling loadData from jQuery search input, length:', value.length);
                            // Reset pagination when searching
                            if (typeof resetPaginationForFilters === 'function') {
                                console.log('Resetting pagination for search (jQuery)');
                                resetPaginationForFilters();
                            } else {
                                console.log('resetPaginationForFilters function not found (jQuery)');
                            }
                            if (typeof loadData === 'function') {
                                console.log('Calling loadData function (jQuery)');
                                loadData();
                            } else {
                                console.log('loadData function not found (jQuery)');
                            }
                        }
                    }, 200);
                });
                console.log('jQuery search input event listener added successfully');
            } else {
                console.log('jQuery is not available');
            }

            // Add event handlers for discussion category checkboxes
            const discussionCategoryCheckboxes = document.querySelectorAll('input[data-category="service"]');
            discussionCategoryCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    // Update service filter count when discussion category changes
                    cdsfilterUpdateFilterCount('service');
                    cdsfilterUpdateSelectedFiltersDisplay();
                });
            });

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
    </script>
     <script>
        class Dropdown {
            constructor(element) {
                this.dropdown = element;
                this.button = element.querySelector('.cdsTYDashboardDropdownsDropdownBtn');
                this.menu = element.querySelector('.cdsTYDashboardDropdownsDropdownMenu');
                this.isOpen = false;
                this.selectedItem = null;
                this.originalButtonText = this.button.textContent;
                
                this.init();
            }

            init() {
                // Toggle dropdown on button click
                this.button.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.toggle();
                });

                // Close on outside click
                document.addEventListener('click', (e) => {
                    if (!this.dropdown.contains(e.target)) {
                        this.close();
                    }
                });

                // Close on escape key
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') {
                        this.close();
                    }
                });

                // Handle menu item clicks
                this.menu.querySelectorAll('.cdsTYDashboardDropdownsDropdownItem').forEach(item => {
                    item.addEventListener('click', () => {
                        this.selectItem(item);
                    });
                });

                // Reset on double-click
                this.button.addEventListener('dblclick', (e) => {
                    e.preventDefault();
                    this.resetSelection();
                });
            }

            toggle() {
                if (this.isOpen) {
                    this.close();
                } else {
                    this.open();
                }
            }

            open() {
                this.isOpen = true;
                this.button.classList.add('cdsTYDashboardDropdownsActive');
                this.menu.classList.add('cdsTYDashboardDropdownsShow');
            }

            close() {
                this.isOpen = false;
                this.button.classList.remove('cdsTYDashboardDropdownsActive');
                this.menu.classList.remove('cdsTYDashboardDropdownsShow');
            }

            selectItem(item) {
                // Remove active class from all items
                this.menu.querySelectorAll('.cdsTYDashboardDropdownsDropdownItem').forEach(menuItem => {
                    menuItem.classList.remove('cdsTYDashboardDropdownsActive');
                });

                // Add active class to selected item
                item.classList.add('cdsTYDashboardDropdownsActive');

                // Update button text with selected item text
                const itemText = item.textContent.trim();
                this.button.textContent = itemText;

                // Store selected item
                this.selectedItem = item;

                // Update active filters
                const selectedValue = item.getAttribute('data-value');
                if (selectedValue === 'all') {
                    // Remove feedPostBy filter if "All" is selected
                    cdsfilterActiveFilters.feedPostBy = [];
                } else {
                    // Add feedPostBy filter
                    cdsfilterActiveFilters.feedPostBy = [`Discussion Type: ${itemText}`];
                    
                    // Automatically open the active filters card when adding a filter
                    if (cdsfilterFiltersCardState === 'closed') {
                        cdsfilterOpenFiltersCard();
                    }
                }

                // Update the active filters display
                cdsfilterUpdateSelectedFiltersDisplay();

                // Reset pagination when filters are applied
                if (typeof resetPaginationForFilters === 'function') {
                    resetPaginationForFilters();
                }

                // Close dropdown
                this.close();

                // Call loadData function when selection changes
                if (typeof loadData === 'function') {
                    console.log('Calling loadData function from Discussion Type dropdown');
                    loadData();
                } else {
                    console.log('loadData function not found');
                }
            }

            resetSelection() {
                // Reset button text to original
                this.button.textContent = this.originalButtonText;
                
                // Remove active class from all items
                this.menu.querySelectorAll('.cdsTYDashboardDropdownsDropdownItem').forEach(menuItem => {
                    menuItem.classList.remove('cdsTYDashboardDropdownsActive');
                });
                
                // Clear feedPostBy active filter
                cdsfilterActiveFilters.feedPostBy = [];
                
                // Update the active filters display
                cdsfilterUpdateSelectedFiltersDisplay();
                
                // Reset pagination when filters are reset
                if (typeof resetPaginationForFilters === 'function') {
                    resetPaginationForFilters();
                }
                
                this.selectedItem = null;

                // Call loadData function when selection is reset
                if (typeof loadData === 'function') {
                    console.log('Calling loadData function from Discussion Type dropdown reset');
                    loadData();
                } else {
                    console.log('loadData function not found');
                }
            }
        }

        // Initialize dropdown when page loads
        document.addEventListener('DOMContentLoaded', () => {
            const dropdownElement = document.querySelector('.cdsTYDashboardDropdownsDropdown');
            new Dropdown(dropdownElement);
        });

        document.querySelector('.calendar-toggle').addEventListener('click', function () {
            const box = document.getElementById('calendarBox');
            box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
        });
    </script>