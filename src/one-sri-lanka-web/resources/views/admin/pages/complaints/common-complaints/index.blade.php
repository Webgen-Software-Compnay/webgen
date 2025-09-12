@extends('admin.layouts.master')
@section('content')
    @component('admin.components.breadcrumb')
    @slot('title') Common Complaints Management @endslot
    @slot('subtitle') Manage frequently reported issues and their forms @endslot
    @endcomponent

    <!-- Content area -->
    <div class="content">
        <!-- Main Content -->
        <div class="row">
            <!-- Common Complaints List -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list-ul me-2"></i>Common Complaints
                        </h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-secondary btn-sm" onclick="exportComplaints()">
                                <i class="fas fa-download me-2"></i>Export Data
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="showAddComplaintModal()">
                                <i class="fas fa-plus me-2"></i>Add New
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover datatable-column-search-inputs" id="complaintsTable">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Form Status</th>
                                        <th>Status</th>
                                        <th>Usage</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic content will be loaded here -->
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
                            <!-- Dynamic content will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /content area -->

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
                    <button type="button" class="btn btn-primary" onclick="saveComplaint()">
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
                            <div class="bg-light rounded p-3">
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
                    <button type="button" class="btn btn-primary" onclick="saveFormStructure()">
                        <i class="fas fa-save me-2"></i>Save Form
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('center-scripts')
    <script src="{{ asset('assets/admin/js/jquery/jquery.min.js')}}"></script>
    <script src="{{ asset('assets/admin/js/vendor/tables/datatables/datatables.min.js')}}"></script>
@endsection

@section('scripts')
    <!-- Set URL variables for JavaScript -->
    <script>
        window.commonComplaintsUrl = '{{ route("admin.common-complaints.get") }}';
        window.commonComplaintsEditUrl = '{{ route("admin.common-complaints.edit", ":id") }}';
        window.commonComplaintsDeleteUrl = '{{ route("admin.common-complaints.destroy", ":id") }}';
        window.commonComplaintsFormBuilderUrl = '{{ route("admin.common-complaints.form-builder", ":id") }}';
        window.commonComplaintsUsageUrl = '{{ route("admin.common-complaints.usage", ":id") }}';
        window.commonComplaintsAnalyticsUrl = '{{ route("admin.common-complaints.analytics", ":id") }}';
        window.commonComplaintsDuplicateUrl = '{{ route("admin.common-complaints.duplicate", ":id") }}';
        window.commonComplaintsStoreUrl = '{{ route("admin.common-complaints.store") }}';
        window.commonComplaintsUpdateUrl = '{{ route("admin.common-complaints.update", ":id") }}';
    </script>
    
    <!-- Include custom common complaints JavaScript -->
    <script src="{{ asset('assets/admin/pages/common_complaints.js')}}"></script>
    
    <script>
        // Simple wrapper functions for modal interactions
        function showAddComplaintModal() {
            const modal = new bootstrap.Modal(document.getElementById('complaintModal'));
            document.getElementById('modalTitle').textContent = 'Add New Common Complaint';
            document.getElementById('complaintForm').reset();
            document.getElementById('complaintId').value = '';
            modal.show();
        }

        function saveComplaint() {
            const form = document.getElementById('complaintForm');
            if (form.checkValidity()) {
                // Implement your save logic here
                console.log('Saving complaint...');
                
                // Example AJAX save
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                
                // Add your save endpoint here
                console.log('Data to save:', data);
                
                // Close modal after save
                bootstrap.Modal.getInstance(document.getElementById('complaintModal')).hide();
                
                // Refresh table
                if (window.refreshCommonComplaintsTable) {
                    window.refreshCommonComplaintsTable();
                }
            } else {
                form.reportValidity();
            }
        }

        function exportComplaints() {
            if (window.exportCommonComplaints) {
                window.exportCommonComplaints('csv');
            }
        }

        function saveFormStructure() {
            console.log('Saving form structure...');
            bootstrap.Modal.getInstance(document.getElementById('formBuilderModal')).hide();
        }
    </script>
@endsection