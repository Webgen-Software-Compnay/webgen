-- Sample Form Templates for Common Complaint Types

-- Bus Service Complaint Form
INSERT INTO complaint_form_structures (
    name, 
    slug, 
    complaint_category_id, 
    form_structure, 
    validation_rules, 
    status, 
    sort_order, 
    created_by, 
    updated_by,
    created_at,
    updated_at
) VALUES (
    'Bus Service Complaint Form',
    'bus-service-complaint-form',
    2, -- Assuming this is the bus service category ID
    '[
        {
            "field_type": "text",
            "field_name": "route_number",
            "label": "Bus Route Number",
            "placeholder": "e.g., 120, 138, 177",
            "required": true,
            "max_length": 20,
            "order": 1,
            "help_text": "Enter the route number displayed on the bus"
        },
        {
            "field_type": "text",
            "field_name": "bus_number",
            "label": "Bus Registration Number",
            "placeholder": "e.g., WP CAB-1234",
            "required": false,
            "max_length": 30,
            "order": 2,
            "help_text": "Registration number if visible"
        },
        {
            "field_type": "select",
            "field_name": "complaint_type",
            "label": "Type of Complaint",
            "required": true,
            "order": 3,
            "options": [
                {"value": "late_arrival", "label": "Late Arrival"},
                {"value": "overcrowding", "label": "Overcrowding"},
                {"value": "rude_behavior", "label": "Rude Behavior"},
                {"value": "poor_condition", "label": "Poor Bus Condition"},
                {"value": "unsafe_driving", "label": "Unsafe Driving"},
                {"value": "no_service", "label": "No Service"},
                {"value": "other", "label": "Other"}
            ]
        },
        {
            "field_type": "date",
            "field_name": "incident_date",
            "label": "Date of Incident",
            "required": true,
            "order": 4
        },
        {
            "field_type": "time",
            "field_name": "incident_time",
            "label": "Time of Incident",
            "required": true,
            "order": 5
        },
        {
            "field_type": "text",
            "field_name": "bus_stop",
            "label": "Bus Stop/Location",
            "placeholder": "e.g., Fort Railway Station, Pettah",
            "required": true,
            "max_length": 100,
            "order": 6
        },
        {
            "field_type": "textarea",
            "field_name": "description",
            "label": "Description of Complaint",
            "placeholder": "Please provide detailed information about the incident...",
            "required": true,
            "rows": 4,
            "max_length": 1000,
            "order": 7
        },
        {
            "field_type": "radio",
            "field_name": "urgency_level",
            "label": "Urgency Level",
            "required": true,
            "order": 8,
            "options": [
                {"value": "low", "label": "Low - General feedback"},
                {"value": "medium", "label": "Medium - Should be addressed"},
                {"value": "high", "label": "High - Urgent attention needed"}
            ]
        },
        {
            "field_type": "file",
            "field_name": "evidence",
            "label": "Supporting Evidence (Optional)",
            "required": false,
            "order": 9,
            "accept": "image/*,.pdf",
            "max_size": 5,
            "help_text": "Upload photos or documents related to your complaint (max 5MB)"
        }
    ]',
    '{}',
    'active',
    1,
    'System',
    'System',
    NOW(),
    NOW()
);

