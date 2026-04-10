const ChatCommon = {
    config: null,
    
    init() {
        this.config = window.groupChatConfig || {};
        this.bindCommonEvents();
    },
    
    bindCommonEvents() {
        // Search functionality
        $(document).on("keyup", "#search-input", (e) => {
            this.handleSearch(e);
        });
        
        // Modal handling
        $(document).on("click", ".modal-toggle", (e) => {
            this.showModal(e);
        });
        
        // Right slide panel
        $(document).on("click", "[data-href]", (e) => {
            this.showRightPanel(e);
        });
        
        // File upload
        $(document).on("change", "#file-upload", (e) => {
            this.handleFileUpload(e);
        });
    },
    
    handleSearch(e) {
        const searchTerm = e.target.value;
        clearTimeout(this.searchTimeout);
        
        this.searchTimeout = setTimeout(() => {
            this.performSearch(searchTerm);
        }, 300);
    },
    
    performSearch(searchTerm) {
        $.ajax({
            url: this.config.baseUrl + 'group/search',
            method: 'POST',
            data: {
                search: searchTerm,
                _token: this.config.csrfToken
            },
            success: (response) => {
                if (response.status) {
                    this.updateSearchResults(response.results);
                }
            }
        });
    },
    
    showModal(e) {
        const modalId = $(e.currentTarget).data("modal");
        $(`#${modalId}`).modal('show');
    },
    
    showRightPanel(e) {
        const href = $(e.currentTarget).data("href");
        this.loadRightPanel(href);
    },
    
    loadRightPanel(href) {
        $.ajax({
            url: href,
            method: 'GET',
            success: (response) => {
                if (response.status) {
                    $(".right-slide-panel").html(response.contents);
                    $(".right-slide-panel").addClass("active");
                }
            }
        });
    },
    
    handleFileUpload(e) {
        const files = e.target.files;
        if (files.length > 0) {
            this.previewFiles(files);
        }
    },
    
    previewFiles(files) {
        const $preview = $("#file-preview");
        $preview.empty();
        
        Array.from(files).forEach((file, index) => {
            const fileHtml = this.createFilePreviewHtml(file, index);
            $preview.append(fileHtml);
        });
    },
    
    createFilePreviewHtml(file, index) {
        return `
            <div class="file-preview-item">
                <span class="file-name">${file.name}</span>
                <button type="button" class="file-remove" data-index="${index}">×</button>
            </div>
        `;
    }
}; 