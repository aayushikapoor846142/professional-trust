<div class="CdsIndividualChat-files-panel" id="filesPanel">
    <div class="CdsIndividualChat-files-panel-header">
        <div class="CdsIndividualChat-files-panel-title">Files Shared In Chat</div>
        <button class="CdsIndividualChat-files-panel-close" id="closeFilesPanel">✕</button>
    </div>
    <div class="CdsIndividualChat-files-panel-content">
        <!-- Files Section -->
        <div class="CdsIndividualChat-files-section">
            <div class="CdsIndividualChat-files-section-header">
                <span>Files</span>
                <i class="fa-solid fa-chevron-down"></i>
            </div>
            
            <!-- Search Section -->
            <div class="CdsIndividualChat-files-search" id="filesSearchSection" style="display: none;">
                <div class="CdsIndividualChat-files-search-block">
                    <span class="CdsIndividualChat-files-search-icon">
                        <i class="fa-sharp fa-regular fa-magnifying-glass" aria-hidden="true"></i>
                    </span>
                    <input type="text" class="CdsIndividualChat-files-search-input" id="search-file-input"
                        placeholder="Search Files Here">
                    <a href="javascript:;" class="CdsIndividualChat-files-clear-text" id="clearFileSearch">
                        <i class="fa-times"></i>
                    </a>
                </div>
                <div class="CdsIndividualChat-files-action-btn">
                    <button type="button" id="searchFilesBtn" class="CdsIndividualChat-files-search-btn">
                        <i class="fa-sharp fa-regular fa-magnifying-glass" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            
            <!-- Files Container -->
            <div id="attachments-container" class="CdsIndividualChat-files-container">
                <!-- Dynamic files will be loaded here -->
                <div class="CdsIndividualChat-files-loading">
                    <div class="CdsIndividualChat-files-loading-spinner"></div>
                    <p>Loading files...</p>
                </div>
            </div>
            
            <!-- Load More Button -->
            <div id="loadMoreContainer" class="CdsIndividualChat-files-load-more" style="display: none;">
                <button id="loadMoreFiles" class="CdsIndividualChat-files-load-more-btn">
                    Load More Files
                </button>
            </div>
            
            <!-- No Files Message -->
            <div id="noFilesMessage" class="CdsIndividualChat-files-empty" style="display: none;">
                <div class="CdsIndividualChat-files-empty-icon">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
                <p>No files shared in this chat yet</p>
            </div>
        </div>
    </div>
</div>

<!-- File Preview Modal -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filePreviewModalLabel">File Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="filePreviewContent">
                <!-- File content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="downloadFileBtn" class="btn btn-primary" download>Download</a>
            </div>
        </div>
    </div>
</div>