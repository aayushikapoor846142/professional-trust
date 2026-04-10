class FormBuilder {
    constructor() {
        this.formFields = [
            {
                'label': "Fields Groups",
                'icon_class': "from-group-icon",
                'type': "fieldGroups"
            },
            {
                'label': "Text Input",
                'icon_class': "text-input-icon",
                'type': "textInput",
                'img': "<img src=css/text-input.svg />",
            }, {
                'label': "Number Input",
                'icon_class': "number-input-icon",
                'type': "numberInput"
            }, {
                'label': "Text Editor",
                'icon_class': "textarea-input-icon",
                'type': "textarea"
            }, {
                'label': "Email",
                'icon_class': "email-input-icon",
                'type': "emailInput"
            }, {
                'label': "Link Input",
                'icon_class': "link-input-icon",
                'type': "url"
            }, {
                'label': "Drop Down",
                'icon_class': "dropDown-icon",
                'type': "dropDown"
            }, {
                'label': "Checkbox",
                'icon_class': "checkbox-icon",
                'type': "checkbox"
            }, {
                'label': "Radio",
                'icon_class': "radio-icon",
                'type': "radio"
            }, {
                'label': "Datepicker",
                'icon_class': "datepicker-icon",
                'type': "dateInput"
            }, {
                'label': "Google Address",
                'icon_class': "google-address-icon",
                'type': "addressInput"
            }, {
                'label': "Document Upload",
                'icon_class': "document-upload-icon",
                'type': "fileUpload"
            }
        ];

        this.attributes = {
            'default': [{
                'label': "Label",
                'type': 'label',
                'value': '',
                'field': 'hidden'
            }, {
                'label': "Name",
                'type': 'name',
                'value': '',
                'field': 'hidden'
            }, {
                'label': "Short Description",
                'type': "shortDesc",
                'value': '',
                'field': 'hidden'
            }, {
                'label': "Placeholder",
                'type': 'placeholder',
                'value': '',
            }, {
                'label': "Max Length",
                'type': 'maxlength',
                'value': '',
            }, {
                'label': "Required",
                'type': 'required',
                'field': "checkbox",
                'value': '',
            }, {
                'label': "Step Heading",
                'type': 'stepHeading',
                'value': 'Step Heading',
            }, {
                'label': "Step Description",
                'type': 'stepDescription',
                'value': 'Step Description',
            }],
            'fileUpload': [{
                'label': "Allowed File Types",
                'type': 'allowedTypes',
                'value': '.jpg,.jpeg,.png,.pdf,.doc,.docx',
            }, {
                'label': "Max File Size (MB)",
                'type': 'maxFileSize',
                'field': 'number',
                'value': '5',
            }, {
                'label': "Max Files",
                'type': 'maxFiles',
                'field': 'number',
                'value': '10',
            }, {
                'label': "Multiple Files",
                'type': 'multiple',
                'field': 'checkbox',
                'value': '1',
            }],
            'fieldGroups': [{
                'label': "Label",
                'type': 'label',
                'field': "hidden"
            }, {
                'label': "Short Description",
                'type': "shortDesc",
                'value': '',
                'field': 'hidden'
            }, {
                'label': "Font Size",
                'type': 'font_size',
                'field': "dropdown",
                'options': [{
                    "label": "H1",
                    "value": "32"
                }, {
                    "label": "H2",
                    "value": "24"
                }, {
                    "label": "H3",
                    "value": "18"
                }, {
                    "label": "H4",
                    "value": "16"
                }, {
                    "label": "H5",
                    "value": "13"
                }, {
                    "label": "H6",
                    "value": "12"
                }]
            }, {
                'label': "Step Heading",
                'type': 'stepHeading',
                'value': 'Step Heading',
            }, {
                'label': "Step Description",
                'type': 'stepDescription',
                'value': 'Step Description',
            }],
            'textarea': [{
                'label': "Text Limit",
                'type': 'textLimit',
                'field': "dropdown",
                'options': [{
                    "label": "Word Limit",
                    "value": "wordLimit"
                }, {
                    "label": "Character Limit",
                    "value": "characterLimit"
                }, {
                    "label": "None",
                    "value": "none"
                }]
            }, {
                'label': "Add Length",
                'type': 'addLength',
                'field': "number",
                'value': '',
            }]
        };

        this.saveUrl = '';
        this.redirectBack = '';
        this.defaultJson = '';
        this.formName = '';
        this.formType = '';
        this.previewUrl = '';
        this.uploadCount = 0;
        this.sortableInstance = null;
        this.dataFormat = 'form';
        this.debugMode = false;
        this.csrfToken = null;
        this.fileUploaders = {}; // Store file uploader instances
    }

    // Initialize form generator
    formGenerator(element, params = {}) {
        const loader = "<div class='fg-loader'></div>";
        
        // Set parameters
        if (params.previewUrl !== undefined) {
            this.previewUrl = params.previewUrl;
        }
        if (params.saveUrl !== undefined) {
            this.saveUrl = params.saveUrl;
        }
        if (params.formName !== undefined) {
            this.formName = params.formName;
        }
        if (params.formType !== undefined) {
            this.formType = params.formType;
        } else {
            this.formType = 'step_form';
        }
        if (params.redirectBack !== undefined) {
            this.redirectBack = params.redirectBack;
        }
        if (params.dataFormat !== undefined) {
            this.dataFormat = params.dataFormat;
        }
        if (params.debugMode !== undefined) {
            this.debugMode = params.debugMode;
        }
        if (params.csrfToken !== undefined) {
            this.csrfToken = params.csrfToken;
        }

        element.classList.add("fg-container", "cds-form-container-assessment");
        
        const form_index = this.getRandom(10);
        
        // Build HTML structure
        let leftHtml = '<div class="cds-form-container-assessment-body-left-panel hide-left">';
        leftHtml += '<div class="field-list">';
        leftHtml += '<div class="cds-form-container-assessment-field-box-header">';
        leftHtml += '<div class="cds-form-container-assessment-field-box-header-toolbar"><h4>Form Fields</h4><button type="button" class="cds-component-expand-button-main cds-component-add-button-main "></button><button type="button" class="cds-component-expand-button-main close-panel"></button></div>';
        leftHtml += '<div class="cds-form-container-assessment-field-box-header-toolbar">';
        leftHtml += '</div>';
        leftHtml += '<ul class="input-fields form-fields">';
        
        this.formFields.forEach(val => {
            leftHtml += `<li data-field="${val.type}" data-label="${val.label}" data-icon="${val.icon_class}" class="fg-field">`;
            leftHtml += `<div class="fg-field-text"><span class="${val.icon_class} fg-btn-add icon-span">&nbsp;</span><span class="input-label">${val.label}</span></div>`;
            leftHtml += '<button type="button" title="Click to Add" class="fg-btn-add"></button>';
            leftHtml += '</li>';
        });
        
        leftHtml += '</ul>';
        leftHtml += '</div>';
        leftHtml += '</div>';
        leftHtml += '<div class="selected-fields">';
        leftHtml += `<ul class="fg-selected-fields ${this.formType === 'step_form' ? 'step-fields' : ''} sortable-list"><li class="disable-sort-item" style="visibility:hidden"><div>Item ${this.getRandom(9)}</div></li></ul>`;
        leftHtml += '</div>';
        leftHtml += '</div>';

        const mainHtml = `<div class="cds-form-container-assessment-body-main-panel-wrapper"><div class="cds-form-container-assessment-body-main-panel">${loader}</div></div>`;
        const rightHtml = '<div class="cds-form-container-assessment-body-right-panel"></div>';
        
        let form = `<form class="fg-form" method="post" id="fg-form-${form_index}"><div class="cds-form-container-assessment-view">`;
        form += '<div class="fg-group cds-form-container-assessment-header">';
        form += '<div class="cds-form-container-assessment-header-right"><div class="cds-form-container-titlebar"><label>Unique Form Name</label>';
        form += `<input type="text" class="form-control" value="${this.formName}" placeholder="Enter Form Name" name="form_name" oninput="validateHtmlTags(this)"/>`;
        form += '</div>';
        form += '<div class="cds-form-container-selectbar"><label>Select Form Type</label>';
        form += '<select class="form-select" name="form_type">';
        form += '<option disabled value="">Select Form Type</option>';
        form += `<option ${this.formType === 'step_form' ? 'selected' : ''} value="step_form">Step Form</option>`;
        form += `<option ${this.formType === 'single_form' ? 'selected' : ''} value="single_form">Single Form</option>`;
        form += '</select>';
        form += '</div>';
        form += '</div><div class="cds-form-container-assessment-header-left">';
        
        if (this.previewUrl !== '') {
            form += `<button type="button" class="cds-component-preview-btn" data-href="${this.previewUrl}" title="Preview">Preview</button>`;
        }
        
        form += '<button type="button" class="cds-component-save-button"><i class="fa fa-save"></i> Save Form</button>';
        form += '</div>';
        form += '</div>';
        form += '</div>';
        form += '<div class="cds-form-container-assessment-field-box-header-titlebar"><h4>Form Fields</h4><div id="infoDiv"></div></div>';
        form += '<div class="cds-form-container-assessment-body"></div>';
        form += '</div></form>';

        element.innerHTML = form;

        const formEl = element.querySelector(`#fg-form-${form_index}`);
        const bodyEl = formEl.querySelector(".cds-form-container-assessment-body");
        
        bodyEl.innerHTML = leftHtml + mainHtml + rightHtml;
        
        if (params.defaultJson === undefined) {
            element.querySelector(".fg-loader")?.remove();
        }

        // Initialize sortable
        this.initSortable();
        
        // Bind events
        this.bindEvents();

        // Process default JSON if provided
        if (params.defaultJson !== undefined) {
            this.processDefaultJson(params.defaultJson);
        }
    }

    // Show field area
    showFieldArea(index, fieldType, label, icon) {
        let html = `<div id="fga-${index}" data-type="${fieldType}" data-index="${index}" class="fg-field-area">`;
        html += '<div class="setting-btn">';
        html += '<button type="button" class="btn-setting"><span class="setting-icon"></span></button>';
        html += '</div>';
        html += `<div class="right-head-block"><p class="${icon}"></p><label class="fg-label editable-label" id="editable-${index}" placeholder="Input Label..." contenteditable="true"><span>${label}</span></label></div>`;
        html += `<p class="fg-description editable-desc" id="description-${index}" contenteditable="true" placeholder="Description..."></p>`;

        // Add field-specific HTML
        switch (fieldType) {
            case 'textInput':
                html += '<input type="text" class="fg-control" />';
                break;
            case 'addressInput':
                html += '<input type="text" class="fg-control google-address" />';
                break;
            case 'fileUpload':
                html += this.createFileUploadHTML(index);
                break;
            case 'numberInput':
                html += '<input type="number" class="fg-control" />';
                break;
            case 'emailInput':
                html += '<input type="email" class="fg-control" />';
                break;
            case 'url':
                html += '<input type="url" class="fg-control" />';
                break;
            case 'textarea':
                html += '<textarea type="url" class="fg-control"></textarea>';
                break;
            case 'dateInput':
                html += '<input type="text" class="fg-control datepicker" />';
                break;
            case 'checkbox':
            case 'radio':
            case 'dropDown':
                const rndindex = this.getRandom(5);
                html += `<div class="multiple-options"><div class="option-value"><input type="text" name="fg_fields[${index}][settings][options][${rndindex}]" class="fg-control fg-options" placeholder="Enter Option Value"><a href="javascript:;" class="remove-choice"><i class="fa fa-times"></i></a></div></div>`;
                html += '<div class="choice-action"><button type="button" class="add-choice"><i class="fa fa-plus"></i> Add Options</button></div>';
                break;
        }

        html += '</div>';

        const mainPanel = document.querySelector(".cds-form-container-assessment-body-main-panel");
        
        // Hide all field areas
        mainPanel.querySelectorAll(".fg-field-area").forEach(el => el.style.display = 'none');
        
        // Check if field area already exists
        const existingArea = mainPanel.querySelector(`.fg-field-area[data-index="${index}"]`);
        if (!existingArea) {
            mainPanel.querySelector(".no-data-message")?.remove();
            mainPanel.insertAdjacentHTML('beforeend', html);
            mainPanel.querySelector(".fg-loader")?.remove();
            mainPanel.querySelector(`.fg-field-area[data-index="${index}"]`).style.display = 'block';
            
            // Initialize file upload if it's a file upload field
            if (fieldType === 'fileUpload') {
                setTimeout(() => {
                    this.initializeFileUploadField(index);
                }, 100);
            }
        } else {
            existingArea.style.display = 'block';
        }
    }

    // Create file upload HTML
    createFileUploadHTML(index) {
        return `
            <div class="custom-file-upload" data-index="${index}">
                <div class="file-upload-area" id="file-upload-${index}">
                    <input type="file" id="file-input-${index}" class="file-input-hidden" multiple />
                    <div class="file-upload-dropzone">
                        <div class="upload-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                        </div>
                        <p class="upload-text">Drag and drop your files here</p>
                        <p class="upload-subtext">or click to browse your files</p>
                        <button type="button" class="browse-btn">Browse Files</button>
                    </div>
                    <div class="file-list" id="file-list-${index}"></div>
                </div>
            </div>
        `;
    }

    // Initialize file upload field
    initializeFileUploadField(index) {
        const fileUploadArea = document.querySelector(`#file-upload-${index}`);
        const fileInput = document.querySelector(`#file-input-${index}`);
        const dropzone = fileUploadArea?.querySelector('.file-upload-dropzone');
        const browseBtn = fileUploadArea?.querySelector('.browse-btn');
        const fileList = document.querySelector(`#file-list-${index}`);

        if (!fileUploadArea || !fileInput || !dropzone) return;

        // Create file uploader instance
        this.fileUploaders[index] = {
            files: [],
            element: fileUploadArea,
            input: fileInput,
            list: fileList
        };

        // Browse button click
        browseBtn?.addEventListener('click', () => {
            fileInput.click();
        });

        // File input change
        fileInput.addEventListener('change', (e) => {
            this.handleFiles(e.target.files, index);
        });

        // Drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.add('drag-over');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => {
                dropzone.classList.remove('drag-over');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            this.handleFiles(files, index);
        }, false);
    }

    // Handle files
    handleFiles(files, index) {
        const fileArray = Array.from(files);
        const uploader = this.fileUploaders[index];
        
        if (!uploader) return;

        fileArray.forEach(file => {
            // Check if file already exists
            const exists = uploader.files.some(f => f.name === file.name && f.size === file.size);
            if (!exists) {
                uploader.files.push(file);
                this.displayFile(file, index);
            }
        });
    }

    // Display file
    displayFile(file, index) {
        const fileList = document.querySelector(`#file-list-${index}`);
        if (!fileList) return;

        const fileId = `file-${index}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        const fileSize = this.formatFileSize(file.size);
        const fileExt = file.name.split('.').pop().toLowerCase();
        
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.id = fileId;
        fileItem.innerHTML = `
            <div class="file-info">
                <div class="file-icon">${this.getFileIcon(fileExt)}</div>
                <div class="file-details">
                    <p class="file-name">${file.name}</p>
                    <p class="file-size">${fileSize}</p>
                </div>
            </div>
            <div class="file-actions">
                ${this.isImageFile(fileExt) ? `<button type="button" class="preview-btn" data-file-id="${fileId}">Preview</button>` : ''}
                <button type="button" class="remove-btn" data-file-id="${fileId}" data-index="${index}">Remove</button>
            </div>
            <div class="file-progress" style="display: none;">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%"></div>
                </div>
                <span class="progress-text">0%</span>
            </div>
        `;

        fileList.appendChild(fileItem);

        // Add remove functionality
        const removeBtn = fileItem.querySelector('.remove-btn');
        removeBtn?.addEventListener('click', () => {
            this.removeFile(fileId, file, index);
        });

        // Add preview functionality for images
        if (this.isImageFile(fileExt)) {
            const previewBtn = fileItem.querySelector('.preview-btn');
            previewBtn?.addEventListener('click', () => {
                this.previewImage(file);
            });
        }
    }

    // Remove file
    removeFile(fileId, file, index) {
        const fileItem = document.getElementById(fileId);
        const uploader = this.fileUploaders[index];
        
        if (fileItem && uploader) {
            fileItem.remove();
            uploader.files = uploader.files.filter(f => f !== file);
        }
    }

    // Preview image
    previewImage(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const modal = document.createElement('div');
            modal.className = 'file-preview-modal';
            modal.innerHTML = `
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <img src="${e.target.result}" alt="${file.name}">
                    <p class="preview-filename">${file.name}</p>
                </div>
            `;
            document.body.appendChild(modal);

            const closeBtn = modal.querySelector('.close-modal');
            closeBtn?.addEventListener('click', () => {
                modal.remove();
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        };
        reader.readAsDataURL(file);
    }

    // Helper functions
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    isImageFile(ext) {
        return ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(ext.toLowerCase());
    }

    getFileIcon(ext) {
        const icons = {
            pdf: '📄',
            doc: '📝',
            docx: '📝',
            xls: '📊',
            xlsx: '📊',
            ppt: '📊',
            pptx: '📊',
            txt: '📝',
            zip: '🗜️',
            rar: '🗜️',
            jpg: '🖼️',
            jpeg: '🖼️',
            png: '🖼️',
            gif: '🖼️',
            mp4: '🎬',
            mp3: '🎵',
            default: '📎'
        };
        return icons[ext.toLowerCase()] || icons.default;
    }

    // Initialize sortable functionality
    initSortable() {
        const sortableList = document.querySelector(".fg-selected-fields");
        if (!sortableList || typeof Sortable === 'undefined') return;

        this.sortableInstance = new Sortable(sortableList, {
            animation: 150,
            handle: '.fg-field-block',
            draggable: 'li:not(.disable-sort-item)',
            onStart: (evt) => {
                const hasClass = evt.item.classList.contains("droppable");
                document.querySelectorAll(".fg-field-block .sortable-list").forEach(el => {
                    el.style.display = hasClass ? 'none' : 'block';
                });
            },
            onEnd: (evt) => {
                document.querySelector(".step-fields")?.classList.remove("drag-start");
                document.querySelectorAll(".fg-field-block .sortable-list").forEach(el => {
                    el.style.display = 'block';
                });
            },
            onUpdate: (evt) => {
                setTimeout(() => {
                    this.updateFieldGroupsOrder();
                }, 1500);
            }
        });
    }

    // Update field groups order
    updateFieldGroupsOrder() {
        document.querySelectorAll(".fg-selected-fields > .fg-field-block").forEach(fieldBlock => {
            const fieldType = fieldBlock.dataset.type;
            const fieldIndex = fieldBlock.dataset.index;
            
            if (fieldType === 'fieldGroups') {
                const groupFields = fieldBlock.querySelector(".group-fields");
                if (groupFields && fieldBlock.querySelectorAll(".sortable-list > .fg-field-block:not(.disable-sort-item)").length > 0) {
                    groupFields.innerHTML = '';
                    let indx = 0;
                    
                    fieldBlock.querySelectorAll(".sortable-list > .fg-field-block:not(.disable-sort-item)").forEach(subField => {
                        const subIndex = subField.dataset.index;
                        const inputField = `<input type="hidden" class="field-input" name="fg_fields[${fieldIndex}][groupFields][${indx}]" value="${subIndex}" />`;
                        groupFields.insertAdjacentHTML('beforeend', inputField);
                        indx++;
                    });
                }
            }
        });
    }

    // Bind events
    bindEvents() {
        // Form type change
        document.addEventListener("change", (e) => {
            if (e.target.classList.contains("form-select")) {
                if (e.target.value === 'step_form') {
                    document.querySelectorAll(".step-info-field").forEach(el => el.style.display = 'block');
                    document.querySelector(".fg-selected-fields")?.classList.add("step-fields");
                } else {
                    document.querySelectorAll(".step-info-field").forEach(el => el.style.display = 'none');
                    document.querySelector(".fg-selected-fields")?.classList.remove("step-fields");
                }
                this.updateFormTypeDisplay(e.target.value);
            }
        });

        // Remove field
        document.addEventListener("click", (e) => {
            if (e.target.closest(".remove-field")) {
                e.preventDefault();
                if (!confirm("Are you sure to remove field")) {
                    return false;
                }
                
                const button = e.target.closest(".remove-field");
                const fieldBlock = button.closest(".fg-field-block");
                const index = fieldBlock.dataset.index;
                const type = fieldBlock.dataset.type;
                
                if (type === 'fieldGroups') {
                    fieldBlock.querySelectorAll(".sortable-list .fg-field-block").forEach(subField => {
                        const subindex = subField.dataset.index;
                        document.querySelectorAll(`.fg-field-area[data-index="${subindex}"], .setting-area[data-index="${subindex}"]`).forEach(el => el.remove());
                    });
                    
                    setTimeout(() => {
                        document.querySelectorAll(`.fg-field-area[data-index="${index}"], .setting-area[data-index="${index}"]`).forEach(el => el.remove());
                        fieldBlock.remove();
                    }, 350);
                } else {
                    fieldBlock.remove();
                    document.querySelectorAll(`.fg-field-area[data-index="${index}"], .setting-area[data-index="${index}"]`).forEach(el => el.remove());
                }
            }
        });

        // Copy field
        document.addEventListener("click", (e) => {
            if (e.target.closest(".copy-field")) {
                e.preventDefault();
                if (!confirm("Are you sure to copy field")) {
                    return false;
                }
                
                const button = e.target.closest(".copy-field");
                const field = button.dataset.field;
                this.copyField(button, field);
            }
        });

        // Add field
        document.addEventListener("click", (e) => {
            if (e.target.closest(".cds-component-add-button") || e.target.closest(".fg-btn-add")) {
                e.preventDefault();
                const fieldEl = e.target.closest('.fg-field');
                if (fieldEl) {
                    const index = this.getRandom(9);
                    const fieldType = fieldEl.dataset.field;
                    const label = fieldEl.dataset.label;
                    const icon = fieldEl.dataset.icon;
                    this.addFields(index, fieldType, label, icon);
                }
            }
        });

        // Preview button
        document.addEventListener("click", (e) => {
            if (e.target.classList.contains("cds-component-preview-btn")) {
                e.preventDefault();
                const url = e.target.dataset.href;
                this.showPopup(url);
            }
        });

        // Save button
        document.addEventListener("click", (e) => {
            if (e.target.classList.contains("cds-component-save-button")) {
                e.preventDefault();
                this.saveForm();
            }
        });

        // Expand button
        document.addEventListener("click", (e) => {
            if (e.target.classList.contains("cds-component-expand-button-main")) {
                e.preventDefault();
                document.querySelector(".cds-form-container-assessment-body-left-panel")?.classList.toggle("hide-left");
            }
        });

        // Settings button
    
        document.addEventListener("click", (e) => {
            const button = e.target.closest('.btn-setting');
            if (button) {
                e.preventDefault();
                const panel = document.querySelector(".cds-form-container-assessment-body-right-panel");
                if (panel) {
                    panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
                }
            }
        });

        // Close settings
        document.addEventListener("click", (e) => {
            if (e.target.classList.contains("close-setting")) {
                e.preventDefault();
                const panel = document.querySelector(".cds-form-container-assessment-body-right-panel");
                if (panel) {
                    panel.style.display = 'none';
                }
            }
        });

        // Field block click
        document.addEventListener("click", (e) => {
            if (e.target.closest(".fg-field-block-label")) {
                e.preventDefault();
                this.handleFieldClick(e.target.closest(".fg-field-block-label"));
            }
        });

        // Settings field change
        document.addEventListener("change", (e) => {
            if (e.target.classList.contains("fg-setting-field")) {
                this.handleSettingChange(e.target);
            }
        });

        // Content editable handling
        this.initContentEditable();

        // Add choice
        document.addEventListener("click", (e) => {
            if (e.target.closest(".add-choice")) {
                e.preventDefault();
                this.addChoice(e.target.closest(".add-choice"));
            }
        });

        // Remove choice
        document.addEventListener("click", (e) => {
            if (e.target.closest(".remove-choice")) {
                e.preventDefault();
                this.removeChoice(e.target.closest(".remove-choice"));
            }
        });

        // Step fields click handlers
        document.addEventListener("click", (e) => {
            if (e.target.matches(".step-fields > li > span:first-child") || 
                e.target.matches(".step-fields > li > .label-bottom")) {
                const fieldGroups = e.target.parentElement.querySelector(".fieldGroups");
                if (fieldGroups) {
                    fieldGroups.click();
                }
            }
        });

        // Outside click handler
        document.addEventListener('click', (e) => {
            const parentElement = document.querySelector('.cds-form-container-assessment-body-left-panel');
            if (parentElement && !parentElement.contains(e.target)) {
                document.querySelectorAll(".fg-field-block").forEach(el => el.classList.remove('active'));
            }
        });
    }

    // Initialize content editable
    initContentEditable() {
        document.addEventListener('focus', (e) => {
            if (e.target.hasAttribute('contenteditable')) {
                e.target.dataset.before = e.target.innerHTML;
            }
        }, true);

        ['blur', 'keyup', 'paste', 'input'].forEach(eventType => {
            document.addEventListener(eventType, (e) => {
                if (e.target.hasAttribute('contenteditable')) {
                    if (e.target.dataset.before !== e.target.innerHTML) {
                        e.target.dataset.before = e.target.innerHTML;
                        
                        const text = e.target.textContent;
                        const fieldArea = e.target.closest(".fg-field-area");
                        if (fieldArea) {
                            const fg_index = fieldArea.dataset.index;
                            
                            if (e.target.classList.contains("editable-label")) {
                                const labelInput = document.querySelector(`.fs-settings[data-index="${fg_index}"] .fg-control[data-fieldtype="label"]`);
                                if (labelInput) labelInput.value = text;
                                
                                const labelSpan = document.querySelector(`.fg-field-block[data-index="${fg_index}"] > .fg-label .fg-field-block-label span`);
                                if (labelSpan) labelSpan.innerHTML = text;
                            }
                            
                            if (e.target.classList.contains("editable-desc")) {
                                const descInput = document.querySelector(`.fs-settings[data-index="${fg_index}"] .fg-control[data-fieldtype="shortDesc"]`);
                                if (descInput) descInput.value = text;
                            }
                        }
                    }
                }
            }, true);
        });
    }

    // Handle field click
    handleFieldClick(labelEl) {
        document.querySelectorAll(".fg-field-block").forEach(el => el.classList.remove("active"));
        const fieldBlock = labelEl.closest("li");
        fieldBlock.classList.add("active");

        const fieldType = fieldBlock.dataset.type;
        const label = fieldBlock.dataset.label;
        const icon = fieldBlock.dataset.icon;
        const index = fieldBlock.dataset.index;

        this.showFieldArea(index, fieldType, label, icon);
        this.showFieldSettings(index, fieldType, label);
    }

    // Show field settings
    showFieldSettings(index, fieldType, label) {
        const formSelect = document.querySelector(".form-select");
        let fieldSettings = `<div class="setting-area" data-index="${index}">`;
        fieldSettings += '<div class="right-head">';
        fieldSettings += '<h4 class="fg-head">Field Settings</h4><button type="button" class="close-setting"></button>';
        fieldSettings += '</div>';
        fieldSettings += `<div class="fs-settings" data-index="${index}">`;

        // Add default settings
        if (fieldType !== 'fieldGroups') {
            this.attributes['default'].forEach(val => {
                let cls = '';
                if (val.field !== undefined && val.field === 'hidden') {
                    cls = 'fg-hide';
                }
                
                if (val.type === 'stepHeading' || val.type === 'stepDescription') {
                    if (formSelect && formSelect.value === 'step_form') {
                        cls += ' step-info-field';
                    } else {
                        cls += ' default-hide step-info-field';
                    }
                }

                fieldSettings += `<div class="fg-field-setting ${cls}">`;
                fieldSettings += `<label class="fs-label">${val.label}</label>`;
                
                fieldSettings += this.createSettingField(val, index, label);
                fieldSettings += '</div>';
            });
        }

        // Add field-specific settings
        if (this.attributes[fieldType]) {
            this.attributes[fieldType].forEach(val => {
                let cls = '';
                if (val.field !== undefined && val.field === 'hidden') {
                    cls = 'fg-hide';
                }
                if (val.type === 'stepHeading' || val.type === 'stepDescription') {
                    cls += ' default-hide step-info-field';
                }

                fieldSettings += `<div class="fg-field-setting ${cls}">`;
                fieldSettings += `<label class="fs-label">${val.label}</label>`;
                fieldSettings += this.createSettingField(val, index, label);
                fieldSettings += '</div>';
            });
        }

        fieldSettings += '</div></div>';

        const rightPanel = document.querySelector(".cds-form-container-assessment-body-right-panel");
        
        // Hide all setting areas
        rightPanel.querySelectorAll(".setting-area").forEach(el => el.style.display = 'none');
        
        // Check if settings area already exists
        const existingSettings = rightPanel.querySelector(`.setting-area[data-index="${index}"]`);
        if (!existingSettings) {
            rightPanel.insertAdjacentHTML('beforeend', fieldSettings);
            rightPanel.querySelector(`.setting-area[data-index="${index}"]`).style.display = 'block';
        } else {
            existingSettings.style.display = 'block';
        }

        // Trigger change event for textLimit select if exists
        const textLimitSelect = rightPanel.querySelector(`select[data-fieldtype="textLimit"]`);
        if (textLimitSelect) {
            textLimitSelect.dispatchEvent(new Event('change'));
        }
    }

    // Create setting field HTML
    createSettingField(val, index, label) {
        let defaultValue = '';
        let html = '';

        if (val.value !== undefined && val.value !== '') {
            defaultValue = val.value;
        }

        if (val.field !== undefined) {
            switch (val.field) {
                case 'checkbox':
                    html += `<input type="checkbox" value="1" name="fg_fields[${index}][settings][${val.type}]" data-fieldtype="${val.type}" class="fg-setting-field" />`;
                    break;
                case 'hidden':
                    if (val.type === 'label') {
                        defaultValue = label;
                    }
                    if (val.type === 'name') {
                        defaultValue = "fg_" + this.getRandom(5);
                    }
                    html += `<input type="hidden" value="${defaultValue}" name="fg_fields[${index}][settings][${val.type}]" data-fieldtype="${val.type}" class="fg-control fg-setting-field" />`;
                    break;
                case 'color':
                    html += `<input type="color" value="${defaultValue}" name="fg_fields[${index}][settings][${val.type}]" data-fieldtype="${val.type}" class="fg-control fg-setting-field" />`;
                    break;
                case 'number':
                    html += `<input type="number" value="${defaultValue}" name="fg_fields[${index}][settings][${val.type}]" data-fieldtype="${val.type}" class="fg-control fg-setting-field" />`;
                    break;
                case 'dropdown':
                    html += `<select name="fg_fields[${index}][settings][${val.type}]" data-fieldtype="${val.type}" class="fg-control fg-setting-field">`;
                    if (val.options) {
                        val.options.forEach(option => {
                            const selected = defaultValue === option.value ? 'selected' : '';
                            html += `<option ${selected} value="${option.value}">${option.label}</option>`;
                        });
                    }
                    html += '</select>';
                    break;
            }
        } else {
            html += `<input type="text" value="${defaultValue}" name="fg_fields[${index}][settings][${val.type}]" data-fieldtype="${val.type}" class="fg-control fg-setting-field" />`;
        }

        return html;
    }

    // Handle setting change
    handleSettingChange(field) {
        const fieldtype = field.dataset.fieldtype;
        const settingsEl = field.closest(".fs-settings");
        if (!settingsEl) return;
        
        const fg_index = settingsEl.dataset.index;

        switch (fieldtype) {
            case 'label':
                const editableLabel = document.querySelector(`#editable-${fg_index}`);
                if (editableLabel) editableLabel.innerHTML = field.value;
                const blockLabel = document.querySelector(`#fg-${fg_index} .fg-label span`);
                if (blockLabel) blockLabel.innerHTML = field.value;
                break;
            case 'placeholder':
                const controlEl = document.querySelector(`#fga-${fg_index} .fg-control`);
                if (controlEl) controlEl.setAttribute("placeholder", field.value);
                break;
            case 'class':
                const classControlEl = document.querySelector(`#fga-${fg_index} .fg-control`);
                if (classControlEl) classControlEl.classList.add(field.value);
                break;
            case 'textLimit':
                const addLengthField = settingsEl.querySelector(`input[data-fieldtype="addLength"]`);
                if (addLengthField) {
                    const parentSetting = addLengthField.closest(".fg-field-setting");
                    if (field.value === "none") {
                        parentSetting.style.display = 'none';
                        addLengthField.value = '';
                    } else {
                        parentSetting.style.display = 'block';
                    }
                }
                break;
            case 'allowedTypes':
                const fileInput = document.querySelector(`#file-input-${fg_index}`);
                if (fileInput) fileInput.setAttribute("accept", field.value);
                break;
            case 'multiple':
                const fileInputMultiple = document.querySelector(`#file-input-${fg_index}`);
                if (fileInputMultiple) {
                    if (field.checked) {
                        fileInputMultiple.setAttribute("multiple", "multiple");
                    } else {
                        fileInputMultiple.removeAttribute("multiple");
                    }
                }
                break;
        }
    }

    // Add fields
    addFields(index, fieldType, label, icon) {
        let html = '';
        const actionBtn = `
            <div class="action-fields">
                <button type="button" data-field="${fieldType}" title="Copy Field" class="copy-field"></button>
                <button type="button" class="remove-field"></button>
            </div>
        `;

        html = `<li id="fg-${index}" data-type="${fieldType}" data-label="${label}" data-icon="${icon}" data-index="${index}" class="fg-field-block">`;
        html += `<div class="fg-label"><div class="fg-field-block-label ${fieldType}"><p class="${icon}"></p><span>${label}</span></div>`;
        html += actionBtn + '</div><div class="label-bottom"></div>';
        html += `<input type="hidden" class="field-input" value="${fieldType}" name="fg_fields[${index}][fields]">`;
        
        if (fieldType === 'fieldGroups') {
            html += '<ul class="sortable-list"></ul>';
            html += '<div class="group-fields"></div>';
        }
        
        html += '</li>';

        const activeField = document.querySelector(".fg-field-block.active");
        const activeFieldType = activeField?.dataset.type;
        
        if (fieldType !== 'fieldGroups' && activeFieldType === 'fieldGroups') {
            // Add to active field group
            const sortableList = activeField.querySelector(".sortable-list");
            if (sortableList) {
                sortableList.insertAdjacentHTML('beforeend', html);
                
                const fieldIndex = activeField.dataset.index;
                const groupFieldsEl = activeField.querySelector(".group-fields");
                if (groupFieldsEl && activeField.querySelectorAll(".sortable-list > .fg-field-block:not(.disable-sort-item)").length > 0) {
                    groupFieldsEl.innerHTML = '';
                    let indx = 0;
                    
                    activeField.querySelectorAll(".sortable-list > .fg-field-block:not(.disable-sort-item)").forEach(subField => {
                        const subIndex = subField.dataset.index;
                        const inputfield = `<input type="hidden" class="field-input" name="fg_fields[${fieldIndex}][groupFields][${indx}]" value="${subIndex}" />`;
                        groupFieldsEl.insertAdjacentHTML('beforeend', inputfield);
                        indx++;
                    });
                }
                
                setTimeout(() => {
                    document.querySelector(`.fg-field-block[data-index="${index}"] .fg-field-block-label`)?.click();
                    document.querySelectorAll(".fg-field-block").forEach(el => el.classList.remove("active"));
                    document.querySelector(`.fg-field-block[data-index="${fieldIndex}"]`)?.classList.add("active");
                }, 1000);
            }
        } else {
            // Add to main list
            const selectedFields = document.querySelector(".fg-selected-fields");
            if (selectedFields) {
                selectedFields.insertAdjacentHTML('beforeend', html);
                
                const newFieldBlock = document.querySelector(`#fg-${index}`);
                if (fieldType === 'fieldGroups' && newFieldBlock) {
                    const html2 = `<li class="disable-sort-item" style="visibility:hidden"><div>Item ${this.getRandom(9)}</div></li>`;
                    const sortableList = newFieldBlock.querySelector('.sortable-list');
                    if (sortableList) {
                        sortableList.insertAdjacentHTML('beforeend', html2);
                    }
                    newFieldBlock.classList.add("droppable");
                }
                
                setTimeout(() => {
                    document.querySelector(`.fg-field-block[data-index="${index}"] .fg-field-block-label`)?.click();
                }, 1000);
            }
        }
    }

    // Copy field
    copyField(button, field) {
        const fieldBlock = button.closest(`.fg-field-block[data-type="${field}"]`);
        if (!fieldBlock) return;

        const old_index = fieldBlock.dataset.index;
        const new_index = this.getRandom(9);

        if (field === 'fieldGroups') {
            // Complex copy for field groups
            this.copyFieldGroup(fieldBlock, old_index, new_index);
        } else {
            // Simple field copy
            this.copySimpleField(fieldBlock, old_index, new_index);
        }

        // Update field groups order after copy
        setTimeout(() => {
            this.updateFieldGroupsOrder();
        }, 100);

        // Click on the new field
        setTimeout(() => {
            document.querySelector(`#fg-${new_index} .fg-field-block-label`)?.click();
        }, 200);
    }

    // Copy simple field
    copySimpleField(fieldBlock, old_index, new_index) {
        // Clone field block
        const cloneField = fieldBlock.cloneNode(true);
        cloneField.setAttribute("data-index", new_index);
        cloneField.setAttribute("id", `fg-${new_index}`);
        
        // Update all references from old to new index
        cloneField.innerHTML = cloneField.innerHTML.replace(new RegExp(old_index, 'g'), new_index);
        
        // Insert after original
        fieldBlock.insertAdjacentElement('afterend', cloneField);

        // Clone field area
        const fieldArea = document.querySelector(`#fga-${old_index}`);
        if (fieldArea) {
            const cloneFieldArea = fieldArea.cloneNode(true);
            cloneFieldArea.setAttribute("id", `fga-${new_index}`);
            cloneFieldArea.setAttribute("data-index", new_index);
            cloneFieldArea.innerHTML = cloneFieldArea.innerHTML.replace(new RegExp(old_index, 'g'), new_index);
            fieldArea.insertAdjacentElement('afterend', cloneFieldArea);

            // Re-initialize file upload if it's a file upload field
            if (fieldBlock.dataset.type === 'fileUpload') {
                setTimeout(() => {
                    this.initializeFileUploadField(new_index);
                }, 100);
            }
        }

        // Clone settings area
        const settingsArea = document.querySelector(`.setting-area[data-index="${old_index}"]`);
        if (settingsArea) {
            const cloneSettingsArea = settingsArea.cloneNode(true);
            cloneSettingsArea.setAttribute("data-index", new_index);
            cloneSettingsArea.innerHTML = cloneSettingsArea.innerHTML.replace(new RegExp(old_index, 'g'), new_index);
            
            // Copy form values
            settingsArea.querySelectorAll(".fg-setting-field").forEach((field, index) => {
                const newField = cloneSettingsArea.querySelectorAll(".fg-setting-field")[index];
                if (newField) {
                    if (field.type === 'checkbox') {
                        newField.checked = field.checked;
                    } else {
                        newField.value = field.value;
                    }
                }
            });
            
            settingsArea.insertAdjacentElement('afterend', cloneSettingsArea);
        }
    }

    // Copy field group (complex)
    copyFieldGroup(fieldBlock, old_index, new_index) {
        // Clone the main field group
        const cloneField = fieldBlock.cloneNode(true);
        cloneField.setAttribute("data-index", new_index);
        cloneField.setAttribute("id", `fg-${new_index}`);
        
        // Clear sortable list in clone
        const cloneSortableList = cloneField.querySelector(".sortable-list");
        if (cloneSortableList) {
            cloneSortableList.innerHTML = '';
        }
        
        // Update references
        cloneField.innerHTML = cloneField.innerHTML.replace(new RegExp(old_index, 'g'), new_index);
        
        // Insert after original
        fieldBlock.insertAdjacentElement('afterend', cloneField);

        // Clone field area
        const fieldArea = document.querySelector(`#fga-${old_index}`);
        if (fieldArea) {
            const cloneFieldArea = fieldArea.cloneNode(true);
            cloneFieldArea.setAttribute("id", `fga-${new_index}`);
            cloneFieldArea.setAttribute("data-index", new_index);
            cloneFieldArea.innerHTML = cloneFieldArea.innerHTML.replace(new RegExp(old_index, 'g'), new_index);
            fieldArea.insertAdjacentElement('afterend', cloneFieldArea);
        }

        // Clone settings
        const settingsArea = document.querySelector(`.setting-area[data-index="${old_index}"]`);
        if (settingsArea) {
            const cloneSettingsArea = settingsArea.cloneNode(true);
            cloneSettingsArea.setAttribute("data-index", new_index);
            cloneSettingsArea.innerHTML = cloneSettingsArea.innerHTML.replace(new RegExp(old_index, 'g'), new_index);
            
            // Copy form values
            this.copyFormValues(settingsArea, cloneSettingsArea);
            
            settingsArea.insertAdjacentElement('afterend', cloneSettingsArea);
        }

        // Copy disable-sort-items
        fieldBlock.querySelectorAll(".sortable-list .disable-sort-item").forEach(item => {
            const cloneItem = item.cloneNode(true);
            cloneSortableList?.appendChild(cloneItem);
        });

        // Copy sub-fields
        fieldBlock.querySelectorAll(".sortable-list .fg-field-block").forEach(subField => {
            const li_old_index = subField.dataset.index;
            const li_new_index = this.getRandom(9);
            
            // Clone sub-field
            const cloneSubField = subField.cloneNode(true);
            cloneSubField.setAttribute("data-index", li_new_index);
            cloneSubField.setAttribute("id", `fg-${li_new_index}`);
            cloneSubField.innerHTML = cloneSubField.innerHTML.replace(new RegExp(li_old_index, 'g'), li_new_index);
            
            cloneSortableList?.appendChild(cloneSubField);

            // Clone sub-field area
            const subFieldArea = document.querySelector(`#fga-${li_old_index}`);
            if (subFieldArea) {
                const cloneSubFieldArea = subFieldArea.cloneNode(true);
                cloneSubFieldArea.setAttribute("id", `fga-${li_new_index}`);
                cloneSubFieldArea.setAttribute("data-index", li_new_index);
                cloneSubFieldArea.innerHTML = cloneSubFieldArea.innerHTML.replace(new RegExp(li_old_index, 'g'), li_new_index);
                subFieldArea.insertAdjacentElement('afterend', cloneSubFieldArea);

                // Re-initialize file upload if needed
                if (subField.dataset.type === 'fileUpload') {
                    setTimeout(() => {
                        this.initializeFileUploadField(li_new_index);
                    }, 100);
                }
            }

            // Clone sub-field settings
            const subSettingsArea = document.querySelector(`.setting-area[data-index="${li_old_index}"]`);
            if (subSettingsArea) {
                const cloneSubSettingsArea = subSettingsArea.cloneNode(true);
                cloneSubSettingsArea.setAttribute("data-index", li_new_index);
                cloneSubSettingsArea.innerHTML = cloneSubSettingsArea.innerHTML.replace(new RegExp(li_old_index, 'g'), li_new_index);
                
                this.copyFormValues(subSettingsArea, cloneSubSettingsArea);
                
                subSettingsArea.insertAdjacentElement('afterend', cloneSubSettingsArea);
            }
        });
    }

    // Copy form values between elements
    copyFormValues(source, target) {
        source.querySelectorAll(".fg-setting-field").forEach((field, index) => {
            const targetField = target.querySelectorAll(".fg-setting-field")[index];
            if (targetField) {
                if (field.type === 'checkbox') {
                    targetField.checked = field.checked;
                } else {
                    targetField.value = field.value;
                }
            }
        });
    }

    // Add choice option
    addChoice(button) {
        const fieldArea = button.closest(".fg-field-area");
        if (!fieldArea) return;
        
        const index = fieldArea.dataset.index;
        const rndindex = this.getRandom(5);
        const options = `<div class="option-value"><input type="text" name="fg_fields[${index}][settings][options][${rndindex}]" class="fg-control fg-options" placeholder="Enter Option Value"><a href="javascript:;" class="remove-choice"><i class="fa fa-times"></i></a></div>`;
        
        const multipleOptions = fieldArea.querySelector(".multiple-options");
        if (multipleOptions) {
            multipleOptions.insertAdjacentHTML('beforeend', options);
        }
    }

    // Remove choice option
    removeChoice(button) {
        const multipleOptions = button.closest(".multiple-options");
        if (multipleOptions && multipleOptions.querySelectorAll(".option-value").length <= 1) {
            alert("Cannot delete the option. Need at least one option");
            return false;
        }
        button.closest(".option-value")?.remove();
    }

    // Save form
    saveForm() {
        // Remove any existing error messages
        document.querySelectorAll(".fg-error").forEach(el => el.remove());

        // Check if fields are selected
        if (document.querySelectorAll(".fg-selected-fields .fg-field-block").length === 0) {
            alert("Please select the fields to save");
            return;
        }

        // Validate form name
        const formNameInput = document.querySelector("input[name='form_name']");
        if (formNameInput && formNameInput.value === '') {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fg-error';
            errorDiv.textContent = 'The field is mandatory';
            formNameInput.closest(".fg-group")?.appendChild(errorDiv);
            return false;
        }

        // Remove error classes
        document.querySelectorAll(".fg-field-block").forEach(el => el.classList.remove("fg-error-field"));

        // Validate options
        let validate = true;
        document.querySelectorAll(".fg-options").forEach(option => {
            if (option.value === '') {
                validate = false;
                const index = option.closest(".fg-field-area").dataset.index;
                
                const errorDiv = document.createElement('div');
                errorDiv.className = 'fg-error';
                errorDiv.textContent = 'The field is mandatory';
                option.closest(".option-value")?.appendChild(errorDiv);
                
                document.querySelector(`.fg-field-block[data-index="${index}"]`)?.classList.add("fg-error-field");
            }
        });

        // Validate field groups
        document.querySelectorAll(".step-fields > li[data-type='fieldGroups']").forEach(fieldGroup => {
            const index = fieldGroup.dataset.index;
            if (fieldGroup.querySelectorAll(".sortable-list > li").length <= 1) {
                document.querySelector(`.fg-field-block[data-index="${index}"]`)?.classList.add("fg-error-field");
                
                const html = '<div class="fg-error">Field group required some fields in it.</div>';
                const fieldArea = document.querySelector(`.fg-field-area[data-index="${index}"]`);
                if (fieldArea) {
                    fieldArea.insertAdjacentHTML('beforeend', html);
                }
                validate = false;
            }
        });

        if (!validate) {
            return false;
        }

        // Disable save button
        const saveButton = document.querySelector(".cds-component-save-button");
        if (saveButton) {
            saveButton.setAttribute("disabled", "disabled");
        }

        // Get form element
        const formElement = document.querySelector(".fg-form");
        
        // Prepare request based on data format
        let fetchOptions = {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        // Add CSRF token if provided
        if (this.csrfToken) {
            fetchOptions.headers['X-CSRF-TOKEN'] = this.csrfToken;
        }

        // Prepare form data
        const formData = new FormData(formElement);

        // Add file data for file upload fields
        Object.keys(this.fileUploaders).forEach(index => {
            const uploader = this.fileUploaders[index];
            if (uploader && uploader.files.length > 0) {
                const fieldName = document.querySelector(`.fs-settings[data-index="${index}"] .fg-control[data-fieldtype="name"]`)?.value;
                if (fieldName) {
                    uploader.files.forEach((file, i) => {
                        formData.append(`fg_fields[${index}][files][${i}]`, file);
                    });
                }
            }
        });

        // Handle different data formats
        switch (this.dataFormat) {
            case 'json':
                // Send as JSON
                const jsonData = this.convertFormToJSON(formElement);
                
                // Add file info to JSON
                Object.keys(this.fileUploaders).forEach(index => {
                    const uploader = this.fileUploaders[index];
                    if (uploader && uploader.files.length > 0) {
                        if (!jsonData.fg_fields[index]) {
                            jsonData.fg_fields[index] = {};
                        }
                        jsonData.fg_fields[index].fileNames = uploader.files.map(f => f.name);
                    }
                });
                
                fetchOptions.headers['Content-Type'] = 'application/json';
                fetchOptions.body = JSON.stringify(jsonData);
                
                if (this.debugMode) {
                    console.log('Sending JSON data:', jsonData);
                }
                break;
                
            case 'multipart':
                // Send as FormData (multipart/form-data)
                fetchOptions.body = formData;
                
                // Check for _token field (Laravel CSRF)
                if (!this.csrfToken && formData.get('_token')) {
                    fetchOptions.headers['X-CSRF-TOKEN'] = formData.get('_token');
                }
                
                if (this.debugMode) {
                    console.log('Sending FormData:');
                    for (let [key, value] of formData.entries()) {
                        console.log(key, value);
                    }
                }
                break;
                
            case 'form':
            default:
                // For file uploads, we need to use multipart
                if (Object.keys(this.fileUploaders).some(index => this.fileUploaders[index].files.length > 0)) {
                    fetchOptions.body = formData;
                } else {
                    // Send as URL-encoded form data (default)
                    const params = new URLSearchParams(formData);
                    fetchOptions.headers['Content-Type'] = 'application/x-www-form-urlencoded';
                    fetchOptions.body = params.toString();
                }
                
                // Check for _token field (Laravel CSRF)
                if (!this.csrfToken && formData.get('_token')) {
                    fetchOptions.headers['X-CSRF-TOKEN'] = formData.get('_token');
                }
                
                if (this.debugMode) {
                    if (fetchOptions.body instanceof FormData) {
                        console.log('Sending FormData with files');
                    } else {
                        console.log('Sending URL-encoded data:', fetchOptions.body);
                    }
                }
                break;
        }
        
        // Send request
        fetch(this.saveUrl, fetchOptions)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(response => {
            if (this.debugMode) {
                console.log('Server response:', response);
            }
            
            if (saveButton) {
                saveButton.removeAttribute("disabled");
            }
            
            if (response.status === true) {
                if (this.redirectBack !== '') {
                    window.location.href = this.redirectBack;
                } else if (response.redirect_back !== undefined) {
                    window.location.href = response.redirect_back;
                } else {
                    location.reload();
                }
            } else {
                // Handle error response
                alert(response.message || 'Error saving form');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (saveButton) {
                saveButton.removeAttribute("disabled");
            }
            alert('An error occurred while saving the form. Please check the console for details.');
        });
    }

    // Convert form to JSON
    convertFormToJSON(form) {
        const formData = new FormData(form);
        const json = {};
        
        // Group all form entries by their base key
        const entries = {};
        for (const [key, value] of formData.entries()) {
            if (!entries[key]) {
                entries[key] = [];
            }
            entries[key].push(value);
        }
        
        // Process each entry
        for (const [key, values] of Object.entries(entries)) {
            // Handle nested keys like fg_fields[index][settings][name]
            const keys = key.match(/[^\[\]]+/g);
            
            if (keys) {
                // If multiple values for the same key (like checkboxes), use array
                const value = values.length > 1 ? values : values[0];
                this.setNestedValue(json, keys, value);
            } else {
                json[key] = values.length > 1 ? values : values[0];
            }
        }
        
        return json;
    }

    // Set nested value in object
    setNestedValue(obj, keys, value) {
        let current = obj;
        
        for (let i = 0; i < keys.length - 1; i++) {
            const key = keys[i];
            const nextKey = keys[i + 1];
            
            // Determine if next level should be array or object
            if (!current[key]) {
                // If next key is a number or current level expects array notation
                current[key] = (!isNaN(nextKey) || nextKey === '') ? [] : {};
            }
            
            current = current[key];
        }
        
        const lastKey = keys[keys.length - 1];
        current[lastKey] = value;
    }

    // Get form data for debugging
    getFormData(format = 'all') {
        const formElement = document.querySelector(".fg-form");
        if (!formElement) {
            console.error('Form element not found');
            return null;
        }

        const result = {};

        if (format === 'all' || format === 'form') {
            const formData = new FormData(formElement);
            const urlEncoded = new URLSearchParams(formData).toString();
            result.urlEncoded = urlEncoded;
        }

        if (format === 'all' || format === 'json') {
            const jsonData = this.convertFormToJSON(formElement);
            result.json = jsonData;
        }

        if (format === 'all' || format === 'multipart') {
            const formData = new FormData(formElement);
            const multipartData = {};
            for (let [key, value] of formData.entries()) {
                if (!multipartData[key]) {
                    multipartData[key] = [];
                }
                multipartData[key].push(value);
            }
            result.multipart = multipartData;
        }

        // Add file upload info
        result.fileUploaders = {};
        Object.keys(this.fileUploaders).forEach(index => {
            const uploader = this.fileUploaders[index];
            result.fileUploaders[index] = {
                fileCount: uploader.files.length,
                files: uploader.files.map(f => ({
                    name: f.name,
                    size: f.size,
                    type: f.type
                }))
            };
        });

        console.log('Form Data:', result);
        return result;
    }

    // Get current form configuration as JSON
    exportConfiguration() {
        const formElement = document.querySelector(".fg-form");
        if (!formElement) {
            console.error('Form not found');
            return null;
        }

        const config = {
            form_name: formElement.querySelector('input[name="form_name"]')?.value || '',
            form_type: formElement.querySelector('select[name="form_type"]')?.value || '',
            fields: {}
        };

        // Export all fields configuration
        document.querySelectorAll(".fg-selected-fields > .fg-field-block").forEach(fieldBlock => {
            const index = fieldBlock.dataset.index;
            const fieldType = fieldBlock.dataset.type;
            
            config.fields[index] = {
                index: index,
                fields: fieldType,
                settings: {},
                groupFields: []
            };

            // Get settings for this field
            const settingArea = document.querySelector(`.setting-area[data-index="${index}"]`);
            if (settingArea) {
                settingArea.querySelectorAll('.fg-setting-field').forEach(field => {
                    const fieldName = field.name.match(/\[settings\]\[([^\]]+)\]/);
                    if (fieldName && fieldName[1]) {
                        if (field.type === 'checkbox') {
                            config.fields[index].settings[fieldName[1]] = field.checked ? '1' : '0';
                        } else {
                            config.fields[index].settings[fieldName[1]] = field.value;
                        }
                    }
                });
            }

            // Get options for dropdowns, checkboxes, radios
            const fieldArea = document.querySelector(`.fg-field-area[data-index="${index}"]`);
            if (fieldArea && ['dropDown', 'checkbox', 'radio'].includes(fieldType)) {
                config.fields[index].settings.options = {};
                fieldArea.querySelectorAll('.fg-options').forEach((option, idx) => {
                    config.fields[index].settings.options[idx] = option.value;
                });
            }

            // Handle field groups
            if (fieldType === 'fieldGroups') {
                fieldBlock.querySelectorAll('.sortable-list > .fg-field-block').forEach(subField => {
                    config.fields[index].groupFields.push(subField.dataset.index);
                });
            }
        });

        return config;
    }

    // Show popup
    showPopup(url) {
        // Implement your popup logic here
        window.open(url, 'preview', 'width=800,height=600');
    }

    // Get random number
    getRandom(length) {
        return Math.floor(Math.pow(10, length - 1) + Math.random() * 9 * Math.pow(10, length - 1));
    }

    // Process default JSON
    processDefaultJson(defaultJsonString) {
        const defaultJson = JSON.parse(defaultJsonString);
        let lindex;

        // First pass: create all fields
        Object.entries(defaultJson).forEach(([key, val]) => {
            // Handle legacy fgDropzone type
            let fieldType = val.fields;
            if (fieldType === 'fgDropzone') {
                fieldType = 'fileUpload';
            }
            
            const field = document.querySelector(`.fg-field[data-field="${fieldType}"]`);
            if (field) {
                const label = field.dataset.label;
                const icon = field.dataset.icon;
                lindex = val.index;
                this.addFields(lindex, fieldType, label, icon);
            }
        });

        // Second pass: organize field groups
        setTimeout(() => {
            if (document.querySelectorAll(".fg-selected-fields .fg-field-block[data-type='fieldGroups']").length > 0) {
                Object.entries(defaultJson).forEach(([key, val]) => {
                    if (val.fields === 'fieldGroups' && val.groupFields) {
                        const parentField = document.querySelector(`#fg-${val.index}`);
                        if (parentField) {
                            val.groupFields.forEach((groupFieldId, i) => {
                                const fieldToMove = document.querySelector(`#fg-${groupFieldId}`);
                                const sortableList = parentField.querySelector(".sortable-list");
                                if (fieldToMove && sortableList) {
                                    sortableList.appendChild(fieldToMove);
                                    
                                    const inputfield = `<input type="hidden" class="field-input" name="fg_fields[${val.index}][groupFields][${i}]" value="${groupFieldId}" />`;
                                    const groupFieldsContainer = parentField.querySelector(".group-fields");
                                    if (groupFieldsContainer) {
                                        groupFieldsContainer.insertAdjacentHTML('beforeend', inputfield);
                                    }
                                }
                            });
                        }
                    }
                });
            }

            // Apply settings
            this.applyDefaultSettings(defaultJson);
        }, 1500);
    }

    // Apply default settings
    applyDefaultSettings(defaultJson) {
        const defaultSettings = Object.values(defaultJson);
        
        document.querySelectorAll(".cds-form-container-assessment-body-right-panel .setting-area").forEach((settingArea, i) => {
            if (!defaultSettings[i]) return;
            
            const index = settingArea.dataset.index;
            let fieldtype = defaultSettings[i].fields;
            
            // Handle legacy fgDropzone type
            if (fieldtype === 'fgDropzone') {
                fieldtype = 'fileUpload';
            }
            
            const settings = defaultSettings[i].settings;

            Object.entries(settings).forEach(([key, val]) => {
                switch (key) {
                    case 'label':
                        const editableLabel = document.querySelector(`#editable-${index}`);
                        if (editableLabel) editableLabel.innerHTML = val;
                        const blockLabel = document.querySelector(`.fg-field-block[data-index="${index}"] .fg-label span`);
                        if (blockLabel) blockLabel.innerHTML = val;
                        break;
                    case 'shortDesc':
                        const description = document.querySelector(`#description-${index}`);
                        if (description) description.innerHTML = val;
                        break;
                    case 'options':
                        if (['dropDown', 'checkbox', 'radio'].includes(fieldtype)) {
                            const multipleOptions = document.querySelector(`.fg-field-area[data-index="${index}"] .multiple-options`);
                            if (multipleOptions) {
                                multipleOptions.innerHTML = '';
                                Object.values(settings.options).forEach(optVal => {
                                    const rndindex = this.getRandom(5);
                                    const options = `<div class="option-value"><input value="${optVal}" type="text" name="fg_fields[${index}][settings][options][${rndindex}]" class="fg-control fg-options" placeholder="Enter Option Value"><a href="javascript:;" class="remove-choice"><i class="fa fa-times"></i></a></div>`;
                                    multipleOptions.insertAdjacentHTML('beforeend', options);
                                });
                            }
                        }
                        break;
                    case 'font_size':
                    case 'textLimit':
                    case 'allowedTypes':
                    case 'maxFileSize':
                    case 'maxFiles':
                        const selectField = document.querySelector(`select[name='fg_fields[${index}][settings][${key}]']`);
                        if (selectField) {
                            selectField.value = val;
                        }
                        const inputField = document.querySelector(`input[name='fg_fields[${index}][settings][${key}]']`);
                        if (inputField && inputField.type !== 'checkbox') {
                            inputField.value = val;
                        }
                        break;
                    case 'required':
                    case 'multiple':
                        const checkbox = document.querySelector(`input[name='fg_fields[${index}][settings][${key}]']`);
                        if (checkbox && (val === 'on' || val === 1 || val === '1')) {
                            checkbox.checked = true;
                        }
                        break;
                    default:
                        const defaultInput = document.querySelector(`input[name='fg_fields[${index}][settings][${key}]']`);
                        if (defaultInput) {
                            defaultInput.value = val;
                        }
                        break;
                }
            });
        });
    }

    // Update form type display
    updateFormTypeDisplay(formType) {
        const infoDiv = document.getElementById("infoDiv");
        if (infoDiv) {
            if (formType === "step_form") {
                infoDiv.textContent = "Step Form";
            } else if (formType === "single_form") {
                infoDiv.textContent = "Single Form";
            }
            infoDiv.style.display = formType ? 'block' : 'none';
        }
    }
}