-- Road Infrastructure Complaint Form
INSERT INTO complaint_form_structures (
    name, 
    slug, 
    complaint_category_id, 
    form_structure, 
    validation_rules, 
    status, 
    sort_order, 
    created_by, 
    updated_by,
    created_at,
    updated_at
) VALUES (
    'Road Infrastructure Complaint Form',
    'road-infrastructure-complaint-form',
    3, -- Assuming this is the road infrastructure category ID
    '[
        {
            "field_type": "select",
            "field_name": "road_type",
            "label": "Type of Road",
            "required": true,
            "order": 1,
            "options": [
                {"value": "main_road", "label": "Main Road/Highway"},
                {"value": "urban_road", "label": "Urban Road"},
                {"value": "rural_road", "label": "Rural Road"},
                {"value": "access_road", "label": "Access Road"},
                {"value": "bridge", "label": "Bridge"},
                {"value": "other", "label": "Other"}
            ]
        },
        {
            "field_type": "text",
            "field_name": "road_name",
            "label": "Road Name/Number",
            "placeholder": "e.g., A1 Highway, Galle Road, Marine Drive",
            "required": true,
            "max_length": 150,
            "order": 2
        },
        {
            "field_type": "text",
            "field_name": "nearest_landmark",
            "label": "Nearest Landmark",
            "placeholder": "e.g., Near Town Hall, Opposite Bank of Ceylon",
            "required": true,
            "max_length": 150,
            "order": 3
        },
        {
            "field_type": "select",
            "field_name": "problem_type",
            "label": "Type of Problem",
            "required": true,
            "order": 4,
            "options": [
                {"value": "potholes", "label": "Potholes"},
                {"value": "cracks", "label": "Road Cracks"},
                {"value": "flooding", "label": "Water Logging/Flooding"},
                {"value": "drainage", "label": "Drainage Issues"},
                {"value": "lighting", "label": "Street Lighting"},
                {"value": "signs", "label": "Road Signs/Markings"},
                {"value": "barriers", "label": "Safety Barriers"},
                {"value": "debris", "label": "Road Debris"},
                {"value": "other", "label": "Other"}
            ]
        },
        {
            "field_type": "radio",
            "field_name": "severity",
            "label": "Severity Level",
            "required": true,
            "order": 5,
            "options": [
                {"value": "minor", "label": "Minor - Cosmetic issues"},
                {"value": "moderate", "label": "Moderate - Affects traffic flow"},
                {"value": "severe", "label": "Severe - Safety hazard"}
            ]
        },
        {
            "field_type": "textarea",
            "field_name": "description",
            "label": "Detailed Description",
            "placeholder": "Describe the problem in detail, including size, location, and impact...",
            "required": true,
            "rows": 4,
            "max_length": 1000,
            "order": 6
        },
        {
            "field_type": "checkbox",
            "field_name": "affected_vehicles",
            "label": "Affected Vehicle Types",
            "required": false,
            "order": 7,
            "options": [
                {"value": "cars", "label": "Cars"},
                {"value": "buses", "label": "Buses"},
                {"value": "trucks", "label": "Trucks"},
                {"value": "motorcycles", "label": "Motorcycles"},
                {"value": "bicycles", "label": "Bicycles"},
                {"value": "pedestrians", "label": "Pedestrians"}
            ]
        },
        {
            "field_type": "date",
            "field_name": "first_noticed",
            "label": "When did you first notice this problem?",
            "required": false,
            "order": 8
        },
        {
            "field_type": "file",
            "field_name": "photos",
            "label": "Photos of the Problem",
            "required": false,
            "order": 9,
            "accept": "image/*",
            "max_size": 10,
            "help_text": "Upload clear photos showing the problem (max 10MB)"
        }
    ]',
    '{}',
    'active',
    1,
    'System',
    'System',
    NOW(),
    NOW()
);

-- Water Supply Complaint Form
INSERT INTO complaint_form_structures (
    name, 
    slug, 
    complaint_category_id, 
    form_structure, 
    validation_rules, 
    status, 
    sort_order, 
    created_by, 
    updated_by,
    created_at,
    updated_at
) VALUES (
    'Water Supply Complaint Form',
    'water-supply-complaint-form',
    4, -- Assuming this is the water supply category ID
    '[
        {
            "field_type": "text",
            "field_name": "account_number",
            "label": "Water Board Account Number",
            "placeholder": "e.g., WB123456789",
            "required": false,
            "max_length": 20,
            "order": 1,
            "help_text": "Your water bill account number if available"
        },
        {
            "field_type": "select",
            "field_name": "complaint_category",
            "label": "Complaint Category",
            "required": true,
            "order": 2,
            "options": [
                {"value": "no_water", "label": "No Water Supply"},
                {"value": "low_pressure", "label": "Low Water Pressure"},
                {"value": "dirty_water", "label": "Dirty/Contaminated Water"},
                {"value": "pipe_leak", "label": "Pipe Leak"},
                {"value": "meter_issue", "label": "Water Meter Issue"},
                {"value": "billing", "label": "Billing Issue"},
                {"value": "connection", "label": "New Connection Request"},
                {"value": "other", "label": "Other"}
            ]
        },
        {
            "field_type": "text",
            "field_name": "property_address",
            "label": "Property Address",
            "placeholder": "Full address where the problem exists",
            "required": true,
            "max_length": 200,
            "order": 3
        },
        {
            "field_type": "select",
            "field_name": "area_type",
            "label": "Area Type",
            "required": true,
            "order": 4,
            "options": [
                {"value": "urban", "label": "Urban Area"},
                {"value": "suburban", "label": "Suburban Area"},
                {"value": "rural", "label": "Rural Area"},
                {"value": "estate", "label": "Housing Estate"},
                {"value": "commercial", "label": "Commercial Area"}
            ]
        },
        {
            "field_type": "radio",
            "field_name": "duration",
            "label": "How long has this problem existed?",
            "required": true,
            "order": 5,
            "options": [
                {"value": "today", "label": "Started today"},
                {"value": "few_days", "label": "Few days"},
                {"value": "week", "label": "About a week"},
                {"value": "month", "label": "About a month"},
                {"value": "longer", "label": "Longer than a month"}
            ]
        },
        {
            "field_type": "checkbox",
            "field_name": "affected_times",
            "label": "When does the problem occur?",
            "required": false,
            "order": 6,
            "options": [
                {"value": "morning", "label": "Morning (6AM-12PM)"},
                {"value": "afternoon", "label": "Afternoon (12PM-6PM)"},
                {"value": "evening", "label": "Evening (6PM-10PM)"},
                {"value": "night", "label": "Night (10PM-6AM)"},
                {"value": "all_day", "label": "All day"}
            ]
        },
        {
            "field_type": "textarea",
            "field_name": "description",
            "label": "Detailed Description",
            "placeholder": "Please provide detailed information about the water supply issue...",
            "required": true,
            "rows": 4,
            "max_length": 1000,
            "order": 7
        },
        {
            "field_type": "number",
            "field_name": "affected_households",
            "label": "Number of Affected Households (if known)",
            "required": false,
            "min": 1,
            "max": 1000,
            "order": 8,
            "help_text": "Approximate number if this affects multiple households"
        },
        {
            "field_type": "text",
            "field_name": "contact_number",
            "label": "Alternative Contact Number",
            "placeholder": "e.g., 0771234567",
            "required": false,
            "max_length": 15,
            "order": 9
        }
    ]',
    '{}',
    'active',
    1,
    'System',
    'System',
    NOW(),
    NOW()
);

