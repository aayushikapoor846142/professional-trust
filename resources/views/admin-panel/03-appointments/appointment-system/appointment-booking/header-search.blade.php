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
                            <span>Services<span class="CdsTYDashboard-special-filters-badge" id="serviceCount" style="display: none;">0</span></span>
                            <span class="CdsTYDashboard-special-filters-arrow">▼</span>
                        </button>

                        {{--
                        <button class="CdsTYDashboard-special-filters-dropdown-btn" id="mostLikeOptionsBtn" onclick="cdsfilterToggleMostLike()">
                            <span>Most Like</span>
                        </button>

                        <button class="CdsTYDashboard-special-filters-dropdown-btn" id="mostTrendingCommentsBtn" onclick="cdsfilterToggleMostTrendingComments()">
                            <span>Most Trending Comments</span>
                        </button>
                        --}}

                        <div class="cdsTYDashboardDropdownsDropdown">
                            <button class="cdsTYDashboardDropdownsDropdownBtn" data-original-text="Feed Post By">Status</button>
                            <div class="cdsTYDashboardDropdownsDropdownMenu">
                                <div class="cdsTYDashboardDropdownsDropdownItem" data-value="all">
                                    All
                                </div>
                                @foreach($status as $value)
                                <div class="cdsTYDashboardDropdownsDropdownItem" data-value="{{$value}}">
                                    {{$value}}
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="cdsTYDashboardDropdownsDropdown">
                            <button class="cdsTYDashboardDropdownsDropdownBtn calendar-toggle">📅</button>
                        </div>
                    </div>
                    <div class="CdsTYDashboard-special-filters-buttons-row">
                        <!-- Service Options Panel -->
                        <div class="CdsTYDashboard-special-filters-panel-wrapper" id="serviceOptionsPanel">
                            <div class="CdsTYDashboard-special-filters-panel">
                                <div class="CdsTYDashboard-special-filters-panel-header">
                                    <h3 class="CdsTYDashboard-special-filters-panel-title">Services</h3>
                                    <button class="CdsTYDashboard-special-filters-close-panel" onclick="cdsfilterClosePanel('serviceOptions')">✕</button>
                                </div>
                                <div class="CdsTYDashboard-special-filters-panel-body">
                                    <div class="CdsTYDashboard-special-filters-section">
                                        <div class="CdsTYDashboard-special-filters-section-title">Main Service</div>
                                        {!! FormHelper::formSelect([
                                            'name' => 'parent_service_id',
                                            'id' => 'parent_service_id',
                                            'label' => 'Main Service',
                                            'value_column' => 'unique_id',
                                            'label_column' => 'name',
                                            'options' => $mainServices,
                                            'is_multiple' => false,
                                            'required' => false,
                                            'attributes' => [
                                                'onchange' => "serviceList(this.value, 'sub_service_id')"
                                            ]
                                        ]) !!}   
                                        <div class="CdsTYDashboard-special-filters-section-title">Sub Service</div>
                                        {!! FormHelper::formSelect([
                                            'name' => 'sub_service_id',
                                            'id' => 'sub_service_id',
                                            'label' => 'Sub Service',
                                            'options' => [], // Initially empty, will be populated dynamically
                                            'placeholder' => 'All SubService',
                                            'is_multiple' => false,
                                            'required' => false
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
                        <div id="activeFiltersCard" class="CdsTYDashboard-special-filters-content-area is-hidden mt-3" style="display: none;">
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
        </div>
        
    </div>

    <div class="CdsTYDashboard-special-filters-overlay" id="overlay" onclick="cdsfilterCloseAllPanels()"></div>


 <script>
        let cdsfilterActivePanel = null;
        let cdsfilterActiveFilters = { service: [], seller: [], budget: [], mostLike: [], mostTrendingComments: [], feedPostBy: [] };
        let cdsfilterTempFilters = {};
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

        function cdsfilterCloseAllPanels() { 
            ['serviceOptions', 'sellerDetails', 'budget'].forEach(cdsfilterClosePanel); 
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
                // Count service dropdowns
                const parentService = document.getElementById('parent_service_id');
                const subService = document.getElementById('sub_service_id');
                if (parentService && parentService.value) count++;
                if (subService && subService.value) count++;
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
                // Clear service dropdowns
                const parentService = document.getElementById('parent_service_id');
                const subService = document.getElementById('sub_service_id');
                if (parentService) parentService.value = '';
                if (subService) {
                    subService.innerHTML = '<option value="">All SubService</option>';
                    subService.value = '';
                }
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
                    const originalText = button.getAttribute('data-original-text') || 'Feed Post By';
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
            
            // Handle service category specifically
            if (category === 'service') {
                const parentService = document.getElementById('parent_service_id');
                const subService = document.getElementById('sub_service_id');
                
                if (parentService && parentService.value) {
                    const parentOption = parentService.options[parentService.selectedIndex];
                    const parentText = parentOption ? parentOption.text : 'Main Service';
                    cdsfilterActiveFilters[category].push(`Main: ${parentText}`);
                }
                
                if (subService && subService.value) {
                    const subOption = subService.options[subService.selectedIndex];
                    const subText = subOption ? subOption.text : 'Sub Service';
                    cdsfilterActiveFilters[category].push(`Sub: ${subText}`);
                }
            } else {
                // Handle other categories with checkboxes
                document.querySelectorAll(`input[data-category="${category}"]:checked`).forEach(checkbox => {
                    const label = checkbox.nextElementSibling ? checkbox.nextElementSibling.textContent.trim().split('(')[0].trim() : checkbox.id;
                    cdsfilterActiveFilters[category].push(label);
                });
            }

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

                // Handle service category specifically
                if (category === 'service') {
                    if (filter.startsWith('Main: ')) {
                        const parentService = document.getElementById('parent_service_id');
                        if (parentService) parentService.value = '';
                    }
                    if (filter.startsWith('Sub: ')) {
                        const subService = document.getElementById('sub_service_id');
                        if (subService) {
                            subService.innerHTML = '<option value="">All SubService</option>';
                            subService.value = '';
                        }
                    }
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
                if (category === 'feedPostBy' && filter.startsWith('Feed Post By: ')) {
                    // Reset the dropdown to "All"
                    const dropdownElement = document.querySelector('.cdsTYDashboardDropdownsDropdown');
                    if (dropdownElement) {
                        const button = dropdownElement.querySelector('.cdsTYDashboardDropdownsDropdownBtn');
                        const originalText = button.getAttribute('data-original-text') || 'Feed Post By';
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
            ['service', 'seller'].forEach(cdsfilterUpdateFilterCount);
            


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
                    if (filterText && filterText.startsWith('Feed Post By: ')) {
                        const selectedText = filterText.replace('Feed Post By: ', '');
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

            // Add event handlers for service dropdowns
            const parentService = document.getElementById('parent_service_id');
            const subService = document.getElementById('sub_service_id');
            
            if (parentService) {
                parentService.addEventListener('change', function() {
                    const service_id = this.value;
                    if (service_id) {
                        serviceList(service_id, 'sub_service_id');
                    } else {
                        // Clear sub-service when main service is cleared
                        if (subService) {
                            subService.innerHTML = '<option value="">All SubService</option>';
                            subService.value = '';
                        }
                    }
                });
            }

            if (subService) {
                subService.addEventListener('change', function() {
                    // Update filter count and display when sub service changes
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

        // Function to populate sub-services based on selected main service
        function serviceList(serviceId, subServiceElementId) {
            if (!serviceId) {
                const subServiceElement = document.getElementById(subServiceElementId);
                if (subServiceElement) {
                    subServiceElement.innerHTML = '<option value="">All SubService</option>';
                }
                return;
            }

            // Make AJAX call to get sub-services
            fetch(`/api/services/${serviceId}/sub-services`)
                .then(response => response.json())
                .then(data => {
                    const subServiceElement = document.getElementById(subServiceElementId);
                    if (subServiceElement) {
                        subServiceElement.innerHTML = '<option value="">All SubService</option>';
                        if (data.subServices && data.subServices.length > 0) {
                            data.subServices.forEach(subService => {
                                const option = document.createElement('option');
                                option.value = subService.unique_id;
                                option.textContent = subService.name;
                                subServiceElement.appendChild(option);
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching sub-services:', error);
                    const subServiceElement = document.getElementById(subServiceElementId);
                    if (subServiceElement) {
                        subServiceElement.innerHTML = '<option value="">All SubService</option>';
                    }
                });
        }

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
                    cdsfilterActiveFilters.feedPostBy = [`Feed Post By: ${itemText}`];
                    
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
                    console.log('Calling loadData function from Feed Post By dropdown');
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
                    console.log('Calling loadData function from Feed Post By dropdown reset');
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