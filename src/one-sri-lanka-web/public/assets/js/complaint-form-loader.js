/**
 * Complaint Form Loader
 * Dynamically loads form structure for common complaints
 */

class ComplaintFormLoader {
    constructor() {
        this.baseUrl = window.location.origin;
    }

    /**
     * Load form data for a specific common complaint
     */
    async loadFormData(complaintId) {
        console.log('loadFormData called with complaintId:', complaintId);
        console.log('Base URL:', this.baseUrl);
        
        const url = `${this.baseUrl}/common-complaint/${complaintId}/form-data`;
        console.log('Fetching from URL:', url);
        
        try {
            const response = await fetch(url);
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            
            const data = await response.json();
            console.log('Response data:', data);

            if (data.success) {
                console.log('Form data loaded successfully:', data.data);
                return data.data;
            } else {
                console.error('Failed to load form data:', data.message);
                return null;
            }
        } catch (error) {
            console.error('Error loading form data:', error);
            return null;
        }
    }

    /**
     * Render dynamic form fields
     */
    renderDynamicForm(formStructure, containerId) {
        const container = document.getElementById(containerId);
        if (!container) {
            console.error('Container not found:', containerId);
            return;
        }

        // Clear existing content
        container.innerHTML = '';

        // Handle case where formStructure might be a string
        let fields = formStructure;
        if (typeof formStructure === 'string') {
            try {
                fields = JSON.parse(formStructure);
            } catch (e) {
                console.error('Error parsing form structure:', e);
                container.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error parsing form structure</div>';
                return;
            }
        }

        // Ensure formStructure is an array
        if (!fields || !Array.isArray(fields) || fields.length === 0) {
            container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No additional form fields required for this complaint.</div>';
            return;
        }

        // Add form title
        const formTitle = document.createElement('h5');
        formTitle.className = 'mb-3 complaint-title';
        formTitle.textContent = 'Additional Information Required';
        container.appendChild(formTitle);

        // Sort fields by order if specified
        fields.sort((a, b) => (a.order || 0) - (b.order || 0));

        fields.forEach((field, index) => {
            const fieldElement = this.createFormField(field, index);
            container.appendChild(fieldElement);
        });
    }

