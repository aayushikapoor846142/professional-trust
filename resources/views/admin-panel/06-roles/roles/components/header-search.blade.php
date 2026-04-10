<div class="CdsTYDashboard-special-filters-page-container">
    <div class="CdsTYDashboard-special-filters-header-section">
        <h1 class="px-3">Search Results</h1>
        <p class="CdsTYDashboard-special-filters-results-count" id="resultsCountDisplay"></p>
        <div class="CdsTYDashboard-special-filters-search-input-container g-0">
            <input type="text" class="CdsTYDashboard-special-filters-search-input" placeholder="Search for Name, Added By..." id="searchInput">
            <span class="CdsTYDashboard-special-filters-search-icon">🔍</span>
        </div>
    </div>
</div>

 <script>
    // Init
    document.addEventListener('DOMContentLoaded', () => {
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
    });
</script>