-- Healthcare Service Complaint Form
INSERT INTO complaint_form_structures (
    name, 
    slug, 
    complaint_category_id, 
    form_structure, 
    validation_rules, 
    status, 
    sort_order, 
    created_by, 
    updated_by,
    created_at,
    updated_at
) VALUES (
    'Healthcare Service Complaint Form',
    'healthcare-service-complaint-form',
    5, -- Assuming this is the healthcare category ID
    '[
        {
            "field_type": "text",
            "field_name": "hospital_clinic_name",
            "label": "Hospital/Clinic Name",
            "placeholder": "e.g., National Hospital, Colombo South Teaching Hospital",
            "required": true,
            "max_length": 150,
            "order": 1
        },
        {
            "field_type": "select",
            "field_name": "facility_type",
            "label": "Type of Healthcare Facility",
            "required": true,
            "order": 2,
            "options": [
                {"value": "government_hospital", "label": "Government Hospital"},
                {"value": "private_hospital", "label": "Private Hospital"},
                {"value": "clinic", "label": "Clinic"},
                {"value": "dispensary", "label": "Dispensary"},
                {"value": "specialist_center", "label": "Specialist Center"},
                {"value": "emergency_service", "label": "Emergency Service"}
            ]
        },
        {
            "field_type": "select",
            "field_name": "complaint_category",
            "label": "Complaint Category",
            "required": true,
            "order": 3,
            "options": [
                {"value": "long_waiting", "label": "Long Waiting Times"},
                {"value": "poor_treatment", "label": "Poor Treatment Quality"},
                {"value": "rude_staff", "label": "Rude Staff Behavior"},
                {"value": "billing_issue", "label": "Billing/Payment Issue"},
                {"value": "facility_condition", "label": "Poor Facility Conditions"},
                {"value": "medication", "label": "Medication Issues"},
                {"value": "appointment", "label": "Appointment Problems"},
                {"value": "discrimination", "label": "Discrimination"},
                {"value": "other", "label": "Other"}
            ]
        },
        {
            "field_type": "text",
            "field_name": "department_ward",
            "label": "Department/Ward",
            "placeholder": "e.g., Emergency, Cardiology, OPD",
            "required": false,
            "max_length": 100,
            "order": 4
        },
        {
            "field_type": "date",
            "field_name": "visit_date",
            "label": "Date of Visit",
            "required": true,
            "order": 5
        },
        {
            "field_type": "time",
            "field_name": "visit_time",
            "label": "Approximate Time of Visit",
            "required": false,
            "order": 6
        },
        {
            "field_type": "radio",
            "field_name": "patient_type",
            "label": "Patient Type",
            "required": true,
            "order": 7,
            "options": [
                {"value": "myself", "label": "Myself"},
                {"value": "family_member", "label": "Family Member"},
                {"value": "friend", "label": "Friend"},
                {"value": "witnessed", "label": "I witnessed the incident"}
            ]
        },
        {
            "field_type": "textarea",
            "field_name": "description",
            "label": "Detailed Description of the Problem",
            "placeholder": "Please describe what happened, who was involved, and how it affected you or the patient...",
            "required": true,
            "rows": 5,
            "max_length": 1500,
            "order": 8
        },
        {
            "field_type": "text",
            "field_name": "staff_name",
            "label": "Staff Member Name (if known)",
            "placeholder": "Name of doctor, nurse, or staff member involved",
            "required": false,
            "max_length": 100,
            "order": 9
        },
        {
            "field_type": "radio",
            "field_name": "severity",
            "label": "How serious was this issue?",
            "required": true,
            "order": 10,
            "options": [
                {"value": "minor", "label": "Minor inconvenience"},
                {"value": "moderate", "label": "Significant problem"},
                {"value": "serious", "label": "Serious issue affecting health"},
                {"value": "critical", "label": "Critical - life threatening"}
            ]
        }
    ]',
    '{}',
    'active',
    1,
    'System',
    'System',
    NOW(),
    NOW()
);