    /**
     * Create a form field element
     */
    createFormField(field, index) {
        const div = document.createElement('div');
        div.className = 'row mb-3';

        const colDiv = document.createElement('div');
        colDiv.className = 'col-md-12';

        const label = document.createElement('label');
        label.className = 'form-label';
        label.htmlFor = `field_${index}`;
        label.textContent = field.label || field.field_name || `Field ${index + 1}`;
        if (field.required) {
            const requiredSpan = document.createElement('span');
            requiredSpan.className = 'text-danger';
            requiredSpan.textContent = ' *';
            label.appendChild(requiredSpan);
        }

        let input;
        const fieldName = `dynamic_fields[${field.field_name || 'field_' + index}]`;
        const fieldId = `field_${index}`;

        switch (field.field_type) {
            case 'text':
            case 'email':
                input = document.createElement('input');
                input.type = field.field_type;
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldName;
                if (field.placeholder) input.placeholder = field.placeholder;
                if (field.required) input.required = true;
                break;

            case 'number':
                input = document.createElement('input');
                input.type = 'number';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldName;
                if (field.placeholder) input.placeholder = field.placeholder;
                if (field.min !== undefined) input.min = field.min;
                if (field.max !== undefined) input.max = field.max;
                if (field.required) input.required = true;
                break;

            case 'textarea':
                input = document.createElement('textarea');
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldName;
                input.rows = field.rows || 3;
                if (field.placeholder) input.placeholder = field.placeholder;
                if (field.required) input.required = true;
                break;

            case 'select':
                input = document.createElement('select');
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldName;
                if (field.required) input.required = true;

                // Add default option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Please select...';
                input.appendChild(defaultOption);

                // Add options
                if (field.options && Array.isArray(field.options)) {
                    field.options.forEach(option => {
                        const optionElement = document.createElement('option');
                        optionElement.value = option;
                        optionElement.textContent = option;
                        input.appendChild(optionElement);
                    });
                }
                break;

            case 'checkbox':
                const checkboxContainer = document.createElement('div');
                checkboxContainer.className = 'form-check-group';
                
                if (field.options && Array.isArray(field.options)) {
                    field.options.forEach((option, optionIndex) => {
                        const checkDiv = document.createElement('div');
                        checkDiv.className = 'form-check';
                        
                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.className = 'form-check-input';
                        checkbox.id = `${fieldId}_${optionIndex}`;
                        checkbox.name = `${fieldName}[]`;
                        checkbox.value = option;
                        
                        const checkLabel = document.createElement('label');
                        checkLabel.className = 'form-check-label';
                        checkLabel.htmlFor = `${fieldId}_${optionIndex}`;
                        checkLabel.textContent = option;
                        
                        checkDiv.appendChild(checkbox);
                        checkDiv.appendChild(checkLabel);
                        checkboxContainer.appendChild(checkDiv);
                    });
                }
                input = checkboxContainer;
                break;

            case 'date':
                input = document.createElement('input');
                input.type = 'date';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldName;
                if (field.required) input.required = true;
                break;

            case 'datetime-local':
                input = document.createElement('input');
                input.type = 'datetime-local';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldName;
                if (field.required) input.required = true;
                break;

            case 'ndx_government_office':
            case 'ndx_school':
            case 'ndx_hospital':
            case 'ndx_police_station':
            case 'ndx_bus_route':
            case 'ndx_bank_branch':
            case 'ndx_utility_provider':
                // Special field types with API integration
                input = document.createElement('select');
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldName;
                if (field.required) input.required = true;

                // Add data attributes for Select2 initialization
                input.setAttribute('data-field-type', field.field_type);
                if (field.api_config && field.api_config.searchable) {
                    input.setAttribute('data-searchable', 'true');
                }

                // Get appropriate loading message and placeholder
                const loadingText = this.getLoadingText(field.field_type);
                const placeholderText = field.placeholder || this.getPlaceholderText(field.field_type);

                // Add loading option
                const loadingOption = document.createElement('option');
                loadingOption.value = '';
                loadingOption.textContent = loadingText;
                input.appendChild(loadingOption);

                // Load data from API if configured
                if (field.api_config && field.api_config.endpoint) {
                    // Force refresh for schools to bypass old cache
                    const forceRefresh = field.field_type === 'ndx_school';
                    this.loadApiOptions(input, field.api_config, placeholderText, forceRefresh);
                } else {
                    // Fallback to default select
                    input.innerHTML = `<option value="">${placeholderText}</option>`;
                }
                break;

            default:
                // Handle any unknown field types as text input
                input = document.createElement('input');
                input.type = 'text';
                input.className = 'form-control';
                input.id = fieldId;
                input.name = fieldName;
                if (field.placeholder) input.placeholder = field.placeholder;
                if (field.required) input.required = true;
        }

        colDiv.appendChild(label);
        colDiv.appendChild(input);

        // Add help text if provided
        if (field.help_text) {
            const helpText = document.createElement('small');
            helpText.className = 'form-text text-muted';
            helpText.textContent = field.help_text;
            colDiv.appendChild(helpText);
        }

        div.appendChild(colDiv);
        return div;
    }

