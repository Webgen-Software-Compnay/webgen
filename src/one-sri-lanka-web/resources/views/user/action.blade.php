@extends('user.layouts.app')

@section('title', 'One Sri Lanka Complaints')

@section('content')
<div class="container mt-4">
    <h5 class="fw-bold">Complaints</h5>
    <div class="row g-3">
        @if(isset($categories) && count($categories) > 0)
            @foreach($categories as $category)
                <div class="col-md-3 col-sm-6">
                    <button class="btn btn-custom w-100" 
                            onclick="loadCategoryComplaints({{ $category['id'] }}, '{{ $category['name'] }}', '{{ $category['slug'] }}')"
                            title="{{ $category['description'] ?? $category['name'] }}">
                        <div class="d-flex flex-column align-items-center p-2">
                            <span class="fw-bold">{{ $category['name'] }}</span>
                            @if(!empty($category['description']))
                                <small class="text-muted mt-1">{{ strlen($category['description']) > 50 ? substr($category['description'], 0, 50) . '...' : $category['description'] }}</small>
                            @endif
                        </div>
                    </button>
                </div>
            @endforeach
        @else
            <!-- Fallback static categories if API fails -->
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-custom w-100" 
                        data-bs-toggle="modal" 
                        data-bs-target="#genericComplaintModal"
                        data-category-name="Food"
                        data-category-icon="🍽️">🍽️ Food</button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-custom w-100"
                        data-bs-toggle="modal" 
                        data-bs-target="#genericComplaintModal"
                        data-category-name="Transport"
                        data-category-icon="🚌">🚌 Transport</button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-custom w-100"
                        data-bs-toggle="modal" 
                        data-bs-target="#genericComplaintModal"
                        data-category-name="Education"
                        data-category-icon="🎓">🎓 Education</button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-custom w-100"
                        data-bs-toggle="modal" 
                        data-bs-target="#genericComplaintModal"
                        data-category-name="Health"
                        data-category-icon="🏥">🏥 Health</button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-custom w-100"
                        data-bs-toggle="modal" 
                        data-bs-target="#genericComplaintModal"
                        data-category-name="Media"
                        data-category-icon="📺">📺 Media</button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-custom w-100"
                        data-bs-toggle="modal" 
                        data-bs-target="#genericComplaintModal"
                        data-category-name="Companies"
                        data-category-icon="🏢">🏢 Companies</button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-custom w-100"
                        data-bs-toggle="modal" 
                        data-bs-target="#genericComplaintModal"
                        data-category-name="Agriculture"
                        data-category-icon="🌾">🌾 Agriculture</button>
            </div>
            <div class="col-md-3 col-sm-6">
                <button class="btn btn-custom w-100"
                        data-bs-toggle="modal" 
                        data-bs-target="#genericComplaintModal"
                        data-category-name="Other"
                        data-category-icon="📋">📋 Other</button>
            </div>
        @endif
    </div>
    
    @if(isset($categories) && count($categories) > 0)
        <div class="mt-3">
            <small class="text-muted">
                <i class="bi bi-info-circle"></i> 
                Loaded {{ count($categories) }} active complaint categories from the system
            </small>
        </div>
    @endif
</div>

<!-- Common Complaints Modal -->
<div class="modal fade" id="commonComplaintsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content p-3">
            <div class="modal-header">
                <button class="back-btn" data-bs-dismiss="modal">&lt;</button>
                <h5 class="ms-3 fw-bold">
                    <span id="modalCategoryTitle">Category Complaints</span>
                </h5>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <strong>Category:</strong> <span id="selectedCategoryName">-</span>
                            <div id="selectedCategoryDescription" class="mt-1" style="display: none;">
                                <small class="text-muted"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Loading indicator -->
                <div id="loadingIndicator" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading common complaints...</p>
                </div>

                <!-- Common Complaints List -->
                <div id="commonComplaintsList" style="display: none;">
                    <h6 class="mb-3">Available Common Complaints</h6>
                    <div class="row" id="complaintsContainer">
                        <!-- Dynamic content will be loaded here -->
                    </div>
                    
                    <!-- Custom Complaint Option -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <i class="bi bi-plus-circle-fill fs-1 text-primary"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-1">Create Custom Complaint</h6>
                                            <p class="card-text text-muted small mb-2">
                                                Don't see what you're looking for? Create a custom complaint for this category.
                                            </p>
                                            <button class="btn btn-outline-primary btn-sm" onclick="createCustomComplaint()">
                                                <i class="bi bi-plus me-1"></i> Create Custom Complaint
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No complaints found -->
                <div id="noComplaintsFound" style="display: none;">
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <h6 class="mt-2">No Common Complaints Found</h6>
                        <p class="text-muted">There are no pre-defined complaints for this category yet.</p>
                        <button class="btn btn-primary" onclick="createCustomComplaint()">
                            <i class="bi bi-plus me-1"></i> Create Custom Complaint
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-cancel" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
                <div class="mb-3">
                    <textarea class="form-control" rows="3" placeholder="Description"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-submit">Submit</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        background-color: #d9d9e0;
    }
    .navbar-custom {
        background-color: #0000ff;
    }
    .navbar-custom .navbar-brand {
        color: white;
        font-weight: bold;
        font-size: 14px;
    }
    .profile-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: radial-gradient(circle, blue, red);
        cursor: pointer;
    }
    .btn-custom {
        background-color: #0000ff;
        color: white;
        font-weight: bold;
        border-radius: 10px;
        padding: 10px 20px;
        width: 150px;
    }
    .btn-custom:hover {
        background-color: #0000cc;
    }
    .modal-content {
        border-radius: 10px;
    }
    .modal-header {
        border-bottom: none;
        align-items: center;
    }
    .back-btn {
        background-color: #0000ff;
        color: white;
        font-size: 20px;
        border-radius: 10px;
        width: 40px;
        height: 40px;
        text-align: center;
        line-height: 20px;
        border: none;
    }
    .form-check-input {
        border: 2px solid #0000ff;
    }
    .form-control, .form-select {
        border: 2px solid #0000ff;
    }
    .btn-cancel {
        background-color: #a3a3ff;
        color: white;
        font-weight: bold;
        border-radius: 10px;
        padding: 10px 20px;
    }
    .btn-next, .btn-submit {
        background-color: #0000ff;
        color: white;
        font-weight: bold;
        border-radius: 10px;
        padding: 10px 20px;
    }
