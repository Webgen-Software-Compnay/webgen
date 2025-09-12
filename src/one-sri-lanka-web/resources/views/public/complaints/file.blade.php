@extends('admin.layouts.master')
@section('title', 'File a Complaint')

@section('content')
    <div class="content">
        <!-- Hero Section -->
        <div class="card bg-primary text-white mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="card-title mb-3">File Your Complaint</h2>
                        <p class="card-text mb-4">Help us improve Sri Lanka by reporting issues in your community. Your voice matters.</p>
                        <div class="d-flex gap-4">
                            <div class="text-center">
                                <h5 class="mb-0">24/7</h5>
                                <small>Support Available</small>
                            </div>
                            <div class="text-center">
                                <h5 class="mb-0">72hrs</h5>
                                <small>Average Response</small>
                            </div>
                            <div class="text-center">
                                <h5 class="mb-0">95%</h5>
                                <small>Resolution Rate</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center">
                        <i class="fas fa-clipboard-list fa-4x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step Indicator -->
        <div class="row justify-content-center mb-4">
            <div class="col-auto">
                <div class="d-flex gap-3">
                    <div class="step-indicator active" id="step1">
                        <span class="step-number">1</span>
                        <small class="step-label">Category</small>
                    </div>
                    <div class="step-indicator" id="step2">
                        <span class="step-number">2</span>
                        <small class="step-label">Type</small>
                    </div>
                    <div class="step-indicator" id="step3">
                        <span class="step-number">3</span>
                        <small class="step-label">Details</small>
                    </div>
                    <div class="step-indicator" id="step4">
                        <span class="step-number">4</span>
                        <small class="step-label">Review</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Step 1: Category Selection -->
                <div class="card form-step active" id="stepContent1">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-folder-open me-2"></i>Select Complaint Category
                        </h5>
                        <p class="text-muted mb-0">Choose the category that best describes your issue</p>
                    </div>
                    <div class="card-body">
                        <div class="row" id="categoriesContainer">
                            <!-- Categories will be loaded dynamically -->
                        </div>
                        <div class="text-center mt-4">
                            <button class="btn btn-primary btn-lg" onclick="nextStep()" disabled id="nextBtn1">
                                Continue <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Common Complaints -->
                <div class="card form-step" id="stepContent2" style="display: none;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Common Issues in <span id="selectedCategoryName"></span>
                        </h5>
                        <p class="text-muted mb-0">Select if your issue matches any of these common complaints</p>
                    </div>
                    <div class="card-body">
                        <div id="commonComplaintsContainer">
                            <!-- Common complaints will be loaded dynamically -->
                        </div>
                        <div class="text-center mt-4">
                            <button class="btn btn-outline-secondary me-3" onclick="prevStep()">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </button>
                            <button class="btn btn-primary btn-lg" onclick="nextStep()" id="nextBtn2">
                                Continue <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Dynamic Form -->
                <div class="card form-step" id="stepContent3" style="display: none;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-edit me-2"></i>Provide Details
                        </h5>
                        <p class="text-muted mb-0">Fill in the required information for your complaint</p>
                    </div>
                    <div class="card-body">
                        <form id="complaintForm">
                            <div id="dynamicFormContainer">
                                <!-- Dynamic form fields will be loaded here -->
                            </div>
                            
                            <!-- File Upload Section -->
                            <div class="mb-3">
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
                            <button class="btn btn-primary btn-lg" onclick="nextStep()" id="nextBtn3">
                                Review Complaint <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Review & Submit -->
                <div class="card form-step" id="stepContent4" style="display: none;">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-check-circle me-2"></i>Review Your Complaint
                        </h5>
                        <p class="text-muted mb-0">Please review all information before submitting</p>
                    </div>
                    <div class="card-body">
                        <div class="bg-light rounded p-3 mb-4" id="complaintPreview">
                            <!-- Preview will be generated here -->
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="termsAgreement">
                            <label class="form-check-label">
                                I agree to the <a href="#" target="_blank">Terms & Conditions</a> and confirm that the information provided is accurate.
                            </label>
                        </div>
                        
                        <div class="text-center">
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
            <div class="col-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-question-circle me-2"></i>Need Help?
                        </h6>
                    </div>
                    <div class="card-body">
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
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>Your Activity
                        </h6>
                    </div>
                    <div class="card-body">
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

@endsection

@section('center-scripts')
    <script src="{{ asset('assets/admin/js/jquery/jquery.min.js')}}"></script>