    /**
     * Load and render form for complaint creation page
     */
    async loadComplaintForm(complaintId, containerId = 'dynamic-form-fields') {
        console.log('loadComplaintForm called with:', { complaintId, containerId });
        
        const loadingMessage = document.getElementById('form-loading-message');
        const errorMessage = document.getElementById('form-error-message');
        const container = document.getElementById(containerId);

        console.log('Elements found:', {
            loadingMessage: !!loadingMessage,
            errorMessage: !!errorMessage,
            container: !!container
        });

        // Show loading
        if (loadingMessage) {
            console.log('Showing loading message');
            loadingMessage.style.display = 'block';
        }
        if (errorMessage) errorMessage.style.display = 'none';
        if (container) container.innerHTML = '';

        try {
            console.log('Calling loadFormData for complaint ID:', complaintId);
            const formData = await this.loadFormData(complaintId);
            console.log('loadFormData returned:', formData);

            // Hide loading
            if (loadingMessage) {
                console.log('Hiding loading message');
                loadingMessage.style.display = 'none';
            }

            if (formData && formData.form_structure) {
                console.log('Form data received:', formData);
                console.log('Form structure:', formData.form_structure);
                console.log('Is array?', Array.isArray(formData.form_structure));
                
                this.renderDynamicForm(formData.form_structure, containerId);
                
                // Initialize Select2 for searchable fields after form is rendered
                setTimeout(() => {
                    if (typeof window.initializeSelect2ForNDXFields === 'function') {
                        window.initializeSelect2ForNDXFields();
                    }
                }, 1000);
                
                // Update page title if needed
                if (formData.complaint && formData.complaint.title) {
                    const titleElement = document.querySelector('.complaint-title');
                    if (titleElement) {
                        titleElement.textContent = formData.complaint.title;
                    }
                }
            } else {
                console.log('No form structure found in data:', formData);
                if (container) {
                    container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>No additional form fields required for this complaint.</div>';
                }
            }
        } catch (error) {
            console.error('Error loading complaint form:', error);
            console.error('Error stack:', error.stack);
            
            // Hide loading and show error
            if (loadingMessage) {
                console.log('Hiding loading message due to error');
                loadingMessage.style.display = 'none';
            }
            if (errorMessage) {
                console.log('Showing error message');
                errorMessage.style.display = 'block';
                errorMessage.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Failed to load form data. Please try again.';
            }
            
            // Show fallback form
            if (container) {
                console.log('Showing fallback form');
                container.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Could not load dynamic form. Please fill out the basic complaint information below.</div>';
            }
        }
    }

    /**
     * Load options for select fields from API endpoints
     * @param {HTMLSelectElement} selectElement - The select element to populate
     * @param {Object} apiConfig - API configuration object
     * @param {string} placeholder - Placeholder text for the select
     */
    loadApiOptions(selectElement, apiConfig, placeholder = 'Please select...', forceRefresh = false) {
        const { endpoint, cacheTime = 3600000, searchable = false } = apiConfig;
        
        console.log('loadApiOptions called with:', { endpoint, cacheTime, searchable, forceRefresh });
        
        // Check cache first (unless forcing refresh)
        const cacheKey = `api_options_${endpoint}`;
        const cached = !forceRefresh ? this.getCachedData(cacheKey, cacheTime) : null;
        
        if (cached) {
            console.log('Using cached data for:', endpoint);
            this.populateSelectOptions(selectElement, cached, placeholder);
            return;
        }

        // Show loading state
        selectElement.innerHTML = '<option value="">Loading...</option>';

        // Make API request
        const cacheBuster = Date.now();
        const apiUrl = `${window.location.origin}${endpoint}?v=${cacheBuster}`;
        console.log('Fetching from API endpoint:', apiUrl);
        fetch(apiUrl)
            .then(response => {
                console.log('API response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('API response data:', data);
                
                // Handle different response structures
                let optionsArray = [];
                if (Array.isArray(data)) {
                    optionsArray = data;
                } else if (data.data && Array.isArray(data.data)) {
                    optionsArray = data.data;
                } else if (data.success && data.data && Array.isArray(data.data)) {
                    optionsArray = data.data;
                } else {
                    console.warn('API response is not in expected format:', data);
                    // Create fallback options
                    optionsArray = [];
                }
                
                console.log('Processed options array:', optionsArray);
                
                // Cache the data
                this.setCachedData(cacheKey, optionsArray);
                
                // Populate select options
                this.populateSelectOptions(selectElement, optionsArray, placeholder);
                
                // Add search functionality if enabled
                if (searchable) {
                    this.makeSelectSearchable(selectElement);
                }
            })
            .catch(error => {
                console.error('Error loading API options from', endpoint, ':', error);
                // Show error state with fallback options
                const fallbackOptions = this.getFallbackOptions(endpoint);
                this.populateSelectOptions(selectElement, fallbackOptions, placeholder);
            });
    }

    /**
     * Populate select element with options from API data
     * @param {HTMLSelectElement} selectElement - The select element
     * @param {Array} data - Array of option data
     * @param {string} placeholder - Placeholder text
     */
    populateSelectOptions(selectElement, data, placeholder) {
        console.log('populateSelectOptions called with:', { data, placeholder });
        console.log('data type:', typeof data, 'isArray:', Array.isArray(data));
        
        selectElement.innerHTML = '';
        
        // Add placeholder option
        const placeholderOption = document.createElement('option');
        placeholderOption.value = '';
        placeholderOption.textContent = placeholder;
        selectElement.appendChild(placeholderOption);
        
        // Ensure data is an array
        if (!Array.isArray(data)) {
            console.warn('Data is not an array, using fallback');
            data = [];
        }
        
        // Add data options
        data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id || item.value || item.code || item;
            option.textContent = item.name || item.label || item.text || item.title || item;
            selectElement.appendChild(option);
        });
        
