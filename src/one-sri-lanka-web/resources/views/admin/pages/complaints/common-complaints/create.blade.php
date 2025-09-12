@extends('admin.layouts.master')

@section('title', 'Create Common Complaint')

@section('css')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
<style>
/* Enhanced Select2 Styling for NDX Fields */
.select2-container--bootstrap-5 .select2-selection {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    min-height: 38px;
}

.select2-container--bootstrap-5 .select2-selection--single {
    height: 38px;
    line-height: 38px;
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    color: #212529;
    padding-left: 12px;
    padding-right: 20px;
}

.select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
    height: 36px;
    right: 10px;
}

.select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
}

.select2-results__option {
    padding: 8px 12px;
}

.select2-results__option--highlighted[aria-selected] {
    background-color: #0d6efd;
    color: white;
}

/* Enhanced result display */
.select2-result-item {
    padding: 2px 0;
}

.select2-result-title {
    font-weight: 500;
    color: #212529;
}

.select2-result-meta {
    font-size: 0.85em;
    color: #6c757d !important;
    margin-top: 2px;
}

.select2-result-phone {
    font-size: 0.8em;
    color: #28a745 !important;
    margin-top: 1px;
}

/* API Field indicators */
.api-field {
    position: relative;
}

.api-field::after {
    content: "🌐";
    position: absolute;
    right: 30px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    font-size: 12px;
    opacity: 0.7;
}

.searchable-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6'/%3e%3c/svg%3e");
}

/* Form field badges */
.badge-ndx {
    background-color: #17a2b8;
    color: white;
    font-size: 0.7em;
    padding: 2px 6px;
    border-radius: 10px;
}

.badge-searchable {
    background-color: #28a745;
    color: white;
    font-size: 0.7em;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 4px;
}
</style>
@endsection

@section('content')
@component('admin.components.breadcrumb')
@slot('title') {{ $title }} @endslot
@slot('subtitle') {{ $subtitle }} @endslot
@endcomponent

