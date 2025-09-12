<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Complaint - One Sri Lanka</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
        }
        .tracking-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-top: -50px;
            position: relative;
            z-index: 10;
        }
        .status-timeline {
            position: relative;
            padding-left: 3rem;
        }
        .status-item {
            position: relative;
            padding: 1.5rem 0;
            border-left: 3px solid #e9ecef;
        }
        .status-item.completed {
            border-left-color: #28a745;
        }
        .status-item.active {
            border-left-color: #007bff;
        }
        .status-icon {
            position: absolute;
            left: -12px;
            top: 1.5rem;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: white;
        }
        .status-icon.completed {
            background: #28a745;
        }
        .status-icon.active {
            background: #007bff;
        }
        .complaint-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
        }
        .feedback-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-top: 2rem;
        }
        .star-rating {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin: 1rem 0;
        }
        .star {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }
        .star:hover,
        .star.active {
            color: #ffc107;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
        }
        .floating-actions {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .quick-stats {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="bg-light">

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Track Your Complaint</h1>
                <p class="lead mb-4">Stay updated on your complaint status and resolution progress</p>
            </div>
            <div class="col-lg-4 text-center">
                <div class="d-inline-block p-4 bg-white bg-opacity-10 rounded-circle">
                    <i class="fas fa-search fa-4x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Tracking Search Card -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="tracking-card">
                <div class="text-center mb-4">
                    <h3>Enter Your Tracking Details</h3>
                    <p class="text-muted">Use your tracking number and verification details to check status</p>
                </div>

                <form id="trackingForm">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label">Tracking Number</label>
                                <input type="text" class="form-control form-control-lg" id="trackingNumber" 
                                       placeholder="e.g., RN-2024-001234" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Verification</label>
                                <select class="form-control form-control-lg" id="verificationType">
                                    <option value="phone">Phone Number</option>
                                    <option value="email">Email Address</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" id="verificationLabel">Phone Number</label>
                        <input type="text" class="form-control form-control-lg" id="verificationValue" 
                               placeholder="Enter your phone number" required>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary-custom btn-lg">
                            <i class="fas fa-search me-2"></i>Track Complaint
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Complaint Details Section (Hidden initially) -->
    <div class="row mt-5" id="complaintDetails" style="display: none;">
        <div class="col-lg-8">
            <!-- Complaint Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Complaint Details</h5>
                </div>
                <div class="card-body">
                    <div class="complaint-details" id="complaintInfo">
                        <!-- Complaint details will be loaded here -->
                    </div>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-timeline me-2"></i>Status Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="status-timeline" id="statusTimeline">
                        <!-- Timeline will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="quick-stats mb-4">
                <h6><i class="fas fa-chart-pie me-2"></i>Quick Info</h6>
                <div id="quickStats">
                    <!-- Stats will be loaded here -->
                </div>
            </div>

            <!-- Actions -->
            <div class="quick-stats">
                <h6><i class="fas fa-tools me-2"></i>Available Actions</h6>
                <div class="d-grid gap-2" id="availableActions">
                    <!-- Actions will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Feedback Section (Only for resolved complaints) -->
    <div class="row" id="feedbackSection" style="display: none;">
        <div class="col-lg-8 mx-auto">
            <div class="feedback-section">
                <div class="text-center mb-4">
                    <h4><i class="fas fa-star me-2"></i>Rate Your Experience</h4>
                    <p class="text-muted">Help us improve our service by sharing your feedback</p>
                </div>

                <form id="feedbackForm">
                    <input type="hidden" id="feedbackTrackingNumber">
                    
                    <div class="text-center mb-4">
                        <label class="form-label">Overall Satisfaction</label>
                        <div class="star-rating" id="starRating">
                            <i class="fas fa-star star" data-rating="1"></i>
                            <i class="fas fa-star star" data-rating="2"></i>
                            <i class="fas fa-star star" data-rating="3"></i>
                            <i class="fas fa-star star" data-rating="4"></i>
                            <i class="fas fa-star star" data-rating="5"></i>
                        </div>
                        <input type="hidden" id="rating" name="rating" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Comments</label>
                        <textarea class="form-control" id="feedback" name="feedback" rows="4" 
                                  placeholder="Share your experience with our complaint resolution process..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Suggestions for Improvement</label>
                        <textarea class="form-control" id="improvements" name="improve_suggestions" rows="3" 
                                  placeholder="How can we improve our service?"></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Floating Actions -->
<div class="floating-actions">
    <div class="btn-group-vertical">
        <button class="btn btn-primary rounded-circle p-3 mb-2" onclick="showHelp()" title="Help">
            <i class="fas fa-question"></i>
        </button>
        <button class="btn btn-success rounded-circle p-3" onclick="fileNewComplaint()" title="File New Complaint">
            <i class="fas fa-plus"></i>
        </button>
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
                <h4>Thank You for Your Feedback!</h4>
                <p class="text-muted">Your feedback has been submitted successfully and will help us improve our services.</p>
                
                <div class="d-grid gap-2 mt-4">
                    <button class="btn btn-primary" onclick="hideModal()">Close</button>
                    <button class="btn btn-outline-secondary" onclick="fileNewComplaint()">File Another Complaint</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// Complaint Tracking System
class ComplaintTrackingSystem {
    constructor() {
        this.apiBaseUrl = 'http://127.0.0.1:8080/api';
        this.currentRating = 0;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.setupStarRating();
        
        // Check if tracking number is in URL
        const urlParams = new URLSearchParams(window.location.search);
        const trackingNumber = urlParams.get('tracking');
        if (trackingNumber) {
            document.getElementById('trackingNumber').value = trackingNumber;
        }
    }

    setupEventListeners() {
        // Tracking form submission
        document.getElementById('trackingForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.trackComplaint();
        });

        // Verification type change
        document.getElementById('verificationType').addEventListener('change', (e) => {
            const label = document.getElementById('verificationLabel');
            const input = document.getElementById('verificationValue');
            
            if (e.target.value === 'email') {
                label.textContent = 'Email Address';
                input.placeholder = 'Enter your email address';
                input.type = 'email';
            } else {
                label.textContent = 'Phone Number';
                input.placeholder = 'Enter your phone number';
                input.type = 'text';
            }
        });

        // Feedback form submission
        document.getElementById('feedbackForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitFeedback();
        });
    }

    setupStarRating() {
        const stars = document.querySelectorAll('.star');
        
        stars.forEach(star => {
            star.addEventListener('mouseenter', () => {
                const rating = parseInt(star.dataset.rating);
                this.highlightStars(rating);
            });
            
            star.addEventListener('mouseleave', () => {
                this.highlightStars(this.currentRating);
            });
            
            star.addEventListener('click', () => {
                this.currentRating = parseInt(star.dataset.rating);
                document.getElementById('rating').value = this.currentRating;
                this.highlightStars(this.currentRating);
            });
        });
    }

    highlightStars(rating) {
        const stars = document.querySelectorAll('.star');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    async trackComplaint() {
        const trackingNumber = document.getElementById('trackingNumber').value;
        const verificationType = document.getElementById('verificationType').value;
        const verificationValue = document.getElementById('verificationValue').value;

        const trackingData = {
            tracking_number: trackingNumber
        };

        if (verificationType === 'email') {
            trackingData.email = verificationValue;
        } else {
            trackingData.phone = verificationValue;
        }

        try {
            this.showLoading(true);
            
            const response = await fetch('/complaints/track', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(trackingData)
            });

            const data = await response.json();

            if (data.success) {
                this.displayComplaintDetails(data.data);
            } else {
                this.showError(data.message || 'Complaint not found or access denied');
            }

        } catch (error) {
            console.error('Error tracking complaint:', error);
            this.showError('Unable to connect to tracking service. Please try again.');
        } finally {
            this.showLoading(false);
        }
    }

    displayComplaintDetails(complaint) {
        // Show complaint details section
        document.getElementById('complaintDetails').style.display = 'block';
        
        // Update complaint information
        this.renderComplaintInfo(complaint);
        
        // Update status timeline
        this.renderStatusTimeline(complaint.status_history || []);
        
        // Update quick stats
        this.renderQuickStats(complaint);
        
        // Update available actions
        this.renderAvailableActions(complaint);
        
        // Show feedback section if complaint is resolved
        if (complaint.status === 'resolved' && !complaint.feedback_submitted) {
            document.getElementById('feedbackSection').style.display = 'block';
            document.getElementById('feedbackTrackingNumber').value = complaint.tracking_number;
        }
        
        // Scroll to details
        document.getElementById('complaintDetails').scrollIntoView({ behavior: 'smooth' });
    }

    renderComplaintInfo(complaint) {
        const infoContainer = document.getElementById('complaintInfo');
        
        infoContainer.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Tracking Number</h6>
                    <p class="fw-bold text-primary">${complaint.tracking_number}</p>
                </div>
                <div class="col-md-6">
                    <h6>Current Status</h6>
                    <p><span class="badge bg-${this.getStatusColor(complaint.status)} fs-6">${complaint.status.toUpperCase()}</span></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Category</h6>
                    <p>${complaint.category}</p>
                </div>
                <div class="col-md-6">
                    <h6>Priority</h6>
                    <p><span class="badge bg-${this.getPriorityColor(complaint.priority)}">${complaint.priority.toUpperCase()}</span></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6>Submitted Date</h6>
                    <p>${new Date(complaint.created_at).toLocaleDateString()}</p>
                </div>
                <div class="col-md-6">
                    <h6>Expected Resolution</h6>
                    <p>${complaint.expected_resolution || 'Within 72 hours'}</p>
                </div>
            </div>
            
            <hr>
            
            <h6>Description</h6>
            <p>${complaint.description || 'No description provided'}</p>
            
            ${complaint.assigned_officer ? `
                <h6>Assigned Officer</h6>
                <p><i class="fas fa-user me-2"></i>${complaint.assigned_officer}</p>
            ` : ''}
            
            ${complaint.resolution_notes ? `
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle me-2"></i>Resolution Notes</h6>
                    <p class="mb-0">${complaint.resolution_notes}</p>
                </div>
            ` : ''}
        `;
    }

    renderStatusTimeline(statusHistory) {
        const timeline = document.getElementById('statusTimeline');
        
        const statuses = [
            { key: 'submitted', label: 'Complaint Submitted', icon: 'fas fa-paper-plane' },
            { key: 'acknowledged', label: 'Acknowledged', icon: 'fas fa-eye' },
            { key: 'investigating', label: 'Under Investigation', icon: 'fas fa-search' },
            { key: 'in_progress', label: 'Resolution in Progress', icon: 'fas fa-cogs' },
            { key: 'resolved', label: 'Resolved', icon: 'fas fa-check-circle' }
        ];

        let timelineHtml = '';
        
        statuses.forEach((status, index) => {
            const historyItem = statusHistory.find(h => h.status === status.key);
            const isCompleted = historyItem !== undefined;
            const isActive = !isCompleted && index === statusHistory.length;
            
            timelineHtml += `
                <div class="status-item ${isCompleted ? 'completed' : (isActive ? 'active' : '')}">
                    <div class="status-icon ${isCompleted ? 'completed' : (isActive ? 'active' : '')}">
                        <i class="${status.icon}"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">${status.label}</h6>
                        ${historyItem ? `
                            <p class="text-muted small mb-1">${new Date(historyItem.created_at).toLocaleString()}</p>
                            ${historyItem.notes ? `<p class="small">${historyItem.notes}</p>` : ''}
                        ` : '<p class="text-muted small">Pending</p>'}
                    </div>
                </div>
            `;
        });
        
        timeline.innerHTML = timelineHtml;
    }

    renderQuickStats(complaint) {
        const statsContainer = document.getElementById('quickStats');
        
        const daysSinceSubmitted = Math.floor((new Date() - new Date(complaint.created_at)) / (1000 * 60 * 60 * 24));
        
        statsContainer.innerHTML = `
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span>Days Since Submitted:</span>
                <strong>${daysSinceSubmitted}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span>Priority Level:</span>
                <strong>${complaint.priority.toUpperCase()}</strong>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span>Department:</span>
                <strong>${complaint.department || 'General'}</strong>
            </div>
            <div class="d-flex justify-content-between py-2">
                <span>Reference ID:</span>
                <strong>${complaint.id}</strong>
            </div>
        `;
    }

    renderAvailableActions(complaint) {
        const actionsContainer = document.getElementById('availableActions');
        
        let actionsHtml = '';
        
        // Always available actions
        actionsHtml += `
            <button class="btn btn-outline-primary btn-sm" onclick="trackingSystem.downloadReport('${complaint.tracking_number}')">
                <i class="fas fa-download me-2"></i>Download Report
            </button>
            <button class="btn btn-outline-info btn-sm" onclick="trackingSystem.shareComplaint('${complaint.tracking_number}')">
                <i class="fas fa-share me-2"></i>Share Status
            </button>
        `;
        
        // Status-specific actions
        if (complaint.status === 'submitted' || complaint.status === 'acknowledged') {
            actionsHtml += `
                <button class="btn btn-outline-warning btn-sm" onclick="trackingSystem.addMoreInfo('${complaint.tracking_number}')">
                    <i class="fas fa-plus me-2"></i>Add Information
                </button>
            `;
        }
        
        if (complaint.status !== 'resolved' && complaint.status !== 'closed') {
            actionsHtml += `
                <button class="btn btn-outline-danger btn-sm" onclick="trackingSystem.escalateComplaint('${complaint.tracking_number}')">
                    <i class="fas fa-exclamation-triangle me-2"></i>Escalate
                </button>
            `;
        }
        
        actionsContainer.innerHTML = actionsHtml;
    }

    async submitFeedback() {
        const feedbackData = {
            tracking_number: document.getElementById('feedbackTrackingNumber').value,
            rating: parseInt(document.getElementById('rating').value),
            feedback: document.getElementById('feedback').value,
            improve_suggestions: document.getElementById('improvements').value
        };

        if (!feedbackData.rating) {
            this.showError('Please provide a rating');
            return;
        }

        try {
            const response = await fetch('/complaints/feedback', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(feedbackData)
            });

            const data = await response.json();

            if (data.success) {
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
                
                // Hide feedback section
                document.getElementById('feedbackSection').style.display = 'none';
            } else {
                this.showError(data.message || 'Failed to submit feedback');
            }

        } catch (error) {
            console.error('Error submitting feedback:', error);
            this.showError('Unable to submit feedback. Please try again.');
        }
    }

    getStatusColor(status) {
        const colors = {
            'submitted': 'primary',
            'acknowledged': 'info',
            'investigating': 'warning',
            'in_progress': 'warning',
            'resolved': 'success',
            'closed': 'secondary'
        };
        return colors[status] || 'secondary';
    }

    getPriorityColor(priority) {
        const colors = {
            'low': 'success',
            'medium': 'warning',
            'high': 'danger',
            'urgent': 'danger'
        };
        return colors[priority] || 'secondary';
    }

    showLoading(show) {
        const button = document.querySelector('#trackingForm button[type="submit"]');
        if (show) {
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
            button.disabled = true;
        } else {
            button.innerHTML = '<i class="fas fa-search me-2"></i>Track Complaint';
            button.disabled = false;
        }
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

    // Action handlers
    downloadReport(trackingNumber) {
        // Implementation for downloading complaint report
        alert(`Downloading report for ${trackingNumber}`);
    }

    shareComplaint(trackingNumber) {
        const shareUrl = `${window.location.origin}/complaints/track?tracking=${trackingNumber}`;
        navigator.clipboard.writeText(shareUrl);
        this.showSuccess('Tracking link copied to clipboard!');
    }

    addMoreInfo(trackingNumber) {
        alert(`Add more information for ${trackingNumber}`);
    }

    escalateComplaint(trackingNumber) {
        if (confirm('Are you sure you want to escalate this complaint? This will mark it as urgent and notify supervisors.')) {
            alert(`Escalating complaint ${trackingNumber}`);
        }
    }

    showSuccess(message) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
        alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        alert.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alert);
        
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 3000);
    }
}

// Global functions
function showHelp() {
    alert('Help system would provide tracking assistance and FAQ');
}

function fileNewComplaint() {
    window.location.href = '/complaints/file';
}

function hideModal() {
    bootstrap.Modal.getInstance(document.getElementById('successModal')).hide();
}

// Initialize the system
const trackingSystem = new ComplaintTrackingSystem();

// Add CSRF token to page head if not present
if (!document.querySelector('meta[name="csrf-token"]')) {
    const meta = document.createElement('meta');
    meta.name = 'csrf-token';
    meta.content = '{{ csrf_token() }}';
    document.head.appendChild(meta);
}
</script>

</body>
</html>