        console.log(`Added ${data.length} options to select element`);
        
        // Initialize Select2 for searchable fields
        this.initializeSelect2IfNeeded(selectElement);
    }

    /**
     * Initialize Select2 for searchable select elements
     * @param {HTMLSelectElement} selectElement - The select element
     */
    initializeSelect2IfNeeded(selectElement) {
        // Check if this is a searchable NDX field
        const fieldType = selectElement.getAttribute('data-field-type');
        const isSearchable = selectElement.getAttribute('data-searchable') === 'true';
        
        if (isSearchable && (fieldType === 'ndx_school' || fieldType === 'ndx_hospital' || fieldType === 'ndx_police_station')) {
            // Try to initialize immediately if jQuery is available
            if (typeof $ !== 'undefined' && $.fn.select2) {
                this.initSelect2ForElement(selectElement);
            } else {
                // Wait for jQuery and Select2 to load
                console.log('Waiting for jQuery and Select2 to load...');
                setTimeout(() => {
                    if (typeof $ !== 'undefined' && $.fn.select2) {
                        this.initSelect2ForElement(selectElement);
                    } else {
                        console.warn('jQuery or Select2 still not available after wait');
                    }
                }, 1000);
            }
        }
    }

    /**
     * Initialize Select2 for a specific element
     * @param {HTMLSelectElement} selectElement - The select element
     */
    initSelect2ForElement(selectElement) {
        // Add a small delay to ensure DOM is ready
        setTimeout(() => {
            try {
                // Initialize Select2
                $(selectElement).select2({
                    placeholder: selectElement.querySelector('option').textContent,
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $(selectElement).closest('.modal').length ? $(selectElement).closest('.modal') : $('body')
                });
                
                console.log('Select2 initialized for:', selectElement.id);
            } catch (error) {
                console.error('Error initializing Select2:', error);
            }
        }, 100);
    }

    /**
     * Simple caching mechanism for API data
     * @param {string} key - Cache key
     * @param {number} maxAge - Maximum age in milliseconds
     * @returns {*} Cached data or null
     */
    getCachedData(key, maxAge) {
        try {
            const cached = localStorage.getItem(key);
            if (!cached) return null;
            
            const data = JSON.parse(cached);
            const now = Date.now();
            
            if (now - data.timestamp > maxAge) {
                localStorage.removeItem(key);
                return null;
            }
            
            return data.value;
        } catch (error) {
            console.warn('Cache error:', error);
            return null;
        }
    }

    /**
     * Store data in cache
     * @param {string} key - Cache key
     * @param {*} value - Data to cache
     */
    setCachedData(key, value) {
        try {
            const data = {
                value: value,
                timestamp: Date.now()
            };
            localStorage.setItem(key, JSON.stringify(data));
        } catch (error) {
            console.warn('Cache storage error:', error);
        }
    }

    /**
     * Make a select element searchable (basic implementation)
     * @param {HTMLSelectElement} selectElement - The select element
     */
    makeSelectSearchable(selectElement) {
        // This is a basic implementation
        // For a more robust solution, consider using libraries like Select2 or Choices.js
        selectElement.setAttribute('data-searchable', 'true');
        
        // Add basic search functionality
        selectElement.addEventListener('focus', function() {
            // Could implement search functionality here
            // For now, just add a class to indicate it's searchable
            this.classList.add('searchable-select');
        });
    }

    /**
     * Get loading text for different field types
     * @param {string} fieldType - The field type
     * @returns {string} Loading text
     */
    getLoadingText(fieldType) {
        const loadingTexts = {
            'ndx_government_office': 'Loading government offices...',
            'ndx_school': 'Loading schools...',
            'ndx_hospital': 'Loading hospitals...',
            'ndx_police_station': 'Loading police stations...',
            'ndx_bus_route': 'Loading bus routes...',
            'ndx_bank_branch': 'Loading bank branches...',
            'ndx_utility_provider': 'Loading utility providers...'
        };
        return loadingTexts[fieldType] || 'Loading options...';
    }

    /**
     * Get placeholder text for different field types
     * @param {string} fieldType - The field type
     * @returns {string} Placeholder text
     */
    getPlaceholderText(fieldType) {
        const placeholderTexts = {
            'ndx_government_office': 'Select government office...',
            'ndx_school': 'Select school...',
            'ndx_hospital': 'Select hospital...',
            'ndx_police_station': 'Select police station...',
            'ndx_bus_route': 'Select bus route...',
            'ndx_bank_branch': 'Select bank branch...',
            'ndx_utility_provider': 'Select utility provider...'
        };
        return placeholderTexts[fieldType] || 'Please select...';
    }

    /**
     * Get fallback options when API fails
     * @param {string} endpoint - The API endpoint that failed
     * @returns {Array} Fallback options array
     */
    getFallbackOptions(endpoint) {
        console.log('Getting fallback options for endpoint:', endpoint);
        
        // Provide fallback options based on endpoint
        if (endpoint.includes('government-offices')) {
            return [
                { id: 'district-secretariat', name: 'District Secretariat' },
                { id: 'divisional-secretariat', name: 'Divisional Secretariat' },
                { id: 'grama-niladhari', name: 'Grama Niladhari Office' },
                { id: 'municipal-council', name: 'Municipal Council' },
                { id: 'provincial-council', name: 'Provincial Council' },
                { id: 'other', name: 'Other Government Office' }
            ];
        } else if (endpoint.includes('schools')) {
            return [
                { id: 'primary-school', name: 'Primary School' },
                { id: 'secondary-school', name: 'Secondary School' },
                { id: 'national-school', name: 'National School' },
                { id: 'provincial-school', name: 'Provincial School' },
                { id: 'private-school', name: 'Private School' },
                { id: 'international-school', name: 'International School' },
                { id: 'other', name: 'Other Educational Institution' }
            ];
        } else if (endpoint.includes('hospitals')) {
            return [
                { id: 'national-hospital', name: 'National Hospital' },
                { id: 'teaching-hospital', name: 'Teaching Hospital' },
                { id: 'district-hospital', name: 'District General Hospital' },
                { id: 'base-hospital', name: 'Base Hospital' },
                { id: 'private-hospital', name: 'Private Hospital' },
                { id: 'ayurveda-hospital', name: 'Ayurveda Hospital' },
                { id: 'other', name: 'Other Healthcare Facility' }
            ];
        } else if (endpoint.includes('police-stations')) {
            return [
                { id: 'police-station-1', name: 'Colombo Police Station' },
                { id: 'police-station-2', name: 'Fort Police Station' },
                { id: 'police-station-3', name: 'Pettah Police Station' },
                { id: 'police-station-4', name: 'Mount Lavinia Police Station' },
                { id: 'police-station-5', name: 'Nugegoda Police Station' },
                { id: 'other', name: 'Other Police Station' }
            ];
        } else if (endpoint.includes('bus-routes')) {
            return [
                { id: 'route-100', name: 'Route 100 - Colombo - Kandy' },
                { id: 'route-101', name: 'Route 101 - Colombo - Galle' },
                { id: 'route-102', name: 'Route 102 - Colombo - Negombo' },
                { id: 'route-103', name: 'Route 103 - Colombo - Kalutara' },
                { id: 'route-104', name: 'Route 104 - Colombo - Ratnapura' },
                { id: 'other', name: 'Other Bus Route' }
            ];
        } else if (endpoint.includes('bank-branches')) {
            return [
                { id: 'commercial-bank', name: 'Commercial Bank' },
                { id: 'peoples-bank', name: 'Peoples Bank' },
                { id: 'bank-of-ceylon', name: 'Bank of Ceylon' },
                { id: 'sampath-bank', name: 'Sampath Bank' },
                { id: 'hnb', name: 'Hatton National Bank' },
                { id: 'seylan-bank', name: 'Seylan Bank' },
                { id: 'other', name: 'Other Bank' }
            ];
        } else if (endpoint.includes('utility-providers')) {
            return [
                { id: 'ceb', name: 'Ceylon Electricity Board (CEB)' },
                { id: 'nwsdb', name: 'National Water Supply & Drainage Board' },
                { id: 'slt', name: 'Sri Lanka Telecom' },
                { id: 'dialog', name: 'Dialog Axiata' },
                { id: 'mobitel', name: 'Mobitel' },
                { id: 'hutch', name: 'Hutchison' },
                { id: 'other', name: 'Other Utility Provider' }
            ];
        }
        
        // Default fallback
        return [
            { id: 'option1', name: 'Option 1' },
            { id: 'option2', name: 'Option 2' },
            { id: 'other', name: 'Other' }
        ];
    }
}