// Form Renderer Class
class FormRenderer {
    constructor() {
        this.formJson = null;
        this.formType = '';
        this.saveUrl = '';
        this.redirectBack = '';
        this.uploadCount = 0;
        this.csrfToken = null;
        this.fileUploaders = {}; // Store file uploader instances
    }

    // Initialize form renderer
    formRender(element, params = {}) {
        this.formJson = JSON.parse(params.formJson);
        this.formType = params.formType;
        this.saveUrl = params.saveUrl || '';
        this.redirectBack = params.redirectBack || '';
        this.csrfToken = params.csrfToken || null;

        let renderHtml = '<div class="cds-assessment-form-render"><div id="fgr-form" class="cds-assessment-form-render-container">';
        renderHtml += '<div class="fgr-render cds-assessment-form-render-wrap">';
        renderHtml += '<div class="fgr-render-inner cds-assessment-form-render-body">';
        
        // Identify group fields
        const groupFieldsIds = [];
        Object.keys(this.formJson).forEach(key => {
            if (this.formJson[key]['fields'] === 'fieldGroups') {
                const groupFields = this.formJson[key]['groupFields'];
                for (let i = 0; i < groupFields.length; i++) {
                    if (!groupFieldsIds.includes(groupFields[i])) {
                        groupFieldsIds.push(groupFields[i]);
                    }
                }
            }
        });

        // Add step form UI if needed
        if (this.formType === 'step_form') {
            renderHtml += '<div class="cds-assessment-form-render-body-left-panel">';
            renderHtml += '<div class="form-steps">';
            renderHtml += '</div>';
            renderHtml += '</div>';
        }

        renderHtml += '<div class="cds-assessment-form-render-body-right-panel"><h4 class="step-heading">Step Heading</h4>';
        renderHtml += '<div class="forms-area cds-fields-groups">';
        
        // Render fields
        Object.keys(this.formJson).forEach(key => {
            let flag = 1;
            for (let i = 0; i < groupFieldsIds.length; i++) {
                if (this.formJson[key]['index'] === groupFieldsIds[i]) {
                    flag = 0;
                }
            }
            if (flag === 1) {
                if (this.formJson[key]['fields'] === 'fieldGroups') {
                    renderHtml += this.renderfieldGroups(this.formJson, key);
                } else {
                    renderHtml += this.renderField(this.formJson, key);
                }
            }
        });

        renderHtml += '</div>';
        renderHtml += '</div>';
        renderHtml += '</div>';
        renderHtml += '</div></div></div>';

        element.innerHTML = renderHtml;

        // Initialize step form
        if (this.formType === 'step_form') {
            this.initializeStepForm();
        } else {
            this.initializeSingleForm();
        }

        // Apply default values if provided
        if (params.defaultValues !== undefined && params.defaultValues !== '') {
            this.applyDefaultValues(params.defaultValues);
        }

        // Initialize plugins
        this.initializeDatepicker();
        this.initializeFileUploads();
        this.initializeGoogleMaps();

        // Bind events
        this.bindEvents();
    }