-- Education Service Complaint Form
INSERT INTO complaint_form_structures (
    name, 
    slug, 
    complaint_category_id, 
    form_structure, 
    validation_rules, 
    status, 
    sort_order, 
    created_by, 
    updated_by,
    created_at,
    updated_at
) VALUES (
    'Education Service Complaint Form',
    'education-service-complaint-form',
    6, -- Assuming this is the education category ID
    '[
        {
            "field_type": "text",
            "field_name": "institution_name",
            "label": "School/Institution Name",
            "placeholder": "Name of the school, college, or university",
            "required": true,
            "max_length": 150,
            "order": 1
        },
        {
            "field_type": "select",
            "field_name": "institution_type",
            "label": "Type of Institution",
            "required": true,
            "order": 2,
            "options": [
                {"value": "government_school", "label": "Government School"},
                {"value": "private_school", "label": "Private School"},
                {"value": "international_school", "label": "International School"},
                {"value": "university", "label": "University"},
                {"value": "technical_college", "label": "Technical College"},
                {"value": "vocational_institute", "label": "Vocational Institute"},
                {"value": "other", "label": "Other"}
            ]
        },
        {
            "field_type": "select",
            "field_name": "complaint_category",
            "label": "Complaint Category",
            "required": true,
            "order": 3,
            "options": [
                {"value": "teaching_quality", "label": "Teaching Quality"},
                {"value": "infrastructure", "label": "Infrastructure/Facilities"},
                {"value": "administration", "label": "Administrative Issues"},
                {"value": "fees_billing", "label": "Fees/Billing"},
                {"value": "discrimination", "label": "Discrimination/Harassment"},
                {"value": "safety", "label": "Safety Concerns"},
                {"value": "transport", "label": "School Transport"},
                {"value": "meals", "label": "School Meals"},
                {"value": "admissions", "label": "Admissions Process"},
                {"value": "other", "label": "Other"}
            ]
        },
        {
            "field_type": "text",
            "field_name": "grade_class",
            "label": "Grade/Class/Department",
            "placeholder": "e.g., Grade 10A, Computer Science Department",
            "required": false,
            "max_length": 50,
            "order": 4
        },
        {
            "field_type": "radio",
            "field_name": "complainant_type",
            "label": "You are a:",
            "required": true,
            "order": 5,
            "options": [
                {"value": "student", "label": "Student"},
                {"value": "parent", "label": "Parent/Guardian"},
                {"value": "teacher", "label": "Teacher/Staff"},
                {"value": "other", "label": "Other"}
            ]
        },
        {
            "field_type": "text",
            "field_name": "student_name",
            "label": "Student Name (if complaint is about a specific student)",
            "placeholder": "Name of the affected student",
            "required": false,
            "max_length": 100,
            "order": 6
        },
        {
            "field_type": "date",
            "field_name": "incident_date",
            "label": "Date of Incident",
            "required": false,
            "order": 7
        },
        {
            "field_type": "textarea",
            "field_name": "description",
            "label": "Detailed Description",
            "placeholder": "Please provide detailed information about the issue, including what happened, when, where, and who was involved...",
            "required": true,
            "rows": 5,
            "max_length": 1500,
            "order": 8
        },
        {
            "field_type": "text",
            "field_name": "staff_involved",
            "label": "Staff Member(s) Involved (if any)",
            "placeholder": "Names of teachers, administrators, or staff involved",
            "required": false,
            "max_length": 150,
            "order": 9
        },
        {
            "field_type": "select",
            "field_name": "urgency",
            "label": "Urgency Level",
            "required": true,
            "order": 10,
            "options": [
                {"value": "low", "label": "Low - General feedback"},
                {"value": "medium", "label": "Medium - Should be addressed soon"},
                {"value": "high", "label": "High - Urgent attention needed"},
                {"value": "emergency", "label": "Emergency - Immediate action required"}
            ]
        },
        {
            "field_type": "checkbox",
            "field_name": "resolution_desired",
            "label": "Desired Resolution (check all that apply)",
            "required": false,
            "order": 11,
            "options": [
                {"value": "investigation", "label": "Investigation of the matter"},
                {"value": "apology", "label": "Formal apology"},
                {"value": "policy_change", "label": "Policy change"},
                {"value": "training", "label": "Staff training"},
                {"value": "compensation", "label": "Compensation"},
                {"value": "other", "label": "Other"}
            ]
        }
    ]',
    '{}',
    'active',
    1,
    'System',
    'System',
    NOW(),
    NOW()
);
