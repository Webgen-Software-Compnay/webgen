/**
 * Dynamic Complaint Form Builder with NDX Integration
 * Advanced form creation with real-time government data
 */

class CommonComplaintFormBuilder {
    constructor() {
        // NDX/API-enabled field types
        this.fieldTypes = {
            // Basic Input Fields
            'text': { label: 'Text Input', hasOptions: false, group: 'basic' },
            'textarea': { label: 'Text Area', hasOptions: false, group: 'basic' },
            'email': { label: 'Email Address', hasOptions: false, group: 'basic' },
            'phone': { label: 'Phone Number', hasOptions: false, group: 'basic' },
            'number': { label: 'Number Input', hasOptions: false, group: 'basic' },
            'date': { label: 'Date Picker', hasOptions: false, group: 'basic' },
            'time': { label: 'Time Picker', hasOptions: false, group: 'basic' },
            'file': { label: 'File Upload', hasOptions: false, group: 'basic' },
            
            // Static Selection Fields
            'select': { label: 'Dropdown Select', hasOptions: true, group: 'selection' },
            'radio': { label: 'Radio Buttons', hasOptions: true, group: 'selection' },
            'checkbox': { label: 'Checkboxes', hasOptions: true, group: 'selection' },
            
            // NDX Government Data Fields
            'ndx_hospital': { 
                label: 'Hospital Selection', 
                hasOptions: false, 
                group: 'healthcare',
                apiEndpoint: '/api/ndx/hospitals',
                searchable: true,
                dependsOn: ['district'],
                cacheTime: 3600000, // 1 hour
                description: 'Select from live hospital data via NDX'
            },
            'ndx_police_station': { 
                label: 'Police Station', 
                hasOptions: false, 
                group: 'security',
                apiEndpoint: '/api/ndx/police-stations',
                searchable: true,
                dependsOn: ['district'],
                cacheTime: 3600000,
                description: 'Real-time police station data'
            },
            'ndx_school': { 
                label: 'School/Educational Institute', 
                hasOptions: false, 
                group: 'education',
                apiEndpoint: '/api/ndx/schools',
                searchable: true,
                dependsOn: ['district', 'education_zone'],
                cacheTime: 1800000, // 30 minutes
                description: 'Schools and universities from Department of Education'
            },
            'ndx_government_office': { 
                label: 'Government Office', 
                hasOptions: false, 
                group: 'government',
                apiEndpoint: '/api/ndx/government-offices',
                searchable: true,
                dependsOn: ['district', 'office_type'],
                cacheTime: 3600000,
                description: 'Government departments and offices'
            },
            'ndx_court': { 
                label: 'Court/Tribunal', 
                hasOptions: false, 
                group: 'legal',
                apiEndpoint: '/api/ndx/courts',
                searchable: true,
                dependsOn: ['district', 'court_type'],
                cacheTime: 7200000, // 2 hours
                description: 'Courts and legal institutions'
            },
            
            // Geographic Hierarchy (NDX Enabled)
            'geo_province': { 
                label: 'Province', 
                hasOptions: false, 
                group: 'geographic',
                apiEndpoint: '/api/geo/provinces',
                cacheTime: 86400000, // 24 hours
                description: 'Sri Lankan provinces'
            },
            'geo_district': { 
                label: 'District', 
                hasOptions: false, 
                group: 'geographic',
                apiEndpoint: '/api/geo/districts',
                dependsOn: ['province'],
                cacheTime: 86400000,
                description: 'Districts within selected province'
            },
            'geo_divisional_secretariat': { 
                label: 'Divisional Secretariat', 
                hasOptions: false, 
                group: 'geographic',
                apiEndpoint: '/api/geo/divisional-secretariats',
                dependsOn: ['district'],
                cacheTime: 86400000,
                description: 'DS divisions within district'
            },
            'geo_grama_niladhari': { 
                label: 'Grama Niladhari Division', 
                hasOptions: false, 
                group: 'geographic',
                apiEndpoint: '/api/geo/grama-niladhari',
                dependsOn: ['divisional_secretariat'],
                cacheTime: 86400000,
                description: 'GN divisions within DS'
            },
            
            // Business/Service Fields
            'service_bank_branch': { 
                label: 'Bank Branch', 
                hasOptions: false, 
                group: 'financial',
                apiEndpoint: '/api/services/bank-branches',
                searchable: true,
                dependsOn: ['district'],
                cacheTime: 3600000,
                description: 'Bank branches and ATM locations'
            },
            'service_postal_office': { 
                label: 'Postal Office', 
                hasOptions: false, 
                group: 'postal',
                apiEndpoint: '/api/services/postal-offices',
                dependsOn: ['district'],
                cacheTime: 7200000,
                description: 'Post offices and postal services'
            },
            'transport_bus_route': { 
                label: 'Bus Route', 
                hasOptions: false, 
                group: 'transport',
                apiEndpoint: '/api/transport/bus-routes',
                searchable: true,
                dependsOn: ['from_location', 'to_location'],
                cacheTime: 1800000,
                description: 'SLTB and private bus routes'
            },
            'transport_railway_station': { 
                label: 'Railway Station', 
                hasOptions: false, 
                group: 'transport',
                apiEndpoint: '/api/transport/railway-stations',
                searchable: true,
                dependsOn: ['district'],
                cacheTime: 7200000,
                description: 'Railway stations and train services'
            }
        };
        
        this.formFields = [];
        this.fieldCounter = 0;
        this.apiCache = new Map();
        this.dependencyMap = new Map();
        this.init();
    }

    init() {
        console.log('🚀 Initializing Dynamic Complaint Form Builder with NDX Integration');
        this.waitForJQuery(() => {
            this.setupEventHandlers();
            this.populateFieldTypes();
            this.setupApiEndpoints();
            console.log('✅ Dynamic Form Builder ready');
        });
    }

