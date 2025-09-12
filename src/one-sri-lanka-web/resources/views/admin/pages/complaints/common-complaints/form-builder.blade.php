@extends('admin.layouts.master')
@section('content')
    @component('admin.components.breadcrumb')
    @slot('title') Form Builder @endslot
    @slot('subtitle') Build dynamic forms for: {{ $complaint['title'] ?? 'Unknown Complaint' }} @endslot
    @endcomponent

    <!-- Content area -->
    <div class="content">
        <div class="row">
            <!-- Form Builder Panel -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-magic me-2"></i>Form Builder
                        </h5>
                        <div>
                            <button class="btn btn-outline-secondary btn-sm me-2" onclick="previewForm()">
                                <i class="fas fa-eye me-2"></i>Preview
                            </button>
                            <button class="btn btn-primary btn-sm" onclick="saveForm()">
                                <i class="fas fa-save me-2"></i>Save Form
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Field Types Toolbar -->
                        <div class="mb-4">
                            <h6>Add Field:</h6>
                            <div class="btn-toolbar" role="toolbar">
                                <div class="btn-group me-2" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('text')">
                                        <i class="fas fa-font me-1"></i>Text
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('textarea')">
                                        <i class="fas fa-align-left me-1"></i>Textarea
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('select')">
                                        <i class="fas fa-list me-1"></i>Select
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('radio')">
                                        <i class="fas fa-dot-circle me-1"></i>Radio
                                    </button>
                                </div>
                                <div class="btn-group me-2" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('checkbox')">
                                        <i class="fas fa-check-square me-1"></i>Checkbox
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('file')">
                                        <i class="fas fa-file me-1"></i>File
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('date')">
                                        <i class="fas fa-calendar me-1"></i>Date
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addField('number')">
                                        <i class="fas fa-hashtag me-1"></i>Number
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Form Fields Container -->
                        <div id="formFieldsContainer" class="border rounded p-3" style="min-height: 300px;">
                            <div id="fieldsDropZone" class="text-center text-muted p-5">
                                <i class="fas fa-plus-circle fa-3x mb-3"></i>
                                <h5>Build Your Form</h5>
                                <p>Click on field types above to add them to your form, or drag and drop to reorder</p>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-4 d-flex justify-content-between">
                            <div>
                                <button class="btn btn-outline-danger" onclick="clearForm()">
                                    <i class="fas fa-trash me-2"></i>Clear All
                                </button>
                            </div>
                            <div>
                                <button class="btn btn-outline-info me-2" onclick="importTemplate()">
                                    <i class="fas fa-download me-2"></i>Import Template
                                </button>
                                <button class="btn btn-outline-success" onclick="exportTemplate()">
                                    <i class="fas fa-upload me-2"></i>Export Template
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Panel -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="fas fa-eye me-2"></i>Live Preview
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="formPreview">
                            <p class="text-muted text-center">
                                <i class="fas fa-info-circle me-2"></i>
                                Add fields to see preview
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Complaint Info -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>Complaint Details
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Title:</strong><br>
                            {{ $complaint['title'] ?? 'Unknown' }}
                        </div>
                        <div class="mb-2">
                            <strong>Category:</strong><br>
                            {{ $complaint['category'] ?? 'Unknown' }}
                        </div>
                        <div class="mb-2">
                            <strong>Priority:</strong><br>
                            <span class="badge bg-primary">{{ ucfirst($complaint['priority'] ?? 'medium') }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Current Usage:</strong><br>
                            <span class="badge bg-info">{{ $complaint['usage_count'] ?? 0 }} times</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="fas fa-tools me-2"></i>Quick Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.common-complaints.edit', $complaint['id'] ?? 1) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-edit me-2"></i>Edit Complaint
                            </a>
                            <button class="btn btn-outline-info btn-sm" onclick="loadTemplate()">
                                <i class="fas fa-template me-2"></i>Load Template
                            </button>
                            <a href="{{ route('admin.common-complaints.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Field Editor Modal -->
    <div class="modal fade" id="fieldEditorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Field Editor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="fieldEditorForm">
                        <div class="mb-3">
                            <label class="form-label">Field Label <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="fieldLabel" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Field Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="fieldName" required>
                            <div class="form-text">Used for form processing (no spaces, lowercase)</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Field Type</label>
                            <select class="form-control" id="fieldType" onchange="toggleFieldOptions()">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="select">Select</option>
                                <option value="radio">Radio</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="file">File</option>
                                <option value="date">Date</option>
                                <option value="number">Number</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Placeholder</label>
                            <input type="text" class="form-control" id="fieldPlaceholder">
                        </div>
                        <div class="mb-3" id="optionsContainer" style="display: none;">
                            <label class="form-label">Options (one per line)</label>
                            <textarea class="form-control" id="fieldOptions" rows="4" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="fieldRequired">
                                <label class="form-check-label">Required Field</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Validation Rules</label>
                            <input type="text" class="form-control" id="fieldValidation" placeholder="e.g., min:3,max:100">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveField()">Save Field</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('center-scripts')
    <script src="{{ asset('assets/admin/js/jquery/jquery.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@endsection

@section('scripts')
    <script>
        let formFields = [];
        let currentFieldIndex = -1;
        let sortable;

        $(document).ready(function() {
            initializeSortable();
            loadExistingForm();
        });

        function initializeSortable() {
            const container = document.getElementById('formFieldsContainer');
            sortable = Sortable.create(container, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: function(evt) {
                    // Update field order
                    const movedField = formFields.splice(evt.oldIndex, 1)[0];
                    formFields.splice(evt.newIndex, 0, movedField);
                    updatePreview();
                }
            });
        }

        function loadExistingForm() {
            // Load existing form structure if available
            const existingForm = @json($complaint['form_structure'] ?? '[]');
            if (existingForm && existingForm !== '[]') {
                try {
                    formFields = typeof existingForm === 'string' ? JSON.parse(existingForm) : existingForm;
                    renderFields();
                    updatePreview();
                } catch (e) {
                    console.error('Error parsing existing form structure:', e);
                }
            }
        }

        function addField(type) {
            currentFieldIndex = -1;
            document.getElementById('fieldType').value = type;
            document.getElementById('fieldLabel').value = '';
            document.getElementById('fieldName').value = '';
            document.getElementById('fieldPlaceholder').value = '';
            document.getElementById('fieldOptions').value = '';
            document.getElementById('fieldRequired').checked = false;
            document.getElementById('fieldValidation').value = '';
            
            toggleFieldOptions();
            const modal = new bootstrap.Modal(document.getElementById('fieldEditorModal'));
            modal.show();
        }

        function editField(index) {
            currentFieldIndex = index;
            const field = formFields[index];
            
            document.getElementById('fieldType').value = field.field_type;
            document.getElementById('fieldLabel').value = field.label;
            document.getElementById('fieldName').value = field.field_name;
            document.getElementById('fieldPlaceholder').value = field.placeholder || '';
            document.getElementById('fieldOptions').value = field.options ? field.options.join('\n') : '';
            document.getElementById('fieldRequired').checked = field.required || false;
            document.getElementById('fieldValidation').value = field.validation || '';
            
            toggleFieldOptions();
            const modal = new bootstrap.Modal(document.getElementById('fieldEditorModal'));
            modal.show();
        }

        function toggleFieldOptions() {
            const fieldType = document.getElementById('fieldType').value;
            const optionsContainer = document.getElementById('optionsContainer');
            
            if (['select', 'radio', 'checkbox'].includes(fieldType)) {
                optionsContainer.style.display = 'block';
            } else {
                optionsContainer.style.display = 'none';
            }
        }

        function saveField() {
            const fieldData = {
                field_type: document.getElementById('fieldType').value,
                label: document.getElementById('fieldLabel').value,
                field_name: document.getElementById('fieldName').value,
                placeholder: document.getElementById('fieldPlaceholder').value,
                required: document.getElementById('fieldRequired').checked,
                validation: document.getElementById('fieldValidation').value
            };

            const options = document.getElementById('fieldOptions').value;
            if (options.trim()) {
                fieldData.options = options.split('\n').filter(opt => opt.trim());
            }

            if (currentFieldIndex === -1) {
                // Add new field
                formFields.push(fieldData);
            } else {
                // Update existing field
                formFields[currentFieldIndex] = fieldData;
            }

            renderFields();
            updatePreview();
            
            bootstrap.Modal.getInstance(document.getElementById('fieldEditorModal')).hide();
        }

        function renderFields() {
            const container = document.getElementById('formFieldsContainer');
            
            if (formFields.length === 0) {
                container.innerHTML = `
                    <div id="fieldsDropZone" class="text-center text-muted p-5">
                        <i class="fas fa-plus-circle fa-3x mb-3"></i>
                        <h5>Build Your Form</h5>
                        <p>Click on field types above to add them to your form</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = formFields.map((field, index) => `
                <div class="card mb-2 field-item" data-index="${index}">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-grip-vertical text-muted me-2"></i>
                                <div>
                                    <strong>${field.label}</strong>
                                    <small class="text-muted d-block">${field.field_type} ${field.required ? '(Required)' : ''}</small>
                                </div>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editField(${index})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="removeField(${index})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function removeField(index) {
            if (confirm('Are you sure you want to remove this field?')) {
                formFields.splice(index, 1);
                renderFields();
                updatePreview();
            }
        }

        function updatePreview() {
            const preview = document.getElementById('formPreview');
            
            if (formFields.length === 0) {
                preview.innerHTML = `
                    <p class="text-muted text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Add fields to see preview
                    </p>
                `;
                return;
            }

            const formHTML = formFields.map(field => {
                let fieldHTML = '';
                
                switch (field.field_type) {
                    case 'text':
                    case 'number':
                    case 'date':
                        fieldHTML = `<input type="${field.field_type}" class="form-control" placeholder="${field.placeholder || ''}" ${field.required ? 'required' : ''}>`;
                        break;
                    case 'textarea':
                        fieldHTML = `<textarea class="form-control" rows="3" placeholder="${field.placeholder || ''}" ${field.required ? 'required' : ''}></textarea>`;
                        break;
                    case 'select':
                        const selectOptions = field.options ? field.options.map(opt => `<option value="${opt}">${opt}</option>`).join('') : '';
                        fieldHTML = `<select class="form-control" ${field.required ? 'required' : ''}><option value="">Choose...</option>${selectOptions}</select>`;
                        break;
                    case 'radio':
                        fieldHTML = field.options ? field.options.map((opt, i) => 
                            `<div class="form-check">
                                <input class="form-check-input" type="radio" name="${field.field_name}" id="${field.field_name}_${i}">
                                <label class="form-check-label" for="${field.field_name}_${i}">${opt}</label>
                            </div>`
                        ).join('') : '';
                        break;
                    case 'checkbox':
                        fieldHTML = field.options ? field.options.map((opt, i) => 
                            `<div class="form-check">
                                <input class="form-check-input" type="checkbox" id="${field.field_name}_${i}">
                                <label class="form-check-label" for="${field.field_name}_${i}">${opt}</label>
                            </div>`
                        ).join('') : '';
                        break;
                    case 'file':
                        fieldHTML = `<input type="file" class="form-control" ${field.required ? 'required' : ''}>`;
                        break;
                }

                return `
                    <div class="mb-3">
                        <label class="form-label">${field.label} ${field.required ? '<span class="text-danger">*</span>' : ''}</label>
                        ${fieldHTML}
                    </div>
                `;
            }).join('');

            preview.innerHTML = formHTML;
        }

        function clearForm() {
            if (confirm('Are you sure you want to clear all fields?')) {
                formFields = [];
                renderFields();
                updatePreview();
            }
        }

        function saveForm() {
            const complaintId = {{ $complaint['id'] ?? 1 }};
            const formStructure = JSON.stringify(formFields);

            $.ajax({
                url: `/admin/common-complaints/${complaintId}/form-structure`,
                type: 'PUT',
                data: {
                    form_structure: formStructure
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success('Form structure saved successfully');
                        } else {
                            alert('Form structure saved successfully');
                        }
                    }
                },
                error: function() {
                    const message = 'Failed to save form structure';
                    if (typeof toastr !== 'undefined') {
                        toastr.error(message);
                    } else {
                        alert(message);
                    }
                }
            });
        }

        function previewForm() {
            if (formFields.length === 0) {
                alert('Please add some fields first');
                return;
            }
            
            // Open preview in new window/modal
            const previewHTML = `
                <html>
                <head>
                    <title>Form Preview</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                </head>
                <body class="p-4">
                    <div class="container">
                        <h3>{{ $complaint['title'] ?? 'Complaint Form' }}</h3>
                        <form>${document.getElementById('formPreview').innerHTML}</form>
                    </div>
                </body>
                </html>
            `;
            
            const previewWindow = window.open('', '_blank');
            previewWindow.document.write(previewHTML);
        }

        function importTemplate() {
            alert('Import template functionality would be implemented here');
        }

        function exportTemplate() {
            if (formFields.length === 0) {
                alert('No fields to export');
                return;
            }
            
            const template = {
                name: '{{ $complaint["title"] ?? "Unnamed Template" }}',
                fields: formFields
            };
            
            const blob = new Blob([JSON.stringify(template, null, 2)], {type: 'application/json'});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'form-template.json';
            a.click();
            URL.revokeObjectURL(url);
        }

        function loadTemplate() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.json';
            input.onchange = function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        try {
                            const template = JSON.parse(e.target.result);
                            if (template.fields && Array.isArray(template.fields)) {
                                formFields = template.fields;
                                renderFields();
                                updatePreview();
                                alert('Template loaded successfully');
                            } else {
                                alert('Invalid template format');
                            }
                        } catch (error) {
                            alert('Error reading template file');
                        }
                    };
                    reader.readAsText(file);
                }
            };
            input.click();
        }
    </script>

    <style>
        .field-item {
            cursor: move;
        }
        .sortable-ghost {
            opacity: 0.4;
        }
        .field-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
    </style>
@endsection