    // Initialize step form
    initializeStepForm() {
        let step_html = '';
        const total_step = document.querySelectorAll(".forms-area > .fg-form-group").length;
        let i = 1;
        
        document.querySelectorAll(".forms-area > .fg-form-group").forEach(formGroup => {
            step_html += `<div class="step-count" data-step="${i}">`;
            step_html += '<div class="stepno">';
            step_html += `<span>${i}</span>`;
            step_html += '</div>';
            step_html += `<div class="step-info"><span>${formGroup.dataset.stephead || ''}</span>`;
            step_html += '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path class="arrow-svg-icon" d="M6.71289 14.9405L11.6029 10.0505C12.1804 9.47305 12.1804 8.52805 11.6029 7.95055L6.71289 3.06055" stroke="black" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            step_html += '</div>';
            step_html += '</div>';
            i++;
        });
        
        const formSteps = document.querySelector(".form-steps");
        if (formSteps) {
            formSteps.innerHTML = step_html;
        }

        // Setup step form UI
        document.querySelector(".fgr-render-inner")?.classList.add("step-form");
        
        // Hide all form groups except first
        document.querySelectorAll(".forms-area > .fg-form-group").forEach((group, index) => {
            group.style.display = 'none';
            group.setAttribute("data-step", index + 1);
        });

        // Show first step
        const firstGroup = document.querySelector(".fgr-render-inner .forms-area > .fg-form-group:first-child");
        if (firstGroup) {
            firstGroup.classList.add("step-active");
            firstGroup.style.display = 'block';
        }
        
        document.querySelector(".fgr-render-inner .form-steps > .step-count:first-child")?.classList.add("stepno-active");

        // Add navigation buttons
        const stepBtn = `
            <div class="steps-btn-area">
                <button type="button" class="prev-btn step-btn">Prev</button>
                <button type="button" class="next-btn step-btn">Next</button>
                <button type="button" class="finish-btn save-form step-btn">Save</button>
            </div>
        `;
        
        document.querySelector(".fgr-render")?.insertAdjacentHTML('beforeend', stepBtn);

        // Initial button state
        const prevBtn = document.querySelector(".prev-btn");
        const nextBtn = document.querySelector(".next-btn");
        const finishBtn = document.querySelector(".finish-btn");
        
        if (prevBtn) prevBtn.style.display = 'none';
        if (total_step === 1) {
            if (nextBtn) nextBtn.style.display = 'none';
            if (finishBtn) finishBtn.style.display = 'block';
        } else {
            if (finishBtn) finishBtn.style.display = 'none';
        }
    }