<!-- Content area -->
<div class="content">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">New Common Complaint Details</h4>
                        <div>
                            <a href="{{ route('admin.common-complaints.index') }}" class="btn btn-secondary btn-sm">
                                <i class="bx bx-arrow-back"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form id="createComplaintForm" action="{{ route('admin.common-complaints.store') }}" method="POST">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" 
                                           placeholder="Enter complaint title" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('category_id') is-invalid @enderror" 
                                            id="category_id" name="category_id" required
                                            data-placeholder="Select Category">
                                        <option value="">Select Category</option>
                                        @if(isset($categories) && is_array($categories))
                                            @foreach($categories as $category)
                                                @if(is_array($category) && isset($category['id']) && isset($category['name']))
                                                    <option value="{{ $category['id'] }}" 
                                                            {{ old('category_id') == $category['id'] ? 'selected' : '' }}
                                                            data-description="{{ $category['description'] ?? '' }}"
                                                            data-category-type="{{ $category['category_type'] ?? 'main' }}">
                                                        {{ $category['name'] }}{{ isset($category['category_type']) && $category['category_type'] === 'sub' ? ' (Sub)' : '' }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" id="categoryDescription" style="display: none;"></small>
                                    <div class="mt-1">
                                        <small class="text-info">
                                            <i class="bx bx-info-circle"></i> 
                                            Categories are loaded from the backend API. Start typing to search.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select class="form-control @error('priority') is-invalid @enderror" 
                                            id="priority" name="priority">
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_resolution_time" class="form-label">Estimated Resolution Time (hours)</label>
                                    <input type="number" class="form-control @error('estimated_resolution_time') is-invalid @enderror" 
                                           id="estimated_resolution_time" name="estimated_resolution_time" 
                                           value="{{ old('estimated_resolution_time', 24) }}" min="1" max="720">
                                    @error('estimated_resolution_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Provide a detailed description of this common complaint">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Form Structure Section -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card border">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">
                                            <i class="bx bx-form"></i> Dynamic Form Structure
                                        </h5>
                                        <small class="text-muted">Define the form fields that citizens will see when submitting this complaint</small>
                                    </div>
                                    <div class="card-body">
                                        <div id="formStructureContainer">
                                            <!-- Form fields will be added here dynamically -->
                                            <div class="text-center text-muted py-4" id="noFieldsMessage">
                                                <i class="bx bx-plus-circle font-size-24 mb-2"></i>
                                                <p>No form fields added yet. Click "Add Field" to start building the form.</p>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <button type="button" class="btn btn-outline-primary" id="addFieldBtn">
                                                <i class="bx bx-plus"></i> Add Field
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="previewFormBtn">
                                                <i class="bx bx-show"></i> Preview Form
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="mb-3">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                   id="tags" name="tags" value="{{ old('tags') }}" 
                                   placeholder="Enter tags separated by commas (e.g., urgent, public, infrastructure)">
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Separate multiple tags with commas</small>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hidden field for form structure JSON -->
                        <input type="hidden" name="form_structure" id="formStructureInput" value="[]">

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary" onclick="window.history.back()">
                                            <i class="bx bx-x"></i> Cancel
                                        </button>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-success" id="submitBtn">
                                            <i class="bx bx-check"></i> Create Complaint
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /content area -->

<!-- Form Field Modal - Bootstrap 4/5 Compatible -->
<div class="modal fade" id="fieldModal" tabindex="-1" role="dialog" aria-labelledby="fieldModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fieldModalLabel">Add Form Field</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="fieldForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fieldType" class="form-label">Field Type</label>
                                <select class="form-control" id="fieldType" required>
                                    <option value="">Select Type</option>
                                    <option value="text">Text Input</option>
                                    <option value="textarea">Text Area</option>
                                    <option value="email">Email Input</option>
                                    <option value="phone">Phone Input</option>
                                    <option value="number">Number Input</option>
                                    <option value="date">Date Picker</option>
                                    <option value="file">File Upload</option>
                                    <option value="select">Static Dropdown</option>
                                    <option value="radio">Radio Buttons</option>
                                    <option value="checkbox">Checkboxes</option>
                                    <optgroup label="🏛️ Government Data (NDX)">
                                        <option value="ndx_school">School Selection</option>
                                        <option value="ndx_police_station">Police Station</option>
                                        <option value="ndx_hospital">Hospital/Medical Center</option>
                                        <option value="ndx_grama_niladhari">Grama Niladhari Division</option>
                                        <option value="ndx_divisional_secretariat">Divisional Secretariat</option>
                                        <option value="ndx_district">District</option>
                                        <option value="ndx_province">Province</option>
                                        <option value="ndx_bus_route">Bus Route</option>
                                        <option value="ndx_train_station">Railway Station</option>
                                        <option value="ndx_post_office">Post Office</option>
                                        <option value="ndx_bank_branch">Bank Branch</option>
                                        <option value="ndx_government_office">Government Office</option>
                                    </optgroup>
                                    <optgroup label="🌍 Location Data">
                                        <option value="location_picker">Location Picker (Map)</option>
                                        <option value="address_autocomplete">Address Autocomplete</option>
                                        <option value="coordinates">GPS Coordinates</option>
                                    </optgroup>
                                    <optgroup label="🔗 Dynamic Data">
                                        <option value="api_dropdown">API-based Dropdown</option>
                                        <option value="cascading_dropdown">Cascading Dropdown</option>
                                        <option value="search_select">Search & Select</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fieldName" class="form-label">Field Name</label>
                                <input type="text" class="form-control" id="fieldName" 
                                       placeholder="e.g., incident_location" required>
                                <small class="text-muted">Use lowercase with underscores</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fieldLabel" class="form-label">Label</label>
                        <input type="text" class="form-control" id="fieldLabel" 
                               placeholder="e.g., Incident Location" required>
                    </div>

                    <div class="mb-3">
                        <label for="fieldPlaceholder" class="form-label">Placeholder</label>
                        <input type="text" class="form-control" id="fieldPlaceholder" 
                               placeholder="e.g., Enter the location where incident occurred">
                    </div>

                    <div class="mb-3">
                        <label for="fieldHelp" class="form-label">Help Text</label>
                        <input type="text" class="form-control" id="fieldHelp" 
                               placeholder="Additional help text for users">
                    </div>

                    <!-- Static Options Container (for select, radio, checkbox) -->
                    <div class="mb-3" id="optionsContainer" style="display: none;">
                        <label class="form-label">Static Options</label>
                        <div id="staticOptions">
                            <!-- Static options will be added here -->
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="formBuilder.addStaticOption()">
                            <i class="bx bx-plus"></i> Add Option
                        </button>
                    </div>

                    <!-- NDX/API Configuration will be inserted here dynamically -->
                    <!-- Dependency Configuration will be inserted here dynamically -->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="fieldRequired">
                                <label class="form-check-label" for="fieldRequired">
                                    Required Field
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fieldOrder" class="form-label">Order</label>
                                <input type="number" class="form-control" id="fieldOrder" min="1" value="1">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveFieldBtn">Save Field</button>
            </div>
        </div>
    </div>
</div>

<!-- Form Preview Modal - Bootstrap 4/5 Compatible -->
<div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Form Preview</h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="previewFormContent">
                    <!-- Form preview will be generated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<script>
// Set global API configuration
window.ONE_SRI_LANKA_API_URL = '{{ config("app.api_base_url") }}';

// Test categories endpoint
fetch('/admin/common-complaints/categories')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            console.log(`✅ Successfully loaded ${data.data.length} categories from backend`);
        }
    })
    .catch(error => {
        console.error('❌ Categories API Error:', error);
    });

