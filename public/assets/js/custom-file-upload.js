class FileUploadManager {
    constructor(selector, options = {}) {
        this.selector = selector;
        this.container = null;
        this.files = new Map();
        this.uploadQueue = new Map(); // Track upload status
        
        // Default options
        this.options = {
            maxFileSize: options.maxFileSize || 10 * 1024 * 1024, // 10MB
            allowedTypes: options.allowedTypes || [
                'image/jpeg', 'image/png', 'image/gif', 'application/pdf',
                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/csv', 'application/csv', 'application/vnd.ms-excel' // Fixed CSV MIME types
            ],
            allowedExtensions: options.allowedExtensions || [
                '.jpg', '.jpeg', '.png', '.gif', '.pdf', '.doc', '.docx', '.csv', '.xls', '.xlsx'
            ],
            maxFiles: options.maxFiles || null, // null = unlimited
            autoUpload: options.autoUpload || false, // New option for auto upload
            onFileAdded: options.onFileAdded || null,
            onFileRemoved: options.onFileRemoved || null,
            onError: options.onError || null,
            onReset: options.onReset || null,
            onPaste: options.onPaste || null,
            onUpload: options.onUpload || null, // New callback for handling uploads
            onUploadProgress: options.onUploadProgress || null, // Optional progress callback
            onUploadComplete: options.onUploadComplete || null, // Optional completion callback
            onUploadError: options.onUploadError || null // Optional upload error callback
        };
    }

    init() {
        // Find container by selector
        this.container = document.querySelector(this.selector);
        
        if (!this.container) {
            console.error(`FileUploadManager: Container not found for selector "${this.selector}"`);
            return false;
        }

        // Find required elements within container
        this.uploadArea = this.container.querySelector('.CDSFeed-upload-area');
        this.fileInput = this.container.querySelector('.CDSFeed-file-input');
        this.fileList = this.container.querySelector('.CDSFeed-file-list');

        if (!this.uploadArea || !this.fileInput) {
            console.error('FileUploadManager: Required elements not found within container');
            return false;
        }

        // Update file input accept attribute based on allowed extensions
        if (this.options.allowedExtensions && this.options.allowedExtensions.length > 0) {
            this.fileInput.setAttribute('accept', this.options.allowedExtensions.join(','));
        }

        this.setupEventListeners();
        return true;
    }

    setupEventListeners() {
        // Click to upload
        this.uploadArea.addEventListener('click', (e) => {
            // Prevent triggering if clicking on child elements
            if (e.target === this.uploadArea || e.target.closest('.CDSFeed-upload-icon, .CDSFeed-upload-text, .CDSFeed-upload-hint')) {
                this.fileInput.click();
            }
        });

        // File input change
        this.fileInput.addEventListener('change', (e) => {
            this.handleFiles(e.target.files);
            // Clear input value to allow selecting the same file again
            e.target.value = '';
        });

        // Drag and drop events
        this.uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            this.uploadArea.classList.add('drag-over');
        });

        this.uploadArea.addEventListener('dragleave', (e) => {
            // Only remove class if leaving the upload area completely
            if (e.target === this.uploadArea || !this.uploadArea.contains(e.relatedTarget)) {
                this.uploadArea.classList.remove('drag-over');
            }
        });

        this.uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            this.uploadArea.classList.remove('drag-over');
            this.handleFiles(e.dataTransfer.files);
        });

        // Paste event listener
        this.setupPasteListener();
    }

    setupPasteListener() {
        // Add paste event listener to the document
        // You can also add it to a specific element if needed
        const pasteHandler = (e) => {
            // Check if the paste event is within or near the upload container
            // This prevents interfering with paste in other parts of the page
            if (this.container.contains(document.activeElement) || 
                document.activeElement === document.body ||
                this.uploadArea.contains(document.activeElement)) {
                this.handlePaste(e);
            }
        };

        // Store reference to remove listener if needed
        this.pasteHandler = pasteHandler;
        document.addEventListener('paste', pasteHandler);

        // Also add visual feedback for paste support
        if (this.uploadArea) {
            const uploadHint = this.uploadArea.querySelector('.CDSFeed-upload-hint');
            if (uploadHint && !uploadHint.textContent.includes('paste')) {
                uploadHint.textContent += ' or paste images';
            }
        }
    }

    handlePaste(e) {
        const clipboardData = e.clipboardData || window.clipboardData;
        
        if (!clipboardData) {
            return;
        }

        const items = clipboardData.items;
        const pastedFiles = [];

        // Iterate through clipboard items
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            
            // Check if the item is an image
            if (item.type.indexOf('image') !== -1) {
                const blob = item.getAsFile();
                
                if (blob) {
                    // Create a proper file object with a name
                    const file = new File([blob], this.generatePastedFileName(blob.type), {
                        type: blob.type,
                        lastModified: Date.now()
                    });
                    
                    pastedFiles.push(file);
                }
            }
        }

        // If we found any images, handle them
        if (pastedFiles.length > 0) {
            e.preventDefault(); // Prevent default paste behavior
            
            // Call paste callback if provided
            if (this.options.onPaste) {
                this.options.onPaste(pastedFiles);
            }
            
            // Handle the files
            this.handleFiles(pastedFiles);
            
            // Add visual feedback
            this.showPasteFeedback();
        }
    }

    generatePastedFileName(mimeType) {
        const extension = this.getExtensionFromMimeType(mimeType);
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-').slice(0, -5);
        return `pasted-image-${timestamp}.${extension}`;
    }

    getExtensionFromMimeType(mimeType) {
        const mimeToExt = {
            'image/jpeg': 'jpg',
            'image/png': 'png',
            'image/gif': 'gif',
            'image/webp': 'webp',
            'image/bmp': 'bmp',
            'image/svg+xml': 'svg',
            'text/csv': 'csv',
            'application/csv': 'csv',
            'application/vnd.ms-excel': 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'xlsx',
            'application/pdf': 'pdf',
            'application/msword': 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'docx'
        };
        
        return mimeToExt[mimeType] || 'bin'; // Default to bin for unknown types
    }

    showPasteFeedback() {
        // Add a visual feedback when image is pasted
        this.uploadArea.classList.add('paste-success');
        
        setTimeout(() => {
            this.uploadArea.classList.remove('paste-success');
        }, 1000);
    }

    handleFiles(fileList) {
        const filesToAdd = Array.from(fileList);
        
        // Check max files limit
        if (this.options.maxFiles && this.files.size + filesToAdd.length > this.options.maxFiles) {
            this.showError(`Maximum ${this.options.maxFiles} files allowed`);
            return;
        }

        const validFiles = [];
        filesToAdd.forEach(file => {
            if (this.validateFile(file)) {
                this.addFile(file);
                validFiles.push(file);
            }
        });
        
        this.updateFileList();
        
        // Auto upload if enabled and files were added
        if (this.options.autoUpload && validFiles.length > 0) {
            // Get the file IDs that were just added
            const fileIds = [];
            this.files.forEach((fileData, fileId) => {
                if (validFiles.includes(fileData.file)) {
                    fileIds.push(fileId);
                }
            });
            
            // Trigger upload for these files
            fileIds.forEach(fileId => {
                this.uploadFile(fileId);
            });
        }
    }

    getFileExtension(filename) {
        const parts = filename.split('.');
        if (parts.length > 1) {
            return '.' + parts[parts.length - 1].toLowerCase();
        }
        return '';
    }

    validateFile(file) {
        // Check file size
        if (file.size > this.options.maxFileSize) {
            const maxSizeMB = this.options.maxFileSize / (1024 * 1024);
            this.showError(`${file.name} is too large. Maximum size is ${maxSizeMB}MB.`);
            return false;
        }

        // Check file extension if allowedExtensions is specified
        if (this.options.allowedExtensions && this.options.allowedExtensions.length > 0) {
            const fileExtension = this.getFileExtension(file.name);
            const isExtensionAllowed = this.options.allowedExtensions.some(ext => 
                ext.toLowerCase() === fileExtension
            );
            
            if (!isExtensionAllowed) {
                this.showError(`${file.name} has an unsupported file extension. Allowed extensions: ${this.options.allowedExtensions.join(', ')}`);
                return false;
            }
        }

        // Check MIME type if allowedTypes is specified
        if (this.options.allowedTypes && this.options.allowedTypes.length > 0) {
            // Some browsers might not provide accurate MIME types for certain files like CSV
            // So we'll be more lenient for files with allowed extensions
            const fileExtension = this.getFileExtension(file.name);
            const hasAllowedExtension = this.options.allowedExtensions && 
                this.options.allowedExtensions.some(ext => ext.toLowerCase() === fileExtension);
            
            // If file has an allowed extension but MIME type doesn't match, we'll allow it
            // This helps with CSV files which sometimes have incorrect MIME types
            if (!hasAllowedExtension && !this.options.allowedTypes.includes(file.type)) {
                this.showError(`${file.name} is not a supported file type.`);
                return false;
            }
        }

        // Check if file already exists (by name and size)
        let isDuplicate = false;
        this.files.forEach((fileData) => {
            if (fileData.name === file.name && fileData.size === file.size) {
                isDuplicate = true;
            }
        });

        if (isDuplicate) {
            this.showError(`${file.name} has already been added.`);
            return false;
        }

        return true;
    }

    addFile(file) {
        const fileId = Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        const fileData = {
            id: fileId,
            file: file,
            name: file.name,
            size: file.size,
            type: file.type,
            extension: this.getFileExtension(file.name),
            preview: null,
            isPasted: file.name.startsWith('pasted-image-'), // Track if file was pasted
            uploadStatus: 'pending', // Track upload status: pending, uploading, completed, error
            uploadProgress: 0
        };

        this.files.set(fileId, fileData);

        // Generate preview for images
        if (file.type.startsWith('image/')) {
            this.generatePreview(fileId, file);
        } else {
            // Call callback if no preview generation needed
            if (this.options.onFileAdded) {
                this.options.onFileAdded(fileData);
            }
        }
    }

    generatePreview(fileId, file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const fileData = this.files.get(fileId);
            if (fileData) {
                fileData.preview = e.target.result;
                this.updateFileList();
                
                // Call callback after preview is generated
                if (this.options.onFileAdded) {
                    this.options.onFileAdded(fileData);
                }
            }
        };
        reader.readAsDataURL(file);
    }

    // Upload a single file
    async uploadFile(fileId) {
        const fileData = this.files.get(fileId);
        if (!fileData || fileData.uploadStatus === 'uploading' || fileData.uploadStatus === 'completed') {
            return;
        }

        if (!this.options.onUpload) {
            console.warn('FileUploadManager: No upload handler provided');
            return;
        }

        // Update status
        fileData.uploadStatus = 'uploading';
        this.updateFileList();

        try {
            // Create a progress callback wrapper
            const progressCallback = (progress) => {
                fileData.uploadProgress = progress;
                this.updateFileList();
                
                if (this.options.onUploadProgress) {
                    this.options.onUploadProgress(fileData, progress);
                }
            };

            // Call the external upload function
            const result = await this.options.onUpload(fileData.file, progressCallback, fileData);
            
            // Update status
            fileData.uploadStatus = 'completed';
            fileData.uploadProgress = 100;
            fileData.uploadResult = result; // Store the result if needed
            
            this.updateFileList();
            
            // Call completion callback
            if (this.options.onUploadComplete) {
                this.options.onUploadComplete(fileData, result);
            }
            
        } catch (error) {
            // Handle upload error
            fileData.uploadStatus = 'error';
            fileData.uploadError = error;
            
            this.updateFileList();
            
            if (this.options.onUploadError) {
                this.options.onUploadError(fileData, error);
            } else {
                this.showError(`Failed to upload ${fileData.name}: ${error.message}`);
            }
        }
    }

    // Upload all pending files
    async uploadAllFiles() {
        const pendingFiles = [];
        
        this.files.forEach((fileData, fileId) => {
            if (fileData.uploadStatus === 'pending' || fileData.uploadStatus === 'error') {
                pendingFiles.push(fileId);
            }
        });
        
        // Upload files sequentially or in parallel based on your needs
        for (const fileId of pendingFiles) {
            await this.uploadFile(fileId);
        }
    }

    // Retry failed uploads
    async retryFailedUploads() {
        const failedFiles = [];
        
        this.files.forEach((fileData, fileId) => {
            if (fileData.uploadStatus === 'error') {
                failedFiles.push(fileId);
            }
        });
        
        for (const fileId of failedFiles) {
            await this.uploadFile(fileId);
        }
    }

    removeFile(fileId) {
        const fileData = this.files.get(fileId);
        if (fileData) {
            // Don't remove if currently uploading
            if (fileData.uploadStatus === 'uploading') {
                if (!confirm('This file is currently uploading. Are you sure you want to remove it?')) {
                    return;
                }
            }
            
            this.files.delete(fileId);
            this.updateFileList();
            
            // Call callback
            if (this.options.onFileRemoved) {
                this.options.onFileRemoved(fileData);
            }
        }
    }

    updateFileList() {
        if (!this.fileList) return;

        if (this.files.size === 0) {
            this.fileList.style.display = 'none';
            return;
        }

        this.fileList.style.display = 'block';
        this.fileList.innerHTML = `
            <div class="CDSFeed-file-list-header">
                <h4>Uploaded Files (${this.files.size})</h4>
                <p>Files will be attached to your group</p>
            </div>
        `;

        this.files.forEach((fileData, fileId) => {
            const fileItem = this.createFileItem(fileData);
            this.fileList.appendChild(fileItem);
        });
    }

    createFileItem(fileData) {
        const div = document.createElement('div');
        div.className = 'CDSFeed-file-item';
        div.setAttribute('data-file-id', fileData.id);
        
        // Add classes based on status
        if (fileData.isPasted) {
            div.classList.add('pasted-file');
        }
        if (fileData.uploadStatus) {
            div.classList.add(`upload-${fileData.uploadStatus}`);
        }
        
        // Create status indicator
        let statusHtml = '';
        if (fileData.uploadStatus === 'uploading') {
            statusHtml = `
                <div class="CDSFeed-file-progress">
                    <div class="CDSFeed-file-progress-bar" style="width: ${fileData.uploadProgress}%"></div>
                </div>
                <span class="CDSFeed-file-status">Uploading... ${Math.round(fileData.uploadProgress)}%</span>
            `;
        } else if (fileData.uploadStatus === 'completed') {
            statusHtml = '<span class="CDSFeed-file-status success">✓ Uploaded</span>';
        } else if (fileData.uploadStatus === 'error') {
            statusHtml = '<span class="CDSFeed-file-status error">✗ Upload failed</span>';
        }
        
        div.innerHTML = `
            <div class="CDSFeed-file-preview">
                ${fileData.preview ? 
                    `<img src="${fileData.preview}" alt="${fileData.name}">` : 
                    this.getFileIcon(fileData.type, fileData.extension)
                }
            </div>
            <div class="CDSFeed-file-info">
                <div class="CDSFeed-file-name">${this.truncateFileName(fileData.name)}</div>
                <div class="CDSFeed-file-meta">
                    ${this.formatFileSize(fileData.size)}
                    ${fileData.isPasted ? '<span class="paste-indicator">(pasted)</span>' : ''}
                </div>
                ${statusHtml}
            </div>
            <button type="button" class="CDSFeed-file-remove" data-file-id="${fileData.id}">
                Remove
            </button>
        `;

        // Add click event to remove button
        const removeBtn = div.querySelector('.CDSFeed-file-remove');
        removeBtn.addEventListener('click', () => this.removeFile(fileData.id));

        return div;
    }

    getFileIcon(type, extension) {
        // First check by extension for more accurate icons
        if (extension) {
            const ext = extension.toLowerCase();
            if (ext === '.csv') return '📊 CSV';
            if (ext === '.xls' || ext === '.xlsx') return '📊 Excel';
            if (ext === '.doc' || ext === '.docx') return '📝 Word';
            if (ext === '.ppt' || ext === '.pptx') return '📽️ PowerPoint';
            if (ext === '.pdf') return '📄 PDF';
        }
        
        // Fallback to MIME type checking
        if (type.includes('pdf')) return '📄 PDF';
        if (type.includes('word') || type.includes('document')) return '📝 DOC';
        if (type.includes('excel') || type.includes('spreadsheet')) return '📊 XLS';
        if (type.includes('powerpoint') || type.includes('presentation')) return '📽️ PPT';
        if (type.includes('csv')) return '📊 CSV';
        return '📎 File';
    }

    truncateFileName(name, maxLength = 30) {
        if (name.length <= maxLength) return name;
        const ext = name.split('.').pop();
        const nameWithoutExt = name.slice(0, name.lastIndexOf('.'));
        const truncated = nameWithoutExt.slice(0, maxLength - ext.length - 3) + '...';
        return truncated + '.' + ext;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    showError(message) {
        if (this.options.onError) {
            this.options.onError(message);
        } else {
            // Default error display
            console.error('FileUploadManager:', message);
            alert(message);
        }
    }

    // Get all files
    getFiles() {
        const files = [];
        this.files.forEach((fileData) => {
            files.push(fileData.file);
        });
        return files;
    }

    // Get files with their metadata
    getFilesWithData() {
        const filesArray = [];
        this.files.forEach((fileData) => {
            filesArray.push(fileData);
        });
        return filesArray;
    }

    // Get files by extension
    getFilesByExtension(extension) {
        const files = [];
        this.files.forEach((fileData) => {
            if (fileData.extension === extension || fileData.extension === '.' + extension) {
                files.push(fileData);
            }
        });
        return files;
    }

    // Get files as FormData
    getFormData(fieldName = 'files') {
        const formData = new FormData();
        let index = 0;
        
        this.files.forEach((fileData) => {
            formData.append(`${fieldName}[${index}]`, fileData.file);
            index++;
        });
        
        return formData;
    }

    // Clear all files
    clearFiles() {
        this.files.clear();
        this.updateFileList();
    }

    // Enhanced reset method
    reset() {
        // Clear all files
        this.clearFiles();
        
        // Reset file input
        if (this.fileInput) {
            this.fileInput.value = '';
        }
        
        // Remove any drag-over styling
        if (this.uploadArea) {
            this.uploadArea.classList.remove('drag-over');
            this.uploadArea.classList.remove('paste-success');
        }
        
        // Hide file list
        if (this.fileList) {
            this.fileList.style.display = 'none';
            this.fileList.innerHTML = '';
        }
        
        // Call reset callback if provided
        if (this.options.onReset) {
            this.options.onReset();
        }
    }

    // Get file count
    getFileCount() {
        return this.files.size;
    }

    // Get count by status
    getFileCountByStatus(status) {
        let count = 0;
        this.files.forEach((fileData) => {
            if (fileData.uploadStatus === status) {
                count++;
            }
        });
        return count;
    }

    // Get total size of all files
    getTotalSize() {
        let totalSize = 0;
        this.files.forEach((fileData) => {
            totalSize += fileData.size;
        });
        return totalSize;
    }

    // Check if all files are uploaded
    areAllFilesUploaded() {
        let allUploaded = true;
        this.files.forEach((fileData) => {
            if (fileData.uploadStatus !== 'completed') {
                allUploaded = false;
            }
        });
        return allUploaded;
    }

    // Clean up method to remove event listeners
    destroy() {
        if (this.pasteHandler) {
            document.removeEventListener('paste', this.pasteHandler);
        }
    }
}

// Export for use
window.FileUploadManager = FileUploadManager;