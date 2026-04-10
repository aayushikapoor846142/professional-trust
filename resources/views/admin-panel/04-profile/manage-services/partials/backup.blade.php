<div class="CDSDashboardProfessionalServices-list-visa-item">
    <div class="CDSDashboardProfessionalServices-list-multi-select-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <label class="CDSDashboardProfessionalServices-list-multi-select-label">Select Service Types</label>
            <span class="CDSDashboardProfessionalServices-list-tag CDSDashboardProfessionalServices-list-tag-primary" id="selectedCount_{{ $subservice_id }}" style="background: var(--primary-soft); color: var(--primary); border-color: var(--primary);">
                0 selected
            </span>
        </div>
        <div class="CDSDashboardProfessionalServices-list-multi-select-actions">
            <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="window.selectAllServices_{{ $subservice_id }}()">Select All</button>
            <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="window.clearAllServices_{{ $subservice_id }}()">Clear All</button>
            <button class="CDSDashboardProfessionalServices-list-multi-select-action-btn" onclick="window.selectCommonServices_{{ $subservice_id }}()">Common Types</button>
        </div>
        <div class="CDSDashboardProfessionalServices-list-selected-items CDSDashboardProfessionalServices-list-empty" id="selectedItems_{{ $subservice_id }}">
            No service types selected
        </div>
        <div class="CDSDashboardProfessionalServices-list-search-wrapper">
            <svg class="CDSDashboardProfessionalServices-list-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" class="CDSDashboardProfessionalServices-list-multi-select-search" id="searchInput_{{ $subservice_id }}" placeholder="Search service types..." autocomplete="off">
            <svg class="CDSDashboardProfessionalServices-list-clear-search" id="clearSearch_{{ $subservice_id }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <div class="CDSDashboardProfessionalServices-list-dropdown-list" id="dropdownList_{{ $subservice_id }}"></div>
        </div>
    </div>
</div>

@section('javascript')
<script>

