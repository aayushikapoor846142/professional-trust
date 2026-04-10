@extends('admin-panel.layouts.app')
@section('page-submenu')
{!! pageSubMenu('accounts') !!}
@endsection
@section('content')
<div class="CDSDashboardContainer-container" id="CDSDashboardContainer-dashboardContainer">
    <div class="CDSDashboardContainer-main-content">
        <div class="CDSDashboardContainer-main-content-inner">  
			 <div class="CDSDashboardContainer-main-content-inner-header">
 <div class="CdsModule-header">
        <h1>Manage Your Modules</h1>
        <p>Add and manage your system modules. Each module can have multiple actions assigned to it.</p>
    </div>

    <div class="CdsModule-controls">
        <a href="javascript:;" onclick="cdsModuleOpenModal()" class="CdsModule-add-btn">
            <span class="CdsModule-icon CdsModule-icon-plus"></span>
            Add New Module
        </a>
        <div class="CdsModule-search-wrapper">
            <span class="CdsModule-search-icon CdsModule-icon CdsModule-icon-search"></span>
            <input type="text" class="CdsModule-search" id="search-input" 
                   placeholder="Search by Module Name or Added By"
                   onkeyup="cdsModuleSearch()">
        </div>
        <div class="CdsModule-counter">
            Showing <span id="cdsModuleCount">0</span> modules
        </div>
        <button onclick="showTestCard()" class="CdsModule-add-btn" style="background: var(--CdsModule-warning);">
            <span class="CdsModule-icon CdsModule-icon-plus"></span>
            Test Design
        </button>
                        </div>

			 </div>
			 <div class="CDSDashboardContainer-main-content-inner-body">
    <div class="CdsModule-grid" id="tableList">
        <!-- Module cards will be dynamically inserted here -->
        <!-- Test card to verify design is working -->
        <div class="CdsModule-card" style="display: none;" id="testCard">
            <div class="CdsModule-card-header">
                <div class="CdsModule-module-logo">TE</div>
                <div class="CdsModule-module-info">
                    <div class="CdsModule-module-name">Test Module</div>
                </div>
            </div>
            <div class="CdsModule-details">
                <div class="CdsModule-detail-row">
                    <span class="CdsModule-label">Module Name:</span>
                    <span class="CdsModule-value">Test Module</span>
                </div>
                <div class="CdsModule-detail-row">
                    <span class="CdsModule-label">Slug:</span>
                    <span class="CdsModule-value">test-module</span>
                </div>
                <div class="CdsModule-detail-row">
                    <span class="CdsModule-label">Added By:</span>
                    <span class="CdsModule-value">Test User</span>
                </div>
                <div class="CdsModule-detail-row">
                    <span class="CdsModule-label">Actions:</span>
                    <span class="CdsModule-value">3 actions</span>
            </div>
        </div>
            <div class="CdsModule-actions">
                <a href="#" class="CdsModule-action-btn CdsModule-btn-edit">
                    <span class="CdsModule-icon CdsModule-icon-edit"></span>
                    Edit
                </a>
                <a href="#" class="CdsModule-action-btn CdsModule-btn-delete">
                    <span class="CdsModule-icon CdsModule-icon-delete"></span>
                Delete
                </a>
            </div>
                                </div>
                            </div>

    <div class="CdsModule-empty" id="cdsModuleEmpty" style="display: none;">
        <div class="CdsModule-empty-icon CdsModule-icon CdsModule-icon-module"></div>
        <div class="CdsModule-empty-text">No modules found</div>
        <a href="{{ baseUrl('module/add') }}" class="CdsModule-add-btn">
            <span class="CdsModule-icon CdsModule-icon-plus"></span>
            Add Your First Module
        </a>
                        </div>
@include('components.table-pagination01') 	
                <!-- Pagination -->
   		</div>
	
	</div>
  </div>