    waitForJQuery(callback) {
        if (typeof jQuery !== 'undefined' && typeof $ !== 'undefined') {
            $(document).ready(callback);
        } else {
            console.log('⏳ Waiting for jQuery...');
            setTimeout(() => this.waitForJQuery(callback), 100);
        }
    }

    setupEventHandlers() {
        // Enhanced Add Field Button with multiple event bindings
        const addFieldBtn = document.getElementById('addFieldBtn');
        if (addFieldBtn) {
            // Multiple event binding methods for maximum compatibility
            addFieldBtn.onclick = (e) => {
                e.preventDefault();
                this.showAddFieldModal();
                return false;
            };
            
            $('#addFieldBtn').off('click').on('click', (e) => {
                e.preventDefault();
                this.showAddFieldModal();
                return false;
            });
        }
        
        // Save Field Button
        $('#saveFieldBtn').on('click', (e) => {
            e.preventDefault();
            this.saveField();
        });
        
        // Field Type Change Handler
        $('#fieldType').on('change', () => this.handleFieldTypeChange());
        
        // Preview Form Button
        $('#previewFormBtn').on('click', (e) => {
            e.preventDefault();
            this.showPreviewModal();
        });
        
        // Form Submit Handler
        $('#createComplaintForm').on('submit', (e) => this.handleFormSubmit(e));
        
        // Category change handler
        $('#category_id').on('change', () => this.handleCategoryChange());
        
        console.log('✅ Event handlers attached');
    }

    populateFieldTypes() {
        const fieldTypeSelect = $('#fieldType');
        fieldTypeSelect.empty().append('<option value="">Select Field Type</option>');
        
        // Group field types for better UX
        const groups = {
            '📝 Basic Fields': ['text', 'textarea', 'email', 'phone', 'number', 'date', 'time', 'file'],
            '☑️ Selection Fields': ['select', 'radio', 'checkbox'],
            '🏥 Healthcare (NDX)': ['ndx_hospital'],
            '🚔 Security & Law (NDX)': ['ndx_police_station', 'ndx_court'],
            '🎓 Education (NDX)': ['ndx_school'],
            '🏛️ Government (NDX)': ['ndx_government_office'],
            '🗺️ Geographic Data': ['geo_province', 'geo_district', 'geo_divisional_secretariat', 'geo_grama_niladhari'],
            '🏦 Financial Services': ['service_bank_branch'],
            '📮 Postal Services': ['service_postal_office'],
            '🚌 Transport': ['transport_bus_route', 'transport_railway_station']
        };

        Object.entries(groups).forEach(([groupName, types]) => {
            const optgroup = $(`<optgroup label="${groupName}"></optgroup>`);
            types.forEach(type => {
                const fieldConfig = this.fieldTypes[type];
                if (fieldConfig) {
                    optgroup.append(`<option value="${type}" title="${fieldConfig.description || ''}">${fieldConfig.label}</option>`);
                }
            });
            fieldTypeSelect.append(optgroup);
        });
    }

    setupApiEndpoints() {
        // Setup base API URL from environment
        this.baseApiUrl = window.ONE_SRI_LANKA_API_URL || 'http://localhost:8080/api';
        
        // Setup CSRF token for API requests
        this.csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        console.log(`🌐 API Base URL: ${this.baseApiUrl}`);
    }

    showAddFieldModal() {
        console.log('📝 Opening Add Field Modal');
        this.resetModal();
        this.showModal('#fieldModal');
    }

    showModal(selector) {
        const modal = $(selector);
        try {
            if (typeof $.fn.modal !== 'undefined') {
                modal.modal('show');
            } else {
                // Fallback modal show
                modal.addClass('show').css('display', 'block');
                $('body').addClass('modal-open');
                $('.modal-backdrop').remove();
                $('<div class="modal-backdrop fade show"></div>').appendTo('body');
            }
        } catch (error) {
            console.error('❌ Modal show error:', error);
            modal.show();
        }
    }

    resetModal() {
        $('#fieldType').val('');
        $('#fieldName').val('');
        $('#fieldLabel').val('');
        $('#fieldRequired').prop('checked', false);
        $('#fieldPlaceholder').val('');
        $('#fieldHelp').val('');
        $('#optionsContainer').hide();
        $('#ndxConfiguration').remove();
        $('#dependencyConfiguration').remove();
        $('#apiConfiguration').remove();
        $('#staticOptions').empty();
    }

    handleFieldTypeChange() {
        const selectedType = $('#fieldType').val();
        const fieldConfig = this.fieldTypes[selectedType];
        
        console.log(`🔄 Field type changed to: ${selectedType}`);
        
        // Hide all conditional containers
        $('#optionsContainer').hide();
        $('#ndxConfiguration, #dependencyConfiguration, #apiConfiguration').remove();
        
        if (!fieldConfig) return;
        
        // Show field description
        this.showFieldDescription(fieldConfig);
        
        // Show static options for basic selection fields
        if (fieldConfig.hasOptions) {
            this.showStaticOptions();
        }
        
        // Show NDX/API configuration for dynamic fields
        if (fieldConfig.apiEndpoint) {
            this.showApiConfiguration(selectedType, fieldConfig);
        }
        
        // Show dependency configuration
        if (fieldConfig.dependsOn && fieldConfig.dependsOn.length > 0) {
            this.showDependencyConfiguration(fieldConfig.dependsOn);
        }
    }

