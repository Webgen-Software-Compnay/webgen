<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Common Complaints Management - Review Nation Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem 0;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        .complaint-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .complaint-card:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
        }
        .form-builder-preview {
            background: #f8f9fa;
            border: 1px dashed #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            min-height: 200px;
        }
        .field-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            color: white;
        }
    </style>
</head>
<body class="bg-light">

<!-- Admin Header -->
<div class="admin-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">
                    <i class="fas fa-clipboard-list me-3"></i>
                    Common Complaints Management
                </h2>
                <p class="mb-0 opacity-75">Manage frequently reported issues and their forms</p>
            </div>
            <div>
                <button class="btn btn-light me-2" onclick="exportComplaints()">
                    <i class="fas fa-download me-2"></i>Export Data
                </button>
                <button class="btn btn-success" onclick="showAddComplaintModal()">
                    <i class="fas fa-plus me-2"></i>Add New Common Complaint
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0" id="totalComplaints">Loading...</h4>
                        <small class="text-muted">Total Common Complaints</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0" id="activeComplaints">Loading...</h4>
                        <small class="text-muted">Active Templates</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-info">
                        <i class="fas fa-wpforms"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0" id="formsCount">Loading...</h4>
                        <small class="text-muted">Custom Forms</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0" id="usageCount">Loading...</h4>
                        <small class="text-muted">Times Used This Month</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Common Complaints List -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list-ul me-2"></i>Common Complaints
                    </h5>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" id="searchComplaints" placeholder="Search complaints...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="complaintsTable">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Form</th>
                                    <th>Status</th>
                                    <th>Usage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="complaintsTableBody">
                                <!-- Dynamic content -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Builder Panel -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-magic me-2"></i>Quick Form Builder
                    </h6>
                </div>
                <div class="card-body">
                    <div id="quickFormBuilder">
                        <p class="text-muted text-center">Select a complaint to build/edit its form</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Recent Activity
                    </h6>
                </div>
                <div class="card-body">
                    <div id="recentActivity">
                        <!-- Dynamic content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Common Complaint Modal -->
<div class="modal fade" id="complaintModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Common Complaint</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="complaintForm">
                    <input type="hidden" id="complaintId">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="complaintTitle" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-control" id="complaintCategory" required>
                                    <option value="">Select Category</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="complaintDescription" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Priority</label>
                                <select class="form-control" id="complaintPriority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-control" id="complaintStatus">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Form Structure (JSON)</label>
                        <textarea class="form-control" id="formStructure" rows="8" placeholder='[{"field_type": "text", "field_name": "example", "label": "Example Field", "required": true}]'></textarea>
                        <div class="form-text">Define the form fields for this complaint type in JSON format</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="createForm">
                            <label class="form-check-label">
                                Auto-create form template
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-gradient" onclick="saveComplaint()">
                    <i class="fas fa-save me-2"></i>Save Complaint
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form Builder Modal -->
<div class="modal fade" id="formBuilderModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Builder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div id="formBuilderContainer">
                            <!-- Form builder will be loaded here -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-builder-preview">
                            <h6>Form Preview</h6>
                            <div id="formPreviewContainer">
                                <p class="text-muted">Form preview will appear here</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-gradient" onclick="saveFormStructure()">
                    <i class="fas fa-save me-2"></i>Save Form
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
// Common Complaints Management System
class CommonComplaintsManager {
    constructor() {
        this.apiBaseUrl = 'http://127.0.0.1:8080/api';
        this.currentComplaint = null;
        this.formBuilder = null;
        this.init();
    }

    async init() {
        await this.loadStatistics();
        await this.loadCategories();
        await this.loadComplaints();
        this.setupEventListeners();
        this.initializeDataTable();
    }