// Initialize global form loader
window.complaintFormLoader = new ComplaintFormLoader();

// Auto-load form if complaint ID is present in URL
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking for common_complaint_id parameter...');
    const urlParams = new URLSearchParams(window.location.search);
    const complaintId = urlParams.get('common_complaint_id');
    const dynamicFormFields = document.getElementById('dynamic-form-fields');
    
    console.log('Current URL:', window.location.href);
    console.log('URL parameters:', urlParams.toString());
    console.log('Common complaint ID:', complaintId);
    console.log('Dynamic form fields element found:', !!dynamicFormFields);
    
    if (complaintId && dynamicFormFields) {
        console.log('Loading complaint form for ID:', complaintId);
        
        // Add a small delay to ensure DOM is fully ready
        setTimeout(() => {
            console.log('Starting form load after delay...');
            window.complaintFormLoader.loadComplaintForm(complaintId);
        }, 500);
    } else {
        if (!complaintId) {
            console.log('No common_complaint_id parameter found in URL');
        }
        if (!dynamicFormFields) {
            console.log('No dynamic-form-fields element found on page');
        }
    }

    // Also load district list for the top-level district select if present
    if (typeof window.complaintFormLoader !== 'undefined') {
        try {
            window.complaintFormLoader.loadDistricts && window.complaintFormLoader.loadDistricts();
        } catch (err) {
            console.error('Error loading districts on DOMContentLoaded:', err);
        }
    }
});

