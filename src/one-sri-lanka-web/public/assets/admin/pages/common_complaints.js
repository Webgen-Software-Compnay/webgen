const CommonComplaintsAPI = (function () {
    // Configuration constants
    const CONFIG = {
        ACTIONS_COLUMN_INDEX: 5,
        STATUS_COLUMN_INDEX: 3,
        FORM_STATUS_COLUMN_INDEX: 2,
        NON_SEARCHABLE_COLUMNS: [5], // Actions only
        STATUS_OPTIONS: [
            { value: "", text: "All Status" },
            { value: "active", text: "Active" },
            { value: "inactive", text: "Inactive" },
            { value: "draft", text: "Draft" },
        ],
        FORM_STATUS_OPTIONS: [
            { value: "", text: "All Forms" },
            { value: "configured", text: "Configured" },
            { value: "pending", text: "Pending" },
            { value: "incomplete", text: "Incomplete" },
        ],
        // URL templates - will be populated from window variables
        URLS: {
            list: null,
            view: null,
            edit: null,
            delete: null,
            formBuilder: null,
            usage: null,
            analytics: null,
            duplicate: null,
            store: null,
            update: null,
        }
    };

    const _initializeUrls = function() {
        CONFIG.URLS.list = window.commonComplaintsUrl || '/admin/common-complaints/get';
        CONFIG.URLS.edit = window.commonComplaintsEditUrl || '/admin/common-complaints/:id/edit';
        CONFIG.URLS.delete = window.commonComplaintsDeleteUrl || '/admin/common-complaints/:id/destroy';
        CONFIG.URLS.formBuilder = window.commonComplaintsFormBuilderUrl || '/admin/common-complaints/:id/form-builder';
        CONFIG.URLS.usage = window.commonComplaintsUsageUrl || '/admin/common-complaints/:id/usage';
        CONFIG.URLS.analytics = window.commonComplaintsAnalyticsUrl || '/admin/common-complaints/:id/analytics';
        CONFIG.URLS.duplicate = window.commonComplaintsDuplicateUrl || '/admin/common-complaints/:id/duplicate';
        CONFIG.URLS.store = window.commonComplaintsStoreUrl || '/admin/common-complaints/store';
        CONFIG.URLS.update = window.commonComplaintsUpdateUrl || '/admin/common-complaints/:id/update';
        
        // Debug: Log URL configuration
        console.log('Common Complaints URL Configuration:', CONFIG.URLS);
    };

    const _buildUrl = function(template, id) {
        return template.replace(':id', id);
    };

    const _setDataTableDefaults = function () {
        if (!$().DataTable) {
            console.warn("Warning - datatables.min.js is not loaded.");
            return false;
        }

        $.extend($.fn.dataTable.defaults, {
            autoWidth: false,
            columnDefs: [
                {
                    orderable: false,
                    width: 120,
                    targets: [CONFIG.ACTIONS_COLUMN_INDEX],
                },
            ],
            dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
            language: {
                search: '<span class="me-3">Filter:</span> <div class="form-control-feedback form-control-feedback-end flex-fill">_INPUT_<div class="form-control-feedback-icon"><i class="ph-magnifying-glass opacity-50"></i></div></div>',
                searchPlaceholder: "Type to filter...",
                lengthMenu: '<span class="me-3">Show:</span> _MENU_',
                paginate: {
                    first: "First",
                    last: "Last",
                    next: document.dir == "rtl" ? "&larr;" : "&rarr;",
                    previous: document.dir == "rtl" ? "&rarr;" : "&larr;",
                },
            },
            processing: true,
            serverSide: true,
            responsive: true,
            order: [[0, 'desc']], // Default sort by ID descending
        });

        return true;
    };

    const _renderStatusBadge = function (data, type, row) {
        // Handle both raw data and pre-rendered HTML
        if (typeof data === 'string' && data.includes('<span class="badge')) {
            return data; // Already rendered
        }

        // Normalize the status value
        const status = String(data || '').toLowerCase().trim();
        
        let badgeClass, textClass, text;
        
        switch (status) {
            case 'active':
            case '1':
            case 'true':
                badgeClass = "bg-success";
                textClass = "text-success";
                text = "Active";
                break;
            case 'inactive':
            case '0':
            case 'false':
                badgeClass = "bg-danger";
                textClass = "text-danger";
                text = "Inactive";
                break;
            case 'draft':
                badgeClass = "bg-warning";
                textClass = "text-warning";
                text = "Draft";
                break;
            default:
                badgeClass = "bg-secondary";
                textClass = "text-secondary";
                text = "Unknown";
        }

        return `<span class="badge ${badgeClass} bg-opacity-10 ${textClass}">${text}</span>`;
    };

    const _renderFormStatusBadge = function (data, type, row) {
        // Handle both raw data and pre-rendered HTML
        if (typeof data === 'string' && data.includes('<span class="badge')) {
            return data; // Already rendered
        }

        // Normalize the status value
        const status = String(data || '').toLowerCase().trim();
        
        let badgeClass, textClass, text;
        
        switch (status) {
            case 'configured':
                badgeClass = "bg-success";
                textClass = "text-success";
                text = "Configured";
                break;
            case 'pending':
                badgeClass = "bg-warning";
                textClass = "text-warning";
                text = "Pending";
                break;
            case 'incomplete':
                badgeClass = "bg-danger";
                textClass = "text-danger";
                text = "Incomplete";
                break;
            default:
                badgeClass = "bg-secondary";
                textClass = "text-secondary";
                text = "Not Set";
        }

        return `<span class="badge ${badgeClass} bg-opacity-10 ${textClass}">${text}</span>`;
    };

    const _renderUsageCount = function (data, type, row) {
        const count = parseInt(data) || 0;
        let badgeClass = "bg-info";
        
        if (count > 100) {
            badgeClass = "bg-success";
        } else if (count > 50) {
            badgeClass = "bg-warning";
        } else if (count === 0) {
            badgeClass = "bg-secondary";
        }

        return `<span class="badge ${badgeClass} bg-opacity-10">${count}</span>`;
    };

    const _renderActionDropdown = function (row) {
        const editUrl = _buildUrl(CONFIG.URLS.edit, row.id);
        const deleteUrl = _buildUrl(CONFIG.URLS.delete, row.id);
        const formBuilderUrl = _buildUrl(CONFIG.URLS.formBuilder, row.id);
        
        return `
            <div class="d-inline-flex">
                <div class="dropdown">
                    <a href="#" class="text-body" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ph-list"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="${editUrl}" class="dropdown-item">
                            <i class="ph-pencil me-2"></i>Edit
                        </a>
                        <a href="#" class="dropdown-item" onclick="CommonComplaintsAPI.openFormBuilder(${row.id})">
                            <i class="ph-layout me-2"></i>Form Builder
                        </a>
                        <a href="#" class="dropdown-item" onclick="CommonComplaintsAPI.viewUsage(${row.id})">
                            <i class="ph-chart-bar me-2"></i>View Usage
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item text-danger" onclick="CommonComplaintsAPI.deleteComplaint(${row.id})">
                            <i class="ph-trash me-2"></i>Delete
                        </a>
                    </div>
                </div>
            </div>
        `;
    };

    const _createStatusSelect = function (options) {
        let html = '<select class="form-select form-select-sm">';
        options.forEach((option) => {
            html += `<option value="${option.value}">${option.text}</option>`;
        });
        html += "</select>";
        return html;
    };

    const _createSearchInput = function (title) {
        return `<input type="text" class="form-control form-control-sm" placeholder="Search ${title}" />`;
    };

    const _setupColumnSearchInputs = function () {
        $(".datatable-column-search-inputs thead tr:eq(1) th").each(function (index) {
            const title = $(this).text().trim();

            if (index === CONFIG.STATUS_COLUMN_INDEX) {
                $(this).html(_createStatusSelect(CONFIG.STATUS_OPTIONS));
            } else if (index === CONFIG.FORM_STATUS_COLUMN_INDEX) {
                $(this).html(_createStatusSelect(CONFIG.FORM_STATUS_OPTIONS));
            } else if (CONFIG.NON_SEARCHABLE_COLUMNS.includes(index)) {
                $(this).html("");
            } else {
                $(this).html(_createSearchInput(title));
            }
        });
    };

    const _attachSearchEvents = function (dataTable) {
        dataTable.columns().every(function (index) {
            const column = this;
            const $th = $(".datatable-column-search-inputs thead tr:eq(1) th").eq(index);
            const $input = $("input", $th);
            const $select = $("select", $th);

            // Debounce function for better performance
            let searchTimeout;

            if ($input.length) {
                $input.on("keyup change clear", function () {
                    const value = this.value;
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        if (column.search() !== value) {
                            column.search(value).draw();
                        }
                    }, 300); // 300ms debounce
                });
            }

            if ($select.length) {
                $select.on("change", function () {
                    const value = this.value;
                    if (column.search() !== value) {
                        column.search(value).draw();
                    }
                });
            }
        });
    };

    const _initializeDataTable = function () {
        _initializeUrls();

        const dataTable = $(".datatable-column-search-inputs").DataTable({
            ajax: {
                url: CONFIG.URLS.list,
                type: 'GET',
                error: function(xhr, error, thrown) {
                    console.error('DataTable AJAX Error Details:', {
                        url: CONFIG.URLS.list,
                        status: xhr.status,
                        error: error,
                        thrown: thrown,
                        response: xhr.responseText
                    });

                    // Show user-friendly error message
                    const errorMessage = xhr.status === 500 
                        ? 'Server error occurred. Please try again later.'
                        : xhr.status === 404
                        ? 'API endpoint not found. Please contact administrator.'
                        : 'Unable to load data. Please try again.';

                    // Display error notification
                    if (typeof showAlert === 'function') {
                        showAlert('Error', errorMessage, 'error');
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', errorMessage, 'error');
                    } else {
                        alert('Error: ' + errorMessage);
                    }
                }
            },
            columns: [
                { 
                    data: "title",
                    title: "Title",
                    render: function(data, type, row) {
                        if (type === 'display' && data && data.length > 40) {
                            return `<span title="${data}">${data.substring(0, 40)}...</span>`;
                        }
                        return data || '-';
                    }
                },
                { 
                    data: "category",
                    title: "Category"
                },
                {
                    data: "form_status",
                    title: "Form Status",
                    render: function (data, type, row) {
                        return _renderFormStatusBadge(data, type, row);
                    },
                },
                {
                    data: "status",
                    title: "Status",
                    render: function (data, type, row) {
                        return _renderStatusBadge(data, type, row);
                    },
                },
                {
                    data: "usage_count",
                    title: "Usage",
                    className: "text-center",
                    render: function (data, type, row) {
                        return _renderUsageCount(data, type, row);
                    },
                },
                {
                    data: null,
                    title: "Actions",
                    orderable: false,
                    searchable: false,
                    className: "text-center",
                    width: "120px",
                    render: function (data, type, row) {
                        return _renderActionDropdown(row);
                    },
                },
            ],
            orderCellsTop: true,
            serverSide: true, // Enable server-side processing
            processing: true,
            initComplete: function () {
                _attachSearchEvents(this.api());
            },
            drawCallback: function(settings) {
                // Re-initialize Bootstrap dropdowns after each draw
                const dropdowns = document.querySelectorAll('[data-bs-toggle="dropdown"]');
                dropdowns.forEach(dropdown => {
                    if (!bootstrap.Dropdown.getInstance(dropdown)) {
                        new bootstrap.Dropdown(dropdown);
                    }
                });
            }
        });

        _setupColumnSearchInputs();

        return dataTable;
    };

    const _componentDatatableAPI = function () {
        if (!_setDataTableDefaults()) return;
        return _initializeDataTable();
    };

    // Public API
    return {
        init: function () {
            _initializeUrls();
            const table = _componentDatatableAPI();
            this.initFormHandlers();
            return table;
        },

        // Expose methods for external use
        renderStatusBadge: _renderStatusBadge,
        renderFormStatusBadge: _renderFormStatusBadge,
        renderActionDropdown: _renderActionDropdown,
        
        // Action methods
        openFormBuilder: function(id) {
            const url = _buildUrl(CONFIG.URLS.formBuilder, id);
            if (typeof showFormBuilderModal === 'function') {
                showFormBuilderModal(id);
            } else {
                window.location.href = url;
            }
        },

        viewUsage: function(id) {
            if (typeof showUsageModal === 'function') {
                showUsageModal(id);
            } else {
                alert(`Viewing usage statistics for complaint ID: ${id}`);
            }
        },

        deleteComplaint: function(id) {
            const confirmation = typeof Swal !== 'undefined' 
                ? Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {
                    if (result.isConfirmed) {
                        this._performDelete(id);
                    }
                  })
                : confirm('Are you sure you want to delete this complaint?');

            if (confirmation === true) {
                this._performDelete(id);
            }
        },

        _performDelete: function(id) {
            const deleteUrl = _buildUrl(CONFIG.URLS.delete, id);
            
            // Perform AJAX delete request
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Deleted!', 'The complaint has been deleted.', 'success');
                    } else if (typeof toastr !== 'undefined') {
                        toastr.success('Complaint deleted successfully');
                    }
                    CommonComplaintsAPI.refreshTable();
                },
                error: function(xhr, status, error) {
                    const message = 'Failed to delete complaint. Please try again.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error!', message, 'error');
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    } else {
                        alert(message);
                    }
                }
            });
        },
        
        // Utility methods
        refreshTable: function() {
            const table = $('.datatable-column-search-inputs').DataTable();
            if (table) {
                table.ajax.reload(null, false); // false = keep current page
            }
        },
        
        clearSearch: function() {
            const table = $('.datatable-column-search-inputs').DataTable();
            if (table) {
                table.search('').columns().search('').draw();
            }
        },

        // Get table instance
        getTable: function() {
            return $('.datatable-column-search-inputs').DataTable();
        },

        // Export functionality
        exportData: function(format = 'csv') {
            const table = $('.datatable-column-search-inputs').DataTable();
            if (table) {
                // Implementation depends on your export requirements
                console.log(`Exporting data in ${format} format`);
                // You can integrate with plugins like Buttons extension
            }
        },

        // Form handling methods
        initFormHandlers: function() {
            // Handle form submission
            const form = document.getElementById('addComplaintForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    // Submit to server via AJAX
                    $.ajax({
                        url: CONFIG.URLS.store,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Close modal and reset form
                            const modal = bootstrap.Modal.getInstance(document.getElementById('addComplaintModal'));
                            if (modal) {
                                modal.hide();
                            }
                            form.reset();
                            
                            // Refresh DataTable
                            CommonComplaintsAPI.refreshTable();
                            
                            // Show success message
                            if (typeof showAlert === 'function') {
                                showAlert('Success', 'Common complaint added successfully!', 'success');
                            } else {
                                alert('Common complaint added successfully!');
                            }
                        },
                        error: function(xhr, status, error) {
                            const errorMsg = xhr.responseJSON?.message || 'An error occurred while saving the complaint';
                            
                            if (typeof showAlert === 'function') {
                                showAlert('Error', errorMsg, 'error');
                            } else {
                                alert('Error: ' + errorMsg);
                            }
                        }
                    });
                });
            }
        }
    };
})();