    setupEventListeners() {
        // Search functionality
        document.getElementById('searchComplaints').addEventListener('input', (e) => {
            this.filterComplaints(e.target.value);
        });

        // Form validation
        document.getElementById('complaintForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveComplaint();
        });
    }

    async loadStatistics() {
        try {
            // Simulate API calls - replace with real endpoints
            const stats = {
                total: 47,
                active: 32,
                forms: 15,
                usage: 1247
            };

            document.getElementById('totalComplaints').textContent = stats.total;
            document.getElementById('activeComplaints').textContent = stats.active;
            document.getElementById('formsCount').textContent = stats.forms;
            document.getElementById('usageCount').textContent = stats.usage;
        } catch (error) {
            console.error('Error loading statistics:', error);
        }
    }

    async loadCategories() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/public/complaint-categories`);
            const data = await response.json();
            
            const categorySelect = document.getElementById('complaintCategory');
            categorySelect.innerHTML = '<option value="">Select Category</option>';
            
            if (data.success) {
                data.data.forEach(category => {
                    categorySelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
                });
            }
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    }

    async loadComplaints() {
        try {
            // Sample data - replace with real API call
            const sampleComplaints = [
                {
                    id: 1,
                    title: "Bus Service Delay",
                    category: "Transportation",
                    hasForm: true,
                    status: "active",
                    usageCount: 156,
                    description: "Frequent delays in bus services during peak hours"
                },
                {
                    id: 2,
                    title: "Road Pothole Issues",
                    category: "Infrastructure", 
                    hasForm: true,
                    status: "active",
                    usageCount: 89,
                    description: "Road surface damage affecting vehicle traffic"
                },
                {
                    id: 3,
                    title: "Water Supply Interruption",
                    category: "Utilities",
                    hasForm: false,
                    status: "active",
                    usageCount: 203,
                    description: "Unexpected water supply cuts in residential areas"
                },
                {
                    id: 4,
                    title: "Hospital Waiting Time",
                    category: "Healthcare",
                    hasForm: true,
                    status: "active",
                    usageCount: 67,
                    description: "Extended waiting times for outpatient services"
                },
                {
                    id: 5,
                    title: "School Infrastructure",
                    category: "Education",
                    hasForm: false,
                    status: "draft",
                    usageCount: 23,
                    description: "Issues with school building maintenance and facilities"
                }
            ];

            this.renderComplaints(sampleComplaints);
            this.loadRecentActivity();
        } catch (error) {
            console.error('Error loading complaints:', error);
        }
    }

    renderComplaints(complaints) {
        const tbody = document.getElementById('complaintsTableBody');
        tbody.innerHTML = '';

        complaints.forEach(complaint => {
            const statusClass = this.getStatusClass(complaint.status);
            const formBadge = complaint.hasForm ? 
                '<span class="badge bg-success">Has Form</span>' : 
                '<span class="badge bg-warning">No Form</span>';

            const row = `
                <tr>
                    <td>
                        <div>
                            <strong>${complaint.title}</strong>
                            <br><small class="text-muted">${complaint.description}</small>
                        </div>
                    </td>
                    <td>${complaint.category}</td>
                    <td>${formBadge}</td>
                    <td><span class="status-badge ${statusClass}">${complaint.status.charAt(0).toUpperCase() + complaint.status.slice(1)}</span></td>
                    <td>
                        <span class="badge bg-info">${complaint.usageCount}</span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="complaintManager.editComplaint(${complaint.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-success" onclick="complaintManager.openFormBuilder(${complaint.id})" title="Form Builder">
                                <i class="fas fa-wpforms"></i>
                            </button>
                            <button class="btn btn-outline-info" onclick="complaintManager.viewUsage(${complaint.id})" title="View Usage">
                                <i class="fas fa-chart-bar"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="complaintManager.deleteComplaint(${complaint.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    getStatusClass(status) {
        const classes = {
            'active': 'bg-success',
            'inactive': 'bg-secondary', 
            'draft': 'bg-warning'
        };
        return classes[status] || 'bg-secondary';
    }

    initializeDataTable() {
        $('#complaintsTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [5] }
            ]
        });
    }

    filterComplaints(searchTerm) {
        const table = $('#complaintsTable').DataTable();
        table.search(searchTerm).draw();
    }

    showAddComplaintModal() {
        document.getElementById('modalTitle').textContent = 'Add New Common Complaint';
        document.getElementById('complaintForm').reset();
        document.getElementById('complaintId').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('complaintModal'));
        modal.show();
    }

    async editComplaint(id) {
        // Load complaint data - replace with real API call
        const complaint = {
            id: id,
            title: "Sample Complaint",
            category_id: 2,
            description: "Sample description",
            priority: "medium",
            status: "active",
            form_structure: [
                {
                    field_type: "text",
                    field_name: "sample_field",
                    label: "Sample Field",
                    required: true
                }
            ]
        };

        document.getElementById('modalTitle').textContent = 'Edit Common Complaint';
        document.getElementById('complaintId').value = complaint.id;
        document.getElementById('complaintTitle').value = complaint.title;
        document.getElementById('complaintCategory').value = complaint.category_id;
        document.getElementById('complaintDescription').value = complaint.description;
        document.getElementById('complaintPriority').value = complaint.priority;
        document.getElementById('complaintStatus').value = complaint.status;
        document.getElementById('formStructure').value = JSON.stringify(complaint.form_structure, null, 2);

        const modal = new bootstrap.Modal(document.getElementById('complaintModal'));
        modal.show();
    }

    async saveComplaint() {
        const formData = {
            title: document.getElementById('complaintTitle').value,
            category_id: document.getElementById('complaintCategory').value,
            description: document.getElementById('complaintDescription').value,
            priority: document.getElementById('complaintPriority').value,
            status: document.getElementById('complaintStatus').value
        };

        try {
            const formStructureText = document.getElementById('formStructure').value;
            if (formStructureText) {
                formData.form_structure = JSON.parse(formStructureText);
            }
        } catch (error) {
            alert('Invalid JSON in form structure');
            return;
        }

        const complaintId = document.getElementById('complaintId').value;
        
        try {
            let response;
            if (complaintId) {
                // Update existing complaint
                response = await fetch(`${this.apiBaseUrl}/admin/common-complaints/${complaintId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });
            } else {
                // Create new complaint
                response = await fetch(`${this.apiBaseUrl}/admin/common-complaints`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });
            }

            if (response.ok) {
                bootstrap.Modal.getInstance(document.getElementById('complaintModal')).hide();
                await this.loadComplaints();
                this.showSuccessMessage('Complaint saved successfully!');
            } else {
                throw new Error('Failed to save complaint');
            }
        } catch (error) {
            console.error('Error saving complaint:', error);
            alert('Error saving complaint. Please try again.');
        }
    }

    openFormBuilder(complaintId) {
        this.currentComplaint = complaintId;
        
        // Load form builder interface
        document.getElementById('formBuilderContainer').innerHTML = `
            <div class="mb-3">
                <h6>Building form for Complaint ID: ${complaintId}</h6>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Use the integrated form builder to create custom forms for this complaint type.
                </div>
            </div>
            
            <div class="form-builder-tools mb-3">
                <div class="btn-group" role="group">
                    <button class="btn btn-outline-primary" onclick="complaintManager.addFormField('text')">
                        <i class="fas fa-font me-1"></i>Text
                    </button>
                    <button class="btn btn-outline-primary" onclick="complaintManager.addFormField('textarea')">
                        <i class="fas fa-align-left me-1"></i>Textarea
                    </button>
                    <button class="btn btn-outline-primary" onclick="complaintManager.addFormField('select')">
                        <i class="fas fa-list me-1"></i>Select
                    </button>
                    <button class="btn btn-outline-primary" onclick="complaintManager.addFormField('radio')">
                        <i class="fas fa-dot-circle me-1"></i>Radio
                    </button>
                    <button class="btn btn-outline-primary" onclick="complaintManager.addFormField('checkbox')">
                        <i class="fas fa-check-square me-1"></i>Checkbox
                    </button>
                </div>
            </div>
            
            <div id="formFieldsList">
                <p class="text-muted">Click buttons above to add form fields</p>
            </div>
        `;

        const modal = new bootstrap.Modal(document.getElementById('formBuilderModal'));
        modal.show();
    }

    addFormField(fieldType) {
        const fieldsList = document.getElementById('formFieldsList');
        const fieldId = 'field_' + Date.now();
        
        if (fieldsList.querySelector('p')) {
            fieldsList.innerHTML = '';
        }

        const fieldHtml = `
            <div class="field-item" data-field-id="${fieldId}">
                <div class="flex-grow-1">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" placeholder="Field Name" value="${fieldType}_field">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control form-control-sm" placeholder="Label" value="${fieldType.charAt(0).toUpperCase() + fieldType.slice(1)} Field">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control form-control-sm">
                                <option value="false">Optional</option>
                                <option value="true">Required</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-sm btn-outline-danger" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        fieldsList.insertAdjacentHTML('beforeend', fieldHtml);
        this.updateFormPreview();
    }

    updateFormPreview() {
        const fields = document.querySelectorAll('.field-item');
        let previewHtml = '<div class="border rounded p-2">';
        
        fields.forEach(field => {
            const inputs = field.querySelectorAll('input, select');
            const fieldName = inputs[0].value;
            const label = inputs[1].value;
            const required = inputs[2].value === 'true';
            
            previewHtml += `
                <div class="mb-2">
                    <label class="form-label small">${label} ${required ? '<span class="text-danger">*</span>' : ''}</label>
                    <input type="text" class="form-control form-control-sm" disabled>
                </div>
            `;
        });
        
        previewHtml += '</div>';
        
        document.getElementById('formPreviewContainer').innerHTML = previewHtml;
    }

    saveFormStructure() {
        const fields = document.querySelectorAll('.field-item');
        const formStructure = [];
        
        fields.forEach((field, index) => {
            const inputs = field.querySelectorAll('input, select');
            const fieldData = {
                field_type: "text", // This would be dynamic based on the field type
                field_name: inputs[0].value,
                label: inputs[1].value,
                required: inputs[2].value === 'true',
                order: index + 1
            };
            formStructure.push(fieldData);
        });

        // Save to API
        console.log('Saving form structure:', formStructure);
        
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('formBuilderModal')).hide();
        
        this.showSuccessMessage('Form structure saved successfully!');
    }

    async deleteComplaint(id) {
        if (confirm('Are you sure you want to delete this complaint? This action cannot be undone.')) {
            try {
                const response = await fetch(`${this.apiBaseUrl}/admin/common-complaints/${id}`, {
                    method: 'DELETE'
                });

                if (response.ok) {
                    await this.loadComplaints();
                    this.showSuccessMessage('Complaint deleted successfully!');
                } else {
                    throw new Error('Failed to delete complaint');
                }
            } catch (error) {
                console.error('Error deleting complaint:', error);
                alert('Error deleting complaint. Please try again.');
            }
        }
    }

    viewUsage(id) {
        // Show usage statistics modal
        alert(`Viewing usage statistics for complaint ID: ${id}\n\nThis would show:\n- Usage trends\n- Popular form fields\n- User feedback\n- Resolution rates`);
    }

    loadRecentActivity() {
        const activities = [
            { action: 'Created', item: 'Bus Delay Form', time: '2 hours ago', icon: 'fas fa-plus', color: 'text-success' },
            { action: 'Updated', item: 'Road Issues Template', time: '5 hours ago', icon: 'fas fa-edit', color: 'text-primary' },
            { action: 'Deleted', item: 'Old Water Form', time: '1 day ago', icon: 'fas fa-trash', color: 'text-danger' },
            { action: 'Published', item: 'Healthcare Form', time: '2 days ago', icon: 'fas fa-check', color: 'text-info' }
        ];

        const activityHtml = activities.map(activity => `
            <div class="d-flex align-items-center mb-2">
                <div class="me-3">
                    <i class="${activity.icon} ${activity.color}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="small">${activity.action} <strong>${activity.item}</strong></div>
                    <div class="text-muted" style="font-size: 0.75rem;">${activity.time}</div>
                </div>
            </div>
        `).join('');

        document.getElementById('recentActivity').innerHTML = activityHtml;
    }

    showSuccessMessage(message) {
        // Create and show success toast
        const toast = document.createElement('div');
        toast.className = 'toast position-fixed top-0 end-0 m-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">${message}</div>
        `;
        
        document.body.appendChild(toast);
        const bootstrapToast = new bootstrap.Toast(toast);
        bootstrapToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    exportComplaints() {
        // Export functionality
        const data = [
            ['Title', 'Category', 'Status', 'Usage Count', 'Has Form'],
            ['Bus Service Delay', 'Transportation', 'Active', '156', 'Yes'],
            ['Road Pothole Issues', 'Infrastructure', 'Active', '89', 'Yes'],
            // ... more data
        ];

        const csvContent = data.map(row => row.join(',')).join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = 'common-complaints-' + new Date().toISOString().slice(0, 10) + '.csv';
        a.click();
        
        window.URL.revokeObjectURL(url);
    }
}

// Initialize the system
const complaintManager = new CommonComplaintsManager();

// Global functions for onclick handlers
function showAddComplaintModal() {
    complaintManager.showAddComplaintModal();
}

function saveComplaint() {
    complaintManager.saveComplaint();
}

function exportComplaints() {
    complaintManager.exportComplaints();
}
</script>

</body>
</html>