    // Initialize single form
    initializeSingleForm() {
        const stepBtn = `
            <div class="save-btn-area">
                <button type="button" class="finish-btn save-form">Save</button>
            </div>
        `;
        document.querySelector(".fgr-render")?.insertAdjacentHTML('beforeend', stepBtn);
    }

    // Bind events
    bindEvents() {
        // Previous button
        document.addEventListener("click", (e) => {
            if (e.target.matches("#fgr-form .prev-btn")) {
                this.handlePrevStep();
            }
        });

        // Next button
        document.addEventListener("click", (e) => {
            if (e.target.matches("#fgr-form .next-btn")) {
                this.handleNextStep();
            }
        });

        // Save form
        document.addEventListener("click", (e) => {
            if (e.target.matches("#fgr-form .save-form")) {
                this.submitForm('save');
            }
        });
    }

    // Handle previous step
    handlePrevStep() {
        const activeStep = document.querySelector("#fgr-form .step-active");
        if (!activeStep) return;
        
        const currentStep = parseInt(activeStep.dataset.step);
        const prevStep = currentStep - 1;
        
        if (prevStep >= 1) {
            // Hide current step
            activeStep.classList.remove("step-active");
            activeStep.style.display = 'none';
            
            // Show previous step
            const prevStepEl = document.querySelector(`#fgr-form .fg-form-group[data-step="${prevStep}"]`);
            if (prevStepEl) {
                prevStepEl.classList.add("step-active");
                prevStepEl.style.display = 'block';
            }
            
            // Update step indicators
            document.querySelectorAll("#fgr-form .step-count").forEach(el => el.classList.remove("stepno-active"));
            document.querySelector(`#fgr-form .step-count[data-step="${prevStep}"]`)?.classList.add("stepno-active");
            
            // Update button visibility
            const prevBtn = document.querySelector(".prev-btn");
            const nextBtn = document.querySelector(".next-btn");
            const finishBtn = document.querySelector(".finish-btn");
            
            if (prevStep === 1 && prevBtn) {
                prevBtn.style.display = 'none';
            }
            
            if (nextBtn) nextBtn.style.display = 'block';
            if (finishBtn) finishBtn.style.display = 'none';
            
            // Scroll to top
            window.scrollTo({ top: document.querySelector(".fgr-render")?.offsetTop || 0, behavior: 'smooth' });
        }
    }

