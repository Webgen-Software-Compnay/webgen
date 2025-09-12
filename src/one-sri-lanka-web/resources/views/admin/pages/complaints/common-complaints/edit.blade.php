@extends('admin.layouts.master')
@section('content')
    @component('admin.components.breadcrumb')
    @slot('title') Edit Common Complaint @endslot
    @slot('subtitle') Modify complaint details and form structure @endslot
    @endcomponent

    <!-- Content area -->
    <div class="content">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-edit me-2"></i>Edit Complaint: {{ $complaint['title'] ?? 'Unknown' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="editComplaintForm">
                            @csrf
                            @method('PUT')
                            <input type="hidden" id="complaintId" value="{{ $complaint['id'] ?? '' }}">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="title" id="title" 
                                               value="{{ $complaint['title'] ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-control" name="category" id="category" required>
                                            <option value="">Select Category</option>
                                            <option value="transportation" {{ ($complaint['category'] ?? '') == 'Transportation' ? 'selected' : '' }}>Transportation</option>
                                            <option value="infrastructure" {{ ($complaint['category'] ?? '') == 'Infrastructure' ? 'selected' : '' }}>Infrastructure</option>
                                            <option value="utilities" {{ ($complaint['category'] ?? '') == 'Utilities' ? 'selected' : '' }}>Utilities</option>
                                            <option value="healthcare" {{ ($complaint['category'] ?? '') == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                            <option value="education" {{ ($complaint['category'] ?? '') == 'Education' ? 'selected' : '' }}>Education</option>
                                            <option value="environment" {{ ($complaint['category'] ?? '') == 'Environment' ? 'selected' : '' }}>Environment</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="4">{{ $complaint['description'] ?? '' }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Priority</label>
                                        <select class="form-control" name="priority" id="priority">
                                            <option value="low" {{ ($complaint['priority'] ?? '') == 'low' ? 'selected' : '' }}>Low</option>
                                            <option value="medium" {{ ($complaint['priority'] ?? '') == 'medium' ? 'selected' : '' }}>Medium</option>
                                            <option value="high" {{ ($complaint['priority'] ?? '') == 'high' ? 'selected' : '' }}>High</option>
                                            <option value="urgent" {{ ($complaint['priority'] ?? '') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-control" name="status" id="status">
                                            <option value="active" {{ ($complaint['status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ ($complaint['status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="draft" {{ ($complaint['status'] ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Form Structure (JSON)</label>
                                <textarea class="form-control" name="form_structure" id="formStructure" rows="8" 
                                          placeholder='[{"field_type": "text", "field_name": "example", "label": "Example Field", "required": true}]'>{{ $complaint['form_structure'] ?? '' }}</textarea>
                                <div class="form-text">Define the form fields for this complaint type in JSON format</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('admin.common-complaints.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to List
                                    </a>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-info me-2" onclick="openFormBuilder()">
                                        <i class="fas fa-magic me-2"></i>Form Builder
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Complaint
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>Complaint Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>ID:</strong> #{{ $complaint['id'] ?? 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <strong>Usage Count:</strong> 
                            <span class="badge bg-info">{{ $complaint['usage_count'] ?? 0 }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Form Status:</strong>
                            @php
                                $formStatus = $complaint['form_status'] ?? 'not_set';
                                $statusClass = $formStatus === 'configured' ? 'success' : ($formStatus === 'pending' ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $formStatus)) }}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Created:</strong> {{ $complaint['created_at'] ?? 'N/A' }}
                        </div>
                        <div class="mb-3">
                            <strong>Last Updated:</strong> {{ $complaint['updated_at'] ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="fas fa-tools me-2"></i>Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" onclick="previewForm()">
                                <i class="fas fa-eye me-2"></i>Preview Form
                            </button>
                            <button class="btn btn-outline-info btn-sm" onclick="viewUsage()">
                                <i class="fas fa-chart-bar me-2"></i>View Usage Stats
                            </button>
                            <button class="btn btn-outline-warning btn-sm" onclick="duplicateComplaint()">
                                <i class="fas fa-copy me-2"></i>Duplicate
                            </button>
                        </div>
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
    <script>
        $(document).ready(function() {
            $('#editComplaintForm').on('submit', function(e) {
                e.preventDefault();
                updateComplaint();
            });
        });

        function updateComplaint() {
            const form = $('#editComplaintForm');
            const complaintId = $('#complaintId').val();
            const formData = {
                title: $('#title').val(),
                category: $('#category').val(),
                description: $('#description').val(),
                priority: $('#priority').val(),
                status: $('#status').val(),
                form_structure: $('#formStructure').val()
            };

            $.ajax({
                url: `/admin/common-complaints/${complaintId}`,
                type: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Complaint updated successfully');
                        } else {
                            alert('Complaint updated successfully');
                        }
                        
                        // Redirect to index page
                        setTimeout(() => {
                            window.location.href = '{{ route("admin.common-complaints.index") }}';
                        }, 1500);
                    }
                },
                error: function(xhr, status, error) {
                    const message = 'Failed to update complaint. Please try again.';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    } else {
                        alert(message);
                    }
                }
            });
        }

        function openFormBuilder() {
            const complaintId = $('#complaintId').val();
            window.location.href = `/admin/common-complaints/${complaintId}/form-builder`;
        }

        function previewForm() {
            const complaintId = $('#complaintId').val();
            alert(`Opening form preview for complaint ${complaintId}`);
        }

        function viewUsage() {
            const complaintId = $('#complaintId').val();
            alert(`Viewing usage statistics for complaint ${complaintId}`);
        }

        function duplicateComplaint() {
            const complaintId = $('#complaintId').val();
            if (confirm('Are you sure you want to duplicate this complaint?')) {
                $.ajax({
                    url: `/admin/common-complaints/${complaintId}/duplicate`,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Complaint duplicated successfully');
                            window.location.href = '{{ route("admin.common-complaints.index") }}';
                        }
                    },
                    error: function() {
                        alert('Failed to duplicate complaint');
                    }
                });
            }
        }
    </script>
@endsection