</style>

<script>
// Global variables to store selected category
let selectedCategory = {
    id: null,
    name: '',
    slug: '',
    description: ''
};

/**
 * Load common complaints for a specific category
 */
function loadCategoryComplaints(categoryId, categoryName, categorySlug) {
    // Store selected category
    selectedCategory.id = categoryId;
    selectedCategory.name = categoryName;
    selectedCategory.slug = categorySlug;
    
    // Update modal title and category info
    document.getElementById('modalCategoryTitle').textContent = categoryName + ' Complaints';
    document.getElementById('selectedCategoryName').textContent = categoryName;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('commonComplaintsModal'));
    modal.show();
    
    // Show loading indicator
    document.getElementById('loadingIndicator').style.display = 'block';
    document.getElementById('commonComplaintsList').style.display = 'none';
    document.getElementById('noComplaintsFound').style.display = 'none';
    
    // Fetch common complaints for this category
    fetchCommonComplaints(categoryId);
}

/**
 * Fetch common complaints from API
 */
async function fetchCommonComplaints(categoryId) {
    try {
        const apiBaseUrl = '{{ config("app.api_base_url") }}';
        
        // Try the main endpoint first
        let response = await fetch(`${apiBaseUrl}/common-complaints?category_id=${categoryId}&status=active`);
        
        // If main endpoint fails, try the sample endpoint
        if (!response.ok) {
            console.log('Main endpoint failed, trying sample endpoint...');
            response = await fetch(`${apiBaseUrl}/common-complaints/sample?category_id=${categoryId}`);
        }
        
        if (response.ok) {
            const data = await response.json();
            displayCommonComplaints(data.data || []);
        } else {
            console.error('Failed to fetch common complaints:', response.status);
            displayCommonComplaints([]);
        }
    } catch (error) {
        console.error('Error fetching common complaints:', error);
        
        // Use local fallback data
        const fallbackComplaints = [
            {
                id: 1,
                title: 'General Service Issue',
                description: 'Report general service-related problems in this category',
                priority: 'medium'
            },
            {
                id: 2,
                title: 'Quality Concern',
                description: 'Report quality-related issues and concerns',
                priority: 'high'
            }
        ];
        
        displayCommonComplaints(fallbackComplaints);
    }
}

/**
 * Display common complaints in the modal
 */
function displayCommonComplaints(complaints) {
    document.getElementById('loadingIndicator').style.display = 'none';
    
    if (complaints.length === 0) {
        document.getElementById('noComplaintsFound').style.display = 'block';
        return;
    }
    
    document.getElementById('commonComplaintsList').style.display = 'block';
    
    const container = document.getElementById('complaintsContainer');
    container.innerHTML = '';
    
    complaints.forEach(complaint => {
        const complaintCard = createComplaintCard(complaint);
        container.appendChild(complaintCard);
    });
}

/**
 * Create a complaint card element
 */
function createComplaintCard(complaint) {
    const col = document.createElement('div');
    col.className = 'col-md-6 mb-3';
    
    col.innerHTML = `
        <div class="card h-100 border-secondary">
            <div class="card-body">
                <h6 class="card-title">${complaint.title || 'Untitled'}</h6>
                <p class="card-text text-muted small">
                    ${complaint.description ? (complaint.description.length > 100 ? complaint.description.substring(0, 100) + '...' : complaint.description) : 'No description available'}
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="bi bi-star-fill text-warning"></i> 
                        Priority: ${complaint.priority || 'Medium'}
                    </small>
                    <button class="btn btn-primary btn-sm" onclick="useCommonComplaint(${complaint.id}, '${complaint.title}')">
                        <i class="bi bi-arrow-right me-1"></i> Use This
                    </button>
                </div>
            </div>
        </div>
    `;
    
    return col;
}

/**
 * Handle common complaint selection
 */
function useCommonComplaint(complaintId, complaintTitle) {
    // Close the modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('commonComplaintsModal'));
    if (modal) modal.hide();
    
    // Redirect to form with common complaint ID
    const baseUrl = '{{ route("complaint.create") }}';
    const params = new URLSearchParams({
        category_id: selectedCategory.id,
        category: selectedCategory.slug,
        category_name: selectedCategory.name,
        common_complaint_id: complaintId,
        common_complaint_title: complaintTitle
    });
    
    window.location.href = `${baseUrl}?${params.toString()}`;
}

/**
 * Handle custom complaint creation
 */
function createCustomComplaint() {
    // Close the modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('commonComplaintsModal'));
    if (modal) modal.hide();
    
    // Redirect to custom complaint form
    const baseUrl = '{{ route("complaint.create") }}';
    const params = new URLSearchParams({
        category_id: selectedCategory.id,
        category: selectedCategory.slug,
        category_name: selectedCategory.name,
        type: 'custom'
    });
    
    window.location.href = `${baseUrl}?${params.toString()}`;
}

// Legacy functions for fallback buttons
function openComplaintForm() {
    createCustomComplaint();
}

function viewComplaints() {
    alert('View complaints feature will be implemented soon!\n\nSelected Category: ' + selectedCategory.name);
}
</script>
@endpush