// Handle form submission
$(document).ready(function() {
    // Form submission handler
    $('form').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        // Ensure form_structure is properly set as an array
        const formStructureInput = $('#formStructureInput');
        let formStructure = [];
        
        // If form builder exists and has fields, get them
        if (window.formBuilder && typeof window.formBuilder.getFieldsArray === 'function') {
            const fields = window.formBuilder.getFieldsArray();
            // Filter out any empty or invalid fields
            formStructure = fields.filter(field => 
                field && 
                field.field_type && 
                field.field_name && 
                field.label
            );
        }
        
        // Ensure we have a clean array (empty if no valid fields)
        formStructure = Array.isArray(formStructure) ? formStructure : [];
        
        // Set the hidden input value as JSON string of the array
        formStructureInput.val(JSON.stringify(formStructure));
        
        console.log('📋 Form structure set for submission:', formStructure);
        
        // Show loading state
        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i> Creating...');
        
        // Get form data
        const formData = new FormData(this);
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('✅ Form submission successful:', response);
                
                if (response.status === 'success' || response.success) {
                    // Create and show success toast
                    const successMessage = `Common complaint "${response.data?.title || 'complaint'}" has been created successfully! (ID: ${response.data?.id})`;
                    showToast('success', 'Success', successMessage);
                    
                    // Show action buttons in the form
                    const actionsHTML = `
                        <div class="alert alert-success d-flex justify-content-between align-items-center mt-3" id="successActions">
                            <span><i class="bx bx-check-circle"></i> Complaint created successfully!</span>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-success me-2" onclick="createAnother()">
                                    <i class="bx bx-plus"></i> Create Another
                                </button>
                                <button type="button" class="btn btn-sm btn-success" onclick="viewComplaints()">
                                    <i class="bx bx-list-ul"></i> View All Complaints
                                </button>
                            </div>
                        </div>
                    `;
                    
                    // Add action buttons after the form
                    $('form').after(actionsHTML);
                    
                    // Auto-redirect after 5 seconds if no action taken
                    setTimeout(() => {
                        if (document.getElementById('successActions')) {
                            window.location.href = '/admin/common-complaints';
                        }
                    }, 5000);
                } else {
                    // Handle unexpected success format
                    const message = response.message || 'The common complaint has been created.';
                    showToast('success', 'Created Successfully', message);
                    
                    setTimeout(() => {
                        window.location.href = '/admin/common-complaints';
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Form submission failed:', xhr.responseJSON);
                
                let errorMessage = 'An error occurred while creating the complaint.';
                let errorDetails = '';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON.errors) {
                        errorDetails = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        errorMessage += ` Details: ${errorDetails}`;
                    }
                    if (xhr.responseJSON.api_error) {
                        try {
                            const apiError = JSON.parse(xhr.responseJSON.api_error);
                            errorMessage = apiError.message || errorMessage;
                        } catch (e) {
                            errorMessage += ` API Error: ${xhr.responseJSON.api_error}`;
                        }
                    }
                }
                
                showToast('error', 'Error!', errorMessage);
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
    });
    });

// Toast notification function
function showToast(type, title, message) {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }
    
    // Determine toast class based on type
    let toastClass = 'text-bg-primary';
    switch(type) {
        case 'success':
            toastClass = 'text-bg-success';
            break;
        case 'error':
            toastClass = 'text-bg-danger';
            break;
        case 'info':
            toastClass = 'text-bg-info';
            break;
        case 'warning':
            toastClass = 'text-bg-warning';
            break;
    }
    
    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastHTML = `
        <div class="toast align-items-center ${toastClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
            <div class="toast-header">
                <strong class="me-auto">${title}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    // Add toast to container
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    // Initialize and show toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, {
        delay: 5000,
        autohide: true
    });
    
    toast.show();
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
}

// Action functions for success buttons
function createAnother() {
    // Remove success actions
    $('#successActions').remove();
    
    // Reset form for another creation
    $('form')[0].reset();
    $('#formStructureInput').val('[]');
    $('#category_id').val('').trigger('change');
    
    // Clear form builder if exists
    if (window.formBuilder && typeof window.formBuilder.clearFields === 'function') {
        window.formBuilder.clearFields();
    }
    
    // Show info toast
    showToast('info', 'Form Reset', 'Form has been cleared. You can create another complaint.');
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function viewComplaints() {
    window.location.href = '/admin/common-complaints';
}
</script><!-- Common Complaint Create JS -->
<script src="{{ asset('assets/admin/pages/common-complaint-create.js') }}"></script>

<script>
// Additional debugging after form builder initialization
$(document).ready(function() {
    setTimeout(() => {
        console.log('🔍 Checking Select2 initialization...');
        // Test manual category loading if form builder is available
        if (window.formBuilder && typeof window.formBuilder.loadCategoriesStatic === 'function') {
            window.formBuilder.loadCategoriesStatic();
        }
    }, 2000);
});
</script>
@endsection