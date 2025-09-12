<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommonComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complaints = [
            // Food Safety & Quality Complaints
            [
                'complaint_category_id' => 1,
                'title' => 'Expired Food Products Sold',
                'description' => 'Reports of expired food items being sold in local markets or stores causing health concerns.',
                'form_data' => json_encode(['location' => 'Colombo District', 'store_name' => 'ABC Supermarket', 'product_name' => 'Milk Powder', 'expiry_date' => '2025-08-01'])
            ],
            [
                'complaint_category_id' => 1,
                'title' => 'Food Contamination at Restaurant',
                'description' => 'Customer reported food poisoning after eating at a popular restaurant chain.',
                'form_data' => json_encode(['restaurant_name' => 'Golden Spoon', 'location' => 'Kandy', 'date_of_incident' => '2025-09-05', 'symptoms' => 'Nausea, vomiting'])
            ],
            [
                'complaint_category_id' => 1,
                'title' => 'Unhygienic Food Preparation',
                'description' => 'Witnessed unsanitary food handling practices at street food vendor.',
                'form_data' => json_encode(['vendor_location' => 'Galle Road', 'time_observed' => 'Morning', 'specific_issues' => 'No gloves, dirty utensils'])
            ],
            [
                'complaint_category_id' => 1,
                'title' => 'Mislabeled Organic Products',
                'description' => 'Products labeled as organic found to contain chemical residues.',
                'form_data' => json_encode(['product_brand' => 'Nature Plus', 'test_results' => 'Available', 'purchase_location' => 'Health Food Store'])
            ],
            [
                'complaint_category_id' => 1,
                'title' => 'Foreign Objects in Packaged Food',
                'description' => 'Metal pieces found in canned food products posing safety hazards.',
                'form_data' => json_encode(['product_name' => 'Canned Fish', 'batch_number' => 'CF2025089', 'manufacturer' => 'Ocean Fresh'])
            ],

            // Transportation Complaints
            [
                'complaint_category_id' => 2,
                'title' => 'Bus Service Delays',
                'description' => 'Frequent and prolonged delays in public bus services causing inconvenience to daily commuters.',
                'form_data' => json_encode(['route_number' => '120', 'time_of_day' => 'Morning Peak', 'average_delay' => '45 minutes', 'frequency' => 'Daily'])
            ],
            [
                'complaint_category_id' => 2,
                'title' => 'Overcrowded Public Transport',
                'description' => 'Buses and trains operating beyond safe capacity limits.',
                'form_data' => json_encode(['transport_type' => 'Bus', 'route' => 'Colombo-Gampaha', 'peak_hours' => '7-9 AM', 'safety_concern' => 'High'])
            ],
            [
                'complaint_category_id' => 2,
                'title' => 'Reckless Driving by Bus Operators',
                'description' => 'Bus drivers engaging in dangerous driving practices endangering passengers.',
                'form_data' => json_encode(['bus_number' => 'NC-1234', 'incident_type' => 'Speeding', 'location' => 'Negombo Road', 'time' => '2:30 PM'])
            ],
            [
                'complaint_category_id' => 2,
                'title' => 'Poor Condition of Railway Facilities',
                'description' => 'Railway stations and trains in deteriorating condition affecting passenger comfort.',
                'form_data' => json_encode(['station_name' => 'Colombo Fort', 'issues' => 'Broken seats, poor lighting', 'train_line' => 'Main Line'])
            ],
            [
                'complaint_category_id' => 2,
                'title' => 'Taxi Fare Overcharging',
                'description' => 'Taxi and three-wheeler drivers charging excessive fares, especially to tourists.',
                'form_data' => json_encode(['vehicle_type' => 'Three-wheeler', 'normal_fare' => '200 LKR', 'charged_fare' => '500 LKR', 'route' => 'Airport to Colombo'])
            ],

            // Healthcare Complaints
            [
                'complaint_category_id' => 4,
                'title' => 'Hospital Staff Shortage',
                'description' => 'Inadequate medical staff availability in public hospitals affecting quality of patient care.',
                'form_data' => json_encode(['hospital_name' => 'Colombo General Hospital', 'department' => 'Emergency', 'waiting_time' => '4 hours', 'staff_ratio' => '1:20'])
            ],
            [
                'complaint_category_id' => 4,
                'title' => 'Medication Shortage in Hospitals',
                'description' => 'Critical medications unavailable in government hospitals forcing patients to buy from private pharmacies.',
                'form_data' => json_encode(['hospital_name' => 'Kandy Hospital', 'medication_type' => 'Insulin', 'shortage_duration' => '2 weeks'])
            ],
            [
                'complaint_category_id' => 4,
                'title' => 'Unsanitary Hospital Conditions',
                'description' => 'Poor hygiene and cleanliness standards in hospital wards and facilities.',
                'form_data' => json_encode(['hospital_name' => 'Base Hospital Chilaw', 'ward_type' => 'General Ward', 'specific_issues' => 'Dirty bathrooms, pest infestation'])
            ],
            [
                'complaint_category_id' => 4,
                'title' => 'Long Wait Times for Appointments',
                'description' => 'Patients waiting months for specialist consultations in government hospitals.',
                'form_data' => json_encode(['specialty' => 'Cardiology', 'wait_time' => '6 months', 'hospital' => 'National Hospital'])
            ],
            [
                'complaint_category_id' => 4,
                'title' => 'Medical Equipment Malfunction',
                'description' => 'Critical medical equipment not functioning properly affecting patient diagnosis and treatment.',
                'form_data' => json_encode(['equipment_type' => 'MRI Scanner', 'hospital' => 'Teaching Hospital Karapitiya', 'downtime' => '2 months'])
            ],

            // Utility Services Complaints
            [
                'complaint_category_id' => 8,
                'title' => 'Street Lighting Issues',
                'description' => 'Non-functional street lights creating safety concerns for pedestrians and motorists during nighttime.',
                'form_data' => json_encode(['street_name' => 'Galle Road, Dehiwala', 'number_of_lights' => '15', 'duration_non_functional' => '3 weeks'])
            ],
            [
                'complaint_category_id' => 8,
                'title' => 'Frequent Power Outages',
                'description' => 'Regular electricity supply interruptions affecting daily life and business operations.',
                'form_data' => json_encode(['area' => 'Maharagama', 'frequency' => 'Daily', 'duration' => '2-4 hours', 'time_pattern' => 'Evening hours'])
            ],
            [
                'complaint_category_id' => 8,
                'title' => 'Water Supply Contamination',
                'description' => 'Dirty, discolored water being supplied through municipal water lines.',
                'form_data' => json_encode(['area' => 'Mount Lavinia', 'water_color' => 'Brown', 'duration' => '1 week', 'health_impact' => 'Stomach issues reported'])
            ],
            [
                'complaint_category_id' => 8,
                'title' => 'Poor Road Conditions',
                'description' => 'Roads with numerous potholes and damaged surfaces causing vehicle damage and accidents.',
                'form_data' => json_encode(['road_name' => 'Kandy-Nuwara Eliya Road', 'length_affected' => '5 km', 'pothole_count' => '50+', 'accident_reports' => '3 this month'])
            ],
            [
                'complaint_category_id' => 8,
                'title' => 'Garbage Collection Delays',
                'description' => 'Municipal garbage collection services not operating on schedule leading to waste accumulation.',
                'form_data' => json_encode(['area' => 'Nugegoda', 'scheduled_day' => 'Tuesday', 'actual_collection' => 'Friday', 'delay_frequency' => 'Weekly'])
            ],

            // Consumer Protection Complaints
            [
                'complaint_category_id' => 6,
                'title' => 'Company Overcharging Customers',
                'description' => 'Private companies charging additional fees without proper notification or justification.',
                'form_data' => json_encode(['company_name' => 'Lanka Telecom', 'service_type' => 'Internet', 'additional_charge' => '500 LKR', 'notification_given' => 'No'])
            ],
            [
                'complaint_category_id' => 6,
                'title' => 'False Advertisement Claims',
                'description' => 'Companies making misleading claims about product benefits and features in advertisements.',
                'form_data' => json_encode(['company_name' => 'Beauty Plus', 'product_type' => 'Cosmetics', 'false_claim' => 'Instant whitening', 'actual_result' => 'No visible change'])
            ],
            [
                'complaint_category_id' => 6,
                'title' => 'Defective Electronic Products',
                'description' => 'Electronic devices failing within warranty period with poor after-sales service.',
                'form_data' => json_encode(['product_type' => 'Smartphone', 'brand' => 'TechMax', 'failure_time' => '2 months', 'warranty_claim_status' => 'Rejected'])
            ],
            [
                'complaint_category_id' => 6,
                'title' => 'Banking Service Charges',
                'description' => 'Banks imposing unexpected charges and fees without proper customer notification.',
                'form_data' => json_encode(['bank_name' => 'National Bank', 'charge_type' => 'ATM withdrawal fee', 'amount' => '25 LKR', 'previous_free' => 'Yes'])
            ],
            [
                'complaint_category_id' => 6,
                'title' => 'Insurance Claim Rejection',
                'description' => 'Insurance companies unfairly rejecting legitimate claims with unclear explanations.',
                'form_data' => json_encode(['insurance_company' => 'Secure Life', 'claim_type' => 'Vehicle accident', 'claim_amount' => '150,000 LKR', 'rejection_reason' => 'Policy technicality'])
            ],

            // Environmental Complaints
            [
                'complaint_category_id' => 7,
                'title' => 'Pesticide Misuse in Agriculture',
                'description' => 'Improper use of pesticides in farming causing environmental damage and health risks.',
                'form_data' => json_encode(['location' => 'Kandy District', 'crop_type' => 'Vegetables', 'pesticide_name' => 'Chlorpyrifos', 'health_effects' => 'Respiratory issues in nearby residents'])
            ],
            [
                'complaint_category_id' => 7,
                'title' => 'Industrial Water Pollution',
                'description' => 'Factories discharging untreated waste water into rivers and streams.',
                'form_data' => json_encode(['factory_name' => 'Textile Mills Ltd', 'water_body' => 'Kelani River', 'pollution_type' => 'Chemical dyes', 'impact' => 'Fish death, water discoloration'])
            ],
            [
                'complaint_category_id' => 7,
                'title' => 'Air Pollution from Vehicles',
                'description' => 'Excessive vehicle emissions in urban areas causing air quality deterioration.',
                'form_data' => json_encode(['area' => 'Colombo city center', 'pollution_source' => 'Buses and trucks', 'health_impact' => 'Increased asthma cases', 'measurement' => 'Above WHO standards'])
            ],
            [
                'complaint_category_id' => 7,
                'title' => 'Illegal Waste Dumping',
                'description' => 'Unauthorized dumping of industrial and household waste in public areas and water bodies.',
                'form_data' => json_encode(['location' => 'Muthurajawela wetland', 'waste_type' => 'Plastic and electronic waste', 'dumping_frequency' => 'Weekly', 'environmental_impact' => 'Habitat destruction'])
            ],
            [
                'complaint_category_id' => 7,
                'title' => 'Deforestation Activities',
                'description' => 'Unauthorized clearing of forest land for commercial and residential development.',
                'form_data' => json_encode(['forest_area' => 'Sinharaja buffer zone', 'area_cleared' => '2 hectares', 'purpose' => 'Commercial development', 'wildlife_impact' => 'Habitat loss'])
            ],

            // Education Complaints
            [
                'complaint_category_id' => 3,
                'title' => 'Teacher Shortage in Rural Schools',
                'description' => 'Government schools in rural areas lacking adequate number of qualified teachers.',
                'form_data' => json_encode(['school_name' => 'Gampaha Central College', 'missing_subjects' => 'Mathematics, Science', 'student_count' => '400', 'teacher_count' => '8'])
            ],
            [
                'complaint_category_id' => 3,
                'title' => 'Poor School Infrastructure',
                'description' => 'School buildings in deteriorating condition affecting student learning environment.',
                'form_data' => json_encode(['school_name' => 'Anuradhapura Primary School', 'issues' => 'Leaking roof, broken windows', 'student_count' => '300', 'repair_needed' => 'Urgent'])
            ],
            [
                'complaint_category_id' => 3,
                'title' => 'Lack of Educational Resources',
                'description' => 'Schools lacking basic educational materials including textbooks and laboratory equipment.',
                'form_data' => json_encode(['school_name' => 'Ratnapura High School', 'missing_resources' => 'Science lab equipment, computers', 'grade_levels_affected' => '6-13'])
            ]
        ];

        foreach ($complaints as $complaint) {
            \App\Models\CommonComplaint::create($complaint);
        }
    }
}
