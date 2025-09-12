@extends('user.layouts.app')

@section('title', 'Submit Complaint - ' . (isset($category['name']) ? $category['name'] : 'General'))

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
<style>
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
        background-color: #0d6efd;
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        Submit Complaint - {{ isset($category['name']) ? $category['name'] : 'General' }}
                    </h4>
                </div>
                <div class="card-body">
                    @if(isset($category) && is_array($category))
                        <div class="alert alert-info mb-4">
                            <strong>Category:</strong> {{ $category['name'] ?? 'General' }}
                            @if(!empty($category['description'] ?? ''))
                                <br><small>{{ $category['description'] }}</small>
                            @endif
                        </div>
                    @endif

                    @if(isset($commonComplaint) && $commonComplaint)
                        <div class="alert alert-success mb-4">
                            <strong>Using Common Complaint:</strong> {{ $commonComplaint['title'] ?? 'Untitled' }}
                            @if(!empty($commonComplaint['description'] ?? ''))
                                <br><small>{{ $commonComplaint['description'] }}</small>
                            @endif
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($otherCommonComplaints) && is_array($otherCommonComplaints))
                        <div class="mb-4">
                            <h5>Other Common Complaints for this Category</h5>
                            <p class="text-muted">You can switch to these other common complaint templates if needed.</p>
                            <div class="row">
                                @foreach($otherCommonComplaints as $complaint)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100 border-secondary">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $complaint['title'] ?? 'Untitled' }}</h6>
                                                <p class="card-text text-muted small">
                                                    @php
                                                        $description = $complaint['description'] ?? '';
                                                        echo strlen($description) > 80 ? substr($description, 0, 80) . '...' : $description;
                                                    @endphp
                                                </p>
                                                <a href="{{ route('complaint.create', [
                                                    'category_id' => $category['id'],
                                                    'category' => $category['slug'],
                                                    'category_name' => $category['name'],
                                                    'common_complaint_id' => $complaint['id'],
                                                    'common_complaint_title' => $complaint['title']
                                                ]) }}" class="btn btn-outline-primary btn-sm">
                                                    Switch to This
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('complaint.store') }}" method="POST" id="complaintForm">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $category['id'] ?? 1 }}">
                        @if(isset($commonComplaint) && $commonComplaint)
                            <input type="hidden" name="common_complaint_id" value="{{ $commonComplaint['id'] }}">
                        @endif

                        <!-- District Select for Filtering Schools -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="district_select" class="form-label">District <span class="text-danger">*</span></label>
                                <select class="form-select" id="district_select" name="district_id" required>
                                    <option value="">Select a district</option>
                                    <!-- District options will be loaded by JS -->
                                </select>
                            </div>
                        </div>

                        <!-- Dynamic Form Fields Container -->
                        <div id="form-loading-message" class="alert alert-info" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-2"></i>Loading form fields...
                        </div>
                        
                        <div id="form-error-message" class="alert alert-danger" style="display: none;">
                            Failed to load form data. Please refresh the page and try again.
                        </div>
                        
                        <div id="dynamic-form-fields">
                            @if(!empty($formStructure) && is_array($formStructure))
                                <!-- Dynamic Form Fields from Database -->
                                <h5 class="mb-3 complaint-title">{{ $commonComplaint['title'] ?? 'Form Fields' }}</h5>
                                
                                @foreach($formStructure as $index => $field)
                                    @if(isset($field['field_type']) && isset($field['field_name']) && isset($field['label']))
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <label for="field_{{ $index }}" class="form-label">
                                                    {{ $field['label'] }}
                                                    @if($field['required'] ?? false)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                
                                                @if($field['field_type'] === 'text')
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="field_{{ $index }}" 
                                                           name="dynamic_fields[{{ $field['field_name'] }}]" 
                                                           value="{{ old('dynamic_fields.' . $field['field_name']) }}"
                                                           placeholder="{{ $field['placeholder'] ?? '' }}"
                                                           {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                
                                                @elseif($field['field_type'] === 'textarea')
                                                    <textarea class="form-control" 
                                                              id="field_{{ $index }}" 
                                                              name="dynamic_fields[{{ $field['field_name'] }}]" 
                                                              rows="{{ $field['rows'] ?? 3 }}"
                                                              placeholder="{{ $field['placeholder'] ?? '' }}"
                                                              {{ ($field['required'] ?? false) ? 'required' : '' }}>{{ old('dynamic_fields.' . $field['field_name']) }}</textarea>
                                            
                                            @elseif($field['field_type'] === 'select')
                                                <select class="form-select" 
                                                        id="field_{{ $index }}" 
                                                        name="dynamic_fields[{{ $field['field_name'] }}]"
                                                        {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                    <option value="">Select an option</option>
                                                    @if(isset($field['options']) && is_array($field['options']))
                                                        @foreach($field['options'] as $option)
                                                            <option value="{{ $option }}" {{ old('dynamic_fields.' . $field['field_name']) == $option ? 'selected' : '' }}>
                                                                {{ $option }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            
                                            @elseif($field['field_type'] === 'radio')
                                                @if(isset($field['options']) && is_array($field['options']))
                                                    @foreach($field['options'] as $optionIndex => $option)
                                                        <div class="form-check">
                                                            <input class="form-check-input" 
                                                                   type="radio" 
                                                                   name="dynamic_fields[{{ $field['field_name'] }}]" 
                                                                   id="field_{{ $index }}_{{ $optionIndex }}" 
                                                                   value="{{ $option }}"
                                                                   {{ old('dynamic_fields.' . $field['field_name']) == $option ? 'checked' : '' }}
                                                                   {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                            <label class="form-check-label" for="field_{{ $index }}_{{ $optionIndex }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            
                                            @elseif($field['field_type'] === 'checkbox')
                                                @if(isset($field['options']) && is_array($field['options']))
                                                    @foreach($field['options'] as $optionIndex => $option)
                                                        <div class="form-check">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="dynamic_fields[{{ $field['field_name'] }}][]" 
                                                                   id="field_{{ $index }}_{{ $optionIndex }}" 
                                                                   value="{{ $option }}"
                                                                   {{ is_array(old('dynamic_fields.' . $field['field_name'])) && in_array($option, old('dynamic_fields.' . $field['field_name'])) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="field_{{ $index }}_{{ $optionIndex }}">
                                                                {{ $option }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            
                                            @elseif($field['field_type'] === 'number')
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="field_{{ $index }}" 
                                                       name="dynamic_fields[{{ $field['field_name'] }}]" 
                                                       value="{{ old('dynamic_fields.' . $field['field_name']) }}"
                                                       placeholder="{{ $field['placeholder'] ?? '' }}"
                                                       min="{{ $field['min'] ?? '' }}"
                                                       max="{{ $field['max'] ?? '' }}"
                                                       {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                            
                                            @elseif($field['field_type'] === 'date')
                                                <input type="date" 
                                                       class="form-control" 
                                                       id="field_{{ $index }}" 
                                                       name="dynamic_fields[{{ $field['field_name'] }}]" 
                                                       value="{{ old('dynamic_fields.' . $field['field_name']) }}"
                                                       {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                            
                                            @elseif($field['field_type'] === 'email')
                                                <input type="email" 
                                                       class="form-control" 
                                                       id="field_{{ $index }}" 
                                                       name="dynamic_fields[{{ $field['field_name'] }}]" 
                                                       value="{{ old('dynamic_fields.' . $field['field_name']) }}"
                                                       placeholder="{{ $field['placeholder'] ?? '' }}"
                                                       {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                            
                                            @elseif($field['field_type'] === 'phone')
                                                <input type="tel" 
                                                       class="form-control" 
                                                       id="field_{{ $index }}" 
                                                       name="dynamic_fields[{{ $field['field_name'] }}]" 
                                                       value="{{ old('dynamic_fields.' . $field['field_name']) }}"
                                                       placeholder="{{ $field['placeholder'] ?? '' }}"
                                                       {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                            @endif
                                            
                                            @if(isset($field['help_text']) && !empty($field['help_text']))
                                                <div class="form-text">{{ $field['help_text'] }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <!-- Standard Form Fields for Custom Complaints -->
                            <h5 class="mb-3">Complaint Details</h5>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="title" class="form-label">Complaint Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="{{ old('title', isset($commonComplaint) ? $commonComplaint['title'] ?? '' : '') }}" required 
                                           placeholder="Brief description of your complaint">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Detailed Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="5" 
                                              required placeholder="Please provide detailed information about your complaint">{{ old('description', isset($commonComplaint) ? $commonComplaint['description'] ?? '' : '') }}</textarea>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="{{ old('location') }}" 
                                           placeholder="Where did this issue occur?">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="priority" class="form-label">Priority</label>
                                    <select class="form-select" id="priority" name="priority">
                                        <option value="low" {{ old('priority', isset($commonComplaint) ? $commonComplaint['priority'] ?? 'medium' : 'medium') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', isset($commonComplaint) ? $commonComplaint['priority'] ?? 'medium' : 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', isset($commonComplaint) ? $commonComplaint['priority'] ?? 'medium' : 'medium') == 'high' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        <h5 class="mt-4 mb-3">Contact Information (Optional)</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="contact_name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="contact_name" name="contact_name" 
                                       value="{{ old('contact_name') }}" 
                                       placeholder="Your full name">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                       value="{{ old('contact_phone') }}" 
                                       placeholder="Your phone number">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="contact_email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                       value="{{ old('contact_email') }}" 
                                       placeholder="Your email address">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <a href="{{ route('action') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i> Back to Categories
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Submit Complaint
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.common-complaint-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.common-complaint-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.common-complaint-card.border-success {
    border-width: 2px !important;
}
</style>

@push('scripts')
<!-- jQuery and Select2 for enhanced dropdowns -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- Complaint Form Loader -->
<script src="{{ asset('assets/js/complaint-form-loader.js') }}?v={{ time() }}"></script>

<script>
// Ensure everything is loaded before initializing
$(document).ready(function() {
    console.log('Document ready, jQuery loaded:', typeof $ !== 'undefined');
    console.log('Select2 loaded:', typeof $.fn.select2 !== 'undefined');
    
    // Initialize form loader
    if (typeof ComplaintFormLoader !== 'undefined') {
        ComplaintFormLoader.init();
    }
});
</script>
@endpush

@endsection