    showFieldDescription(fieldConfig) {
        if (fieldConfig.description) {
            const descHtml = `
                <div class="alert alert-info mt-2" role="alert">
                    <small><i class="bx bx-info-circle"></i> ${fieldConfig.description}</small>
                </div>
            `;
            $('#fieldType').parent().after(descHtml);
        }
    }

    showStaticOptions() {
        $('#optionsContainer').show();
        
        if ($('#staticOptions').children().length === 0) {
            this.addStaticOption();
        }
    }

    showApiConfiguration(fieldType, fieldConfig) {
        const apiConfigHtml = `
            <div id="apiConfiguration" class="mt-3">
                <h6 class="mb-3">🌐 NDX/API Configuration</h6>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">API Endpoint</label>
                        <input type="text" class="form-control" id="apiEndpoint" 
                               value="${fieldConfig.apiEndpoint}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cache Duration (minutes)</label>
                        <input type="number" class="form-control" id="cacheTime" 
                               value="${fieldConfig.cacheTime / 60000}" min="1" max="1440">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="searchable" 
                                   ${fieldConfig.searchable ? 'checked' : ''}>
                            <label class="form-check-label" for="searchable">Enable Search</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-primary btn-sm" 
                                onclick="formBuilder.testApiEndpoint('${fieldConfig.apiEndpoint}')">
                            <i class="bx bx-wifi"></i> Test API
                        </button>
                    </div>
                    <div class="col-md-4">
                        <span class="badge badge-info">NDX Enabled</span>
                    </div>
                </div>
            </div>
        `;
        
        $('#optionsContainer').after(apiConfigHtml);
    }

    showDependencyConfiguration(dependencies) {
        const dependencyHtml = dependencies.map(dep => `
            <div class="col-md-6">
                <label class="form-label">Depends on: ${dep.replace('_', ' ').toUpperCase()}</label>
                <select class="form-control dependency-field" data-dependency="${dep}">
                    <option value="">Auto-detect from form</option>
                    ${this.getExistingFieldOptions(dep)}
                </select>
            </div>
        `).join('');
        
        const depConfigHtml = `
            <div id="dependencyConfiguration" class="mt-3">
                <h6 class="mb-3">🔗 Field Dependencies</h6>
                <div class="row">${dependencyHtml}</div>
                <small class="text-muted">This field will update based on the selected dependencies</small>
            </div>
        `;
        
        const insertAfter = $('#apiConfiguration').length ? '#apiConfiguration' : '#optionsContainer';
        $(insertAfter).after(depConfigHtml);
    }

    getExistingFieldOptions(dependencyType) {
        return this.formFields
            .filter(field => 
                field.type.includes(dependencyType) || 
                field.name.toLowerCase().includes(dependencyType.toLowerCase()) ||
                field.type === `geo_${dependencyType}`
            )
            .map(field => `<option value="${field.name}">${field.label}</option>`)
            .join('');
    }