    // Handle next step
    handleNextStep() {
        const activeStep = document.querySelector("#fgr-form .step-active");
        if (!activeStep) return;
        
        const currentStep = parseInt(activeStep.dataset.step);
        
        // Validate current step
        if (!this.validate(this.formType, currentStep)) {
            return false;
        }
        
        const totalSteps = document.querySelectorAll("#fgr-form .form-steps .step-count").length;
        const nextStep = currentStep + 1;
        
        if (nextStep <= totalSteps) {
            // Hide current step
            activeStep.classList.remove("step-active");
            activeStep.style.display = 'none';
            
            // Show next step
            const nextStepEl = document.querySelector(`#fgr-form .fg-form-group[data-step="${nextStep}"]`);
            if (nextStepEl) {
                nextStepEl.classList.add("step-active");
                nextStepEl.style.display = 'block';
            }
            
            // Update step indicators
            document.querySelectorAll("#fgr-form .step-count").forEach(el => el.classList.remove("stepno-active", "stepno-complete"));
            document.querySelector(`#fgr-form .step-count[data-step="${nextStep}"]`)?.classList.add("stepno-active");
            
            // Mark completed steps
            for (let i = 1; i < nextStep; i++) {
                document.querySelector(`#fgr-form .step-count[data-step="${i}"]`)?.classList.add("stepno-complete");
            }
            
            // Update button visibility
            const prevBtn = document.querySelector(".prev-btn");
            const nextBtn = document.querySelector(".next-btn");
            const finishBtn = document.querySelector(".finish-btn");
            
            if (prevBtn) prevBtn.style.display = 'block';
            
            if (nextStep === totalSteps) {
                if (nextBtn) nextBtn.style.display = 'none';
                if (finishBtn) finishBtn.style.display = 'block';
            }
            
            // Scroll to top
            window.scrollTo({ top: document.querySelector(".fgr-render")?.offsetTop || 0, behavior: 'smooth' });
        }
    }

