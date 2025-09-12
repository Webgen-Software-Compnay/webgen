@extends('admin.layouts.master')
@section('title', 'Track Your Complaint')

@section('content')
    <div class="content">
        <!-- Header Section -->
        <div class="card bg-gradient-primary text-white mb-4">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h2 class="card-title mb-3">Track Your Complaint</h2>
                        <p class="card-text mb-4">Enter your tracking number or personal details to check the status of your complaint.</p>
                        <div class="row">
                            <div class="col-auto">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock me-2"></i>
                                    <span>Average Resolution: 72 hours</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-line me-2"></i>
                                    <span>95% Success Rate</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center">
                        <i class="fas fa-search fa-4x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Search Section -->
            <div class="col-lg-8">
                <!-- Tracking Number Search -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-hashtag me-2"></i>Search by Tracking Number
                        </h5>
                        <p class="text-muted mb-0">Enter your complaint tracking number (e.g., RN-2024-001234)</p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="trackingNumber" 
                                       placeholder="Enter tracking number (RN-2024-XXXXXX)"
                                       style="text-transform: uppercase;">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary btn-lg w-100" onclick="searchByTracking()">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alternative Search -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Search by Personal Details
                        </h5>
                        <p class="text-muted mb-0">Don't have your tracking number? Search using your personal information</p>
                    </div>
                    <div class="card-body">
                        <form id="personalSearchForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">National ID / Passport Number</label>
                                        <input type="text" class="form-control" name="nic" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="phone" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" class="form-control" name="email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Date Range</label>
                                        <select class="form-control" name="date_range">
                                            <option value="7">Last 7 days</option>
                                            <option value="30">Last 30 days</option>
                                            <option value="90">Last 3 months</option>
                                            <option value="365">Last year</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-outline-primary" onclick="searchByPersonalDetails()">
                                    <i class="fas fa-search me-2"></i>Search My Complaints
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Results Section -->
                <div id="searchResults" style="display: none;"></div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="fileNewComplaint()">
                                <i class="fas fa-plus me-2"></i>File New Complaint
                            </button>
                            <button class="btn btn-outline-info" onclick="downloadReport()">
                                <i class="fas fa-download me-2"></i>Download Report
                            </button>
                            <button class="btn btn-outline-warning" onclick="requestUpdate()">
                                <i class="fas fa-bell me-2"></i>Request Update
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Guide -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Status Guide
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="status-guide">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-secondary me-2">RECEIVED</span>
                                <small>Complaint submitted and assigned</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-primary me-2">REVIEWING</span>
                                <small>Under initial assessment</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-warning me-2">IN PROGRESS</span>
                                <small>Investigation/action underway</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-info me-2">PENDING</span>
                                <small>Awaiting additional information</small>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-success me-2">RESOLVED</span>
                                <small>Issue has been addressed</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-danger me-2">CLOSED</span>
                                <small>Case closed or rejected</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-question-circle me-2"></i>Need Help?
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">Can't find your complaint or need assistance?</p>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-success btn-sm" onclick="contactSupport()">
                                <i class="fas fa-headset me-2"></i>Contact Support
                            </button>
                            <button class="btn btn-outline-info btn-sm" onclick="showFAQ()">
                                <i class="fas fa-book me-2"></i>View FAQ
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complaint Details Modal -->
    <div class="modal fade" id="complaintModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-alt me-2"></i>Complaint Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="complaintDetails">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="downloadComplaintReport()">
                        <i class="fas fa-download me-2"></i>Download Report
                    </button>
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
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -19px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #007bff;
            border: 2px solid white;
            box-shadow: 0 0 0 2px #007bff;
        }
        .timeline-item.completed::before {
            background: #28a745;
            box-shadow: 0 0 0 2px #28a745;
        }
        .timeline-item.current::before {
            background: #ffc107;
            box-shadow: 0 0 0 2px #ffc107;
            animation: pulse 2s infinite;
        }
        .complaint-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .complaint-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
        }
    </style>

    <script>
        // Complaint Tracking System
        class ComplaintTrackingSystem {
            constructor() {
                this.apiBaseUrl = '{{ config("app.api_base_url", "http://127.0.0.1:8080/api") }}';
                this.currentComplaint = null;
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.loadURLTracking();
            }

            setupEventListeners() {
                // Auto-format tracking number input
                document.getElementById('trackingNumber').addEventListener('input', (e) => {
                    let value = e.target.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');
                    if (value && !value.startsWith('RN-')) {
                        if (value.length <= 4) {
                            value = 'RN-' + value;
                        }
                    }
                    e.target.value = value;
                });

                // Enter key support
                document.getElementById('trackingNumber').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.searchByTracking();
                    }
                });
            }

            loadURLTracking() {
                const urlParams = new URLSearchParams(window.location.search);
                const trackingNumber = urlParams.get('tracking');
                if (trackingNumber) {
                    document.getElementById('trackingNumber').value = trackingNumber;
                    this.searchByTracking();
                }
            }

            async searchByTracking() {
                const trackingNumber = document.getElementById('trackingNumber').value.trim();
                
                if (!trackingNumber) {
                    this.showError('Please enter a tracking number');
                    return;
                }

                this.showLoading();

                try {
                    // Simulate API call
                    await this.delay(1000);
                    
                    const mockComplaint = this.generateMockComplaint(trackingNumber);
                    this.displayComplaint(mockComplaint);
                    
                } catch (error) {
                    console.error('Error searching complaint:', error);
                    this.showError('Failed to find complaint. Please check your tracking number and try again.');
                }
            }

            async searchByPersonalDetails() {
                const formData = new FormData(document.getElementById('personalSearchForm'));
                const data = {};
                
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }

                if (!data.nic || !data.phone) {
                    this.showError('Please provide both NIC and phone number');
                    return;
                }

                this.showLoading();

                try {
                    // Simulate API call
                    await this.delay(1500);
                    
                    const mockComplaints = this.generateMockComplaints(data);
                    this.displayMultipleComplaints(mockComplaints);
                    
                } catch (error) {
                    console.error('Error searching complaints:', error);
                    this.showError('Failed to find complaints. Please check your details and try again.');
                }
            }

            generateMockComplaint(trackingNumber) {
                const statuses = ['RECEIVED', 'REVIEWING', 'IN_PROGRESS', 'PENDING', 'RESOLVED', 'CLOSED'];
                const categories = ['Transportation', 'Infrastructure', 'Utilities', 'Healthcare'];
                const priorities = ['low', 'medium', 'high', 'urgent'];
                
                const status = statuses[Math.floor(Math.random() * statuses.length)];
                const category = categories[Math.floor(Math.random() * categories.length)];
                const priority = priorities[Math.floor(Math.random() * priorities.length)];
                
                return {
                    tracking_number: trackingNumber,
                    title: `Issue with ${category.toLowerCase()} services`,
                    description: 'Sample complaint description for demonstration purposes.',
                    category: category,
                    priority: priority,
                    status: status,
                    submitted_date: '2024-01-15',
                    last_updated: '2024-01-18',
                    assigned_to: 'John Doe',
                    timeline: this.generateTimeline(status),
                    attachments: [
                        { name: 'photo1.jpg', size: '2.3MB' },
                        { name: 'document.pdf', size: '1.1MB' }
                    ]
                };
            }

            generateMockComplaints(searchData) {
                const count = Math.floor(Math.random() * 5) + 1;
                const complaints = [];
                
                for (let i = 0; i < count; i++) {
                    complaints.push({
                        tracking_number: `RN-2024-${String(Math.floor(Math.random() * 999999)).padStart(6, '0')}`,
                        title: `Complaint ${i + 1}`,
                        category: ['Transportation', 'Infrastructure', 'Utilities'][Math.floor(Math.random() * 3)],
                        status: ['RECEIVED', 'REVIEWING', 'IN_PROGRESS', 'RESOLVED'][Math.floor(Math.random() * 4)],
                        submitted_date: new Date(Date.now() - Math.random() * 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                        priority: ['low', 'medium', 'high'][Math.floor(Math.random() * 3)]
                    });
                }
                
                return complaints;
            }

            generateTimeline(currentStatus) {
                const allSteps = [
                    { status: 'RECEIVED', title: 'Complaint Received', description: 'Your complaint has been submitted and assigned a tracking number.' },
                    { status: 'REVIEWING', title: 'Under Review', description: 'Our team is reviewing your complaint and gathering initial information.' },
                    { status: 'IN_PROGRESS', title: 'Investigation Started', description: 'We have started investigating your complaint and taking necessary actions.' },
                    { status: 'RESOLVED', title: 'Issue Resolved', description: 'Your complaint has been successfully resolved.' }
                ];

                const statusIndex = allSteps.findIndex(step => step.status === currentStatus);
                const timeline = [];

                allSteps.forEach((step, index) => {
                    const item = { ...step };
                    if (index <= statusIndex) {
                        item.completed = true;
                        item.date = new Date(Date.now() - (statusIndex - index) * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
                    }
                    if (index === statusIndex) {
                        item.current = true;
                    }
                    timeline.push(item);
                });

                return timeline;
            }

            displayComplaint(complaint) {
                const resultsDiv = document.getElementById('searchResults');
                
                const statusColors = {
                    'RECEIVED': 'secondary',
                    'REVIEWING': 'primary',
                    'IN_PROGRESS': 'warning',
                    'PENDING': 'info',
                    'RESOLVED': 'success',
                    'CLOSED': 'danger'
                };

                const priorityColors = {
                    'low': 'success',
                    'medium': 'warning',
                    'high': 'danger',
                    'urgent': 'dark'
                };

                let timelineHtml = '';
                complaint.timeline.forEach(item => {
                    const itemClass = item.completed ? (item.current ? 'current' : 'completed') : '';
                    timelineHtml += `
                        <div class="timeline-item ${itemClass}">
                            <div class="card border-0">
                                <div class="card-body py-2">
                                    <h6 class="mb-1">${item.title}</h6>
                                    <p class="text-muted small mb-1">${item.description}</p>
                                    ${item.date ? `<small class="text-success"><i class="fas fa-check me-1"></i>${item.date}</small>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });

                const html = `
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-file-alt me-2"></i>Complaint Found
                                </h5>
                                <span class="badge bg-${statusColors[complaint.status]} fs-6">
                                    ${complaint.status.replace('_', ' ')}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Tracking Number</h6>
                                    <p class="text-primary fw-bold">${complaint.tracking_number}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Priority</h6>
                                    <span class="badge bg-${priorityColors[complaint.priority]} text-uppercase">${complaint.priority}</span>
                                </div>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Category</h6>
                                    <p>${complaint.category}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Assigned To</h6>
                                    <p>${complaint.assigned_to}</p>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h6>Description</h6>
                                <p>${complaint.description}</p>
                            </div>
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6>Submitted Date</h6>
                                    <p>${complaint.submitted_date}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Last Updated</h6>
                                    <p>${complaint.last_updated}</p>
                                </div>
                            </div>
                            
                            <h6 class="mb-3">Progress Timeline</h6>
                            <div class="timeline">
                                ${timelineHtml}
                            </div>
                            
                            ${complaint.attachments.length > 0 ? `
                                <h6 class="mb-3">Attachments</h6>
                                <div class="row">
                                    ${complaint.attachments.map(file => `
                                        <div class="col-md-6 mb-2">
                                            <div class="d-flex align-items-center p-2 border rounded">
                                                <i class="fas fa-file me-2"></i>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold">${file.name}</div>
                                                    <small class="text-muted">${file.size}</small>
                                                </div>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : ''}
                            
                            <div class="text-center mt-4">
                                <button class="btn btn-primary me-2" onclick="trackingSystem.showComplaintDetails('${complaint.tracking_number}')">
                                    <i class="fas fa-eye me-2"></i>View Full Details
                                </button>
                                <button class="btn btn-outline-secondary me-2" onclick="trackingSystem.downloadReport('${complaint.tracking_number}')">
                                    <i class="fas fa-download me-2"></i>Download Report
                                </button>
                                <button class="btn btn-outline-info" onclick="trackingSystem.subscribeUpdates('${complaint.tracking_number}')">
                                    <i class="fas fa-bell me-2"></i>Get Updates
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                resultsDiv.innerHTML = html;
                resultsDiv.style.display = 'block';
                resultsDiv.scrollIntoView({ behavior: 'smooth' });
            }

            displayMultipleComplaints(complaints) {
                const resultsDiv = document.getElementById('searchResults');
                
                if (complaints.length === 0) {
                    resultsDiv.innerHTML = `
                        <div class="card">
                            <div class="card-body text-center p-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h4>No Complaints Found</h4>
                                <p class="text-muted">No complaints found matching your search criteria.</p>
                                <button class="btn btn-primary" onclick="fileNewComplaint()">
                                    <i class="fas fa-plus me-2"></i>File New Complaint
                                </button>
                            </div>
                        </div>
                    `;
                    resultsDiv.style.display = 'block';
                    return;
                }

                const statusColors = {
                    'RECEIVED': 'secondary',
                    'REVIEWING': 'primary', 
                    'IN_PROGRESS': 'warning',
                    'RESOLVED': 'success'
                };

                const priorityColors = {
                    'low': 'success',
                    'medium': 'warning',
                    'high': 'danger'
                };

                const complaintsHtml = complaints.map(complaint => `
                    <div class="col-md-6 mb-3">
                        <div class="card complaint-card h-100" onclick="trackingSystem.showComplaintDetails('${complaint.tracking_number}')">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="card-title mb-0">${complaint.title}</h6>
                                    <span class="badge bg-${statusColors[complaint.status]} ms-2">
                                        ${complaint.status}
                                    </span>
                                </div>
                                <p class="text-primary fw-bold">${complaint.tracking_number}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-${priorityColors[complaint.priority]} text-uppercase">
                                        ${complaint.priority}
                                    </span>
                                    <small class="text-muted">${complaint.submitted_date}</small>
                                </div>
                                <p class="text-muted small mt-2 mb-0">Category: ${complaint.category}</p>
                            </div>
                        </div>
                    </div>
                `).join('');

                const html = `
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-list me-2"></i>Found ${complaints.length} Complaint${complaints.length > 1 ? 's' : ''}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                ${complaintsHtml}
                            </div>
                        </div>
                    </div>
                `;

                resultsDiv.innerHTML = html;
                resultsDiv.style.display = 'block';
                resultsDiv.scrollIntoView({ behavior: 'smooth' });
            }

            showComplaintDetails(trackingNumber) {
                const complaint = this.generateMockComplaint(trackingNumber);
                
                const modalBody = document.getElementById('complaintDetails');
                modalBody.innerHTML = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Tracking Number:</strong><br>
                            <span class="text-primary">${complaint.tracking_number}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge bg-primary">${complaint.status}</span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Category:</strong><br>
                            ${complaint.category}
                        </div>
                        <div class="col-md-6">
                            <strong>Priority:</strong><br>
                            <span class="badge bg-warning">${complaint.priority}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Title:</strong><br>
                        ${complaint.title}
                    </div>
                    
                    <div class="mb-3">
                        <strong>Description:</strong><br>
                        ${complaint.description}
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Submitted:</strong><br>
                            ${complaint.submitted_date}
                        </div>
                        <div class="col-md-6">
                            <strong>Last Updated:</strong><br>
                            ${complaint.last_updated}
                        </div>
                    </div>
                `;
                
                const modal = new bootstrap.Modal(document.getElementById('complaintModal'));
                modal.show();
            }

            showLoading() {
                const resultsDiv = document.getElementById('searchResults');
                resultsDiv.innerHTML = `
                    <div class="card">
                        <div class="card-body text-center p-5">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5>Searching...</h5>
                            <p class="text-muted">Please wait while we search for your complaint.</p>
                        </div>
                    </div>
                `;
                resultsDiv.style.display = 'block';
            }

            showError(message) {
                const resultsDiv = document.getElementById('searchResults');
                resultsDiv.innerHTML = `
                    <div class="card border-danger">
                        <div class="card-body text-center p-5">
                            <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                            <h5 class="text-danger">Search Failed</h5>
                            <p class="text-muted">${message}</p>
                            <button class="btn btn-outline-primary" onclick="document.getElementById('searchResults').style.display='none'">
                                Try Again
                            </button>
                        </div>
                    </div>
                `;
                resultsDiv.style.display = 'block';
            }

            delay(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }

            downloadReport(trackingNumber) {
                alert(`Downloading report for ${trackingNumber}...`);
            }

            subscribeUpdates(trackingNumber) {
                alert(`You will receive updates for ${trackingNumber} via email and SMS.`);
            }
        }

        // Initialize the tracking system
        const trackingSystem = new ComplaintTrackingSystem();

        // Global functions
        function searchByTracking() {
            trackingSystem.searchByTracking();
        }

        function searchByPersonalDetails() {
            trackingSystem.searchByPersonalDetails();
        }

        function fileNewComplaint() {
            window.location.href = '/complaints/file';
        }

        function downloadReport() {
            alert('General report download functionality would be implemented here.');
        }

        function requestUpdate() {
            alert('Update request functionality would be implemented here.');
        }

        function contactSupport() {
            alert('Support contact functionality would be implemented here.');
        }

        function showFAQ() {
            window.open('/faq', '_blank');
        }

        function downloadComplaintReport() {
            alert('Downloading detailed complaint report...');
        }
    </script>
@endsection