// Global function to initialize Select2 for all NDX select elements
window.initializeSelect2ForNDXFields = function() {
    console.log('Manually initializing Select2 for NDX fields...');
    
    // Find all select elements with NDX data attributes or searchable-select class
    const ndxSelects = document.querySelectorAll('select[data-field-type^="ndx_"][data-searchable="true"], select.searchable-select[data-field-type^="ndx_"]');
    
    console.log('Found', ndxSelects.length, 'searchable select elements');
    
    ndxSelects.forEach(selectElement => {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            // Check if Select2 is already initialized
            if (!$(selectElement).hasClass('select2-hidden-accessible')) {
                try {
                    $(selectElement).select2({
                        placeholder: 'Search and select...',
                        allowClear: true,
                        width: '100%'
                    });
                    console.log('Select2 initialized for:', selectElement.id || selectElement.name);
                } catch (error) {
                    console.error('Error initializing Select2:', error);
                }
            } else {
                console.log('Select2 already initialized for:', selectElement.id || selectElement.name);
            }
        } else {
            console.warn('jQuery or Select2 not available for:', selectElement.id || selectElement.name);
        }
    });
};

// Auto-initialize after a delay to ensure all fields are loaded
setTimeout(() => {
    window.initializeSelect2ForNDXFields();
}, 2000);