</div>


    <!-- Modal for Add/Edit -->
    <div class="CdsModule-modal" id="cdsModuleModal">
        <div class="CdsModule-modal-content">
            <div class="CdsModule-modal-header">
                <h2 class="CdsModule-modal-title" id="cdsModuleModalTitle">Edit Module</h2>
            </div>
            <div class="CdsModule-modal-body">
                <form id="cdsModuleForm">
                    <div class="CdsModule-form-group">
                        <label class="CdsModule-form-label">Module Name</label>
                        <input type="text" class="CdsModule-form-input" id="cdsModuleName" required>
                    </div>
                    <div class="CdsModule-form-group">
                        <label class="CdsModule-form-label">Module Actions</label>
                        <div class="CdsModule-actions-list" id="cdsModuleActionsList">
                            <!-- Actions will be loaded here -->
                        </div>
                        <div class="CdsModule-add-action">
                            <button type="button" class="CdsModule-btn CdsModule-btn-secondary" onclick="addActionField()">
                                <span class="CdsModule-icon CdsModule-icon-plus"></span>
                                Add Action
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="CdsModule-modal-footer">
                <button class="CdsModule-btn CdsModule-btn-secondary" onclick="cdsModuleCloseModal()">Cancel</button>
                <button class="CdsModule-btn CdsModule-btn-primary" onclick="cdsModuleSave()">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div class="CdsModule-toast" id="cdsModuleToast"></div>

  <!-- End Content -->
    @endsection

