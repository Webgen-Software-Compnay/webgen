<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File a Complaint - One Sri Lanka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
        }
        .complaint-category {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid #e9ecef;
            height: 100%;
        }
        .complaint-category:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        .complaint-category.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        .category-icon {
            width: 60px;
            height: 60px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: #667eea;
        }
        .complaint-category.selected .category-icon {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 1rem;
            font-weight: bold;
            color: #6c757d;
        }
        .step.active {
            background: #667eea;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
        }
        .btn-primary-custom:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
        .floating-help {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
        .complaint-preview {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid #dee2e6;
        }
        .field-group {
            margin-bottom: 1.5rem;
        }
        .quick-actions {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        /* Select2 Bootstrap 5 styling */
        .select2-container {
            width: 100% !important;
        }
        
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 6px 12px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #495057;
            line-height: 26px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        
        .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #667eea;
        }
    </style>
</head>
<body class="bg-light">

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">File Your Complaint</h1>
                <p class="lead mb-4">Help us improve Sri Lanka by reporting issues in your community. Your voice matters.</p>
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <h5 class="mb-0">24/7</h5>
                        <small>Support Available</small>
                    </div>
                    <div class="me-4">
                        <h5 class="mb-0">72hrs</h5>
                        <small>Average Response</small>
                    </div>
                    <div>
                        <h5 class="mb-0">95%</h5>
                        <small>Resolution Rate</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center">
                <div class="d-inline-block p-4 bg-white bg-opacity-10 rounded-circle">
                    <i class="fas fa-clipboard-list fa-4x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <!-- Step Indicator -->
    <div class="step-indicator">
        <div class="step active" id="step1">1</div>
        <div class="step" id="step2">2</div>
        <div class="step" id="step3">3</div>
        <div class="step" id="step4">4</div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Step 1: Category Selection -->
            <div class="form-step active" id="stepContent1">
                <div class="form-container">
                    <h3 class="text-center mb-4">Select Complaint Category</h3>
                    <p class="text-center text-muted mb-5">Choose the category that best describes your issue</p>
                    
                    <div class="row" id="categoriesContainer">
                        <!-- Categories will be loaded dynamically -->
                    </div>
                    
                    <div class="text-center mt-4">
                        <button class="btn btn-primary-custom btn-lg" onclick="nextStep()" disabled id="nextBtn1">
                            Continue <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Common Complaints -->
            <div class="form-step" id="stepContent2">
                <div class="form-container">
                    <h3 class="text-center mb-4">Common Issues in <span id="selectedCategoryName"></span></h3>
                    <p class="text-center text-muted mb-5">Select if your issue matches any of these common complaints</p>
                    
                    <div id="commonComplaintsContainer">
                        <!-- Common complaints will be loaded dynamically -->
                    </div>
                    
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-secondary me-3" onclick="prevStep()">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </button>
                        <button class="btn btn-primary-custom btn-lg" onclick="nextStep()" id="nextBtn2">
                            Continue <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Dynamic Form -->
            <div class="form-step" id="stepContent3">
                <div class="form-container">
                    <h3 class="text-center mb-4">Provide Details</h3>
                    <p class="text-center text-muted mb-5">Fill in the required information for your complaint</p>
                    
                    <form id="complaintForm">
                        <div id="dynamicFormContainer">
                            <!-- Dynamic form fields will be loaded here -->
                        </div>
                        
                        <!-- File Upload Section -->
                        <div class="field-group">
                            <label class="form-label">Supporting Documents (Optional)</label>
                            <input type="file" class="form-control" id="supportingFiles" multiple accept="image/*,application/pdf,.doc,.docx">
                            <div class="form-text">You can upload images, PDFs, or documents (Max 5MB each)</div>
                            <div id="filePreview" class="mt-2"></div>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-secondary me-3" onclick="prevStep()">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </button>
                        <button class="btn btn-primary-custom btn-lg" onclick="nextStep()" id="nextBtn3">
                            Review Complaint <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 4: Review & Submit -->
            <div class="form-step" id="stepContent4">
                <div class="form-container">
                    <h3 class="text-center mb-4">Review Your Complaint</h3>
                    <p class="text-center text-muted mb-5">Please review all information before submitting</p>
                    
                    <div class="complaint-preview" id="complaintPreview">
                        <!-- Preview will be generated here -->
                    </div>
                    
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" id="termsAgreement">
                        <label class="form-check-label">
                            I agree to the <a href="#" target="_blank">Terms & Conditions</a> and confirm that the information provided is accurate.
                        </label>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button class="btn btn-outline-secondary me-3" onclick="prevStep()">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </button>
                        <button class="btn btn-success btn-lg" onclick="submitComplaint()" disabled id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i>Submit Complaint
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 d-none d-lg-block">
            <div class="quick-actions">
                <h6><i class="fas fa-question-circle me-2"></i>Need Help?</h6>
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="showHelp()">
                        <i class="fas fa-headset me-2"></i>Contact Support
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="showFAQ()">
                        <i class="fas fa-book me-2"></i>FAQ
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="showStatus()">
                        <i class="fas fa-search me-2"></i>Check Status
                    </button>
                </div>
            </div>

            <div class="quick-actions mt-3">
                <h6><i class="fas fa-chart-line me-2"></i>Your Activity</h6>
                <div class="small text-muted">
                    <div class="d-flex justify-content-between py-1">
                        <span>Complaints Filed:</span>
                        <strong>3</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span>Resolved:</span>
                        <strong>2</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span>In Progress:</span>
                        <strong>1</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h4>Complaint Submitted Successfully!</h4>
                <p class="text-muted mb-4">Your complaint has been registered and assigned tracking number:</p>
                <h5 class="text-primary" id="trackingNumber">#RN-2024-001234</h5>
                <p class="small text-muted">You will receive updates via email and SMS</p>
                
                <div class="d-grid gap-2 mt-4">
                    <button class="btn btn-primary" onclick="trackComplaint()">
                        <i class="fas fa-search me-2"></i>Track This Complaint
                    </button>
                    <button class="btn btn-outline-secondary" onclick="fileAnother()">
                        <i class="fas fa-plus me-2"></i>File Another Complaint
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Help Button -->
<div class="floating-help">
    <button class="btn btn-primary rounded-circle p-3" onclick="showHelp()">
        <i class="fas fa-comment-alt"></i>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
// Complaint Filing System
class ComplaintFilingSystem {
    constructor() {
        this.apiBaseUrl = 'http://127.0.0.1:8080/api';
        this.currentStep = 1;
        this.selectedCategory = null;
        this.selectedComplaint = null;
        this.formData = {};
        this.uploadedFiles = [];
        this.init();
    }

    async init() {
        await this.loadCategories();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Terms agreement checkbox
        document.getElementById('termsAgreement').addEventListener('change', (e) => {
            document.getElementById('submitBtn').disabled = !e.target.checked;
        });

        // File upload handling
        document.getElementById('supportingFiles').addEventListener('change', (e) => {
            this.handleFileUpload(e.target.files);
        });

        // Form validation
        document.addEventListener('input', () => {
            this.validateCurrentStep();
        });
    }

    async loadCategories() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/public/complaint-categories`);
            const data = await response.json();
            
            if (data.success) {
                this.renderCategories(data.data);
            }
        } catch (error) {
            console.error('Error loading categories:', error);
            this.showError('Failed to load categories. Please refresh and try again.');
        }
    }

    renderCategories(categories) {
        const container = document.getElementById('categoriesContainer');
        container.innerHTML = '';

        const categoryIcons = {
            'Transportation': 'fas fa-bus',
            'Infrastructure': 'fas fa-road',
            'Utilities': 'fas fa-tint',
            'Healthcare': 'fas fa-hospital',
            'Education': 'fas fa-graduation-cap',
            'Environment': 'fas fa-leaf',
            'Public Safety': 'fas fa-shield-alt',
            'Government Services': 'fas fa-building'
        };

        categories.forEach(category => {
            const icon = categoryIcons[category.name] || 'fas fa-folder';
            const categoryHtml = `
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="complaint-category" onclick="complaintSystem.selectCategory(${category.id}, '${category.name}')">
                        <div class="category-icon">
                            <i class="${icon}"></i>
                        </div>
                        <h6>${category.name}</h6>
                        <p class="small text-muted">${category.description || 'Report issues related to ' + category.name.toLowerCase()}</p>
                    </div>
                </div>
            `;
            container.innerHTML += categoryHtml;
        });
    }

    selectCategory(categoryId, categoryName) {
        // Remove previous selection
        document.querySelectorAll('.complaint-category').forEach(el => {
            el.classList.remove('selected');
        });

        // Add selection to clicked category
        event.currentTarget.classList.add('selected');
        
        this.selectedCategory = { id: categoryId, name: categoryName };
        document.getElementById('selectedCategoryName').textContent = categoryName;
        document.getElementById('nextBtn1').disabled = false;
    }

    async loadCommonComplaints(categoryId) {
        try {
            const response = await fetch(`${this.apiBaseUrl}/public/common-complaints/category/${categoryId}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderCommonComplaints(data.data);
            }
        } catch (error) {
            console.error('Error loading common complaints:', error);
            document.getElementById('commonComplaintsContainer').innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No common complaints found for this category. You can still file a custom complaint.
                </div>
            `;
        }
    }

    renderCommonComplaints(complaints) {
        const container = document.getElementById('commonComplaintsContainer');
        
        if (complaints.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No common complaints found for this category. Continue to file a custom complaint.
                </div>
            `;
            return;
        }

        container.innerHTML = '';

        complaints.forEach(complaint => {
            const complaintHtml = `
                <div class="card mb-3 complaint-option" onclick="complaintSystem.selectComplaint(${complaint.id}, '${complaint.title}')">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="fas fa-exclamation-circle text-warning fa-lg"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="card-title">${complaint.title}</h6>
                                <p class="card-text text-muted small">${complaint.description}</p>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2">Priority: ${complaint.priority}</span>
                                    <span class="badge bg-info">Used ${complaint.usage_count || 0} times</span>
                                </div>
                            </div>
                            <div>
                                <i class="fas fa-chevron-right text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += complaintHtml;
        });

        // Add custom complaint option
        container.innerHTML += `
            <div class="card mb-3 complaint-option border-primary" onclick="complaintSystem.selectCustomComplaint()">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-plus-circle text-primary fa-lg"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="card-title text-primary">File Custom Complaint</h6>
                            <p class="card-text text-muted small">My issue is not listed above</p>
                        </div>
                        <div>
                            <i class="fas fa-chevron-right text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    selectComplaint(complaintId, title) {
        document.querySelectorAll('.complaint-option').forEach(el => {
            el.classList.remove('border-primary');
        });
        
        event.currentTarget.classList.add('border-primary');
        this.selectedComplaint = { id: complaintId, title: title, isCustom: false };
    }

    selectCustomComplaint() {
        document.querySelectorAll('.complaint-option').forEach(el => {
            el.classList.remove('border-primary');
        });
        
        event.currentTarget.classList.add('border-primary');
        this.selectedComplaint = { id: null, title: 'Custom Complaint', isCustom: true };
    }

    async loadDynamicForm() {
        if (this.selectedComplaint.isCustom) {
            this.renderCustomForm();
        } else {
            try {
                const response = await fetch(`${this.apiBaseUrl}/public/forms/common-complaint/${this.selectedComplaint.id}`);
                const data = await response.json();
                
                if (data.success && data.data.form_structure) {
                    this.renderDynamicForm(data.data.form_structure);
                } else {
                    this.renderCustomForm();
                }
            } catch (error) {
                console.error('Error loading form:', error);
                this.renderCustomForm();
            }
        }
    }

    renderDynamicForm(formStructure) {
        const container = document.getElementById('dynamicFormContainer');
        container.innerHTML = '';

        formStructure.forEach((field, index) => {
            const fieldHtml = this.generateFormField(field, index);
            container.innerHTML += fieldHtml;
        });
    }

    renderCustomForm() {
        const container = document.getElementById('dynamicFormContainer');
        container.innerHTML = `
            <div class="field-group">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" required>
            </div>
            <div class="field-group">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" rows="4" required placeholder="Describe your complaint in detail..."></textarea>
            </div>
            <div class="field-group">
                <label class="form-label">Location (Optional)</label>
                <input type="text" class="form-control" name="location" placeholder="Where did this issue occur?">
            </div>
            <div class="field-group">
                <label class="form-label">Priority</label>
                <select class="form-control" name="priority">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
        `;
    }

    generateFormField(field, index) {
        const requiredMark = field.required ? '<span class="text-danger">*</span>' : '';
        
        switch (field.field_type) {
            case 'text':
            case 'email':
            case 'number':
                return `
                    <div class="field-group">
                        <label class="form-label">${field.label} ${requiredMark}</label>
                        <input type="${field.field_type}" class="form-control" name="${field.field_name}" ${field.required ? 'required' : ''}>
                    </div>
                `;
            
            case 'textarea':
                return `
                    <div class="field-group">
                        <label class="form-label">${field.label} ${requiredMark}</label>
                        <textarea class="form-control" name="${field.field_name}" rows="3" ${field.required ? 'required' : ''}></textarea>
                    </div>
                `;
            
            case 'select':
                const options = field.options ? field.options.map(opt => `<option value="${opt}">${opt}</option>`).join('') : '';
                return `
                    <div class="field-group">
                        <label class="form-label">${field.label} ${requiredMark}</label>
                        <select class="form-control" name="${field.field_name}" ${field.required ? 'required' : ''}>
                            <option value="">Select an option</option>
                            ${options}
                        </select>
                    </div>
                `;
            
            case 'radio':
                const radioOptions = field.options ? field.options.map((opt, i) => `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="${field.field_name}" value="${opt}" id="${field.field_name}_${i}">
                        <label class="form-check-label" for="${field.field_name}_${i}">${opt}</label>
                    </div>
                `).join('') : '';
                return `
                    <div class="field-group">
                        <label class="form-label">${field.label} ${requiredMark}</label>
                        ${radioOptions}
                    </div>
                `;
            
            case 'checkbox':
                return `
                    <div class="field-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="${field.field_name}" value="true" id="${field.field_name}">
                            <label class="form-check-label" for="${field.field_name}">${field.label}</label>
                        </div>
                    </div>
                `;
            
            default:
                return '';
        }
    }

    handleFileUpload(files) {
        const preview = document.getElementById('filePreview');
        preview.innerHTML = '';
        
        this.uploadedFiles = Array.from(files);
        
        this.uploadedFiles.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'alert alert-light d-flex align-items-center justify-content-between';
            fileItem.innerHTML = `
                <div>
                    <i class="fas fa-file me-2"></i>
                    <span>${file.name}</span>
                    <small class="text-muted ms-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="complaintSystem.removeFile(${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            preview.appendChild(fileItem);
        });
    }

    removeFile(index) {
        this.uploadedFiles.splice(index, 1);
        
        // Update file input
        const fileInput = document.getElementById('supportingFiles');
        const dt = new DataTransfer();
        this.uploadedFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
        
        // Refresh preview
        this.handleFileUpload(this.uploadedFiles);
    }

    nextStep() {
        if (this.currentStep === 1) {
            this.loadCommonComplaints(this.selectedCategory.id);
        } else if (this.currentStep === 2) {
            this.loadDynamicForm();
        } else if (this.currentStep === 3) {
            this.generatePreview();
        }
        
        this.currentStep++;
        this.updateStepIndicator();
        this.showStep(this.currentStep);
    }

    prevStep() {
        this.currentStep--;
        this.updateStepIndicator();
        this.showStep(this.currentStep);
    }

    showStep(step) {
        document.querySelectorAll('.form-step').forEach(el => {
            el.classList.remove('active');
        });
        document.getElementById(`stepContent${step}`).classList.add('active');
    }

    updateStepIndicator() {
        for (let i = 1; i <= 4; i++) {
            const stepEl = document.getElementById(`step${i}`);
            stepEl.classList.remove('active', 'completed');
            
            if (i < this.currentStep) {
                stepEl.classList.add('completed');
            } else if (i === this.currentStep) {
                stepEl.classList.add('active');
            }
        }
    }

    generatePreview() {
        const formData = new FormData(document.getElementById('complaintForm'));
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        this.formData = data;
        
        let previewHtml = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Category</h6>
                    <p>${this.selectedCategory.name}</p>
                </div>
                <div class="col-md-6">
                    <h6>Complaint Type</h6>
                    <p>${this.selectedComplaint.title}</p>
                </div>
            </div>
            <hr>
        `;
        
        Object.entries(data).forEach(([key, value]) => {
            if (value) {
                previewHtml += `
                    <div class="mb-3">
                        <strong>${key.replace('_', ' ').toUpperCase()}:</strong>
                        <p class="mb-0">${value}</p>
                    </div>
                `;
            }
        });
        
        if (this.uploadedFiles.length > 0) {
            previewHtml += `
                <div class="mb-3">
                    <strong>SUPPORTING DOCUMENTS:</strong>
                    <ul class="mb-0">
                        ${this.uploadedFiles.map(file => `<li>${file.name}</li>`).join('')}
                    </ul>
                </div>
            `;
        }
        
        document.getElementById('complaintPreview').innerHTML = previewHtml;
    }

    validateCurrentStep() {
        let isValid = false;
        
        switch (this.currentStep) {
            case 1:
                isValid = this.selectedCategory !== null;
                document.getElementById('nextBtn1').disabled = !isValid;
                break;
            case 2:
                isValid = this.selectedComplaint !== null;
                document.getElementById('nextBtn2').disabled = !isValid;
                break;
            case 3:
                const form = document.getElementById('complaintForm');
                isValid = form.checkValidity();
                document.getElementById('nextBtn3').disabled = !isValid;
                break;
        }
        
        return isValid;
    }

    async submitComplaint() {
        const submitData = {
            category_id: this.selectedCategory.id,
            common_complaint_id: this.selectedComplaint.isCustom ? null : this.selectedComplaint.id,
            form_data: this.formData,
            files: this.uploadedFiles
        };

        try {
            // Show loading state
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
            submitBtn.disabled = true;

            // Create FormData for file upload
            const formData = new FormData();
            formData.append('category_id', submitData.category_id);
            if (submitData.common_complaint_id) {
                formData.append('common_complaint_id', submitData.common_complaint_id);
            }
            formData.append('form_data', JSON.stringify(submitData.form_data));
            
            this.uploadedFiles.forEach((file, index) => {
                formData.append(`files[${index}]`, file);
            });

            const response = await fetch(`${this.apiBaseUrl}/public/complaints/submit`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('trackingNumber').textContent = data.data.tracking_number;
                
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
                
                // Reset form
                this.resetForm();
            } else {
                throw new Error(data.message || 'Failed to submit complaint');
            }

        } catch (error) {
            console.error('Error submitting complaint:', error);
            this.showError('Failed to submit complaint. Please try again.');
            
            // Restore button
            document.getElementById('submitBtn').innerHTML = originalText;
            document.getElementById('submitBtn').disabled = false;
        }
    }

    resetForm() {
        this.currentStep = 1;
        this.selectedCategory = null;
        this.selectedComplaint = null;
        this.formData = {};
        this.uploadedFiles = [];
        
        document.getElementById('complaintForm').reset();
        document.getElementById('termsAgreement').checked = false;
        document.getElementById('submitBtn').disabled = true;
        
        this.updateStepIndicator();
        this.showStep(1);
    }

    showError(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show position-fixed';
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }
}

// Global functions
function nextStep() {
    complaintSystem.nextStep();
}

function prevStep() {
    complaintSystem.prevStep();
}

function trackComplaint() {
    window.location.href = '/track-complaint';
}

function fileAnother() {
    bootstrap.Modal.getInstance(document.getElementById('successModal')).hide();
    complaintSystem.resetForm();
}

function showHelp() {
    alert('Help system would open here with live chat or support ticket system.');
}

function showFAQ() {
    window.open('/faq', '_blank');
}

function showStatus() {
    window.location.href = '/track-complaint';
}

// Initialize the system
const complaintSystem = new ComplaintFilingSystem();
</script>

</body>
</html>