    // Render field
    renderField(formJson, key) {
        const fieldData = formJson[key];
        
        // Handle legacy fgDropzone type
        let fieldType = fieldData['fields'];
        if (fieldType === 'fgDropzone') {
            fieldType = 'fileUpload';
        }
        
        switch (fieldType) {
            case 'textInput':
                return this.renderTextInput(fieldData['settings'], key);
            case 'addressInput':
                return this.renderAddressInput(fieldData['settings'], key);
            case 'fileUpload':
                return this.renderFileUpload(fieldData['settings'], key);
            case 'numberInput':
                return this.renderNumberInput(fieldData['settings'], key);
            case 'emailInput':
                return this.renderEmailInput(fieldData['settings'], key);
            case 'textarea':
                return this.renderTextarea(fieldData['settings'], key);
            case 'url':
                return this.renderUrlInput(fieldData['settings'], key);
            case 'dropDown':
                return this.renderDropDown(fieldData['settings'], key);
            case 'checkbox':
                return this.renderCheckbox(fieldData['settings'], key);
            case 'radio':
                return this.renderRadio(fieldData['settings'], key);
            case 'dateInput':
                return this.renderDateInput(fieldData['settings'], key);
            default:
                return '';
        }
    }

    // Render text input
    renderTextInput(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        const placeholder = settings.placeholder !== null ? settings.placeholder : '';
        const maxlength = settings.maxlength !== null ? `maxlength="${settings.maxlength}"` : '';
        
        html += `<input type="text" ${required} ${maxlength} name="fg_field[${settings.name}]" data-field="textbox" class="fgr-control ${settings.name}" placeholder="${placeholder}">`;
        html += '</div>';
        
        return html;
    }

    // Render file upload
    renderFileUpload(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        const txtindex = this.getRandom(10);
        const multiple = settings.multiple !== undefined && settings.multiple == 1 ? 'multiple' : '';
        const allowedTypes = settings.allowedTypes || '.jpg,.jpeg,.png,.pdf,.doc,.docx';
        const maxFileSize = settings.maxFileSize || '5';
        const maxFiles = settings.maxFiles || '10';
        
        html += `
            <div class="custom-file-upload-render" data-index="${txtindex}" data-name="${settings.name}">
                <input type="file" 
                    id="file-input-render-${txtindex}" 
                    class="file-input-hidden ${required ? 'required-file' : ''}" 
                    ${multiple} 
                    accept="${allowedTypes}"
                    data-max-size="${maxFileSize}"
                    data-max-files="${maxFiles}"
                    ${required}
                />
                <div class="file-upload-dropzone">
                    <div class="upload-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                    </div>
                    <p class="upload-text">Drag and drop your files here</p>
                    <p class="upload-subtext">or click to browse your files</p>
                    <button type="button" class="browse-btn">Browse Files</button>
                    <p class="upload-info">Allowed: ${allowedTypes} | Max size: ${maxFileSize}MB</p>
                </div>
                <div class="file-list" id="file-list-render-${txtindex}"></div>
                <input type="hidden" name="fg_field[${settings.name}]" class="fgr-control fg-files ${settings.name}" data-field="fileupload" />
            </div>
        `;
        
        html += '</div>';
        
        return html;
    }