    async testApiEndpoint(endpoint) {
        console.log(`🧪 Testing API endpoint: ${endpoint}`);
        
        try {
            const fullUrl = `${this.baseApiUrl}${endpoint}`;
            const response = await this.fetchApiData(fullUrl, {}, true);
            
            if (response && response.length > 0) {
                const sampleData = response.slice(0, 3).map(item => 
                    `• ${item.name || item.title || item.label || JSON.stringify(item)}`
                ).join('\n');
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '✅ API Test Successful!',
                        html: `<strong>Found ${response.length} records</strong><br><br>Sample data:<br><pre style="text-align: left; font-size: 12px;">${sampleData}</pre>`,
                        icon: 'success',
                        confirmButtonText: 'Great!'
                    });
                } else {
                    alert(`✅ API Test Successful!\nFound ${response.length} records.\n\nSample:\n${sampleData}`);
                }
            } else {
                alert('⚠️ API returned no data');
            }
        } catch (error) {
            console.error('❌ API test failed:', error);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '❌ API Test Failed',
                    text: error.message,
                    icon: 'error'
                });
            } else {
                alert(`❌ API Test Failed: ${error.message}`);
            }
        }
    }

    async fetchApiData(endpoint, parameters = {}, forceRefresh = false) {
        const cacheKey = `${endpoint}_${JSON.stringify(parameters)}`;
        const cached = this.apiCache.get(cacheKey);
        
        // Return cached data if available and not expired
        if (!forceRefresh && cached && (Date.now() - cached.timestamp) < cached.ttl) {
            console.log(`📦 Using cached data for ${endpoint}`);
            return cached.data;
        }
        
        console.log(`🌐 Fetching fresh data from ${endpoint}`);
        
        try {
            // Build query string
            const queryParams = new URLSearchParams(parameters);
            const fullUrl = `${endpoint}?${queryParams}`;
            
            const response = await fetch(fullUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            const resultData = data.data || data;
            
            // Cache the successful response
            this.apiCache.set(cacheKey, {
                data: resultData,
                timestamp: Date.now(),
                ttl: 3600000 // Default 1 hour cache
            });
            
            return resultData;
            
        } catch (error) {
            console.error(`❌ API Error for ${endpoint}:`, error);
            
            // Return cached data if available, even if expired
            if (cached) {
                console.log(`🔄 Using expired cache data for ${endpoint}`);
                return cached.data;
            }
            
            // Return fallback data for development
            return this.getFallbackData(endpoint);
        }
    }

    getFallbackData(endpoint) {
        console.log(`🏗️ Using fallback data for ${endpoint}`);
        
        const fallbackData = {
            '/api/ndx/hospitals': [
                { id: 1, name: 'National Hospital of Sri Lanka', district: 'Colombo', type: 'National', address: 'Regent Street, Colombo 07' },
                { id: 2, name: 'Teaching Hospital Kandy', district: 'Kandy', type: 'Teaching', address: 'William Gopallawa Mawatha, Kandy' },
                { id: 3, name: 'General Hospital Galle', district: 'Galle', type: 'General', address: 'Karapitiya, Galle' },
                { id: 4, name: 'Teaching Hospital Karapitiya', district: 'Galle', type: 'Teaching', address: 'Karapitiya, Galle' },
                { id: 5, name: 'Base Hospital Chilaw', district: 'Puttalam', type: 'Base', address: 'Hospital Road, Chilaw' }
            ],
            '/api/ndx/police-stations': [
                { id: 1, name: 'Colombo Central Police Station', district: 'Colombo', division: 'Colombo', phone: '011-2326284' },
                { id: 2, name: 'Fort Police Station', district: 'Colombo', division: 'Colombo', phone: '011-2421111' },
                { id: 3, name: 'Kandy Police Station', district: 'Kandy', division: 'Kandy', phone: '081-2222222' },
                { id: 4, name: 'Galle Police Station', district: 'Galle', division: 'Galle', phone: '091-2232056' },
                { id: 5, name: 'Negombo Police Station', district: 'Gampaha', division: 'Negombo', phone: '031-2222271' }
            ],
            '/api/ndx/schools': [
                { id: 1, name: 'Royal College Colombo', district: 'Colombo', type: 'National', zone: 'Colombo Zone' },
                { id: 2, name: 'Trinity College Kandy', district: 'Kandy', type: 'National', zone: 'Kandy Zone' },
                { id: 3, name: 'St. Thomas\' College Mount Lavinia', district: 'Colombo', type: 'National', zone: 'Colombo Zone' },
                { id: 4, name: 'Ananda College Colombo', district: 'Colombo', type: 'National', zone: 'Colombo Zone' },
                { id: 5, name: 'Mahanama College Colombo', district: 'Colombo', type: 'National', zone: 'Colombo Zone' }
            ],
            '/api/geo/provinces': [
                { id: 1, name: 'Western Province', code: 'WP', capital: 'Colombo' },
                { id: 2, name: 'Central Province', code: 'CP', capital: 'Kandy' },
                { id: 3, name: 'Southern Province', code: 'SP', capital: 'Galle' },
                { id: 4, name: 'Northern Province', code: 'NP', capital: 'Jaffna' },
                { id: 5, name: 'Eastern Province', code: 'EP', capital: 'Trincomalee' }
            ],
            '/api/geo/districts': [
                { id: 1, name: 'Colombo', province_id: 1, code: 'CO' },
                { id: 2, name: 'Gampaha', province_id: 1, code: 'GA' },
                { id: 3, name: 'Kalutara', province_id: 1, code: 'KA' },
                { id: 4, name: 'Kandy', province_id: 2, code: 'KD' },
                { id: 5, name: 'Matale', province_id: 2, code: 'MT' },
                { id: 6, name: 'Nuwara Eliya', province_id: 2, code: 'NE' },
                { id: 7, name: 'Galle', province_id: 3, code: 'GL' },
                { id: 8, name: 'Matara', province_id: 3, code: 'MR' },
                { id: 9, name: 'Hambantota', province_id: 3, code: 'HB' }
            ]
        };
        
        return fallbackData[endpoint] || [];
    }

    saveField() {
        console.log('💾 Saving field...');
        
        const fieldData = this.collectFieldData();
        
        if (!this.validateFieldData(fieldData)) {
            return;
        }
        
        // Add field to collection
        this.formFields.push(fieldData);
        this.fieldCounter++;
        
        // Update UI
        this.renderFieldList();
        this.setupFieldDependencies(fieldData);
        
        // Close modal
        this.hideModal('#fieldModal');
        
        // Show success message
        this.showSuccessMessage(fieldData);
        
        console.log('✅ Field saved successfully:', fieldData);
    }

    hideModal(selector) {
        const modal = $(selector);
        try {
            if (typeof $.fn.modal !== 'undefined') {
                modal.modal('hide');
            } else {
                modal.removeClass('show').css('display', 'none');
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            }
        } catch (error) {
            console.error('❌ Modal hide error:', error);
            modal.hide();
        }
    }

    showSuccessMessage(fieldData) {
        const fieldConfig = this.fieldTypes[fieldData.type];
        let message = `✅ Field "${fieldData.label}" added successfully!`;
        
        if (fieldConfig && fieldConfig.apiEndpoint) {
            message = `🌐 NDX-enabled field "${fieldData.label}" added successfully!`;
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Success!',
                text: message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    collectFieldData() {
        const selectedType = $('#fieldType').val();
        const fieldConfig = this.fieldTypes[selectedType];
        
        const fieldData = {
            id: this.fieldCounter + 1,
            type: selectedType,
            name: $('#fieldName').val(),
            label: $('#fieldLabel').val(),
            required: $('#fieldRequired').is(':checked'),
            placeholder: $('#fieldPlaceholder').val(),
            help: $('#fieldHelp').val(),
            order: this.formFields.length + 1
        };
        
        // Add static options for basic selection fields
        if (fieldConfig && fieldConfig.hasOptions) {
            fieldData.options = this.collectStaticOptions();
        }
        
        // Add API configuration for dynamic fields
        if (fieldConfig && fieldConfig.apiEndpoint) {
            fieldData.apiConfig = {
                endpoint: $('#apiEndpoint').val(),
                cacheTime: parseInt($('#cacheTime').val()) * 60000,
                searchable: $('#searchable').is(':checked'),
                dependencies: this.collectDependencies()
            };
        }
        
        return fieldData;
    }

    collectStaticOptions() {
        const options = [];
        $('#staticOptions .option-row').each(function() {
            const value = $(this).find('.option-value').val();
            const label = $(this).find('.option-label').val();
            if (value && label) {
                options.push({ value, label });
            }
        });
        return options;
    }

    collectDependencies() {
        const dependencies = {};
        $('.dependency-field').each(function() {
            const depType = $(this).data('dependency');
            const depField = $(this).val();
            if (depField) {
                dependencies[depType] = depField;
            }
        });
        return dependencies;
    }

    validateFieldData(fieldData) {
        if (!fieldData.type) {
            alert('❌ Please select a field type');
            return false;
        }
        
        if (!fieldData.name) {
            alert('❌ Please enter a field name');
            return false;
        }
        
        if (!fieldData.label) {
            alert('❌ Please enter a field label');
            return false;
        }
        
        // Check for duplicate field names
        if (this.formFields.some(field => field.name === fieldData.name)) {
            alert('❌ Field name already exists');
            return false;
        }
        
        return true;
    }

    setupFieldDependencies(fieldData) {
        if (fieldData.apiConfig && fieldData.apiConfig.dependencies) {
            Object.entries(fieldData.apiConfig.dependencies).forEach(([depType, depField]) => {
                if (!this.dependencyMap.has(depField)) {
                    this.dependencyMap.set(depField, []);
                }
                this.dependencyMap.get(depField).push(fieldData.name);
            });
        }
    }

    renderFieldList() {
        const container = $('#formStructureContainer');
        const noFieldsMessage = $('#noFieldsMessage');
        
        if (this.formFields.length === 0) {
            noFieldsMessage.show();
            return;
        }
        
        noFieldsMessage.hide();
        container.empty();
        
        this.formFields.forEach((field, index) => {
            const fieldHtml = this.generateFieldHtml(field, index);
            container.append(fieldHtml);
        });
        
        // Make fields sortable for reordering
        if (typeof $.fn.sortable !== 'undefined') {
            container.sortable({
                handle: '.drag-handle',
                update: () => this.updateFieldOrder()
            });
        }
    }

    generateFieldHtml(field, index) {
        const fieldConfig = this.fieldTypes[field.type];
        const isApiField = fieldConfig && fieldConfig.apiEndpoint;
        const requiredBadge = field.required ? '<span class="badge badge-danger ml-2">Required</span>' : '';
        const ndxBadge = isApiField ? '<span class="badge badge-info ml-2">🌐 NDX</span>' : '';
        
        return `
            <div class="card mb-2" data-field-id="${field.id}">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <span class="drag-handle me-2" style="cursor: move;">
                                <i class="bx bx-grid-vertical text-muted"></i>
                            </span>
                            <div>
                                <span class="badge badge-primary mr-2">${fieldConfig ? fieldConfig.label : field.type}</span>
                                <strong>${field.label}</strong>
                                ${requiredBadge}
                                ${ndxBadge}
                                <br><small class="text-muted">Field Name: ${field.name}</small>
                                ${isApiField ? `<br><small class="text-success">API: ${field.apiConfig.endpoint}</small>` : ''}
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                    onclick="formBuilder.editField(${field.id})">
                                <i class="bx bx-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                    onclick="formBuilder.removeField(${field.id})">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    updateFieldOrder() {
        $('#formStructureContainer .card').each((index, element) => {
            const fieldId = parseInt($(element).data('field-id'));
            const field = this.formFields.find(f => f.id === fieldId);
            if (field) {
                field.order = index + 1;
            }
        });
        
        this.formFields.sort((a, b) => a.order - b.order);
        console.log('📋 Field order updated');
    }

    removeField(fieldId) {
        if (confirm('Are you sure you want to remove this field?')) {
            this.formFields = this.formFields.filter(field => field.id !== fieldId);
            this.renderFieldList();
            this.updateFormStructureInput();
            console.log(`🗑️ Field ${fieldId} removed`);
        }
    }

    editField(fieldId) {
        const field = this.formFields.find(f => f.id === fieldId);
        if (!field) return;
        
        // Populate modal with field data
        $('#fieldType').val(field.type).trigger('change');
        $('#fieldName').val(field.name);
        $('#fieldLabel').val(field.label);
        $('#fieldRequired').prop('checked', field.required);
        $('#fieldPlaceholder').val(field.placeholder || '');
        $('#fieldHelp').val(field.help || '');
        
        // Remove the field temporarily (will be re-added on save)
        this.formFields = this.formFields.filter(f => f.id !== fieldId);
        
        this.showModal('#fieldModal');
        console.log(`✏️ Editing field: ${field.name}`);
    }

    showPreviewModal() {
        console.log('👁️ Showing form preview');
        
        if (this.formFields.length === 0) {
            alert('❌ Please add some fields before previewing');
            return;
        }
        
        const previewHtml = this.generateFormPreview();
        $('#previewFormContent').html(previewHtml);
        
        // Initialize API fields in preview
        this.initializeApiFieldsInPreview();
        
        this.showModal('#previewModal');
    }

    generateFormPreview() {
        let html = '<div class="row">';
        
        // Sort fields by order
        const sortedFields = [...this.formFields].sort((a, b) => a.order - b.order);
        
        sortedFields.forEach(field => {
            html += this.generateFieldPreview(field);
        });
        
        html += '</div>';
        return html;
    }

    generateFieldPreview(field) {
        const fieldConfig = this.fieldTypes[field.type];
        const requiredAttr = field.required ? 'required' : '';
        const requiredLabel = field.required ? ' <span class="text-danger">*</span>' : '';
        
        let previewHtml = `<div class="col-md-6 mb-3">`;
        previewHtml += `<label class="form-label">${field.label}${requiredLabel}`;
        
        // Add field type badge
        if (fieldConfig && fieldConfig.apiEndpoint) {
            previewHtml += ` <span class="badge badge-info badge-sm">🌐 NDX</span>`;
        }
        
        previewHtml += `</label>`;
        
        // Generate field based on type
        if (fieldConfig && fieldConfig.apiEndpoint) {
            // API-enabled field with enhanced search capabilities
            const searchableClass = fieldConfig.searchable ? 'searchable-select' : '';
            const placeholder = fieldConfig.searchable ? 
                `Type to search ${fieldConfig.label.toLowerCase()}...` : 
                `Select ${fieldConfig.label.toLowerCase()}...`;
                
            previewHtml += `
                <select class="form-control api-field ${searchableClass}" 
                        data-endpoint="${fieldConfig.apiEndpoint}" 
                        data-field-name="${field.name}"
                        data-searchable="${fieldConfig.searchable || false}"
                        data-placeholder="${placeholder}"
                        data-allow-clear="true"
                        data-minimum-input-length="${fieldConfig.searchable ? '2' : '0'}"
                        ${requiredAttr}>
                    <option value="">Loading ${fieldConfig.label}...</option>
                </select>
            `;
        } else {
            // Regular field
            switch (field.type) {
                case 'text':
                case 'email':
                case 'phone':
                case 'number':
                    previewHtml += `<input type="${field.type}" class="form-control" placeholder="${field.placeholder || ''}" ${requiredAttr}>`;
                    break;
                    
                case 'textarea':
                    previewHtml += `<textarea class="form-control" rows="3" placeholder="${field.placeholder || ''}" ${requiredAttr}></textarea>`;
                    break;
                    
                case 'date':
                case 'time':
                    previewHtml += `<input type="${field.type}" class="form-control" ${requiredAttr}>`;
                    break;
                    
                case 'file':
                    previewHtml += `<input type="file" class="form-control" ${requiredAttr}>`;
                    break;
                    
                case 'select':
                    previewHtml += `<select class="form-control" ${requiredAttr}>`;
                    previewHtml += `<option value="">Select...</option>`;
                    if (field.options && field.options.length > 0) {
                        field.options.forEach(option => {
                            previewHtml += `<option value="${option.value}">${option.label}</option>`;
                        });
                    }
                    previewHtml += `</select>`;
                    break;
                    
                default:
                    previewHtml += `<input type="text" class="form-control" placeholder="${field.placeholder || ''}" ${requiredAttr}>`;
            }
        }
        
        if (field.help) {
            previewHtml += `<small class="text-muted">${field.help}</small>`;
        }
        
        previewHtml += `</div>`;
        return previewHtml;
    }

    async initializeApiFieldsInPreview() {
        const apiFields = $('#previewFormContent .api-field');
        
        for (let field of apiFields) {
            const $field = $(field);
            const endpoint = $field.data('endpoint');
            const isSearchable = $field.data('searchable');
            const placeholder = $field.data('placeholder') || 'Select...';
            const allowClear = $field.data('allow-clear');
            const minInputLength = parseInt($field.data('minimum-input-length')) || 0;
            
            try {
                const fullUrl = `${this.baseApiUrl}${endpoint}`;
                const data = await this.fetchApiData(fullUrl);
                
                $field.empty().append(`<option value="">${placeholder}</option>`);
                
                data.forEach(item => {
                    // Enhanced option display with additional info
                    let optionText = item.name;
                    if (item.district && item.district !== item.name) {
                        optionText += ` - ${item.district}`;
                    }
                    if (item.type && item.type !== item.name) {
                        optionText += ` (${item.type})`;
                    }
                    
                    $field.append(`<option value="${item.id}" 
                                          data-district="${item.district || ''}"
                                          data-type="${item.type || ''}"
                                          data-phone="${item.phone || ''}"
                                          data-address="${item.address || ''}"
                                          title="${item.address || item.name}">${optionText}</option>`);
                });
                
                // Initialize Select2 for searchable fields
                if (isSearchable && typeof $.fn.select2 !== 'undefined') {
                    $field.select2({
                        placeholder: placeholder,
                        allowClear: allowClear,
                        minimumInputLength: minInputLength,
                        width: '100%',
                        theme: 'bootstrap-5',
                        templateResult: this.formatSelect2Result,
                        templateSelection: this.formatSelect2Selection,
                        escapeMarkup: function(markup) { return markup; }
                    });
                } else if (isSearchable) {
                    // Fallback for when Select2 is not available
                    console.warn('⚠️ Select2 not available, using basic search');
                    this.addBasicSearch($field);
                }
                
            } catch (error) {
                console.error(`❌ Failed to load data for ${endpoint}:`, error);
                $field.html('<option value="">Failed to load data</option>');
            }
        }
    }

    /**
     * Format Select2 results with enhanced display
     */
    formatSelect2Result(item) {
        if (!item.id) return item.text;
        
        const $item = $(item.element);
        const district = $item.data('district');
        const type = $item.data('type');
        const phone = $item.data('phone');
        
        let html = `<div class="select2-result-item">
                      <div class="select2-result-title">${item.text}</div>`;
        
        if (district || type) {
            html += `<div class="select2-result-meta text-muted small">`;
            if (type) html += `${type}`;
            if (district && type) html += ` • `;
            if (district) html += `${district}`;
            html += `</div>`;
        }
        
        if (phone) {
            html += `<div class="select2-result-phone text-muted small">📞 ${phone}</div>`;
        }
        
        html += `</div>`;
        return html;
    }

    /**
     * Format Select2 selection display
     */
    formatSelect2Selection(item) {
        if (!item.id) return item.text;
        
        const $item = $(item.element);
        const type = $item.data('type');
        
        if (type && type !== item.text) {
            return `${item.text} <small class="text-muted">(${type})</small>`;
        }
        
        return item.text;
    }

    /**
     * Add basic search functionality when Select2 is not available
     */
    addBasicSearch($select) {
        const $container = $select.parent();
        const $searchInput = $(`
            <input type="text" class="form-control mb-2" 
                   placeholder="Type to filter options..." 
                   data-search-for="${$select.attr('id')}">
        `);
        
        $container.prepend($searchInput);
        
        const allOptions = $select.find('option').clone();
        
        $searchInput.on('input', function() {
            const searchTerm = $(this).val().toLowerCase();
            
            $select.empty().append(allOptions.first().clone());
            
            if (searchTerm.length >= 2) {
                allOptions.slice(1).each(function() {
                    const $option = $(this);
                    const text = $option.text().toLowerCase();
                    
                    if (text.includes(searchTerm)) {
                        $select.append($option.clone());
                    }
                });
            } else {
                $select.append(allOptions.slice(1).clone());
            }
        });
    }

    handleCategoryChange() {
        const categoryId = $('#category_id').val();
        const selectedOption = $('#category_id').find('option:selected');
        const description = selectedOption.data('description') || selectedOption.attr('data-description') || '';
        $('#categoryDescription').text(description);
        
        console.log('📂 Category changed:', { categoryId, description });
        
        // Clear existing dynamic fields if category changes
        this.clearDynamicFields();
        
        // You could load category-specific templates here
        this.loadCategoryTemplate(categoryId);
    }
    
    clearDynamicFields() {
        console.log('🧹 Clearing dynamic fields for category change');
        // This method can be expanded to clear category-specific fields
    }
    
    loadCategoryTemplate(categoryId) {
        if (!categoryId) return;
        
        console.log(`📋 Loading template for category: ${categoryId}`);
        // Future enhancement: Load category-specific field templates
        // Example: Different templates for Transportation vs Healthcare complaints
    }
    
    loadCategoriesStatic() {
        console.log('📥 Loading categories via static API call...');
        
        $.ajax({
            url: '/admin/common-complaints/categories',
            method: 'GET',
            dataType: 'json',
            timeout: 5000,
            success: (response) => {
                if (response.success && Array.isArray(response.data)) {
                    const categorySelect = $('#category_id');
                    categorySelect.empty().append('<option value="">Select Category</option>');
                    
                    response.data.forEach(category => {
                        const badge = category.category_type === 'sub' ? ' (Sub)' : '';
                        const optionText = category.name + badge;
                        const option = $(`<option value="${category.id}" data-description="${category.description}">${optionText}</option>`);
                        categorySelect.append(option);
                    });
                    
                    console.log(`✅ Loaded ${response.data.length} categories statically`);
                } else {
                    console.error('❌ Invalid categories response:', response);
                    this.loadFallbackCategories();
                }
            },
            error: (xhr, status, error) => {
                console.error('❌ Failed to load categories:', error);
                this.loadFallbackCategories();
            }
        });
    }
    
    loadFallbackCategories() {
        console.log('⚠️ Loading fallback categories...');
        
        const fallbackCategories = [
            { id: 1, name: 'Transportation', description: 'Bus, train, and public transport issues' },
            { id: 2, name: 'Infrastructure', description: 'Roads, bridges, and public infrastructure' },
            { id: 3, name: 'Utilities', description: 'Water, electricity, and other utilities' },
            { id: 4, name: 'Healthcare', description: 'Hospital and medical service issues' },
            { id: 5, name: 'Education', description: 'School and educational facility problems' },
            { id: 6, name: 'Environment', description: 'Waste management and environmental issues' },
            { id: 7, name: 'Public Safety', description: 'Security and safety concerns' },
            { id: 8, name: 'Government Services', description: 'Administrative and government service issues' }
        ];
        
        const categorySelect = $('#category_id');
        categorySelect.empty().append('<option value="">Select Category</option>');
        
        fallbackCategories.forEach(category => {
            const option = $(`<option value="${category.id}" data-description="${category.description}">${category.name}</option>`);
            categorySelect.append(option);
        });
        
        console.log(`✅ Loaded ${fallbackCategories.length} fallback categories`);
    }

    handleFormSubmit(e) {
        console.log('📤 Submitting complaint form with dynamic fields');
        
        // Debug: Check if we have any form fields
        console.log('🔍 Debug - Current form fields count:', this.formFields.length);
        console.log('🔍 Debug - Current form fields:', this.formFields);
        
        // Update form structure input before submission
        this.updateFormStructureInput();
        
        // Debug: Check what's in the form structure input
        const formStructureValue = $('#formStructureInput').val();
        console.log('🔍 Debug - Form structure input value:', formStructureValue);
        console.log('🔍 Debug - Form structure input length:', formStructureValue.length);
        
        // Show loading state
        const submitBtn = $('#submitBtn');
        submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Creating...');
        
        // Allow the form to submit normally
        return true;
    }

    updateFormStructureInput() {
        // Convert formFields to the expected API format
        const formStructureForApi = this.formFields.map(field => ({
            field_type: field.type,
            field_name: field.name,
            label: field.label,
            required: field.required || false,
            placeholder: field.placeholder || '',
            help_text: field.help || '',
            order: field.order || 0,
            options: field.options || [],
            api_config: field.apiConfig || null,
            validation_rules: field.validationRules || {}
        }));
        
        $('#formStructureInput').val(JSON.stringify(formStructureForApi));
        console.log('📋 Form structure updated for submission:', formStructureForApi);
        console.log('📋 JSON string length:', JSON.stringify(formStructureForApi).length);
        
        // Also log the raw form fields for debugging
        console.log('📋 Raw form fields:', this.formFields);
    }

    // Add static option functionality
    addStaticOption() {
        const optionHtml = `
            <div class="option-row row mb-2">
                <div class="col-md-5">
                    <input type="text" class="form-control option-value" placeholder="Option value">
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control option-label" placeholder="Option label">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-option">
                        <i class="bx bx-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        $('#staticOptions').append(optionHtml);
        
        // Attach remove handler
        $('#staticOptions').off('click', '.remove-option').on('click', '.remove-option', function() {
            $(this).closest('.option-row').remove();
        });
    }

    // Public method to get fields array - called from blade template
    getFieldsArray() {
        console.log('🔍 getFieldsArray called, returning:', this.formFields.length, 'fields');
        console.log('🔍 Field details:', this.formFields);
        
        return this.formFields.map(field => ({
            field_type: field.type,
            field_name: field.name,
            label: field.label,
            required: field.required || false,
            placeholder: field.placeholder || '',
            help_text: field.help || '',
            order: field.order || 0,
            options: field.options || [],
            api_config: field.apiConfig || null,
            validation_rules: field.validationRules || {}
        }));
    }

    // Public method to clear all fields
    clearFields() {
        this.formFields = [];
        this.fieldCounter = 0;
        this.renderFieldList();
        this.updateFormStructureInput();
        console.log('🧹 All form fields cleared');
    }

    // Modal reset when hidden
    setupModalResetHandlers() {
        $('#fieldModal').on('hidden.bs.modal', () => {
            console.log('Modal hidden, resetting form...');
            this.resetModal();
        });
    }
}

// Initialize the dynamic form builder
let formBuilder;

// Wait for jQuery and DOM ready
(function() {
    function initializeFormBuilder() {
        if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
            console.log('⏳ Waiting for jQuery...');
            setTimeout(initializeFormBuilder, 100);
            return;
        }
        
        $(document).ready(function() {
            console.log('🚀 Initializing Dynamic Complaint Form Builder...');
            
            // Test if required elements exist
            if ($('#addFieldBtn').length > 0) {
                console.log('✅ Add Field button found');
            } else {
                console.log('❌ Add Field button NOT found');
            }
            
            if ($('#fieldModal').length > 0) {
                console.log('✅ Field modal found');
            } else {
                console.log('❌ Field modal NOT found');
            }
            
            // Initialize the form builder
            formBuilder = new CommonComplaintFormBuilder();
            window.formBuilder = formBuilder; // Make globally accessible
            
            // Initialize Select2 if available
            if (typeof $.fn.select2 !== 'undefined') {
                console.log('✅ Select2 available, initializing category dropdown...');
                $('#category_id').select2({
                    placeholder: 'Select Category',
                    width: '100%',
                    minimumInputLength: 0,
                    allowClear: true,
                    ajax: {
                        url: '/admin/common-complaints/categories',
                        dataType: 'json',
                        delay: 250,
                        cache: true,
                        data: function (params) {
                            return {
                                q: params.term || '',
                                limit: 20,
                                page: params.page || 1
                            };
                        },
                        processResults: function (data, params) {
                            params.page = params.page || 1;
                            
                            if (data.success && Array.isArray(data.data)) {
                                return {
                                    results: data.data.map(category => ({
                                        id: category.id,
                                        text: category.name,
                                        description: category.description || '',
                                        category_type: category.category_type || 'main',
                                        parent_name: category.parent_name || null
                                    })),
                                    pagination: {
                                        more: data.data.length >= 20
                                    }
                                };
                            }
                            
                            console.warn('❌ Invalid categories response:', data);
                            return { results: [] };
                        },
                        error: function(xhr, status, error) {
                            console.error('❌ Categories API error:', error);
                            // Fallback to static options if available
                            return { results: [] };
                        }
                    },
                    templateResult: function(category) {
                        if (category.loading) {
                            return $('<div>Loading categories...</div>');
                        }
                        
                        if (!category.id) {
                            return $('<div>' + category.text + '</div>');
                        }
                        
                        const categoryBadge = category.category_type === 'sub' ? 
                            '<span class="badge badge-secondary badge-sm">Sub</span>' : 
                            '<span class="badge badge-primary badge-sm">Main</span>';
                            
                        const parentInfo = category.parent_name ? 
                            `<small class="text-muted d-block">Under: ${category.parent_name}</small>` : '';
                        
                        return $(`
                            <div class="select2-result-item">
                                <div class="select2-result-title">
                                    ${category.text} ${categoryBadge}
                                </div>
                                ${parentInfo}
                                <div class="select2-result-meta">${category.description}</div>
                            </div>
                        `);
                    },
                    templateSelection: function(category) {
                        if (!category.id) {
                            return category.text;
                        }
                        
                        const badge = category.category_type === 'sub' ? 
                            ' <small class="text-muted">(Sub)</small>' : '';
                        return category.text + badge;
                    },
                    escapeMarkup: function(markup) {
                        return markup; // Allow HTML markup
                    }
                });
                
                // Handle category selection change
                $('#category_id').on('select2:select', function (e) {
                    const data = e.params.data;
                    if (data.description) {
                        $('#categoryDescription').text(data.description).show();
                    } else {
                        $('#categoryDescription').hide();
                    }
                    console.log('📂 Category selected:', data);
                });
                
                // Handle category clear
                $('#category_id').on('select2:clear', function (e) {
                    $('#categoryDescription').hide();
                    console.log('🗑️ Category cleared');
                });
            } else {
                console.log('⚠️ Select2 not available, attempting to load categories statically...');
                // Fallback: Load categories without Select2
                formBuilder.loadCategoriesStatic();
            }
            
            console.log('✅ Dynamic Complaint Form Builder initialization complete');
        });
    }
    
    initializeFormBuilder();
})();