@endsection

@section('scripts')
    <style>
        .step-indicator {
            text-align: center;
            position: relative;
        }
        .step-indicator .step-number {
            display: inline-block;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            line-height: 40px;
            font-weight: bold;
        }
        .step-indicator.active .step-number {
            background: #007bff;
            color: white;
        }
        .step-indicator.completed .step-number {
            background: #28a745;
            color: white;
        }
        .step-label {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.75rem;
        }
        .complaint-category {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            height: 100%;
        }
        .complaint-category:hover {
            border-color: #007bff;
            transform: translateY(-2px);
        }
        .complaint-category.selected {
            border-color: #007bff;
            background: #e7f3ff;
        }
        .category-icon {
            width: 50px;
            height: 50px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
            color: #007bff;
        }
        .complaint-option {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .complaint-option:hover {
            border-color: #007bff !important;
        }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
        }
    </style>

    <script>
        // Complaint Filing System
        class ComplaintFilingSystem {
            constructor() {
                this.apiBaseUrl = '{{ config("app.api_base_url", "http://127.0.0.1:8080/api") }}';
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
                    const response = await fetch('/complaints/categories');
                    const data = await response.json();
                    
                    if (data.success) {
                        this.renderCategories(data.data);
                    }
                } catch (error) {
                    console.error('Error loading categories:', error);
                    this.loadSampleCategories();
                }
            }

            loadSampleCategories() {
                const sampleCategories = [
                    { id: 1, name: 'Transportation', description: 'Bus, train, road transport issues' },
                    { id: 2, name: 'Infrastructure', description: 'Roads, bridges, public facilities' },
                    { id: 3, name: 'Utilities', description: 'Water, electricity, internet services' },
                    { id: 4, name: 'Healthcare', description: 'Hospital, clinic, medical services' },
                    { id: 5, name: 'Education', description: 'School, university, educational issues' },
                    { id: 6, name: 'Environment', description: 'Pollution, waste, environmental concerns' }
                ];
                this.renderCategories(sampleCategories);
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
                    'Environment': 'fas fa-leaf'
                };

                categories.forEach(category => {
                    const icon = categoryIcons[category.name] || 'fas fa-folder';
                    const categoryHtml = `
                        <div class="col-md-4 col-sm-6 mb-3">
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
                    const response = await fetch(`/complaints/common/${categoryId}`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.renderCommonComplaints(data.data);
                    }
                } catch (error) {
                    console.error('Error loading common complaints:', error);
                    this.renderCommonComplaints([]);
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
                        const response = await fetch(`/complaints/form/${this.selectedComplaint.id}`);
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

            renderCustomForm() {
                const container = document.getElementById('dynamicFormContainer');
                container.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" rows="4" required placeholder="Describe your complaint in detail..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location (Optional)</label>
                        <input type="text" class="form-control" name="location" placeholder="Where did this issue occur?">
                    </div>
                    <div class="mb-3">
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
                    el.style.display = 'none';
                });
                const stepEl = document.getElementById(`stepContent${step}`);
                stepEl.classList.add('active');
                stepEl.style.display = 'block';
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
                        if (document.getElementById('nextBtn1')) {
                            document.getElementById('nextBtn1').disabled = !isValid;
                        }
                        break;
                    case 2:
                        isValid = this.selectedComplaint !== null;
                        if (document.getElementById('nextBtn2')) {
                            document.getElementById('nextBtn2').disabled = !isValid;
                        }
                        break;
                    case 3:
                        const form = document.getElementById('complaintForm');
                        isValid = form.checkValidity();
                        if (document.getElementById('nextBtn3')) {
                            document.getElementById('nextBtn3').disabled = !isValid;
                        }
                        break;
                }
                
                return isValid;
            }

            async submitComplaint() {
                console.log('Submitting complaint...');
                
                // Show success modal
                document.getElementById('trackingNumber').textContent = '#RN-' + Date.now();
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
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
        }

        // Initialize the system
        const complaintSystem = new ComplaintFilingSystem();

        // Global functions
        function nextStep() {
            complaintSystem.nextStep();
        }

        function prevStep() {
            complaintSystem.prevStep();
        }

        function trackComplaint() {
            window.location.href = '/complaints/track';
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
            window.location.href = '/complaints/track';
        }

        function submitComplaint() {
            complaintSystem.submitComplaint();
        }
    </script>
@endsection