// Additional function to force Select2 initialization
window.forceInitSelect2 = function() {
    console.log('Force initializing Select2...');
    
    // Target all select elements with class searchable-select
    const allSearchableSelects = document.querySelectorAll('select.searchable-select, select[data-searchable="true"]');
    console.log('Found searchable selects:', allSearchableSelects.length);
    
    allSearchableSelects.forEach(selectElement => {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            // Destroy existing Select2 if present
            if ($(selectElement).hasClass('select2-hidden-accessible')) {
                $(selectElement).select2('destroy');
            }
            
            // Initialize Select2
            $(selectElement).select2({
                placeholder: selectElement.getAttribute('data-placeholder') || 'Search and select...',
                allowClear: true,
                width: '100%'
            });
            
            console.log('Select2 force-initialized for:', selectElement.id || selectElement.name);
        } else {
            console.error('jQuery or Select2 not available');
        }
    });
};

// Add district loading and school filtering helpers to ComplaintFormLoader prototype
ComplaintFormLoader.prototype.loadDistricts = function(selectSelector = '#district_select') {
    const select = document.querySelector(selectSelector);
    if (!select) return;

    // Fetch districts from NDX API
    const apiUrl = `http://127.0.0.1:9090/api/v1/districts`;
    fetch(apiUrl)
        .then(res => res.json())
        .then(data => {
            let districts = [];
            if (Array.isArray(data)) districts = data;
            else if (data.data && Array.isArray(data.data)) districts = data.data;

            // Clear and populate
            select.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Select district...';
            select.appendChild(placeholder);

            districts.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.id || d.value || d.code || d;
                opt.textContent = d.name_en || d.name || d.label || d.text || d;
                select.appendChild(opt);
            });

            // Attach change handler to filter schools
            select.addEventListener('change', function(e) {
                const districtId = e.target.value;
                // Filter any NDX school selects
                ComplaintFormLoader.prototype.filterSchoolsByDistrict(districtId);
            });
        })
        .catch(err => {
            console.error('Failed to load districts:', err);
        });
};

ComplaintFormLoader.prototype.filterSchoolsByDistrict = function(districtId) {
    // Find all school select elements generated by dynamic form
    const schoolSelects = document.querySelectorAll('select[data-field-type="ndx_school"]');
    schoolSelects.forEach(select => {
        // Build API endpoint with district filter
        let endpoint = 'http://127.0.0.1:9090/api/v1/schools';
        if (districtId) endpoint += `?district_id=${encodeURIComponent(districtId)}`;

        // Fetch filtered schools and repopulate the select
        fetch(endpoint)
            .then(res => res.json())
            .then(data => {
                let schools = [];
                if (Array.isArray(data)) schools = data;
                else if (data.data && Array.isArray(data.data)) schools = data.data;

                // Clear and repopulate
                select.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = select.getAttribute('data-placeholder') || 'Select school...';
                select.appendChild(placeholder);

                schools.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id || s.code || s.value || s;
                    opt.textContent = s.name || s.title || s.label || s;
                    select.appendChild(opt);
                });

                // Re-initialize Select2 on this element if needed
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    try {
                        if ($(select).hasClass('select2-hidden-accessible')) {
                            $(select).select2('destroy');
                        }
                        $(select).select2({ placeholder: select.querySelector('option').textContent, allowClear: true, width: '100%' });
                    } catch (e) {
                        console.error('Error reinitializing Select2 for school select:', e);
                    }
                }
            })
            .catch(err => {
                console.error('Failed to fetch filtered schools:', err);
            });
    });
};

// Wait for jQuery to be available, then initialize
function waitForJQueryAndInitialize() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $(document).ready(function() {
            // Wait a bit more for all dynamic content to load
            setTimeout(() => {
                window.initializeSelect2ForNDXFields();
            }, 500);
        });
    } else {
        setTimeout(waitForJQueryAndInitialize, 100);
    }
}

// Start waiting for jQuery
waitForJQueryAndInitialize();

// Provide a static init method to be called from blade templates
ComplaintFormLoader.init = function() {
    if (typeof window.complaintFormLoader === 'undefined') {
        window.complaintFormLoader = new ComplaintFormLoader();
    }

    // Load districts for top-level selector
    try {
        window.complaintFormLoader.loadDistricts && window.complaintFormLoader.loadDistricts();
    } catch (err) {
        console.error('Error during ComplaintFormLoader.init:', err);
    }

    // Ensure Select2 fields are initialized once jQuery is ready
    waitForJQueryAndInitialize();
};