// Global utility functions for alerts
function showAlert(title, message, type = 'success') {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toastId = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'text-bg-success' : 
                   type === 'error' ? 'text-bg-danger' : 
                   type === 'warning' ? 'text-bg-warning' : 'text-bg-info';

    const toastHTML = `
        <div id="${toastId}" class="toast align-items-center ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}:</strong> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);

    // Initialize and show the toast
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
    toast.show();

    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function () {
        this.remove();
    });
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", function () {
    // Store table instance globally if needed
    window.commonComplaintsTable = CommonComplaintsAPI.init();
});

// Export for potential module use
if (typeof module !== "undefined" && module.exports) {
    module.exports = CommonComplaintsAPI;
}

// Global utility functions
window.refreshCommonComplaintsTable = function() {
    CommonComplaintsAPI.refreshTable();
};

window.clearCommonComplaintsSearch = function() {
    CommonComplaintsAPI.clearSearch();
};

window.exportCommonComplaints = function(format = 'csv') {
    CommonComplaintsAPI.exportData(format);
};

// Global action functions for external use
window.showFormBuilderModal = function(id) {
    // This can be implemented based on your modal system
    console.log(`Opening form builder for complaint ID: ${id}`);
    if (typeof complaintsManager !== 'undefined' && complaintsManager.buildForm) {
        complaintsManager.buildForm(id);
    }
};

window.showUsageModal = function(id) {
    // This can be implemented based on your modal system
    console.log(`Showing usage statistics for complaint ID: ${id}`);
    if (typeof complaintsManager !== 'undefined' && complaintsManager.viewUsage) {
        complaintsManager.viewUsage(id);
    }
};