    // Initialize file uploads
    initializeFileUploads() {
        document.querySelectorAll('.custom-file-upload-render').forEach(uploadArea => {
            const index = uploadArea.dataset.index;
            const fieldName = uploadArea.dataset.name;
            const fileInput = uploadArea.querySelector(`#file-input-render-${index}`);
            const dropzone = uploadArea.querySelector('.file-upload-dropzone');
            const browseBtn = uploadArea.querySelector('.browse-btn');
            const fileList = uploadArea.querySelector(`#file-list-render-${index}`);
            const hiddenInput = uploadArea.querySelector('.fg-files');

            if (!fileInput || !dropzone) return;

            // Create file uploader instance
            this.fileUploaders[index] = {
                files: [],
                element: uploadArea,
                input: fileInput,
                list: fileList,
                hiddenInput: hiddenInput,
                fieldName: fieldName
            };

            // Browse button click
            browseBtn?.addEventListener('click', () => {
                fileInput.click();
            });

            // File input change
            fileInput.addEventListener('change', (e) => {
                this.handleFiles(e.target.files, index);
            });

            // Drag and drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.add('drag-over');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, () => {
                    dropzone.classList.remove('drag-over');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                this.handleFiles(files, index);
            }, false);
        });
    }

    // Handle files
    handleFiles(files, index) {
        const fileArray = Array.from(files);
        const uploader = this.fileUploaders[index];
        
        if (!uploader) return;

        const maxSize = parseFloat(uploader.input.dataset.maxSize) * 1024 * 1024; // Convert to bytes
        const maxFiles = parseInt(uploader.input.dataset.maxFiles);
        const allowedTypes = uploader.input.accept.split(',').map(t => t.trim());

        fileArray.forEach(file => {
            // Validation
            const fileExt = '.' + file.name.split('.').pop().toLowerCase();
            
            if (!allowedTypes.includes(fileExt) && allowedTypes[0] !== '') {
                alert(`File type ${fileExt} is not allowed. Allowed types: ${allowedTypes.join(', ')}`);
                return;
            }

            if (file.size > maxSize) {
                alert(`File ${file.name} is too large. Maximum size: ${uploader.input.dataset.maxSize}MB`);
                return;
            }

            if (uploader.files.length >= maxFiles) {
                alert(`Maximum ${maxFiles} files allowed`);
                return;
            }

            // Check if file already exists
            const exists = uploader.files.some(f => f.name === file.name && f.size === file.size);
            if (!exists) {
                uploader.files.push(file);
                this.displayFile(file, index);
                this.updateHiddenInput(index);
            }
        });
    }

    // Display file
    displayFile(file, index) {
        const fileList = document.querySelector(`#file-list-render-${index}`);
        if (!fileList) return;

        const fileId = `file-${index}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        const fileSize = this.formatFileSize(file.size);
        const fileExt = file.name.split('.').pop().toLowerCase();
        
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.id = fileId;
        fileItem.innerHTML = `
            <div class="file-info">
                <div class="file-icon">${this.getFileIcon(fileExt)}</div>
                <div class="file-details">
                    <p class="file-name">${file.name}</p>
                    <p class="file-size">${fileSize}</p>
                </div>
            </div>
            <div class="file-actions">
                ${this.isImageFile(fileExt) ? `<button type="button" class="preview-btn" data-file-id="${fileId}">Preview</button>` : ''}
                <button type="button" class="remove-btn" data-file-id="${fileId}" data-index="${index}">Remove</button>
            </div>
        `;

        fileList.appendChild(fileItem);

        // Add remove functionality
        const removeBtn = fileItem.querySelector('.remove-btn');
        removeBtn?.addEventListener('click', () => {
            this.removeFile(fileId, file, index);
        });

        // Add preview functionality for images
        if (this.isImageFile(fileExt)) {
            const previewBtn = fileItem.querySelector('.preview-btn');
            previewBtn?.addEventListener('click', () => {
                this.previewImage(file);
            });
        }
    }

    // Update hidden input
    updateHiddenInput(index) {
        const uploader = this.fileUploaders[index];
        if (uploader && uploader.hiddenInput) {
            const fileNames = uploader.files.map(f => f.name).join(',');
            uploader.hiddenInput.value = fileNames;
        }
    }

    // Remove file
    removeFile(fileId, file, index) {
        const fileItem = document.getElementById(fileId);
        const uploader = this.fileUploaders[index];
        
        if (fileItem && uploader) {
            fileItem.remove();
            uploader.files = uploader.files.filter(f => f !== file);
            this.updateHiddenInput(index);
        }
    }

    // Preview image
    previewImage(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const modal = document.createElement('div');
            modal.className = 'file-preview-modal';
            modal.innerHTML = `
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <img src="${e.target.result}" alt="${file.name}">
                    <p class="preview-filename">${file.name}</p>
                </div>
            `;
            document.body.appendChild(modal);

            const closeBtn = modal.querySelector('.close-modal');
            closeBtn?.addEventListener('click', () => {
                modal.remove();
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.remove();
                }
            });
        };
        reader.readAsDataURL(file);
    }

    // Helper functions
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    isImageFile(ext) {
        return ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'].includes(ext.toLowerCase());
    }

    getFileIcon(ext) {
        const icons = {
            pdf: '📄',
            doc: '📝',
            docx: '📝',
            xls: '📊',
            xlsx: '📊',
            ppt: '📊',
            pptx: '📊',
            txt: '📝',
            zip: '🗜️',
            rar: '🗜️',
            jpg: '🖼️',
            jpeg: '🖼️',
            png: '🖼️',
            gif: '🖼️',
            mp4: '🎬',
            mp3: '🎵',
            default: '📎'
        };
        return icons[ext.toLowerCase()] || icons.default;
    }

    // Validate form
    validate(type, active_step = '') {
        let validationError = false;
        
        // Remove existing error messages
        document.querySelectorAll(".error-message").forEach(el => el.remove());
        
        const selector = type === 'single_form' 
            ? "#fgr-form .fgr-control" 
            : `.fg-form-group[data-step="${active_step}"] .fgr-control`;
        
        document.querySelectorAll(selector).forEach(field => {
            const attr = field.getAttribute('required');
            
            if (attr !== null) {
                const name = field.getAttribute("name");
                
                if (field.type === 'radio' || field.type === 'checkbox') {
                    const checkedField = document.querySelector(`[name='${name}']:checked`);
                    if (!checkedField) {
                        validationError = true;
                        const errorHtml = '<div class="error-message">This field is required</div>';
                        field.closest(".fg-form-group")?.insertAdjacentHTML('beforeend', errorHtml);
                    }
                } else {
                    if (!field.classList.contains("fg-files") && field.value === '') {
                        validationError = true;
                        const errorHtml = '<div class="error-message">This field is required</div>';
                        field.closest(".fg-form-group")?.insertAdjacentHTML('beforeend', errorHtml);
                    }
                }
            }
            
            // Email validation
            if (!validationError && field.type === 'email' && field.value !== '') {
                const email = field.value;
                const atpos = email.indexOf("@");
                const dotpos = email.lastIndexOf(".");
                if (atpos < 1 || (dotpos - atpos < 2)) {
                    validationError = true;
                    const errorHtml = '<div class="error-message">Invalid Email value entered</div>';
                    field.closest(".fg-form-group")?.insertAdjacentHTML('beforeend', errorHtml);
                }
            }
            
            // Max length validation
            const maxlengthAttr = field.getAttribute('maxlength');
            if (!validationError && maxlengthAttr && field.value.length > parseInt(maxlengthAttr)) {
                validationError = true;
                const errorHtml = `<div class="error-message">Length cannot be greater than ${maxlengthAttr}</div>`;
                field.closest(".fg-form-group")?.insertAdjacentHTML('beforeend', errorHtml);
            }
        });

        // Validate required file uploads
        const fileSelector = type === 'single_form' 
            ? "#fgr-form .required-file" 
            : `.fg-form-group[data-step="${active_step}"] .required-file`;
            
        document.querySelectorAll(fileSelector).forEach(fileInput => {
            const uploadArea = fileInput.closest('.custom-file-upload-render');
            const index = uploadArea?.dataset.index;
            const uploader = this.fileUploaders[index];
            
            if (uploader && uploader.files.length === 0) {
                validationError = true;
                const errorHtml = '<div class="error-message">Please upload at least one file</div>';
                uploadArea.closest(".fg-form-group")?.insertAdjacentHTML('beforeend', errorHtml);
            }
        });
        
        if (validationError) {
            window.scrollTo({ top: document.querySelector(".fgr-render")?.offsetTop || 0, behavior: 'smooth' });
            return false;
        }
        
        return true;
    }

    // Submit form
    submitForm(saveFrom) {
        const formElement = document.querySelector("#fgr-form").closest("form") || document.querySelector("#fgr-form");
        let submit_form = 1;
        
        if (this.formType === 'single_form') {
            if (!this.validate(this.formType)) {
                return false;
            }
        } else {
            const activeStep = document.querySelector(".step-active")?.getAttribute("data-step");
            if (!this.validate(this.formType, activeStep)) {
                return false;
            }
        }
        
        if (submit_form === 1) {
            this.uploadCount = 0;
            
            // Create FormData from the form
            const formData = new FormData();
            
            // Add all form fields
            document.querySelectorAll("#fgr-form input, #fgr-form select, #fgr-form textarea").forEach(field => {
                if (field.type === 'file' || field.classList.contains('file-input-hidden')) {
                    // Skip file inputs, we'll handle them separately
                    return;
                }
                
                if (field.type === 'radio' || field.type === 'checkbox') {
                    if (field.checked) {
                        formData.append(field.name, field.value);
                    }
                } else {
                    formData.append(field.name, field.value);
                }
            });
            
            // Add files from file uploaders
            Object.keys(this.fileUploaders).forEach(index => {
                const uploader = this.fileUploaders[index];
                if (uploader && uploader.files.length > 0) {
                    uploader.files.forEach((file, i) => {
                        formData.append(`files[${uploader.fieldName}][${i}]`, file);
                    });
                }
            });
            
            // Add savefrom parameter
            formData.append('savefrom', saveFrom);
            
            // Create fetch options
            const fetchOptions = {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            };
            
            // Add CSRF token if provided
            if (this.csrfToken) {
                fetchOptions.headers['X-CSRF-TOKEN'] = this.csrfToken;
            } else {
                // Check for _token field (Laravel CSRF)
                const tokenField = document.querySelector("input[name='_token']");
                if (tokenField) {
                    fetchOptions.headers['X-CSRF-TOKEN'] = tokenField.value;
                }
            }
            
            // Send as FormData
            fetch(this.saveUrl, fetchOptions)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(response => {
                if (response.status === true) {
                    if (this.formType !== 'single_form') {
                        const form_id = response.form_id;
                        const activeStep = parseInt(document.querySelector(".step-active")?.getAttribute("data-step"));
                        const lastStep = document.querySelectorAll(".forms-area > .fg-form-group").length;
                        
                        if (lastStep === activeStep) {
                            if (this.redirectBack !== '') {
                                window.location.href = this.redirectBack;
                            }
                        } else {
                            // Navigate to next step
                            const step = activeStep + 1;
                            
                            // Hide current step
                            document.querySelectorAll(".fg-form-group").forEach(el => {
                                el.classList.remove("step-active");
                                el.style.display = 'none';
                            });
                            
                            // Show next step
                            const nextStepEl = document.querySelector(`.fg-form-group[data-step="${step}"]`);
                            if (nextStepEl) {
                                nextStepEl.classList.add("step-active");
                                nextStepEl.style.display = 'block';
                            }
                            
                            // Update step indicators
                            document.querySelectorAll(".step-count").forEach(el => {
                                el.classList.remove("stepno-active", "stepno-complete");
                            });
                            
                            document.querySelector(`.step-count[data-step="${step}"]`)?.classList.add("stepno-active");
                            
                            // Mark completed steps
                            for (let i = 1; i < step; i++) {
                                document.querySelector(`.step-count[data-step="${i}"]`)?.classList.add("stepno-complete");
                            }
                            
                            // Update buttons
                            const prevBtn = document.querySelector(".prev-btn");
                            const nextBtn = document.querySelector(".next-btn");
                            const finishBtn = document.querySelector(".finish-btn");
                            
                            if (prevBtn) prevBtn.style.display = 'block';
                            
                            if (step === lastStep) {
                                if (nextBtn) nextBtn.style.display = 'none';
                                if (finishBtn) finishBtn.style.display = 'block';
                            } else {
                                if (nextBtn) nextBtn.style.display = 'block';
                                if (finishBtn) finishBtn.style.display = 'none';
                            }
                            
                            // Scroll to top
                            window.scrollTo({ top: document.querySelector(".fgr-render")?.offsetTop || 0, behavior: 'smooth' });
                        }
                    } else {
                        if (this.redirectBack !== '') {
                            window.location.href = this.redirectBack;
                        } else if (response.redirect_back !== undefined) {
                            window.location.href = response.redirect_back;
                        } else {
                            location.reload();
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while submitting the form. Please try again.');
            });
        }
    }

    // Get random number
    getRandom(length) {
        return Math.floor(Math.pow(10, length - 1) + Math.random() * 9 * Math.pow(10, length - 1));
    }

    // Initialize datepicker
    initializeDatepicker() {
        if (typeof flatpickr !== 'undefined') {
            document.querySelectorAll(".datepicker").forEach(el => {
                flatpickr(el, {
                    dateFormat: "d-m-Y"
                });
            });
        }
    }

    // Initialize Google Maps
    initializeGoogleMaps() {
        if (typeof google === 'undefined' || !google.maps) return;
        
        setTimeout(() => {
            document.querySelectorAll(".google-address-input").forEach(input => {
                const autocomplete = new google.maps.places.Autocomplete(input, {
                    types: ['geocode']
                });
                
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    // Handle place selection
                });
            });
        }, 1000);
    }

    // Apply default values
    applyDefaultValues(defaultValuesString) {
        const defaultValues = JSON.parse(defaultValuesString);
        
        Object.entries(defaultValues).forEach(([key, val]) => {
            const f_name = `fg_field[${key}]`;
            const fields = document.querySelectorAll(`.${key}`);
            
            if (fields.length > 0) {
                const field = fields[0];
                const fieldType = field.dataset.field;
                
                if (fieldType === 'checkbox' || fieldType === 'radio') {
                    fields.forEach(field => {
                        if (Array.isArray(val)) {
                            if (val.includes(field.value)) {
                                field.checked = true;
                            }
                        } else {
                            if (field.value === val) {
                                field.checked = true;
                            }
                        }
                    });
                } else {
                    const input = document.querySelector(`[name='${f_name}']`);
                    if (input) {
                        input.value = val;
                    }
                }
            }
        });
    }

    // Additional render methods implementation
    renderAddressInput(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        const placeholder = settings.placeholder !== null ? settings.placeholder : '';
        const maxlength = settings.maxlength !== null ? `maxlength="${settings.maxlength}"` : '';
        const txtindex = this.getRandom(10);
        
        html += `<input id="address-${txtindex}" type="text" ${required} ${maxlength} name="fg_field[${settings.name}]" data-field="textbox" class="fgr-control google-address-input ${settings.name}" placeholder="${placeholder}">`;
        html += '</div>';
        
        return html;
    }

    renderfieldGroups(formJson, key) {
        const fieldGroups = formJson[key];
        const settings = fieldGroups['settings'];
        const groupFields = fieldGroups['groupFields'];
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let renderHtml = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            renderHtml += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        renderHtml += '>';
        
        renderHtml += `<div class="group-head" style="background-color:${settings.background_color || ''};font-size:${settings.font_size || ''}px;color:${settings.font_color || ''}">`;
        renderHtml += settings.label;
        renderHtml += '</div>';
        
        for (let i = 0; i < groupFields.length; i++) {
            Object.keys(formJson).forEach(k => {
                if (formJson[k]['index'] === groupFields[i]) {
                    renderHtml += this.renderField(formJson, k);
                }
            });
        }
        
        renderHtml += '</div>';
        return renderHtml;
    }

    renderNumberInput(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        const maxlength = settings.maxlength !== null ? `maxlength="${settings.maxlength}"` : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        const placeholder = settings.placeholder !== null ? settings.placeholder : '';
        
        html += `<input type="number" ${required} ${maxlength} name="fg_field[${settings.name}]" data-field="number" class="fgr-control ${settings.name}" placeholder="${placeholder}">`;
        html += '</div>';
        
        return html;
    }

    renderEmailInput(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        const placeholder = settings.placeholder !== null ? settings.placeholder : '';
        
        html += `<input type="email" ${required} name="fg_field[${settings.name}]" data-field="email" class="fgr-control ${settings.name}" placeholder="${placeholder}">`;
        html += '</div>';
        
        return html;
    }

    renderDateInput(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        const placeholder = settings.placeholder !== null ? settings.placeholder : '';
        
        html += `<input type="text" ${required} name="fg_field[${settings.name}]" data-field="datepicker" class="fgr-control datepicker ${settings.name}" placeholder="${placeholder}">`;
        html += '</div>';
        
        return html;
    }

    renderUrlInput(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        const placeholder = settings.placeholder !== null ? settings.placeholder : '';
        
        html += `<input type="url" ${required} name="fg_field[${settings.name}]" data-field="url" class="fgr-control ${settings.name}" placeholder="${placeholder}">`;
        html += '</div>';
        
        return html;
    }

    renderTextarea(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        const textLimit = settings.textLimit !== undefined ? `textLimit="${settings.textLimit}"` : '';
        const addLength = settings.addLength !== undefined ? `addLength="${settings.addLength}"` : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        const placeholder = settings.placeholder !== null ? settings.placeholder : '';
        const txtindex = this.getRandom(10);
        
        html += `<textarea ${textLimit} ${addLength} ${required} name="fg_field[${settings.name}]" class="fgr-control fg-editor ${settings.name}" data-field="textarea" id="editor-${txtindex}" placeholder="${placeholder}"></textarea>`;
        html += '</div>';
        
        return html;
    }

    renderDropDown(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        html += `<div class="fgr-select-input"><select ${required} class="fgr-select ${settings.name}" data-field="dropdown" name="fg_field[${settings.name}]">`;
        
        const options = settings.options;
        Object.keys(options).forEach(key => {
            html += `<option value="${options[key]}">${options[key]}</option>`;
        });
        
        html += '</select></div></div>';
        
        return html;
    }

    renderCheckbox(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        html += '<div class="fgr-checkbox">';
        
        const options = settings.options;
        let i = 0;
        Object.keys(options).forEach(key => {
            html += `<label for="chk-${key}"><input ${required} id="chk-${key}" class="${settings.name}" name="fg_field[${settings.name}][${i}]" data-field="checkbox" type="checkbox" value="${options[key]}"> <span>${options[key]}</span></label>`;
            i++;
        });
        
        html += '</div></div>';
        
        return html;
    }

    renderRadio(settings, index) {
        const stepHeading = settings.stepHeading;
        const stepDescription = settings.stepDescription;
        
        let html = '<div class="fg-form-group"';
        if ((stepHeading && stepHeading !== '') || (stepDescription && stepDescription !== '')) {
            html += ` data-stephead="${stepHeading || ''}" data-stepdesc="${stepDescription || ''}"`;
        }
        html += '>';
        
        const required = settings.required !== undefined && settings.required == 1 ? 'required' : '';
        const req_mark = required ? '<span class="req_mark">*</span>' : '';
        
        html += `<label>${settings.label}${req_mark}</label>`;
        
        if (settings.shortDesc != null) {
            html += `<div class="fgr-short-desc">${settings.shortDesc}</div>`;
        }
        
        html += '<div class="fgr-radio">';
        
        const options = settings.options;
        Object.keys(options).forEach(key => {
            html += `<label for="chk-${key}"><input ${required} id="chk-${key}" name="fg_field[${settings.name}]" data-field="radio" class="${settings.name}" type="radio" value="${options[key]}"> <span>${options[key]}</span></label>`;
        });
        
        html += '</div></div>';
        
        return html;
    }
}

// Initialize form builder instance
const formBuilder = new FormBuilder();
const formRenderer = new FormRenderer();

// Create jQuery-like API for compatibility
window.CdsFormBuilder = {
    // Form Generator
    formGenerator: function(element, params) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        if (element) {
            formBuilder.formGenerator(element, params);
        }
        return formBuilder; // Return instance for debugging
    },
    
    // Form Renderer
    formRender: function(element, params) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        if (element) {
            formRenderer.formRender(element, params);
        }
        return formRenderer; // Return instance for debugging
    },
    
    // Get form data (for debugging)
    getFormData: function(format = 'all') {
        return formBuilder.getFormData(format);
    },
    
    // Export form configuration
    exportConfiguration: function() {
        return formBuilder.exportConfiguration();
    }
};

// CSS Styles for file upload
const style = document.createElement('style');
style.textContent = `
/* File Upload Styles */
.custom-file-upload, .custom-file-upload-render {
    width: 100%;
    margin: 10px 0;
}

.file-input-hidden {
    display: none;
}

.file-upload-dropzone {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 40px;
    text-align: center;
    background: #f9f9f9;
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-upload-dropzone:hover {
    border-color: #999;
    background: #f5f5f5;
}

.file-upload-dropzone.drag-over {
    border-color: #4CAF50;
    background: #e8f5e9;
}

.upload-icon svg {
    color: #666;
    margin-bottom: 10px;
}

.upload-text {
    font-size: 16px;
    color: #333;
    margin: 10px 0 5px;
}

.upload-subtext {
    font-size: 14px;
    color: #666;
    margin-bottom: 15px;
}

.browse-btn {
    background: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease;
}

.browse-btn:hover {
    background: #45a049;
}

.upload-info {
    font-size: 12px;
    color: #999;
    margin-top: 10px;
}

.file-list {
    margin-top: 20px;
}

.file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    margin-bottom: 10px;
    background: white;
}

.file-info {
    display: flex;
    align-items: center;
    flex-grow: 1;
}

.file-icon {
    font-size: 24px;
    margin-right: 10px;
}

.file-details {
    text-align: left;
}

.file-name {
    font-size: 14px;
    color: #333;
    margin: 0;
    word-break: break-word;
}

.file-size {
    font-size: 12px;
    color: #666;
    margin: 0;
}

.file-actions {
    display: flex;
    gap: 5px;
}

.preview-btn, .remove-btn {
    padding: 5px 10px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    transition: opacity 0.3s ease;
}

.preview-btn {
    background: #2196F3;
    color: white;
}

.remove-btn {
    background: #f44336;
    color: white;
}

.preview-btn:hover, .remove-btn:hover {
    opacity: 0.8;
}

.file-progress {
    margin-top: 10px;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: #e0e0e0;
    border-radius: 2px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #4CAF50;
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
    display: block;
}

/* File Preview Modal */
.file-preview-modal {
    display: flex;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.8);
    align-items: center;
    justify-content: center;
}

