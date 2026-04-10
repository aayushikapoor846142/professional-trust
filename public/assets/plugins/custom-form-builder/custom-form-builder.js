class FormBuilder {
    constructor() {
        this.forms = {};
    }

    cdsFbBuilderRender(containerId, formConfig, options = {}) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error(`Container with id "${containerId}" not found`);
            return;
        }

        // Store form configuration
        this.forms[containerId] = {
            config: formConfig,
            options: options
        };

        // Clear container
        container.innerHTML = '';

        // Create form wrapper
        const formWrapper = document.createElement('div');
        formWrapper.className = 'cdsFbBuilder-form-container';

        // Add form title
        if (formConfig.form_name) {
            const title = document.createElement('h2');
            title.className = 'cdsFbBuilder-form-title';
            title.textContent = formConfig.form_name;
            formWrapper.appendChild(title);
        }

        // Create form element
        const form = document.createElement('form');
        form.id = `cdsFbBuilder-form-${containerId}`;

        // Render questions
        formConfig.questions.forEach(question => {
            const questionElement = this.cdsFbBuilderRenderQuestion(question);
            form.appendChild(questionElement);
        });

        // Add buttons
        const buttonGroup = document.createElement('div');
        buttonGroup.className = 'cdsFbBuilder-button-group';

        // Save button
        const saveButton = document.createElement('button');
        saveButton.type = 'submit';
        saveButton.className = 'cdsFbBuilder-button cdsFbBuilder-button-primary';
        saveButton.textContent = options.saveButtonText || 'Save';
        buttonGroup.appendChild(saveButton);

        // Clear button
        if (options.showClearButton !== false) {
            const clearButton = document.createElement('button');
            clearButton.type = 'button';
            clearButton.className = 'cdsFbBuilder-button cdsFbBuilder-button-secondary';
            clearButton.textContent = options.clearButtonText || 'Clear';
            clearButton.onclick = () => this.cdsFbBuilderClearForm(containerId);
            buttonGroup.appendChild(clearButton);
        }

        form.appendChild(buttonGroup);

        // Handle form submission
        form.onsubmit = (e) => {
            e.preventDefault();
            this.cdsFbBuilderHandleSubmit(containerId);
        };

        formWrapper.appendChild(form);
        container.appendChild(formWrapper);
    }

    cdsFbBuilderRenderQuestion(question) {
        const group = document.createElement('div');
        group.className = 'cdsFbBuilder-question-group';

        // Label
        const label = document.createElement('label');
        label.className = 'cdsFbBuilder-question-label';
        label.innerHTML = question.question;
        
        if (question.required) {
            const required = document.createElement('span');
            required.className = 'cdsFbBuilder-required';
            required.textContent = '*';
            label.appendChild(required);
        }

        group.appendChild(label);

        // Input based on type
        const inputElement = this.cdsFbBuilderCreateInput(question);
        group.appendChild(inputElement);

        // Error message container
        const error = document.createElement('div');
        error.className = 'cdsFbBuilder-error';
        error.id = `cdsFbBuilder-error-${question.id}`;
        group.appendChild(error);

        return group;
    }

    cdsFbBuilderCreateInput(question) {
        const type = question.type.toLowerCase();

        switch (type) {
            case 'text':
            case 'email':
            case 'number':
            case 'date':
            case 'time':
            case 'tel':
            case 'url':
                return this.cdsFbBuilderCreateTextInput(question, type);
            
            case 'textarea':
                return this.cdsFbBuilderCreateTextarea(question);
            
            case 'select':
            case 'dropdown':
                return this.cdsFbBuilderCreateSelect(question);
            
            case 'radio':
                return this.cdsFbBuilderCreateRadioGroup(question);
            
            case 'checkbox':
                return this.cdsFbBuilderCreateCheckboxGroup(question);
            
            default:
                return this.cdsFbBuilderCreateTextInput(question, 'text');
        }
    }

    cdsFbBuilderCreateTextInput(question, type) {
        const input = document.createElement('input');
        input.type = type;
        input.className = 'cdsFbBuilder-input';
        input.id = question.id;
        input.name = question.id;
        
        if (question.default_value) {
            input.value = question.default_value;
        }
        
        if (question.placeholder) {
            input.placeholder = question.placeholder;
        }
        
        if (question.required) {
            input.required = true;
        }

        return input;
    }

    cdsFbBuilderCreateTextarea(question) {
        const textarea = document.createElement('textarea');
        textarea.className = 'cdsFbBuilder-textarea';
        textarea.id = question.id;
        textarea.name = question.id;
        
        if (question.default_value) {
            textarea.value = question.default_value;
        }
        
        if (question.placeholder) {
            textarea.placeholder = question.placeholder;
        }
        
        if (question.required) {
            textarea.required = true;
        }

        return textarea;
    }

    cdsFbBuilderCreateSelect(question) {
        const select = document.createElement('select');
        select.className = 'cdsFbBuilder-select';
        select.id = question.id;
        select.name = question.id;
        
        if (question.required) {
            select.required = true;
        }

        // Add placeholder option
        if (question.placeholder) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = question.placeholder;
            option.disabled = true;
            option.selected = true;
            select.appendChild(option);
        }

        // Add options
        if (question.options && Array.isArray(question.options)) {
            question.options.forEach(opt => {
                const option = document.createElement('option');
                if (typeof opt === 'object') {
                    option.value = opt.value;
                    option.textContent = opt.label || opt.value;
                } else {
                    option.value = opt;
                    option.textContent = opt;
                }
                
                if (question.default_value && option.value === question.default_value) {
                    option.selected = true;
                }
                
                select.appendChild(option);
            });
        }

        return select;
    }

    cdsFbBuilderCreateRadioGroup(question) {
        const group = document.createElement('div');
        group.className = 'cdsFbBuilder-radio-group';

        if (question.options && Array.isArray(question.options)) {
            question.options.forEach((opt, index) => {
                const optionDiv = document.createElement('div');
                optionDiv.className = 'cdsFbBuilder-radio-option';

                const input = document.createElement('input');
                input.type = 'radio';
                input.id = `${question.id}_${index}`;
                input.name = question.id;
                
                let value, label;
                if (typeof opt === 'object') {
                    value = opt.value;
                    label = opt.label || opt.value;
                } else {
                    value = opt;
                    label = opt;
                }
                
                input.value = value;
                
                if (question.default_value && value === question.default_value) {
                    input.checked = true;
                }
                
                if (question.required && index === 0) {
                    input.required = true;
                }

                const labelElement = document.createElement('label');
                labelElement.htmlFor = input.id;
                labelElement.textContent = label;

                optionDiv.appendChild(input);
                optionDiv.appendChild(labelElement);
                group.appendChild(optionDiv);
            });
        }

        return group;
    }

    cdsFbBuilderCreateCheckboxGroup(question) {
        const group = document.createElement('div');
        group.className = 'cdsFbBuilder-checkbox-group';

        if (question.options && Array.isArray(question.options)) {
            question.options.forEach((opt, index) => {
                const optionDiv = document.createElement('div');
                optionDiv.className = 'cdsFbBuilder-checkbox-option';

                const input = document.createElement('input');
                input.type = 'checkbox';
                input.id = `${question.id}_${index}`;
                input.name = question.id;
                
                let value, label;
                if (typeof opt === 'object') {
                    value = opt.value;
                    label = opt.label || opt.value;
                } else {
                    value = opt;
                    label = opt;
                }
                
                input.value = value;
                
                if (question.default_value) {
                    if (Array.isArray(question.default_value)) {
                        input.checked = question.default_value.includes(value);
                    } else if (value === question.default_value) {
                        input.checked = true;
                    }
                }

                const labelElement = document.createElement('label');
                labelElement.htmlFor = input.id;
                labelElement.textContent = label;

                optionDiv.appendChild(input);
                optionDiv.appendChild(labelElement);
                group.appendChild(optionDiv);
            });
        }

        return group;
    }

    cdsFbBuilderHandleSubmit(containerId) {
        const formData = this.cdsFbBuilderGetFormData(containerId);
        const { options } = this.forms[containerId];

        // Call onSave callback if provided
        if (options.onSave && typeof options.onSave === 'function') {
            options.onSave(formData);
        }
    }

    cdsFbBuilderGetFormData(containerId) {
        const form = document.querySelector(`#cdsFbBuilder-form-${containerId}`);
        const { config } = this.forms[containerId];
        const data = {};

        config.questions.forEach(question => {
            const type = question.type.toLowerCase();
            
            if (type === 'checkbox') {
                const checkboxes = form.querySelectorAll(`input[name="${question.id}"]:checked`);
                data[question.id] = Array.from(checkboxes).map(cb => cb.value);
            } else if (type === 'radio') {
                const radio = form.querySelector(`input[name="${question.id}"]:checked`);
                data[question.id] = radio ? radio.value : null;
            } else {
                const element = form.querySelector(`[name="${question.id}"]`);
                data[question.id] = element ? element.value : null;
            }
        });

        return data;
    }

    cdsFbBuilderClearForm(containerId) {
        const form = document.querySelector(`#cdsFbBuilder-form-${containerId}`);
        form.reset();
        
        // Clear error messages
        form.querySelectorAll('.cdsFbBuilder-error').forEach(error => {
            error.textContent = '';
        });
    }

    cdsFbBuilderSetFieldValue(containerId, fieldId, value) {
        const form = document.querySelector(`#cdsFbBuilder-form-${containerId}`);
        const field = form.querySelector(`[name="${fieldId}"]`);
        
        if (field) {
            if (field.type === 'checkbox' || field.type === 'radio') {
                const fields = form.querySelectorAll(`[name="${fieldId}"]`);
                fields.forEach(f => {
                    if (Array.isArray(value)) {
                        f.checked = value.includes(f.value);
                    } else {
                        f.checked = f.value === value;
                    }
                });
            } else {
                field.value = value;
            }
        }
    }

    cdsFbBuilderValidate(containerId) {
        const form = document.querySelector(`#cdsFbBuilder-form-${containerId}`);
        const { config } = this.forms[containerId];
        let isValid = true;

        config.questions.forEach(question => {
            const errorElement = form.querySelector(`#cdsFbBuilder-error-${question.id}`);
            errorElement.textContent = '';

            if (question.required) {
                const type = question.type.toLowerCase();
                let hasValue = false;

                if (type === 'checkbox') {
                    hasValue = form.querySelector(`input[name="${question.id}"]:checked`) !== null;
                } else if (type === 'radio') {
                    hasValue = form.querySelector(`input[name="${question.id}"]:checked`) !== null;
                } else {
                    const field = form.querySelector(`[name="${question.id}"]`);
                    hasValue = field && field.value.trim() !== '';
                }

                if (!hasValue) {
                    errorElement.textContent = 'This field is required';
                    isValid = false;
                }
            }
        });

        return isValid;
    }
}