@section('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --CdsModule-primary: #2563eb;
        --CdsModule-primary-dark: #1d4ed8;
        --CdsModule-success: #10b981;
        --CdsModule-danger: #ef4444;
        --CdsModule-warning: #f59e0b;
        --CdsModule-bg: #f8fafc;
        --CdsModule-card-bg: #ffffff;
        --CdsModule-text: #1e293b;
        --CdsModule-text-light: #64748b;
        --CdsModule-border: #e2e8f0;
        --CdsModule-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1);
        --CdsModule-shadow-lg: 0 10px 25px -5px rgb(0 0 0 / 0.1);
        --CdsModule-radius: 12px;
        --CdsModule-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .CdsModule-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .CdsModule-header {
        background: linear-gradient(135deg, var(--CdsModule-primary) 0%, var(--CdsModule-primary-dark) 100%);
        color: white;
        padding: 2rem;
        border-radius: var(--CdsModule-radius);
        margin-bottom: 2rem;
        box-shadow: var(--CdsModule-shadow-lg);
    }

    .CdsModule-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .CdsModule-header p {
        opacity: 0.9;
        font-size: 1.1rem;
    }

    .CdsModule-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
        align-items: center;
    }

    .CdsModule-add-btn {
        background: var(--CdsModule-primary);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--CdsModule-transition);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .CdsModule-add-btn:hover {
        background: var(--CdsModule-primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--CdsModule-shadow-lg);
        color: white;
        text-decoration: none;
    }

    .CdsModule-search-wrapper {
        flex: 1;
        min-width: 250px;
        position: relative;
    }

    .CdsModule-search {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 3rem;
        border: 1px solid var(--CdsModule-border);
        border-radius: 8px;
        font-size: 1rem;
        transition: var(--CdsModule-transition);
        background: var(--CdsModule-card-bg);
    }

    .CdsModule-search:focus {
        outline: none;
        border-color: var(--CdsModule-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .CdsModule-search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--CdsModule-text-light);
    }

    .CdsModule-counter {
        background: var(--CdsModule-card-bg);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        color: var(--CdsModule-text-light);
        font-weight: 500;
        box-shadow: var(--CdsModule-shadow);
    }

    .CdsModule-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .CdsModule-card {
        background: var(--CdsModule-card-bg);
        border-radius: var(--CdsModule-radius);
        padding: 1.5rem;
        box-shadow: var(--CdsModule-shadow);
        transition: var(--CdsModule-transition);
        position: relative;
        border: 2px solid transparent;
    }

    .CdsModule-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--CdsModule-shadow-lg);
    }

    .CdsModule-card-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--CdsModule-border);
    }

    .CdsModule-module-logo {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .CdsModule-module-info {
        flex: 1;
    }

    .CdsModule-module-name {
        font-weight: 700;
        font-size: 1.1rem;
        color: var(--CdsModule-text);
    }

    .CdsModule-details {
        display: grid;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .CdsModule-detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .CdsModule-label {
        color: var(--CdsModule-text-light);
        font-size: 0.875rem;
        font-weight: 500;
    }

    .CdsModule-value {
        color: var(--CdsModule-text);
        font-weight: 600;
    }

    .CdsModule-actions {
        display: flex;
        gap: 0.75rem;
        padding-top: 1rem;
        border-top: 1px solid var(--CdsModule-border);
    }

    .CdsModule-action-btn {
        flex: 1;
        padding: 0.5rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--CdsModule-transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        text-decoration: none;
    }

    .CdsModule-btn-edit {
        background: var(--CdsModule-primary);
        color: white;
    }

    .CdsModule-btn-edit:hover {
        background: var(--CdsModule-primary-dark);
        color: white;
        text-decoration: none;
    }

    .CdsModule-btn-delete {
        background: var(--CdsModule-danger);
        color: white;
    }

    .CdsModule-btn-delete:hover {
        background: #dc2626;
        color: white;
        text-decoration: none;
    }

    .CdsModule-toast {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: var(--CdsModule-text);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: var(--CdsModule-shadow-lg);
        transform: translateX(400px);
        transition: transform 0.3s ease-out;
        z-index: 2000;
    }

    .CdsModule-toast.CdsModule-show {
        transform: translateX(0);
    }

    .CdsModule-empty {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--CdsModule-card-bg);
        border-radius: var(--CdsModule-radius);
        box-shadow: var(--CdsModule-shadow);
    }

    .CdsModule-empty-icon {
        font-size: 4rem;
        color: var(--CdsModule-text-light);
        margin-bottom: 1rem;
    }

    .CdsModule-empty-text {
        color: var(--CdsModule-text-light);
        font-size: 1.1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 768px) {
        .CdsModule-header h1 {
            font-size: 1.5rem;
        }

        .CdsModule-header p {
            font-size: 1rem;
        }

        .CdsModule-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .CdsModule-search-wrapper {
            order: -1;
        }

        .CdsModule-grid {
            grid-template-columns: 1fr;
        }

        .CdsModule-toast {
            left: 1rem;
            right: 1rem;
            bottom: 1rem;
        }
    }

    @media (max-width: 480px) {
        .CdsModule-container {
            padding: 1rem 0.5rem;
        }

        .CdsModule-header {
            padding: 1.5rem 1rem;
        }

        .CdsModule-card {
            padding: 1rem;
        }

        .CdsModule-actions {
            flex-direction: column;
        }

        .CdsModule-action-btn {
            width: 100%;
        }
    }

    /* Icons */
    .CdsModule-icon::before {
        display: inline-block;
        font-style: normal;
        font-variant: normal;
        text-rendering: auto;
        -webkit-font-smoothing: antialiased;
    }

    .CdsModule-icon-plus::before { content: "➕"; }
    .CdsModule-icon-search::before { content: "🔍"; }
    .CdsModule-icon-edit::before { content: "✏️"; }
    .CdsModule-icon-delete::before { content: "🗑️"; }
    .CdsModule-icon-module::before { content: "📦"; }

    /* Modal Styles */
    .CdsModule-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .CdsModule-modal.CdsModule-active {
        display: flex;
    }

    .CdsModule-modal-content {
        background: var(--CdsModule-card-bg);
        border-radius: var(--CdsModule-radius);
        max-width: 500px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        animation: CdsModule-slideUp 0.3s ease-out;
    }

    @keyframes CdsModule-slideUp {
        from {
            transform: translateY(100px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .CdsModule-modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--CdsModule-border);
    }

    .CdsModule-modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--CdsModule-text);
    }

    .CdsModule-modal-body {
        padding: 1.5rem;
    }

    .CdsModule-form-group {
        margin-bottom: 1.25rem;
    }

    .CdsModule-form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--CdsModule-text);
        font-size: 0.875rem;
    }

    .CdsModule-form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--CdsModule-border);
        border-radius: 6px;
        font-size: 1rem;
        transition: var(--CdsModule-transition);
    }

    .CdsModule-form-input:focus {
        outline: none;
        border-color: var(--CdsModule-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .CdsModule-actions-list {
        margin-bottom: 1rem;
    }

    .CdsModule-action-item {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        align-items: center;
    }

    .CdsModule-action-item input {
        flex: 1;
    }

    .CdsModule-action-item button {
        padding: 0.5rem;
        background: var(--CdsModule-danger);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.75rem;
    }

    .CdsModule-add-action {
        margin-top: 1rem;
    }

    .CdsModule-modal-footer {
        padding: 1.5rem;
        border-top: 1px solid var(--CdsModule-border);
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .CdsModule-btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--CdsModule-transition);
    }

    .CdsModule-btn-primary {
        background: var(--CdsModule-primary);
        color: white;
    }

    .CdsModule-btn-primary:hover {
        background: var(--CdsModule-primary-dark);
    }

    .CdsModule-btn-secondary {
        background: var(--CdsModule-border);
        color: var(--CdsModule-text);
    }

    .CdsModule-btn-secondary:hover {
        background: #cbd5e1;
    }
</style>
  @endsection

  @section('javascript')
  <script type="text/javascript">
    $(document).ready(function() {
      $(".next").click(function() {
        if (!$(this).hasClass('disabled')) {
          changePage('next');
        }
      });
      $(".previous").click(function() {
        if (!$(this).hasClass('disabled')) {
          changePage('prev');
        }
      });
      $("#search-input").keyup(function() {
        var value = $(this).val();
        if (value == '') {
          loadData();
        }
        if (value.length > 3) {
          loadData();
        }
      });
    });

    loadData();

    function loadData(page = 1) {
      var search = $("#search-input").val();
      $.ajax({
        type: "POST",
        url: BASEURL + '/module/ajax-list?page=' + page,
        data: {
          _token: csrf_token,
          search: search
        },
        dataType: 'json',
        beforeSend: function() {
        $("#tableList").html("<div class='text-center py-2'><i class='fa fa-spin fa-spinner fa-3x'></i></div>");
      },
      success: function(data) {
        $("#tableList").html(data.contents);

        if (data.total_records > 0) {
          var pageinfo = data.current_page + " of " + data.last_page + " <small class='text-danger'>(" + data.total_records + " records)</small>";
          $("#pageinfo").html(pageinfo);
          $("#pageno").val(data.current_page);
                    $("#cdsModuleCount").text(data.total_records);
                    
          if (data.current_page < data.last_page) {
            $(".next").removeClass("disabled");
          } else {
            $(".next").addClass("disabled", "disabled");
          }
          if (data.current_page > 1) {
            $(".previous").removeClass("disabled");
          } else {
            $(".previous").addClass("disabled", "disabled");
          }
          $("#pageno").attr("max", data.last_page);
        } else {
                    $("#tableList").html('<div class="CdsModule-empty"><div class="CdsModule-empty-icon CdsModule-icon CdsModule-icon-module"></div><div class="CdsModule-empty-text">No modules found</div><a href="{{ baseUrl("module/add") }}" class="CdsModule-add-btn"><span class="CdsModule-icon CdsModule-icon-plus"></span>Add Your First Module</a></div>');
                    $("#cdsModuleCount").text('0');
        }
      },
    });
  }

    function changePage(action) {
      var page = parseInt($("#pageno").val());
      if (action == 'prev') {
        page--;
      }
      if (action == 'next') {
        page++;
      }
      if (!isNaN(page)) {
        loadData(page);
      } else {
        errorMessage("Invalid Page Number");
      }
    }

    function cdsModuleSearch() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        const cards = document.querySelectorAll('.CdsModule-card');
        let visibleCount = 0;
        
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        document.getElementById('cdsModuleCount').textContent = visibleCount;
    }

    function cdsModuleShowToast(message, type = 'success') {
        const toast = document.getElementById('cdsModuleToast');
        toast.textContent = message;
        toast.style.background = type === 'error' ? 'var(--CdsModule-danger)' : 'var(--CdsModule-text)';
        toast.classList.add('CdsModule-show');
        
        setTimeout(() => {
            toast.classList.remove('CdsModule-show');
        }, 3000);
    }

    // Function to show test card for debugging
    function showTestCard() {
        const testCard = document.getElementById('testCard');
        if (testCard) {
            testCard.style.display = 'block';
            cdsModuleShowToast('Test card shown - design is working!');
        }
    }

    // Auto-show test card after 2 seconds if no data loaded
    setTimeout(function() {
        const cards = document.querySelectorAll('.CdsModule-card');
        if (cards.length <= 1) { // Only test card or no cards
            showTestCard();
        }
    }, 2000);

    // Modal functionality
    let cdsModuleEditId = null;

    function cdsModuleOpenModal(id = null) {
        const modal = document.getElementById('cdsModuleModal');
        const title = document.getElementById('cdsModuleModalTitle');
        const form = document.getElementById('cdsModuleForm');
        
        cdsModuleEditId = id;
        
        if (id) {
            title.textContent = 'Edit Module';
            // Load module data via AJAX
            loadModuleData(id);
        } else {
            title.textContent = 'Add New Module';
            form.reset();
            document.getElementById('cdsModuleActionsList').innerHTML = '';
        }
        
        modal.classList.add('CdsModule-active');
    }

    function cdsModuleCloseModal() {
        const modal = document.getElementById('cdsModuleModal');
        modal.classList.remove('CdsModule-active');
        cdsModuleEditId = null;
    }

    function loadModuleData(id) {
        $.ajax({
            type: "GET",
            url: BASEURL + '/module/get/' + id,
            dataType: 'json',
            success: function(data) {
                if (data.status) {
                    document.getElementById('cdsModuleName').value = data.module.name;
                    
                    // Load actions
                    const actionsList = document.getElementById('cdsModuleActionsList');
                    actionsList.innerHTML = '';
                    
                    if (data.module.moduleAction && data.module.moduleAction.length > 0) {
                        data.module.moduleAction.forEach(function(action, index) {
                            addActionField(action.action, index);
                        });
                    } else {
                        addActionField();
                    }
                } else {
                    cdsModuleShowToast('Error loading module data', 'error');
                }
            },
            error: function() {
                cdsModuleShowToast('Error loading module data', 'error');
            }
        });
    }

    function addActionField(value = '', index = null) {
        const actionsList = document.getElementById('cdsModuleActionsList');
        const actionIndex = index !== null ? index : actionsList.children.length;
        
        const actionItem = document.createElement('div');
        actionItem.className = 'CdsModule-action-item';
        actionItem.innerHTML = `
            <input type="text" class="CdsModule-form-input" 
                   name="actions[]" value="${value}" 
                   placeholder="Enter action name">
            <button type="button" onclick="removeActionField(this)" 
                    style="background: var(--CdsModule-danger); color: white; border: none; padding: 0.5rem; border-radius: 4px; cursor: pointer;">
                ✕
            </button>
        `;
        
        actionsList.appendChild(actionItem);
    }

    function removeActionField(button) {
        button.parentElement.remove();
    }

    function cdsModuleSave() {
        const moduleName = document.getElementById('cdsModuleName').value;
        const actionInputs = document.querySelectorAll('input[name="actions[]"]');
        const actions = Array.from(actionInputs).map(input => input.value).filter(value => value.trim() !== '');
        
        if (!moduleName.trim()) {
            cdsModuleShowToast('Please enter a module name', 'error');
            return;
        }
        
        if (actions.length === 0) {
            cdsModuleShowToast('Please add at least one action', 'error');
            return;
        }
        
        const formData = {
            _token: csrf_token,
            name: moduleName,
            actions: actions
        };
        
        const url = cdsModuleEditId ? 
            BASEURL + '/module/update/' + cdsModuleEditId : 
            BASEURL + '/module/save';
        
        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            dataType: 'json',
            success: function(data) {
                if (data.status) {
                    cdsModuleShowToast(data.message || 'Module saved successfully');
                    cdsModuleCloseModal();
                    loadData(); // Reload the list
                } else {
                    cdsModuleShowToast(data.message || 'Error saving module', 'error');
                }
            },
            error: function() {
                cdsModuleShowToast('Error saving module', 'error');
            }
        });
    }

    // Close modal on outside click
    document.getElementById('cdsModuleModal').addEventListener('click', function(e) {
        if (e.target === this) {
            cdsModuleCloseModal();
        }
    });
</script>
  @endsection