.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border-radius: 8px;
    max-width: 90%;
    max-height: 90%;
    position: relative;
}

.modal-content img {
    max-width: 100%;
    max-height: 70vh;
    display: block;
    margin: 0 auto;
}

.close-modal {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    right: 10px;
    top: 5px;
}

.close-modal:hover,
.close-modal:focus {
    color: #000;
    text-decoration: none;
}

.preview-filename {
    text-align: center;
    margin-top: 10px;
    font-size: 14px;
    color: #333;
}

/* Error message styles */
.error-message {
    color: #f44336;
    font-size: 12px;
    margin-top: 5px;
}

.fg-error {
    color: #f44336;
    font-size: 12px;
    margin-top: 5px;
}

.fg-error-field {
    border-color: #f44336 !important;
}
`;
document.head.appendChild(style);

// Usage Examples:
/*
// Form Generator with file upload:

CdsFormBuilder.formGenerator('.container', {
    saveUrl: '/save-form',
    formName: 'My Form',
    formType: 'step_form',
    dataFormat: 'multipart', // Use multipart for file uploads
    debugMode: true,
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content
});

// Form Renderer with file upload:

CdsFormBuilder.formRender('.render-container', {
    formJson: jsonString,
    formType: 'step_form',
    saveUrl: '/submit-form',
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content
});

// The file upload field now supports:
// - Drag and drop functionality
// - Multiple file selection (configurable)
// - File type restrictions
// - File size limits
// - Preview for images
// - Progress indicators (ready for AJAX upload)
// - File removal
// - Validation

// Server-side handling:
// Files will be sent as multipart/form-data
// Access files in PHP: $_FILES['files']['fieldname']
// Access files in Laravel: $request->file('files.fieldname')
*/

// Polyfill for older browsers
if (!Element.prototype.matches) {
    Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
    Element.prototype.closest = function(s) {
        var el = this;
        do {
            if (el.matches(s)) return el;
            el = el.parentElement || el.parentNode;
        } while (el !== null && el.nodeType === 1);
        return null;
    };
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { FormBuilder, FormRenderer, CdsFormBuilder };
}