(function() {
    alert('helooo');
    const serviceTypes_{{ $subservice_id }} = [
        @foreach($types as $type)
        {
            id: {{ $type->id }},
            name: '{{ addslashes($type->name) }}',
            selected: false // or true if you have selection logic
        }@if(!$loop->last),@endif
        @endforeach
    ];

    let selectedServices_{{ $subservice_id }} = serviceTypes_{{ $subservice_id }}.filter(s => s.selected);
    let searchTimeout_{{ $subservice_id }} = null;

    function renderSelectedItems() {
        const container = document.getElementById('selectedItems_{{ $subservice_id }}');
        const countElement = document.getElementById('selectedCount_{{ $subservice_id }}');
        if (!container) return;

        if (selectedServices_{{ $subservice_id }}.length === 0) {
            container.innerHTML = 'No service types selected';
            container.classList.add('CDSDashboardProfessionalServices-list-empty');
        } else {
            container.classList.remove('CDSDashboardProfessionalServices-list-empty');
            container.innerHTML = selectedServices_{{ $subservice_id }}.map(service => `
                <span class="CDSDashboardProfessionalServices-list-selected-tag">
                    ${service.name}
                    <span class="CDSDashboardProfessionalServices-list-tag-remove" onclick="window.removeService_{{ $subservice_id }}(${service.id})">×</span>
                </span>
            `).join('');
        }

        if (countElement) {
            countElement.textContent = `${selectedServices_{{ $subservice_id }}.length} selected`;
        }
    }

    function renderDropdownItems(searchTerm = '') {
        const dropdownList = document.getElementById('dropdownList_{{ $subservice_id }}');
        if (!dropdownList) return;

        const filteredServices = serviceTypes_{{ $subservice_id }}.filter(service =>
            service.name.toLowerCase().includes(searchTerm.toLowerCase())
        );

        if (filteredServices.length === 0) {
            dropdownList.innerHTML = '<div class="CDSDashboardProfessionalServices-list-no-results">No matching service types found</div>';
        } else {
            dropdownList.innerHTML = filteredServices.map(service => {
                const isSelected = selectedServices_{{ $subservice_id }}.some(s => s.id === service.id);
                return `
                    <div class="CDSDashboardProfessionalServices-list-dropdown-item ${isSelected ? 'CDSDashboardProfessionalServices-list-selected' : ''}" onclick="window.toggleService_{{ $subservice_id }}(${service.id})">
                        <input type="checkbox" class="CDSDashboardProfessionalServices-list-dropdown-checkbox" ${isSelected ? 'checked' : ''}>
                        <span class="CDSDashboardProfessionalServices-list-option-label">${service.name}</span>
                    </div>
                `;
            }).join('');
        }
        // Always show the dropdown when rendering
        dropdownList.classList.add('CDSDashboardProfessionalServices-list-show');
    }

    window.toggleService_{{ $subservice_id }} = function(serviceId) {
        const service = serviceTypes_{{ $subservice_id }}.find(s => s.id === serviceId);
        const index = selectedServices_{{ $subservice_id }}.findIndex(s => s.id === serviceId);

        if (index > -1) {
            selectedServices_{{ $subservice_id }}.splice(index, 1);
        } else {
            selectedServices_{{ $subservice_id }}.push(service);
        }

        renderSelectedItems();
        renderDropdownItems(document.getElementById('searchInput_{{ $subservice_id }}')?.value || '');
    }

    window.removeService_{{ $subservice_id }} = function(serviceId) {
        selectedServices_{{ $subservice_id }} = selectedServices_{{ $subservice_id }}.filter(s => s.id !== serviceId);
        renderSelectedItems();
        renderDropdownItems(document.getElementById('searchInput_{{ $subservice_id }}')?.value || '');
    }

    window.selectAllServices_{{ $subservice_id }} = function() {
        selectedServices_{{ $subservice_id }} = [...serviceTypes_{{ $subservice_id }}];
        renderSelectedItems();
        renderDropdownItems(document.getElementById('searchInput_{{ $subservice_id }}')?.value || '');
    }

    window.clearAllServices_{{ $subservice_id }} = function() {
        selectedServices_{{ $subservice_id }} = [];
        renderSelectedItems();
        renderDropdownItems(document.getElementById('searchInput_{{ $subservice_id }}')?.value || '');
    }

    window.selectCommonServices_{{ $subservice_id }} = function() {
        selectedServices_{{ $subservice_id }} = serviceTypes_{{ $subservice_id }}.slice(0, 4);
        renderSelectedItems();
        renderDropdownItems(document.getElementById('searchInput_{{ $subservice_id }}')?.value || '');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput_{{ $subservice_id }}');
        const clearBtn = document.getElementById('clearSearch_{{ $subservice_id }}');
        const dropdownList = document.getElementById('dropdownList_{{ $subservice_id }}');
        if (searchInput && clearBtn && dropdownList) {
            searchInput.addEventListener('input', function(e) {
                clearBtn.classList.toggle('CDSDashboardProfessionalServices-list-visible', e.target.value.length > 0);
                clearTimeout(searchTimeout_{{ $subservice_id }});
                searchTimeout_{{ $subservice_id }} = setTimeout(() => {
                    renderDropdownItems(e.target.value);
                }, 300);
            });
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                clearBtn.classList.remove('CDSDashboardProfessionalServices-list-visible');
                renderDropdownItems();
            });
            // Show dropdown on focus
            searchInput.addEventListener('focus', function() {
                dropdownList.classList.add('CDSDashboardProfessionalServices-list-show');
            });
        }
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownList || !searchInput) return;
            if (!dropdownList.contains(e.target) && e.target !== searchInput) {
                dropdownList.classList.remove('CDSDashboardProfessionalServices-list-show');
            }
        });
        renderSelectedItems();
        renderDropdownItems();
    });
})();
</script>
@endsection
